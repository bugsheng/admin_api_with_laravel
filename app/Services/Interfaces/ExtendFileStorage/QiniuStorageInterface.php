<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/22
 * Time: 4:44
 */

namespace App\Services\Interfaces\ExtendFileStorage;


use Illuminate\Http\UploadedFile;

interface QiniuStorageInterface
{

    /**
     * 存储上传的文件到七牛
     *
     * @param string       $storage_path
     * @param UploadedFile $file
     * @param bool         $is_public 文件是否公开可见
     *
     * @return mixed
     */
    public function storeQiniuFile(string $storage_path, UploadedFile $file, bool $is_public = false);

    /**
     * 批量存储上传的文件到七牛
     *
     * @param string $storage_path
     * @param array  $files
     * @param bool   $is_public 文件是否公开可见
     *
     * @return mixed
     */
    public function storeQiniuFiles(string $storage_path, array $files, bool $is_public = false);

    /**
     * 删除七牛单个文件
     *
     * @param string $file_path
     * @param string $file_name
     *
     * @return array
     */
    public function deleteQiniuFile(string $file_path, string $file_name);

    /**
     * 批量删除七牛文件（同一个目录下）
     *
     * @param string $file_path
     * @param array  $file_names
     *
     * @return array
     */
    public function deleteOneDirQiniuFiles(string $file_path, array $file_names);
}
