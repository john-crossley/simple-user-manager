<?php

class User extends SingletonAbstract
{
  protected $_userData;
  protected $_newUser;
  protected $_roles;
  protected $_errors = array();
  protected $messages;

  protected function init()
  {
    $this->messages = new Message;
    $this->_userData = new stdClass;
    $this->_userData->originalPassword = null;
    // Assume new user
    $this->_newUser = true;
  }

  /**
   * Gets the current users protected pages list/
   * @return array
   */
  public function getPrivatePages()
  {
    if (!$this->id) return false;
    $result = DB::table('private_pages')->where('user_id', '=', $this->id)->get();

    if ($result) return $result;

    return false;
  }

  public static function deleteUserById($id)
  {
    return DB::table('user')->where('id', '=', $id)->delete();
  }

  public static function getAllUsers($limit, $offset, $orderBy)
  {
    return DB::table('user')
              ->grab($limit)
              ->offset($offset)
              ->order_by('id', $orderBy)
              ->get();
  }

  public function __get($property)
  {
    return (!empty($this->_userData->$property)) ?
      $this->_userData->$property : false;
  }

  public function __set($property, $value)
  {
    $this->_userData->$property = strip_tags($value);
  }

  public static function inUse($column, $value)
  {
    $count = DB::table('user')
                ->where($column, '=', $value)
                ->count()->count;

    if ($count > 0)
      return true; // It's in use

    return false;
  }

  public static function auth($username, $password, $email = false)
  {
    // Create a new instance of the user.
    $user = User::getInstance();

    $user->_newUser = false;
    if ($email) {
      $user->_userData = DB::table('user')->grab(1)->findByEmail('\''.$username.'\'');
    } else {
      $user->_userData = DB::table('user')->grab(1)->findByUsername('\''.$username.'\'');
    }

    if (empty($user->_userData)) {
      // No user has been found
      Flash::make('danger', _rd('record', $username, RECORD_NOT_FOUND));
      redirect('login.php');
    }

    // Prepare the password validation
    $password = Crypter::makePassword($password, $user->_userData->salt);
    $usersActualPassword = $user->_userData->password;

    // Check to see if the passwords match
    if ($password === $usersActualPassword) {

      // Store the users original password.
      $user->_userData->originalPassword = $password;

      // They have been verified.
      $user->getCurrentUserRole($user->_userData->id);

      // Now check to see if the user has been banned
      if ($user->checkPermission('bannedMember')) {
        // Banned slut.
        Flash::make('error', _rd('username', $username, BANNED_ACCOUNT));
        redirect('login.php');
      }

      // Check to see if this person is verified
      if ($user->verified < 1) {
        // Nope just as I expected, this snake isn't verified
        Flash::make('error', _rd('username', $username, ACCOUNT_NOT_YET_ACTIVATED));
        redirect('login.php');
      }

      // Right well, all seems to be well here. I'll inform mama moomin
      // of your presence
      save_user($user);

      // Send the user to outerspace => ALL ABOARD THE HASH ROCKET!
      // Zooooooooooooooooooooom
      Flash::make('success', _rd('username', $username, USER_LOGGED_IN));
      redirect($user->redirect_to);
    } else {
      // Incorrect username and or password
      Flash::make('error', INVALID_USERNAME_AND_OR_PASSWORD);
      redirect('login.php');
    }
    return $user;
  }

  public function getCurrentUserRole($userId, $id = false)
  {
    // Do what? All that!
    $role = DB::table('user_role AS t1')
                ->grab(1)
                ->join('role AS t2', 't1.role_id', '=', 't2.role_id')
                ->where('t1.user_id', '=',  (int)$userId)
                ->get(array('t1.role_id', 't2.role_name'));

    if ($role) { // Yes we have a role
      if ($id) return $role;
      $this->_roleName = $role->role_name;
      $this->_roles[$role->role_name] = Role::getRolePermissions($role->role_id);
    }
    return $this;
  }

  public function checkPermission($permission)
  {
    if (isset($this->_roles)) {
      foreach ($this->_roles as $role) {
        if ($role->hasPermission($permission))
          return true;
      }
    }
  }

  public static function findByUsername($username)
  {
    $user = User::getInstance();
    // Dirty cleaning...
    $username = strip_tags($username);

    $user->_newUser = false;

    $result = DB::table('user')->grab(1)->find(array('`username`' => '\''.$username.'\''));
    if ($result) {
      $user->_userData = $result;
      $user->_userData->originalPassword = $user->_userData->password;
    }
    return $user;
  }

  public static function findById($id)
  {
    // Some basic human rights
    $user = User::getInstance();
    $user->_newUser = false;
    // Check to see if the user exists
    $result = DB::table('user')->grab(1)->find(array('id' => (int)$id));

    // Right we have a result.. Don't we?
    if ($result) {
      $user->_userData = $result;
      $user->_userData->originalPassword = $user->_userData->password;
    }

    // Return the user my child
    return $user;
  }

  public static function findByEmail($email)
  {
    $user = User::getInstance();
    $user->_newUser = false;
    $result = DB::table('user')
              ->grab(1)
              ->where('email', '=', '\''.strip_tags($email).'\'' )
              ->get();

    if ($result) {
      $user->_userData = $result;
      $user->_userData->originalPassword = $user->_userData->password;
    }

    return $user;
  }

  public function save()
  {
    $data = array();

    foreach ($this->_userData as $field => $value) {
      // Don't let the following be modified
      if ($field == 'id' || $field == 'salt' || $field == 'created_at')
        continue; // Skip
      $data[$field] = $value;
    }

    // Todo: Change!
    if ($this->_newUser) {
      if (!isset($data['password'])) {
        $this->_errors[] = "Before the user can be saved, a password needs to be supplied.";
      }

      if (!isset($data['username'])) {
        $this->_errors[] = "Before the user can be saved, a username needs to be supplied.";
      }

      if (!isset($data['email'])) {
        $this->_errors[] = "Before a user can be saved, an email address needs to be supplied.";
      }
    }

    // Check to see if this is a new user
    if (!$this->_newUser) {
      // Has the users password been changed?
      if ($this->_userData->originalPassword !== $this->_userData->password) {
        $password = Crypter::prepPassword($data['password']);
        $data['password'] = $password['password'];
        $data['salt']     = $password['salt'];
      }
    }

    // Record the updated at.
    $data['updated_at'] = date('Y-m-d H:i:s');

    // Remove the data that doesn't match the field names in the database
    unset($data['originalPassword']);
    unset($data['_roleName']);

    if ($this->_newUser) {
      // Before we continue do we have any errors?
      if (!empty($this->_errors)) {
        // Obviously this isn't the best way to handle the errors but whatever it works.
        throw new exception(implode('<br>', $this->_errors));
      }

      // Crypt it up
      $password = Crypter::prepPassword($data['password']);
      $data['password']   = $password['password'];
      $data['salt']       = $password['salt'];
      $data['created_at'] = date('Y-m-d H:i:s');
      return DB::table('user')->insert_get_id($data);
    }

    // Not a new user you say? Then in that case update them!
    return DB::table('user')->where('id', '=', $this->id)->update($data);
  }

  public function logout()
  {
    if (_logged_in())
      unset($_SESSION['USER']);
    return true;
  }

}
