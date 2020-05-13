<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/9
 * Time: 16:07
 */

namespace App\Modules\Admin\Personal\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserInterface as UserRepository;
use App\Services\BaseService;
use App\Services\Interfaces\PersonalInterface;
use App\Validates\Interfaces\CurrentUser\UpdateInfoInterface as UpdateInfoValidate;
use Auth;
use Hash;

class PersonalService extends BaseService implements PersonalInterface
{

    const GUARD_TYPE = 'admin';

    /**
     * @var User
     */
    protected $user;

    /**
     * 用户信息修改验证类
     *
     * @var UpdateInfoValidate
     */
    protected $updateInfoValidate;

    /**
     * 用户数据操作仓库类
     *
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(UpdateInfoValidate $updateInfoValidate, UserRepository $userRepository)
    {
        $this->updateInfoValidate = $updateInfoValidate;

        $this->userRepository = $userRepository;
    }

    /**
     * 获取当前登录的用户信息
     *
     * @return array
     */
    public function getInfo()
    {
        $this->user = Auth::guard(self::GUARD_TYPE)->user();
        $data       = [
            'name'          => $this->user->name,
            'email'         => $this->user->email,
            'avatar'        => $this->user->avatar,
            'last_login_at' => $this->user->last_login_at,
            'last_login_ip' => $this->user->last_login_ip
        ];

        return $this->baseSucceed($data);
    }

    /**
     * 更新用户基础信息
     *
     * @param array $data
     *
     * @return array
     */
    public function updateInfo(array $data)
    {
        $this->user = Auth::guard(self::GUARD_TYPE)->user();
        //数据验证
        $this->updateInfoValidate->setId($this->user->id);
        $result = $this->updateInfoValidate->updateInfo($data);
        if ($result['status'] === false) {
            return $this->baseFailed($result['message']);
        }

        //数据存储更新
        $result = $this->userRepository->update($data, $this->user);
        if ($result === false) {
            return $this->baseFailed('更新用户数据失败');
        }

        return $this->baseSucceed();

    }

    /**
     * 修改个人登录密码
     *
     * @param string $old_password
     * @param string $new_password
     *
     * @return array
     */
    public function updatePassword(string $old_password, string $new_password)
    {
        $this->user = Auth::guard(self::GUARD_TYPE)->user();
        //检查旧密码是否正确
        $is_correct_password = self::checkPassword($this->user, $old_password);
        if (!$is_correct_password) {
            return $this->baseFailed('原密码不正确');
        }

        //更新密码,数据存储
        $data   = [
            'password' => Hash::make($new_password)
        ];
        $result = $this->userRepository->update($data, $this->user);
        if ($result === false) {
            return $this->baseFailed('修改密码失败');
        }

        return $this->baseSucceed();
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

    public function updateAvatar()
    {
    }

    public function getMenus()
    {
    }

    public function getPermissions()
    {
    }

}
