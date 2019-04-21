<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/11
 * Time: 13:54
 */

namespace App\Services\Interfaces;


interface PersonnelInterface
{

    /**
     * 获取当前用户
     * @return mixed
     */
    public function getUserInfo();

    public function getUserPermissions();

    public function updateInfo(array $data);

    public function updatePassword(string $old_password, string $new_password);

    public function updateAvatar();

}
