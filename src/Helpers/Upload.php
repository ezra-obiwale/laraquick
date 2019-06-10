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
     * @param boolean $absoluteUrl Indicates whether to return an absolute url to the upload file or not
     * @return string
     */
    public static function toLocal(UploadedFile $file, $path = '', $absoluteUrl = true) : string
    {
        $name = self::createFilename($file);
        $storeUrl = $file->storeAs($path, $name);
        return $absoluteUrl ? Storage::url(url($storeUrl)) : $storeUrl;
    }

    /**
     * Uploads a file to the aws bucket storage
     *
     * @param UploadedFile $file The oploaded file
     * @param string $path The path to save the file to
     * @param boolean $absoluteUrl Indicates whether to return an absolute url to the upload file or not
     * @return bool|string
     */
    public static function toS3Bucket(UploadedFile $file, $path = '', $absoluteUrl = true)
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

        $pathToFile = $path . '/' . $name;
        return $absoluteUrl ? self::s3url($pathToFile) : $pathToFile;
    }

    /**
     * @see toS3Bucket
     * @deprecated version
     * @param UploadedFile $file
     * @param string $path
     * @param boolean $absoluteUrl Indicates whether to return an absolute url to the upload file or not
     * @return string|bool
     */
    public static function awsUpload(UploadedFile $file, $path = '')
    {
        return self::toS3Bucket($file, $path);
    }

    private static function createFileName(UploadedFile $file) : string
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
    public static function s3url($path) : string
    {
        $config = config('filesystems.disks.s3');
        return '//s3.' . $config['region'] . '.amazonaws.com/'
            . $config['bucket'] . '/' . $path;
    }
}
