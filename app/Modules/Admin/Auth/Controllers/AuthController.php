<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2018/9/29
 * Time: 16:46
 */

namespace App\Modules\Admin\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Auth\Requests\LoginRequest;
use App\Modules\Admin\Auth\Services\AuthService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

/**
 * 后台管理鉴权控制器
 * Class AuthController
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * @var AuthService
     */
    protected $authService;

    /**
     * AuthController constructor.
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * 用户登录
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        //登录次数过多，禁止登录
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->failed('您登录的次数过多，无法再登录', FoundationResponse::HTTP_TOO_MANY_REQUESTS);
        }

        //post提交，只获取post中的用户名和密码
        $login_name     = $request->post('username');
        $login_password = $request->post('password');

        //处理登录
        $loginResult = $this->authService->login($login_name, $login_password);

        //登录失败，提示信息
        if(!$loginResult['status']) {
            return $this->failed($loginResult['message']);
        }

        //登录成功，返回授权数据
        $result = [
            'authorization' => $loginResult['data']
        ];

        return $this->success($result);

    }

    /**
     * 刷新令牌
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request){

        $refresh_token = $request->post('refresh_token','');

        //处理刷新token
        $refreshResult = $this->authService->refreshToken($refresh_token);

        //刷新失败，提示信息
        if(!$refreshResult['status']) {
            return $this->failed($refreshResult['message']);
        }

        //刷新成功，返回新的授权数据
        $result = [
            'authorization' => $refreshResult['data']
        ];

        return $this->success($result);
    }

    /**
     * 用户登出
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function logout()
    {

        $this->authService->logout();

        return $this->message('ok');
    }

}
