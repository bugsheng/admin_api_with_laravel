<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/11
 * Time: 13:54
 */

namespace App\Services\Interfaces;


interface CurrentUserInterface
{

    /**
     * 获取当前用户
     * @return mixed
     */
    public function getUserInfo();

    public function getUserPermissions();

    public function updateInfo(array $data);

    public function updatePassword($old_password, $new_password);

    public function updateAvatar();

}
