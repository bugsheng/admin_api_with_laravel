<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/8
 * Time: 16:52
 */

namespace App\Repositories\Interfaces;

/**
 * 基础资源操作接口
 * Interface BaseInterface
 *
 * @package App\Repositories\Interfaces
 */
interface BaseInterface
{

    /**
     * 查询所有数据
     *
     * @param array $columns
     * @param bool  $withTrash
     *
     * @return mixed
     */
    public function all($columns = ['*'], $withTrash = false);

    /**
     * 分页查询所有数据
     *
     * @param int   $perPage
     * @param array $columns
     * @param bool  $withTrash
     *
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = ['*'], $withTrash = false);

    /**
     * 新增数据
     *
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data);

    /**
     * 更新数据
     *
     * @param array $data
     * @param       $id
     *
     * @return mixed
     */
    public function update(array $data, $id);

    /**
     * 删除数据
     *
     * @param      $id
     * @param bool $withTrash
     *
     * @return mixed
     */
    public function delete($id, $withTrash = false);

    /**
     * 根据主键id获取指定记录
     *
     * @param       $id
     * @param array $columns
     * @param bool  $withTrash
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'], $withTrash = false);

    /**
     * 根据属性获取相应记录
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     * @param bool  $withTrash
     *
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*'], $withTrash = false);

}
