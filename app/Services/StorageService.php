<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/20
 * Time: 12:40
 */

namespace App\Services;

use App\Services\Interfaces\StorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AdapterInterface;

class StorageService extends BaseService implements StorageInterface
{

    /**
     * @param $type
     * @return array
     */
    public function getStoreFileToken($type)
    {
        $data = [];
        switch ($type){
            case 'local':
                $data = [
                    'url' => route('store_local_file'),
                    'type' =>$type
                ];
                break;
            case 'public':
                $data = [
                    'url' => route('store_local_file'),
                    'type' =>$type
                ];
                break;
            case 'ali_oss':
                $data = [
                    'url' => route('store_ali_oss_file'),
                    'type' =>$type
                ];
                break;
            case 'qiniu':
                $data = [
                    'url' => route('store_qiniu_file'),
                    'type' =>$type
                ];
                break;
            default:;
        }

        return $this->baseSucceed($data);
    }

    /**
     * 单个文件本地存储
     * @param string $storage_path
     * @param UploadedFile $file
     * @return array
     */
    public function storeLocalFile(string $storage_path, UploadedFile $file)
    {
        $filePath = $storage_path.'/'.date('Y_m_d',time());
        //上传的网络文件使用public而不是local
        $options = ['disk'=>'public'];

        $result = $this->storeFile($filePath, $file, $options);

        if($result === false) {
            return $this->baseFailed('文件上传失败');
        }

        $data = array_merge([
            'url' => Storage::disk($options['disk'])->url($result['save_path'].'/'.$result['save_name']),
            'is_public' => true
        ], $result);

        return $this->baseSucceed($data);
    }

    /**
     * 多个文件本地存储
     * @param string $storage_path
     * @param array $files
     * @return array
     */
    public function storeLocalFiles(string $storage_path, array $files)
    {
        $data = [];
        foreach($files as $file){
            $result = self::storeLocalFile($storage_path, $file);
            if($result['status'] == false){

                //如果之前有文件上传成功的，删除文件
                if(count($data)){
                    self::deleteLocalFiles($storage_path,array_pluck($data,'save_name'));
                }

                return $this->baseFailed('文件上传失败');
            }
            $data[] = $result['data'];
        }

        return $this->baseSucceed($data);
    }

    /**
     * 删除本地文件
     * @param string $file_path
     * @param string $file_name
     * @return array
     */
    public function deleteLocalFile(string $file_path, string $file_name)
    {

        $file = $file_path.'/'.$file_name;

        $result = Storage::disk('public')->delete($file);

        if($result['status'] == false){
            return $this->baseFailed();
        }

        return $this->baseSucceed();
    }

    /**
     * 批量删除本地文件（同一个目录下）
     * @param string $file_path
     * @param array $file_names
     * @return array
     */
    public function deleteLocalFiles(string $file_path, array $file_names)
    {
        $files = [];
        foreach($file_names as $k => $item){
           $files[] = $file_path.'/'.$item;
        }
        if(!$files){
           return $this->baseFailed('请选择文件');
        }

       $result = Storage::disk('public')->delete($files);

       if($result['status'] == false){
           return $this->baseFailed();
       }

        return $this->baseSucceed();
    }

    /**
     * 存储上传的文件到阿里云Oss
     * @param string $storage_path
     * @param UploadedFile $file
     * @param bool $is_public 文件是否公开可见
     * @return array
     */
    public function storeAliOssFile(string $storage_path, UploadedFile $file, bool $is_public = false){
        $filePath = $storage_path.'/'.date('Y_m_d',time());

        $options = ['disk'=>'ali_oss', 'visibility' => $is_public ? AdapterInterface::VISIBILITY_PUBLIC : AdapterInterface::VISIBILITY_PRIVATE];

        $result = $this->storeFile($filePath, $file, $options);

        if($result === false) {

            return $this->baseFailed('文件上传失败');
        }

        $data = array_merge([
            'url' => $is_public ? Storage::disk($options['disk'])->url($result['save_path'].'/'.$result['save_name']) : Storage::disk($options['disk'])->temporaryUrl($result['save_path'].'/'.$result['save_name'], now()->addMinutes(60)),
            'is_public' => $is_public
        ], $result);

        return $this->baseSucceed($data);
    }

    /**
     * 批量存储上传的文件到阿里云Oss
     * @param string $storage_path
     * @param array $files
     * @param bool $is_public 文件是否公开可见
     * @return mixed
     */
    public function storeAliOssFiles(string $storage_path, array $files, bool $is_public = false)
    {
        $data = [];
        foreach($files as $file){
            $result = self::storeAliOssFile($storage_path, $file, $is_public);
            if($result['status'] == false){

                //TODO 监听失败删除之前上传成功的线上文件
                //如果之前有文件上传成功的，删除文件
                if(count($data)){
                    self::deleteOneDirAliOssFiles($storage_path,array_pluck($data,'save_name'));
                }

                return $this->baseFailed('文件上传失败');
            }
            $data[] = $result['data'];
        }

        return $this->baseSucceed($data);
    }

    /**
     * 删除阿里云Oss单个文件
     * @param string $file_path
     * @param string $file_name
     * @return array
     */
    public function deleteAliOssFile(string $file_path, string $file_name)
    {

        $file = $file_path.'/'.$file_name;

        $result = Storage::disk('ali_oss')->delete($file);

        if($result['status'] == false){
            return $this->baseFailed();
        }

        return $this->baseSucceed();
    }

    /**
     * 批量删除阿里云Oss文件（同一个目录下）
     * @param string $file_path
     * @param array $file_names
     * @return array
     */
    public function deleteOneDirAliOssFiles(string $file_path, array $file_names)
    {
        $files = [];
        foreach($file_names as $k => $item){
            $files[] = $file_path.'/'.$item;
        }
        if(!$files){
            return $this->baseFailed('请选择文件');
        }

        $result = Storage::disk('ali_oss')->delete($file_names);

        if($result['status'] == false){
            return $this->baseFailed();
        }

        return $this->baseSucceed();
    }


    /**
     * 存储上传的文件到七牛
     * @param string $storage_path
     * @param UploadedFile $file
     * @param bool $is_public 文件是否公开可见
     * @return mixed
     */
    public function storeQiniuFile(string $storage_path, UploadedFile $file, bool $is_public = false){
        $filePath = $storage_path.'/'.date('Y_m_d',time());

        $options = ['disk'=>'qiniu', 'visibility' => $is_public ? AdapterInterface::VISIBILITY_PUBLIC : AdapterInterface::VISIBILITY_PRIVATE];

        $result = $this->storeFile($filePath, $file, $options);

        if($result === false) {

            return $this->baseFailed('文件上传失败');
        }

        $data = array_merge([
            'url' => $is_public ? Storage::disk($options['disk'])->url($result['save_path'].'/'.$result['save_name']) : Storage::disk($options['disk'])->temporaryUrl($result['save_path'].'/'.$result['save_name'], now()->addMinutes(60)),
            'is_public' => $is_public
        ], $result);

        return $this->baseSucceed($data);
    }

    /**
     * 批量存储上传的文件到七牛
     * @param string $storage_path
     * @param array $files
     * @param bool $is_public 文件是否公开可见
     * @return mixed
     */
    public function storeQiniuFiles(string $storage_path, array $files, bool $is_public = false)
    {
        $data = [];
        foreach($files as $file){
            $result = self::storeQiniuFile($storage_path, $file, $is_public);
            if($result['status'] == false){

                //TODO 监听失败删除之前上传成功的线上文件
                //如果之前有文件上传成功的，删除文件
                if(count($data)){
                    self::deleteOneDirQiniuFiles($storage_path,array_pluck($data,'save_name'));
                }

                return $this->baseFailed('文件上传失败');
            }
            $data[] = $result['data'];
        }

        return $this->baseSucceed($data);
    }

    /**
     * 删除七牛单个文件
     * @param string $file_path
     * @param string $file_name
     * @return array
     */
    public function deleteQiniuFile(string $file_path, string $file_name)
    {

        $file = $file_path.'/'.$file_name;

        $result = Storage::disk('qiniu')->delete($file);

        if($result['status'] == false){
            return $this->baseFailed();
        }

        return $this->baseSucceed();
    }

    /**
     * 批量删除七牛文件（同一个目录下）
     * @param string $file_path
     * @param array $file_names
     * @return array
     */
    public function deleteOneDirQiniuFiles(string $file_path, array $file_names)
    {
        $files = [];
        foreach($file_names as $k => $item){
            $files[] = $file_path.'/'.$item;
        }
        if(!$files){
            return $this->baseFailed('请选择文件');
        }

        $result = Storage::disk('qiniu')->delete($file_names);

        if($result['status'] == false){
            return $this->baseFailed();
        }

        return $this->baseSucceed();
    }


    /**
     * 文件存储
     * @param string $filePath
     * @param UploadedFile $file
     * @param array $options
     * @return array|false
     */
    protected function storeFile(string $filePath, UploadedFile $file, array $options = ['disk' => 'public']){
        $result = $file->store($filePath, $options);
        if($result === false){
           return false;
        }

        $data = [
            'name'      => $file->getClientOriginalName(),
            'save_name' => ltrim($result, $filePath.'/'),
            'save_path' => $filePath,
            'ext'       => $file->getClientOriginalExtension(),
            'mime'      => $file->getClientMimeType(),
            'location'  => $options['disk']
        ];

        return $data;
    }
}
