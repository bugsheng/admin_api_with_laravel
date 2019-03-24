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
Route::namespace('Api')->prefix('v1')->group(function () {

    Route::get('test', function () {
        echo 'v1/test';
    });

    //授权登录后才可访问的接口
    Route::middleware('auth:adminApi')->group(function () {

        Route::get('test', function () {
            echo 'v1/test';
        });

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

    });

});
