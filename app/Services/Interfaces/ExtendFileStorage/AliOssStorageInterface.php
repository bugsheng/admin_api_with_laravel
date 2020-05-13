<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/22
 * Time: 4:44
 */

namespace App\Services\Interfaces\ExtendFileStorage;


use Illuminate\Http\UploadedFile;

interface AliOssStorageInterface
{

    /**
     * 存储上传的文件到阿里云Oss
     *
     * @param string       $storage_path
     * @param UploadedFile $file
     * @param bool         $is_public 文件是否公开可见
     *
     * @return mixed
     */
    public function storeAliOssFile(string $storage_path, UploadedFile $file, bool $is_public = false);

    /**
     * 批量存储上传的文件到阿里云Oss
     *
     * @param string $storage_path
     * @param array  $files
     * @param bool   $is_public 文件是否公开可见
     *
     * @return mixed
     */
    public function storeAliOssFiles(string $storage_path, array $files, bool $is_public = false);

    /**
     * 删除阿里云Oss单个文件
     *
     * @param string $file_path
     * @param string $file_name
     *
     * @return array
     */
    public function deleteAliOssFile(string $file_path, string $file_name);

    /**
     * 批量删除阿里云Oss文件（同一个目录下）
     *
     * @param string $file_path
     * @param array  $file_names
     *
     * @return array
     */
    public function deleteOneDirAliOssFiles(string $file_path, array $file_names);
}
