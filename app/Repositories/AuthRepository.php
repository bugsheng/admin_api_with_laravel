<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/8
 * Time: 23:10
 */

namespace App\Repositories;


use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * 鉴权查询
 * Class AuthRepository
 * @package App\Repositories
 */
class AuthRepository
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
     * 登录查询
     * @param $value
     * @return User|bool
     */
    public function findForPassport($value){
        try{
            $user = $this->user->where('username', '=', $value)->orWhere('email', '=', $value)->orWhere('name', '=', $value)->firstOrFail();
            return $user;
        }catch (ModelNotFoundException $e){
            return false;
        }
    }

    /**
     * 更新记录登录信息
     * @param User $user
     */
    public function putLoginRecord(User $user){

        $user->last_login_at = $user->login_at;
        $user->last_login_ip = $user->login_ip;
        $user->login_at = now();
        $user->login_ip = request()->ip();
        $user->save();

    }
}
