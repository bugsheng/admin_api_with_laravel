<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/8
 * Time: 23:10
 */

namespace App\Repositories;


use App\Models\User;
use App\Repositories\Interfaces\UserInterface;

/**
 * 用户基础数据操作仓库
 * Class CurrentUserRepository
 *
 * @package App\Repositories
 */
class UserRepository implements UserInterface
{

    /**
     * @var User
     */
    protected $user;

    /**
     * AuthRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * 查询所有用户
     *
     * @param array $columns
     * @param bool  $withTrash
     *
     * @return mixed|void
     */
    public function all($columns = ['*'], $withTrash = false)
    {

    }

    /**
     * 分页查询所有用户
     *
     * @param int   $perPage
     * @param array $columns
     * @param bool  $withTrash
     *
     * @return mixed|void
     */
    public function paginate($perPage = 15, $columns = ['*'], $withTrash = false)
    {
    }

    /**
     * 新增用户
     *
     * @param array $data
     *
     * @return bool|mixed
     */
    public function create(array $data)
    {

        $result = $this->user->fill($data)->save();
        return $result;
    }

    /**
     * 更新用户数据
     *
     * @param array $data
     * @param User  $user
     *
     * @return bool|mixed
     */
    public function update(array $data, $user)
    {

        foreach ($data as $key => $value) {
            $user->$key = $value;
        }

        $result = $user->save();
        return $result;

    }

    /**
     * 通过主键删除用户
     *
     * @param      $ids
     * @param bool $withTrash
     *
     * @return bool|int|mixed|null
     */
    public function delete($ids, $withTrash = false)
    {

        $ids = is_array($ids) ? $ids : func_get_args();
        if ($withTrash) {
            $result = $this->user->withTrashed()->whereIn('id', $ids)->forceDelete();
        } else {
            $result = $this->user->withTrashed()->destroy($ids);
        }

        return $result;
    }

    /**
     * 恢复软删除用户数据
     *
     * @param $id
     *
     * @return mixed|void
     */
    public function restore($id)
    {
        $this->user->withTrashed()->where('id', '=', $id)->restore();
    }

    /**
     * 根据主键id获取指定用户
     *
     * @param       $id
     * @param array $columns
     * @param bool  $withTrash
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'], $withTrash = false)
    {
        $query = $this->user;
        if ($withTrash) {
            $query->withTrashed();
        }
        $user = $query->where('id', '=', $id)->first($columns);
        return $user;
    }

    /**
     * 根据单个属性获取相应用户数据集合
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     * @param bool  $withTrash
     *
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*'], $withTrash = false)
    {
        $query = $this->user;
        if ($withTrash) {
            $query->withTrashed();
        }
        $users = $query->where($field, '=', $value)->get($columns);

        return $users;
    }

}
