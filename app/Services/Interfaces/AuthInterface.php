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
     *
     * @param string $login_name
     * @param string $login_password
     *
     * @return mixed
     */
    public function login(string $login_name, string $login_password);

    /**
     * 刷新令牌
     *
     * @param string $refresh_token
     *
     * @return mixed
     */
    public function refreshToken(string $refresh_token);

    /**
     * 登出
     *
     * @return mixed
     */
    public function logout();

}
