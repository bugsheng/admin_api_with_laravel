<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/11
 * Time: 11:53
 */

namespace App\Validates\CurrentUser;

use App\Validates\Interfaces\CurrentUser\UpdateInfoInterface;
use App\Validates\Validate;

/**
 * 更新数据前验证
 * Class UpdateInfoValidate
 *
 * @package App\Validators\CurrentUser
 */
class UpdateInfoValidate extends Validate implements UpdateInfoInterface
{

    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * 更新当前用户信息数据入库前验证
     *
     * @param $data
     *
     * @return array
     */
    public function updateInfo($data)
    {

        $rules = [
            'name'  => 'required|max:20|unique:users,name,' . $this->id,
            'email' => 'required|email|unique:users,email,' . $this->id
        ];

        $message = [
            'name.required'  => '请输入昵称',
            'name.max'       => '昵称长度不可超过20',
            'name.unique'    => '该昵称已被占用',
            'email.required' => '请输入邮箱',
            'email.email'    => '邮箱格式不正确',
            'email.unique'   => '该邮箱已被占用'
        ];

        $result = $this->validate($data, $rules, $message);

        if ($result === true) {
            return $this->baseSucceed();
        }
        return $this->baseFailed($result['message']);
    }

}
