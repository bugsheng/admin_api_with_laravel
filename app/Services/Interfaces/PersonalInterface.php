<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/11
 * Time: 13:54
 */

namespace App\Services\Interfaces;


interface PersonalInterface
{

    /**
     * 获取当前用户
     *
     * @return mixed
     */
    public function getInfo();

    public function updateInfo(array $data);

    public function updatePassword(string $old_password, string $new_password);

    public function updateAvatar();

}
