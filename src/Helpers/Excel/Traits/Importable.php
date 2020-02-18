<?php

namespace Laraquick\Helpers\Excel\Traits;

use Illuminate\Http\UploadedFile;
use Laraquick\Helpers\Upload;
use Laraquick\Helpers\Excel\Import;

trait Importable
{

    /**
     * Upload a file to local
     *
     * @param UploadedFile $file
     * @param string $path
     * @param boolean $absoluteUrl Indicates whether to return an absolute path to the file
     * @return Import
     */
    public static function upload(UploadedFile $file, $path = '') : Import
    {
        $filePath = Upload::toLocal($file, $path, false);
        return (new Import)
            ->setFilePath($filePath)
            ->setModelClass(self::class);
    }

    /**
     * Loads a file from storage for importing/exporting
     *
     * @param string $filePath
     * @return Import
     */
    public static function loadFile($filePath) : Import
    {
        return (new Import)
            ->setFilePath($filePath)
            ->setModelClass(self::class);
    }
}
