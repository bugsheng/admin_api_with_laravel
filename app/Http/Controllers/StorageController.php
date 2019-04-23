<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUploadFileRequest;
use App\Services\Interfaces\StorageInterface as StorageService;
use Illuminate\Http\Request;

/**
 * Class StorageController
 * @package App\Http\Controllers
 */
class StorageController extends Controller
{

    /**
     * @var StorageService
     */
    protected $storageService;

    /**
     * CurrentUserController constructor.
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    //获取token
    public function getToken(Request $request){

        $type = $request->get('type','public');

        $result = $this->storageService->getStoreFileToken($type);

        $data = [
            'storage_client' => $result['data']
        ];

        return $this->success($data);
    }

    //存储本地文件
    public function storeLocalFile(StoreUploadFileRequest $request){

        $path = $request->get('file_path','');
        $file = $request->file('file');

        if(is_array($file)){
            $result = $this->storageService->storeLocalFiles($path, $file);
        }else{
            $result = $this->storageService->storeLocalFile($path, $file);
        }

        if(!$result['status']){
            return $this->failed('文件上传失败');
        }

        $data = [
            'file_info' => $result['data']
        ];
        return $this->success($data);
    }

    //存储阿里云文件
    public function storeAliOssFile(StoreUploadFileRequest $request){

        $path = $request->get('file_path','');
        $file = $request->file('file');

        if(is_array($file)){
            $result = $this->storageService->storeAliOssFiles($path, $file, false);
        }else{
            $result = $this->storageService->storeAliOssFile($path, $file, false);
        }

        if(!$result['status']){
            return $this->failed('文件上传失败');
        }

        $data = [
            'file_info' => $result['data']
        ];
        return $this->success($data);
    }

    //存储七牛文件
    public function storeQiniuFile(StoreUploadFileRequest $request){
        $path = $request->get('file_path','');
        $file = $request->file('file');

        if(is_array($file)){
            $result = $this->storageService->storeQiniuFiles($path, $file, false);
        }else{
            $result = $this->storageService->storeQiniuFile($path, $file, false);
        }

        if(!$result['status']){
            return $this->failed('文件上传失败');
        }

        $data = [
            'file_info' => $result['data']
        ];
        return $this->success($data);
    }
}
