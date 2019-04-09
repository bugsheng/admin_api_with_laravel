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

    Route::get('test', function () {

        $test = function ($ids){
            $ids = is_array($ids) ? $ids : func_get_args();
            return $ids;
        };

        dd($test([1]));
    });

    /*登录*/
    Route::post('login', 'AuthController@login');

    //授权登录后才可访问的接口
    Route::middleware('auth:adminApi')->group(function () {

        Route::get('login_test', function () {
            echo 'v1/test';
        });

        /*登出*/
        Route::post('logout', 'AuthController@logout');

        /*获取当前登录用户信息*/
        Route::get('current_user','CurrentUserController@info');

        Route::get('current_permissions', 'CurrentUserController@getPermissions');

        Route::put('update_current_user_info', 'CurrentUserController@updateInfo');
        Route::put('update_current_user_avatar', 'CurrentUserController@updateAvatar');
        Route::put('update_current_user_password', 'CurrentUserController@updatePassword');

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

    });

});
