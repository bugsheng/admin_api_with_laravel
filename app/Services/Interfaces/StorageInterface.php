<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/20
 * Time: 12:39
 */

namespace App\Services\Interfaces;

use Illuminate\Http\UploadedFile;

interface StorageInterface
{

    public function getStorageToken($type);

    public function storeLocalFile(string $storage_path, UploadedFile $file);

    public function storeLocalFiles(string $storage_path, array $files);

    public function deleteLocalFile(string $file_path, string $file_name);

    public function deleteLocalFiles(string $file_path, array $file_names);

}
