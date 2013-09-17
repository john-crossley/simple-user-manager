<?php

class Validator
{

  /**
   * array(
   *   'username' => 'admin',
   *   'password' => 'pass1'
   * )
   */
  protected $values = array();

  protected $messages = array(
    'required'    => 'The :attribute field is required',
    'min'         => 'The :attribute should be a minimum of :min characters',
    'max'         => 'The :attribute should be a maximum of :max characters',
    'match'       => 'The :attribute fields do not match',
    'unique'      => 'The :attribute has already been taken',
    'valid_email' => ':email doesn\'t seem to be a valid email',
    'valid_url'   => ':url doesn\'t seem to be a valid URL',
    'banned'      => ':email has used a banned domain. Change your email.'
  );

  protected $customAttributeMessages = array();
  protected $errorMessages = array();


  protected $errors = array();

  public function make(array $data, array $rules = array(), array $messages = array())
  {
    // Do we have any custom messages?
    if (!empty($messages)) {
      foreach ($messages as $key => $value) {
        // Explode the key
        $key = explode('.', $key);
        if (count($key) >= 2) {
          // What's the attribute Cindy?
          $attribute = $key[0];
          // Whats the rule this message should be applied to?
          $rule = $key[1];

          // Store
          $this->customAttributeMessages[$attribute] = array(
            $rule => $value
          );
        }
      }
      $this->messages = array_replace($this->messages, $messages);
    }

    // Loop through the data we need to validate
    foreach ($data as $attribute => $value) {

      // Set the value here bevause the value
      // may not be backed up during validation
      $_SESSION['FORM_ERRORS'][$attribute]['value'] = $value;

      // Store the value.
      $this->values[$attribute] = $value;

      // Check to see if a rule matches an attribute
      if (array_key_exists($attribute, $rules)) {
        foreach ($rules[$attribute] as $rule) {
          $this->validate($value, $rule, $attribute);
        }
      }

    }
    // Cleanup
    $this->removeAnyPasswords();
  }

  protected function removeAnyPasswords()
  {
    // pass([\S]+)?
    // Match something like a pass, password etc.
    foreach ($this->values as $attr => $value) {
      if (preg_match('/pass([\S]+)?/', $attr)) {
        unset($this->values[$attr]);
        unset($_SESSION['FORM_ERRORS'][$attr]['value']);
      }
    }
  }

  protected function storeErrorInformation($attribute, $rule, $data = array())
  {
    // Set an error for the attribute
    // Eg: $this->errors['username'] = true;
    $this->errors[$attribute] = true;

    $this->errorMessages[$attribute][] = $this->createMessage($this->messages[$rule], $data, $rule, $attribute);

    // Store in a session
    $_SESSION['FORM_ERRORS'][$attribute]['error'] = true;
    $_SESSION['FORM_ERRORS'][$attribute]['message'] = $this->errorMessages[$attribute];
  }

  protected function createMessage($message, $attributes, $rule, $attr)
  {
    $tmp = $message;
    foreach ($attributes as $attribute => $value) {
      if (isset($this->customAttributeMessages[$attr][$rule])) {
        $tmp = $this->customAttributeMessages[$attr][$rule];
      }
      $tmp = preg_replace("/$attribute/", $value, $tmp);
    }
    return $tmp;
  }

  protected function validate($value, $rule, $attribute)
  {
    $rule = explode(':', $rule);

    switch (strtolower($rule[0])) {
      case 'required':
        if (empty($value)) {
          $this->storeErrorInformation($attribute, 'required', array(
            ':attribute' => $attribute
          ));
        }
        break;

      case 'min':
        if (strlen($value) <= $rule[1]-1) {
          $this->storeErrorInformation($attribute, 'min', array(
            ':attribute' => $attribute,
            ':min'       => $rule[1]
          ));
        }
        break;

      case 'max':
        if (strlen($value) > $rule[1]) {
          $this->storeErrorInformation($attribute, 'max', array(
            ':attribute' => $attribute,
            ':max'       => $rule[1]
          ));
        }
        break;

      case 'valid_email':
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
          $this->storeErrorInformation($attribute, 'valid_email', array(
            ':email' => $value
          ));
        }
        break;

      case 'banned':
        // Cheap way but works fine
        $bannedExtensions = explode(' ', $rule[1]);
        $extension = explode('@', $value);
        $extension = $extension[1];
        // Check to see if the clien supplied a banned extension
        if (in_array($extension, $bannedExtensions)) {
          $this->storeErrorInformation($attribute, 'banned', array(
            ':email' => $value
          ));
        }
        break;

      case 'valid_url':
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
          $this->storeErrorInformation($attribute, 'valid_url', array(
            ':url' => $value
          ));
        }
        break;

      case 'unique':
        // Client may not have DB accessbile so rather than throwing
        // a nasty fatal we shall check to see if the DB class is
        // available
        if (class_exists('DB')) {
          if (DB::table($rule[1])->where($attribute, '=', '\''.$value.'\'')->count()->count > 0) {
            $this->storeErrorInformation($attribute, 'unique', array(
              ':attribute' => $attribute
            ));
          }
        } else throw new \Exception('[Error] DB Class has not been found!');
        break;

      case 'match':
        if (!isset($rule[1]))
          break; // I'm gone

        // Field data
        $firstField   = $attribute;
        $secondField  = $rule[1];

        // Store the values
        $firstFieldValue  = $this->values[$firstField];
        $secondFieldValue = $this->values[$secondField];

        if ($firstFieldValue !== $secondFieldValue) {
          $this->storeErrorInformation($attribute, 'match', array(
            ':attribute' => $secondField
          ));
        }
        break;

      default:
        # code...
        break;
    }
  }

  public function hasMessage($attribute)
  {
    if (isset($this->errorMessages[$attribute])) {
      return $this->errorMessages[$attribute][0];
    }
  }

  public static function hasMessageSession($attribute)
  {
    if (isset($_SESSION['FORM_ERRORS'][$attribute])) {
      $data = $_SESSION['FORM_ERRORS'];
      if (isset($data[$attribute]['message'])) {
        $_SESSION['FORM_ERRORS'][$attribute]['message'] = null;
        // Return the first error message
        return $data[$attribute]['message'][0];
      }
    }
  }

  public function hasValue($attribute)
  {
    if (isset($this->values[$attribute])) {
      return $this->values[$attribute];
    }
  }

  public static function hasValueSession($attribute)
  {
    if (isset($_SESSION['FORM_ERRORS'][$attribute])) {
      $data = $_SESSION['FORM_ERRORS'];
      if (isset($data[$attribute]['value'])) {
        $_SESSION['FORM_ERRORS'][$attribute]['value'] = null;
        return $data[$attribute]['value'];
      }
    }
  }

  public function hasError($attribute)
  {
    if (isset($this->errors[$attribute])) {
      return 'error';
    }
  }

  public static function hasErrorSession($attribute)
  {
    if (isset($_SESSION['FORM_ERRORS'][$attribute])) {
      $data = $_SESSION['FORM_ERRORS']; // tmp
      if (isset($data[$attribute]['error'])) {
        $_SESSION['FORM_ERRORS'][$attribute]['error'] = null; // Kill it
        return 'error';
      }
    }
  }

  public function success()
  {
    return (empty($this->errors)) ? true : false;
  }

  public function fails()
  {
    return (!empty($this->errors)) ? true : false;
  }
}
