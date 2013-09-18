<?php
/**
 * settings.php
 *
 * The file provides you with an easy way to query the setting
 * table in the database. It is done in away to help reduce
 * burden on the database.
 *
 * @author John Crossley <hello@phpcodemonkey.com>
 * @package user-manager
 * @version 1.0
 */
class Settings
{
  /**
   * @var object stores the settings pulled from the database.
   */
  protected $settings;

  /**
   * @var object stores an instance of the settings object.
   */
  protected static $instance;

  /**
   * The construct pulls the settings from the setting table of the
   * database. If you changed the name of the setting table then you
   * need to ensure you change it here also.
   *
   * @return null
   */
  protected function __construct()
  {
    $this->settings = DB::table('setting')->first();
  }

  /**
   * Initialises an instance of the settings class if
   * one doesn't exist.
   */
  public static function init()
  {
    if (!self::$instance instanceof Settings) {
      self::$instance = new Settings;
    }
    return self::$instance;
  }

  /**
   * Gets the value of the column specified.
   *
   * @param string $property The name of the column you would
   * like to return.
   */
  public static function get($property)
  {
    $settings = static::init();
    if (isset($settings->settings->$property)) {
      return $settings->settings->$property;
    } else return false;
  }

  public static function getAllSettings()
  {
    $settings = static::init();
    return $settings->settings;
  }
}
