<?php

namespace Laraquick\Helpers;

class Config
{
    public static function s3(array $config = [], ?string $root = null): array
    {
        $prefix = preg_replace('/[^a-z0-9]/', '-', env('APP_URL'));

        $defaults = [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'bucket_endpoint' => false,
            'options' => [
                'override_visibility_on_copy' => 'private',
            ],
            'cache' => [
                'store' => 'redis',
                'expire' => 600,
                'prefix' => 's3-cache',
            ],
            'throw' => false,
        ];

        if ($root) {
            $defaults['root'] = self::joinPaths($prefix, $root);
        }

        return array_merge($defaults, $config);
    }

    private static function joinPaths(string $path1, string $path2): string
    {
        return preg_replace(
            '/\\' . DIRECTORY_SEPARATOR . '+/',
            DIRECTORY_SEPARATOR,
            $path1 . DIRECTORY_SEPARATOR . $path2
        );
    }
}
