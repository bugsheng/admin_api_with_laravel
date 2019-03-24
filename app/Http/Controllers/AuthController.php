<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2018/9/29
 * Time: 16:46
 */

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * 鉴权控制器
 * Class AuthController
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{

    use AuthenticatesUsers;

    /**
     * 登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login(Request $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->failed('您登录的次数过多，无法再登录', FoundationResponse::HTTP_TOO_MANY_REQUESTS);
        }

        $login_name = $request->post('username');
        $login_password = $request->post('password');

        //TODO 验证登录用户及密码


//        return $this->success(['token' => $tokens]);

    }

    /**
     * 退出登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (Auth::check()) {

            //TODO 退出登录的移除token操作
        }

        return $this->message('ok');
    }

}
