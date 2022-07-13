<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => array_key_exists('FILESYSTEM_DRIVER', $_SERVER) ? $_SERVER['FILESYSTEM_DRIVER'] : env('FILESYSTEM_DRIVER','local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key'    => array_key_exists('AWS_ACCESS_KEY_ID', $_SERVER) ? $_SERVER['AWS_ACCESS_KEY_ID'] : env('AWS_ACCESS_KEY_ID'),
        'secret' =>  array_key_exists('AWS_SECRET_ACCESS_KEY', $_SERVER) ? $_SERVER['AWS_SECRET_ACCESS_KEY'] : env('AWS_SECRET_ACCESS_KEY'),
        'region' => array_key_exists('AWS_DEFAULT_REGION', $_SERVER) ? $_SERVER['AWS_DEFAULT_REGION'] : env('AWS_DEFAULT_REGION','us-east-1'),
            'bucket' => array_key_exists('AWS_BUCKET', $_SERVER) ? $_SERVER['AWS_BUCKET'] : env('AWS_BUCKET'),
            'url' =>  array_key_exists('AWS_URL', $_SERVER) ? $_SERVER['AWS_URL'] : env('AWS_URL'),
           // the below 'endpoint' url is actually used:
        'endpoint' => env('AWS_URL'),
        // prevent bucket name from being added to the hostname:
        'bucket_endpoint' => false,
        // use older urls:
        'use_path_style_endpoint' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
