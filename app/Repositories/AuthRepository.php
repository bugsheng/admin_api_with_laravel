<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/8
 * Time: 23:10
 */

namespace App\Repositories;


use App\Models\User;
use App\Repositories\Interfaces\AuthInterface;

/**
 * 鉴权查询
 * Class AuthRepository
 * @package App\Repositories
 */
class AuthRepository implements AuthInterface
{

    /**
     * @var User
     */
    protected $user;

    /**
     * AuthRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * 登录用户查询
     * @param $value
     * @return bool|mixed
     */
    public function findForPassport($value){

        $user = $this->user->findForPassport($value);
        return $user;
    }

    /**
     * 更新记录登录信息
     * @param $user
     */
    public function putLoginRecord(User $user){
        $user->last_login_at = $user->login_at;
        $user->last_login_ip = $user->login_ip;
        $user->login_at = now();
        $user->login_ip = request()->ip();
        $user->save();

    }

}
