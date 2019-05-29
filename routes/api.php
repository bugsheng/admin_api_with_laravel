<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * @version v1
 */
Route::prefix('v1')->group(function () {

    //授权登录后才可访问的接口
    Route::middleware('auth:api')->group(function () {

        /* 获取文件上传客户端信息 */
        Route::get('storage_client', 'StorageController@getToken');
        /* 存储本地文件 */
        Route::post('store_local_file', 'StorageController@storeLocalFile')->name('store_local_file');
        /* 存储阿里OSS文件 */
        Route::post('store_ali_oss_file', 'StorageController@storeAliOssFile')->name('store_ali_oss_file');
        /* 存储七牛文件 */
        Route::post('store_qiniu_file', 'StorageController@storeQiniuFile')->name('store_qiniu_file');

    });



});
