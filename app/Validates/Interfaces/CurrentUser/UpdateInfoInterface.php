<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/11
 * Time: 13:11
 */

namespace App\Validates\Interfaces\CurrentUser;


interface UpdateInfoInterface
{

    public function setId($id);

    /**
     * 更新用户信息入库前验证
     *
     * @param $data
     *
     * @return mixed
     */
    public function updateInfo($data);

}
