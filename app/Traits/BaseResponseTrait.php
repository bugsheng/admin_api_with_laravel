<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2018/9/29
 * Time: 13:40
 */

namespace App\Traits;

/**
 * 基础标准调用返回Trait
 * Trait BaseResponseTrait
 *
 * @package App\Traits
 */
trait BaseResponseTrait
{

    public function respond($status, $respond_data, $message)
    {
        return ['status' => $status, 'data' => $respond_data, 'message' => $message];
    }

    public function baseSucceed($respond_data = [], $message = 'Request success!', $status = true)
    {
        return $this->respond($status, $respond_data, $message);
    }

    public function baseFailed($message = 'Request failed!', $respond_data = [], $status = false)
    {
        return $this->respond($status, $respond_data, $message);
    }

}
