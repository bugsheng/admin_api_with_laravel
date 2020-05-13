<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/8
 * Time: 21:45
 */

namespace App\Services;


use App\Events\Logout;
use App\Repositories\Interfaces\AdminAuthInterface as AuthRepository;
use App\Services\Interfaces\AuthInterface;
use App\Traits\ProxyTrait;
use Auth;
use Event;
use Hash;

/**
 * 登录登出鉴权服务
 * Class AuthService
 *
 * @package App\Services
 */
class AuthService extends BaseService implements AuthInterface
{
    use ProxyTrait;

    const LOGIN_ERROR         = '用户名或密码错误';
    const REFRESH_TOKEN_ERROR = '登录失效，请重新登录';
    const GUARD_TYPE          = 'admin';

    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * 登录验证及授权服务
     *
     * @param string $login_name
     * @param string $login_password
     *
     * @return array
     */
    public function login(string $login_name, string $login_password)
    {

        //检查用户是否存在
        $user = $this->authRepository->findForPassport($login_name);
        if ($user == false) {
            return $this->baseFailed(self::LOGIN_ERROR);
        }

        //检查密码是否正确
        $is_correct_password = self::checkPassword($user, $login_password);
        if (!$is_correct_password) {
            return $this->baseFailed(self::LOGIN_ERROR);
        }

        //获取OAuth2.0授权
        $tokens = $this->authenticate(self::GUARD_TYPE, config('auth.guards.' . self::GUARD_TYPE . '.provider'),
            $login_name, $login_password);
        if ($tokens == false) {
            return $this->baseFailed(self::LOGIN_ERROR);
        }

        //记录登录信息
        $this->authRepository->putLoginRecord($user);

        //返回登录授权信息
        return $this->baseSucceed($tokens);

    }

    /**
     * 刷新令牌
     *
     * @param string $refresh_token
     *
     * @return array|mixed
     */
    public function refreshToken(string $refresh_token)
    {

        $tokens = $this->getRefreshToken($refresh_token);

        if ($tokens == false) {
            return $this->baseFailed(self::REFRESH_TOKEN_ERROR);
        }

        //返回登录授权信息
        return $this->baseSucceed($tokens);
    }

    /**
     * 退出登录
     */
    public function logout()
    {

        if (Auth::check()) {

            $currentUser = Auth::user();

            //触发退出登录事件
            Event::dispatch(new Logout(
                $currentUser->token()->id
            ));

        }
    }

    /**
     * 检查登录密码
     *
     * @param $user
     * @param $password
     *
     * @return bool
     */
    protected function checkPassword($user, $password): bool
    {
        return Hash::check($password, $user->password);
    }

}
