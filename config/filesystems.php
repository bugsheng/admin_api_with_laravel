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

        //阿里Oss配置
        'ali_oss' => [
            'driver' => 'ali_oss',
            'access_key_id' => env('ALI_OSS_ACCESS_KEY_ID'),//从OSS获得的AccessKeyId
            'access_key_secret' => env('ALI_OSS_ACCESS_KEY_SECRET'),//从OSS获得的AccessKeySecret
            'bucket' => env('ALI_OSS_BUCKET'),//OSS中设置的空间bucket
            'cdn_domain' => env('ALI_OSS_CDN_DOMAIN'),// 如果isCName为true, getUrl会判断cdnDomain是否设定来决定返回的url，如果cdnDomain未设置，则使用endpoint来生成url，否则使用cdn
            'endpoint' => env('ALI_OSS_ENDPOINT'),//您选定的OSS数据中心访问域名，例如oss-cn-hangzhou.aliyuncs.com
            'endpoint_internal' => env('ALI_OSS_ENDPOINT_INTERNAL'),//内网地址
            'isCName' => env('ALI_OSS_IS_CNAME', false),//<true|false>是否对Bucket做了域名绑定，并且Endpoint参数填写的是自己的域名
            'ssl' => env('ALI_OSS_SSL', false),//<true|false>是否使用ssl 即链接是否使用https
        ]
    ],

];
