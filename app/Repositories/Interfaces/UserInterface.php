<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/8
 * Time: 16:52
 */

namespace App\Repositories\Interfaces;


interface UserInterface extends BaseInterface
{

    /**
     * 用户模型是设置了软删除，则应该有接口去实现回滚软删除数据
     *
     * @param $id
     *
     * @return mixed
     */
    public function restore($id);

}
