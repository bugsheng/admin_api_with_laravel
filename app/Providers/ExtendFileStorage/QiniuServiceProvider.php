<?php

namespace App\Providers\ExtendFileStorage;

use App\Adapters\ExtendFileStorage\QiniuAdapter;
use Qiniu\Auth;
use Qiniu\Cdn\CdnManager;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class QiniuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Storage::extend('qiniu', function ($app, $config) {

            //从七牛获得的AccessKeyId
            $accessKeyId        = $config['access_key_id'];

            //从七牛获得的AccessKeySecret
            $accessKeySecret    = $config['access_key_secret'];

            //存储空间
            $bucket = $config['bucket'];

            //是否开启了ssl
            $ssl = $config['ssl'];

            //cdn域名地址
            $cdnDomain = $config['cdn_domain'];

            $authManager = new Auth($accessKeyId, $accessKeySecret);
            $adapter = new QiniuAdapter($authManager, $bucket, $ssl, $cdnDomain);

            $filesystem =  new Filesystem($adapter);

            return $filesystem;
        });

    }
}
