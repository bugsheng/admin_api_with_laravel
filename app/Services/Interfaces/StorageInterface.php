<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/20
 * Time: 12:39
 */

namespace App\Services\Interfaces;

use App\Services\Interfaces\ExtendFileStorage\AliOssStorageInterface;
use App\Services\Interfaces\ExtendFileStorage\LocalPublicStorageInterface;
use App\Services\Interfaces\ExtendFileStorage\QiniuStorageInterface;

interface StorageInterface extends LocalPublicStorageInterface, AliOssStorageInterface, QiniuStorageInterface
{
    public function getStoreFileToken($type);

}
