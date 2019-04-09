<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/9
 * Time: 16:07
 */

namespace App\Services;


use App\Repositories\Interfaces\UserInterface as UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CurrentUserService extends BaseService
{

    const LOGIN_ERROR = '用户名或密码错误';
    const GUARD_TYPE = 'adminApi';

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * 获取当前登录的用户信息
     * @return array
     */
    public function getUserInfo(){

        $user = Auth::user();

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'last_login_at' => $user->last_login_at,
            'last_login_ip' => $user->last_login_ip
        ];

        return $this->baseSucceed($data);
    }

    public function getUserPermissions(){}

    public function updateInfo($data = []){

        $user = Auth::user();

        //校验更新信息数据

        $result = $this->userRepository->update($data,$user);
        if($result === false){
            return $this->baseFailed('更新用户数据失败');
        }

        return $this->baseSucceed();

    }

    public function updatePassword($password){
        $user = Auth::user();

        $data = [
            'password' => Hash::make($password)
        ];

        $result = $this->userRepository->update($data,$user);
        if($result === false){
            return $this->baseFailed('修改密码失败');
        }

        return $this->baseSucceed();
    }

    public function updateAvatar(){}

}
