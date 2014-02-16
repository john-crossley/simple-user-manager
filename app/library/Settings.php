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
class Settings extends SingletonAbstract
{
    /**
     * @var object stores the settings pulled from the database.
     */
    protected $settings;

    /**
     * The init pulls the settings from the setting table of the
     * database. If you changed the name of the setting table then you
     * need to ensure you change it here also.
     *
     * @return null
     */
    protected function init()
    {
        $this->settings = DB::table('setting')->first();
    }

    /**
     * Gets the value of the column specified.
     *
     * @param string $property The name of the column you would
     * like to return.
     */
    public static function get($property)
    {
        $settings = static::getInstance();
        if (isset($settings->settings->$property)) {
            return $settings->settings->$property;
        } else return false;
    }

    public static function getAllSettings()
    {
        return static::getInstance()->settings;
    }
}
