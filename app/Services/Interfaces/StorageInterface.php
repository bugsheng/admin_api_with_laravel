<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/20
 * Time: 12:39
 */

namespace App\Services\Interfaces;

use App\Services\Interfaces\ExtendFileStorage\AliOssStorageInterface;
use Illuminate\Http\UploadedFile;

interface StorageInterface extends AliOssStorageInterface
{

    public function getStorageToken($type);

    /**
     * @param string $storage_path
     * @param UploadedFile $file
     * @return mixed
     */
    public function storeLocalFile(string $storage_path, UploadedFile $file);

    public function storeLocalFiles(string $storage_path, array $files);

    public function deleteLocalFile(string $file_path, string $file_name);

    public function deleteLocalFiles(string $file_path, array $file_names);

}
