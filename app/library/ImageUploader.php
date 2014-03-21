<?php

class ImageUploader
{
    const MAX_FILE_SIZE = 1024000; // 1mb

    public static function upload(array $file)
    {

        if (!$file['error']) {

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $randomFilename = md5($file['name'] . time()) . '.' . strtolower($ext);


            if ($file['size'] > (ImageUploader::MAX_FILE_SIZE)) {
                throw new Exception('Exceeded max file size: [' . ImageUploader::MAX_FILE_SIZE . ']');
            }

            if (move_uploaded_file($file['tmp_name'], ROOT . 'public/uploads/' . $randomFilename) === true) {
                return $randomFilename;
            }

            throw new Exception('Unable to upload the file. Does your images/upload folder have write permission?');

        } else {
            throw new Exception('The following error occurred: ' . $file['error']);
        }

        return $filename;
    }
}