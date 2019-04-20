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
use Storage;

class StorageService extends BaseService implements StorageInterface
{

    /**
     * @param $type
     * @return array
     */
    public function getStorageToken($type)
    {
        $data = [];
        switch ($type){
            case 'local':
                $data = [
                    'url' => route('storage_local_file'),
                    'type' => 'local'
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
        $result = $file->store($filePath,'public');
        if($result === false){
            return $this->baseFailed('上传失败');
        }

        $data = [
            'name'      => $file->getClientOriginalName(),
            'save_name' => str_after($result,$filePath.'/'),
            'save_path' => $filePath,
            'ext'       => $file->getClientOriginalExtension(),
            'mime'      => $file->getClientMimeType(),
            'location'  => 'public',
            'url'       => Storage::disk('public')->url('storage/'.$result)
        ];

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
       foreach($file_names as $k => $item){
           $file_names[$k] =  $file = $file_path.'/'.$item;
       }

        $result = Storage::disk('public')->delete($file_names);

        if($result['status'] == false){
            return $this->baseFailed();
        }

        return $this->baseSucceed();
    }
}
