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
        $temp = new \App\Repositories\AuthRepository(new \App\Models\User());
        dd($temp->findForPassport('admin'));
    });

    Route::post('login', 'AuthController@login')->name('login');

    //授权登录后才可访问的接口
    Route::middleware('auth:adminApi')->group(function () {

        Route::get('login_test', function () {
            echo 'v1/test';
        });

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

    });

});
