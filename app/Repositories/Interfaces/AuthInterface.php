<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/8
 * Time: 16:52
 */

namespace App\Repositories\Interfaces;


use App\Models\User;

interface AuthInterface
{

    /**
     * 登录用户查询
     * @param $value
     * @return bool|mixed
     */
    public function findForPassport($value);

    /**
     * 更新记录登录信息
     * @param User $user
     */
    public function putLoginRecord(User $user);

}
