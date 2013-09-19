<?php
/**
 * SingletonAbstract.php
 *
 * The correct way to implement the singleton design pattern.
 *
 * @author John Crossley <hello@phpcodemonkey.com>
 * @package advanced-user-manager
 * @version 1.0
 */
abstract class SingletonAbstract
{
  private static $instances = array();

  final private function __construct()
  {
    if (isset(self::$instances[get_called_class()])) {
      throw new Exception('A ' . get_called_class() . ' instance already exists!');
    }
    static::init();
  }

  protected function init() { }

  final public static function getInstance()
  {
    $class = get_called_class();
    if (!isset(self::$instances[$class])) {
      self::$instances[$class] = new static();
    }
    return self::$instances[$class];
  }

  final private function __clone() {}
}
