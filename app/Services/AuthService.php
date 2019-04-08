<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/8
 * Time: 21:45
 */

namespace App\Services;


use App\Repositories\AuthRepository;
use App\Traits\ProxyTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * 登录登出鉴权服务
 * Class AuthService
 * @package App\Services
 */
class AuthService extends BaseService
{
    use ProxyTrait;

    const LOGIN_ERROR = '用户名或密码错误';
    const GUARD_TYPE = 'adminApi';

    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * 登录验证及授权服务
     * @param $login_name
     * @param $login_password
     * @return array
     */
    public function login($login_name, $login_password){

        //检查用户是否存在
        $user = $this->authRepository->findForPassport($login_name);
        if($user == false){
            return $this->baseFailed(self::LOGIN_ERROR);
        }

        //检查密码是否正确
        $is_correct_password = self::checkPassword($user, $login_password);
        if(!$is_correct_password){
            return $this->baseFailed(self::LOGIN_ERROR);
        }

        //获取OAuth2.0授权
        $tokens = $this->authenticate(self::GUARD_TYPE, $login_name, $login_password);
        if($tokens == false){
            return $this->baseFailed(self::LOGIN_ERROR);
        }

        //记录登录信息
        $this->authRepository->putLoginRecord($user);

        //返回登录授权信息
        return $this->baseSucceed($tokens);

    }

    /**
     * 退出登录
     * @return array
     */
    public function logout(){
        if (Auth::guard(self::GUARD_TYPE)->check()) {
            Auth::guard(self::GUARD_TYPE)->user()->token()->revoke();
            Auth::guard(self::GUARD_TYPE)->user()->token()->delete();
        }

        return $this->baseSucceed();
    }

    /**
     * 检查登录密码
     * @param $user
     * @param $password
     * @return bool
     */
    protected function checkPassword($user, $password) :bool
    {
        return Hash::check($password, $user->password);
    }

}
