<?php namespace JohnCrossley;

class Crypter
{
    public function makePassword($password, $salt)
    {
        return sha1($password . $salt);
    }

    public function preparePassword($password)
    {
        $salt = sha1(time() . rand());
        $password = $this->makePassword($password, $salt);
        return array(
            'password' => $password,
            'salt'     => $salt
        );
    }
}