<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/11
 * Time: 13:45
 */

namespace App\Services\Interfaces;


interface AuthInterface
{

    /**
     * 登录
     * @param $login_name
     * @param $login_password
     * @return mixed
     */
    public function login($login_name, $login_password);

    /**
     * 刷新令牌
     * @param $refresh_token
     * @return mixed
     */
    public function refreshToken($refresh_token);

    /**
     * 登出
     * @return mixed
     */
    public function logout();

}
