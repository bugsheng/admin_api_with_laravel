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

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
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
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        'ali_oss' => [
            'driver' => 'ali_oss',
            'access_key_id' => 'LTAIPjFI53jdaPWP',//从OSS获得的AccessKeyId
            'access_key_secret' => 'P5BDI1w0vhhIRTQzeXFw8h9BfY6BoP',//从OSS获得的AccessKeySecret
            'bucket' => 'bugsheng-dev',//OSS中设置的空间bucket
            'endpoint' => 'oss-cn-hangzhou.aliyuncs.com',//您选定的OSS数据中心访问域名，例如oss-cn-hangzhou.aliyuncs.com
            'isCName' => true,//<true|false>是否对Bucket做了域名绑定，并且Endpoint参数填写的是自己的域名
            'cdn_domain' => 'oss-bugsheng-dev.bugsheng.com',// 如果isCName为true, getUrl会判断cdnDomain是否设定来决定返回的url，如果cdnDomain未设置，则使用endpoint来生成url，否则使用cdn
            'endpoint_internal' => 'oss-cn-hangzhou-internal.aliyuncs.com',//内网地址
            'ssl' => false,//<true|false>是否使用ssl 即链接是否使用https

        ]
    ],

];
