<?php
/**
 * Advanced user managers own simple `ACL`
 */
class Role extends SingletonAbstract
{
  public $permissions;

  protected function init()
  {
    $this->permissions = array();
  }

  public static function getRoleNameFromRoleId($id)
  {
    return DB::table('role')
              ->where('role_id', '=', (int)$id)
              ->grab(1)
              ->get(array('role_name'))
              ->role_name;
  }

  public static function deleteOnlyRolePermissions($id)
  {
    // Do we need to delete any?
    $count = DB::table('role_permission')->where('role_id', '=', (int)$id)->count();
    if ((int)$count->count > 0) {
      return DB::table('role_permission')->where('role_id', '=', (int)$id)->delete();
    }
    return true;
  }

  public static function updateUserRole($userId, $roleId)
  {
    return DB::table('user_role')
        ->where('user_id', '=', $userId)
        ->update(array('role_id' => $roleId));
  }

  public static function insertUserRole($userId, $roleId)
  {
    return DB::table('user_role')
              ->insert(array('user_id' => $userId, 'role_id' => $roleId));
  }

  public static function checkRoleNameIsNotInUse($role_name)
  {
    return (DB::table('role')
            ->where('role_name', '=', $role_name)
            ->count()->count > 0) ? true : false;
  }

  public static function getSystemUserGroups()
  {
    return DB::table('role')->get();
  }

  public static function removeUserRoleFromDatabase($roleId)
  {
    return DB::table('role AS t1')
              ->left_join('user_role AS t2', 't1.role_id', '=', 't2.role_id')
              ->left_join('role_permission AS t3', 't1.role_id', '=', 't3.role_id')
              ->where('t1.role_id', '=', (int)$roleId)
              ->delete('t1.*, t2.*, t3.*');
  }

  public static function getAvailablePermissions()
  {
    $result = DB::table('permission')
                  ->get();

    if ($result) return $result;
  }

  public static function getRolePermissions($role_id)
  {
    $role = Role::getInstance();

    $result = DB::table('role_permission AS t1')
                  ->join('permission AS t2', 't1.permission_id', '=', 't2.id')
                  ->where('t1.role_id', '=', (int)$role_id)
                  ->get(array('t2.description'));

    if ($result) {
      foreach ($result as $r) {
        $role->permissions[$r->description] = true;
      }
    }

    return $role;
  }

  public static function insertPermission($role_id, $permission_id)
  {
    return DB::table('role_permission')->insert(array(
      'role_id' => $role_id,
      'permission_id' => $permission_id
    ));
  }

  public static function getRolePermissionData($role_id)
  {
    $result = DB::table('role_permission AS t1')
                  ->join('permission AS t2', 't1.permission_id', '=', 't2.id')
                  ->where('t1.role_id', '=', (int)$role_id)
                  ->get(array('t2.id', 't2.description', 't2.pretty_name'));
    if ($result)
      return $result;
  }

  public function deleteUserRoles($user_id)
  {
    return DB::table('user_role')
                ->where('user_id', '=', (int)$user_id)
                ->delete();
  }

  public static function deleteRole($role_id)
  {
    return DB::table('role_permission')
            ->where('role_id', '=', (int)$role_id)
            ->delete();
  }

  public static function insertRole($role_name)
  {
    return DB::table('role')->insert_get_id(array(
      'role_name' => $role_name
    ));
  }

  public function hasPermission($permission)
  {
    return isset($this->permissions[$permission]);
  }
}
