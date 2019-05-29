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
Route::prefix('v1')->namespace('Personal\Controllers')->middleware('auth:api')->group(function () {

    Route::prefix('personal')->group(function(){
        /*获取当前登录用户信息*/
        Route::get('/info','PersonalController@info');

        /*更新用户信息*/
        Route::patch('/info', 'PersonalController@updateInfo');

        /*更新用户登录密码*/
        Route::patch('/password', 'PersonalController@updatePassword');

        /*更新用户头像*/
        Route::put('/avatar', 'PersonalController@updateAvatar');

    });

});
