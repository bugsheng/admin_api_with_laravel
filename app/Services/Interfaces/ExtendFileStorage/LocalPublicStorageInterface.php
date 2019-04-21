<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/22
 * Time: 5:34
 */

namespace App\Services\Interfaces\ExtendFileStorage;


use Illuminate\Http\UploadedFile;

interface LocalPublicStorageInterface
{

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
