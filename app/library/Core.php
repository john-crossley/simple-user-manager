<?php

/**
 * Core Class
 *
 * Core is for managing all of the main application
 * grunt work. I know it could do more.
 *
 * @author John Crossley <hello@phpcodemonkey.com>
 * @package advanced-user-manager
 * @version 1.0
 */
class Core extends SingletonAbstract
{
    /**
     * The CSRF token
     * @var string
     */
    private $csrf;

    public static function getNewsFromPhpCodemonkey()
    {
        $core = self::getInstance();

        // I'd suggest updating the notifications once every two days.
        $data = DB::table('notifications_centre')->limit(1)->get();

        if (!$data) {
            $data = $core->makeServiceCall();

            if (!$data) return false;

            // Store it in the database
            DB::table('notifications_centre')->insert(array(
                'json_object_data' => $data,
                'created_at' => date(DATABASE_DATETIME_FORMAT)
            ));

            return json_decode($data);

        } else {
            // Right we have some data from the database, check to see how old the data is
            // $lastCheckedForNotifications = getElapsedTime( $data->created_at );
            // die($lastCheckedForNotifications);

            $currentTime = date(DATABASE_DATETIME_FORMAT);
            $lastCheckedForNotifications = $data->created_at;
            $hoursLastChecked = round((strtotime($currentTime) - strtotime($lastCheckedForNotifications)) / (60 * 60));

            if ($hoursLastChecked > 24) {
                // Check for new updates
                $data = $core->makeServiceCall();

                if (!$data) return false;

                // Update the data
                DB::table('notifications_centre')->where('id', '=', 1)->update(array(
                    'json_object_data' => $data,
                    'created_at' => date(DATABASE_DATETIME_FORMAT)
                ));

                return $data;

            }
        }
        return json_decode($data->json_object_data);
    }

    private function makeServiceCall()
    {
        $url = "http://phpcodemonkey.com/api/v1/update/" . PRODUCT_NUMBER;
        $data = @file_get_contents($url);
        if ($data === false) {
            Flash::make('info', UNABLE_TO_RETRIEVE_NOTIFICATIONS);
            return false;
        }
        return $data;
    }

    /**
     * Generates a new CSRF token for the application and stores
     * it inside a session. The system will overwrite the token
     * each time one is requested.
     * @return string The CSRF token
     */
    public function generateToken()
    {
        $this->csrf = strtoupper('csrf' . md5(uniqid() . rand()));
        $_SESSION['CSRF_TOKEN'] = $this->csrf;
        return $this->csrf;
    }

    /**
     * Calls the generateToken function when ever this
     * class is initialised.
     * @return null
     */
    protected function init()
    {
        $this->generateToken();
    }

    /**
     * Returns the CSRF token
     * @return string The CSRF token
     */
    public function getToken()
    {
        return $this->csrf;
    }
}
