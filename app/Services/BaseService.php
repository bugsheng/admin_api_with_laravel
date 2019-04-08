<?php
/**
 * Created by PhpStorm.
 * User: shenglin
 * Date: 2019/4/8
 * Time: 21:42
 */

namespace App\Services;


use App\Traits\BaseResponseTrait;

class BaseService
{

    use BaseResponseTrait;

    protected $model;

    /**
     * BaseService constructor.
     */
    public function __construct()
    {

    }

    /**
     * 获取列表
     *
     * @param array $filters
     * @param int $perPage
     * @param array $columns
     * @param string $orderBy
     * @param string $orderType
     * @param string $pageName
     * @param null $page
     * @return array
     */
    public function paginate($filters = [],$perPage = 15,$columns = ['*'],$orderBy = 'id',$orderType = 'DESC', $pageName = 'page', $page = null){

//        //获取page页数据
//        $lists = $this->model->where($filters)
//            ->orderBy($orderBy,$orderType)
//            ->paginate($perPage,$columns,$pageName,$page);
//
//        //如果当前页无数据，取上一页数据
//        if(!count($lists)){
//            $lists = $this->model->where($filters)
//                ->orderBy($orderBy,$orderType)
//                ->paginate($perPage,$columns,$pageName,$lists->lastPage());
//        }

        return $this->baseSucceed();
    }
}
