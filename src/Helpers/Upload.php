<?php

namespace Laraquick\Helpers;

use Storage;
use Illuminate\Http\UploadedFile;

class Upload
{
    private static $s3;

    /**
     * Uploads a file to the local storage
     *
     * @param UploadedFile $file The oploaded file
     * @param string $path The path to save the file to
     * @return void
     */
    public static function localUpload(UploadedFile $file, $path = '')
    {
        $name = self::createFilename($file);
        return url(Storage::url($file->storeAs($path, $name)));
    }

    /**
     * Uploads a file to the aws bucket storage
     *
     * @param UploadedFile $file The oploaded file
     * @param string $path The path to save the file to
     * @return bool|string
     */
    public static function awsUpload(UploadedFile $file, $path = '')
    {
        $name = self::createFilename($file);
        if (!self::s3()->put(
            $path . '/' . $name,
            fopen($file->getRealPath(), 'r+'),
            'public'
        )
        ) {
            return false;
        }
        return self::s3url($path . '/' . $name);
    }

    private static function createFileName(UploadedFile $file)
    {
        return md5(auth()->id() . time()) . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Shortcut to the s3 storage object
     */
    public static function s3()
    {
        if (!self::$s3) {
            self::$s3 = Storage::disk('s3');
        }

        return self::$s3;
    }

    /**
     * Returns the full url of the s3 bucket file path
     *
     * @param string $path The file path on s3
     * @return string
     */
    public static function s3url($path)
    {
        $config = config('filesystems.disks.s3');
        return '//s3.' . $config['region'] . '.amazonaws.com/'
            . $config['bucket'] . '/' . $path;
    }
}
