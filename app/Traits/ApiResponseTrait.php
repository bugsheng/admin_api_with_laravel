<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2018/9/29
 * Time: 13:41
 */

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Illuminate\Support\Facades\Response;

/**
 * API调用对外返回值结构公共报文体 Trait
 * Trait ApiResponseTrait
 * @package App\Traits
 */
trait ApiResponseTrait
{

    /**
     * HTTP 状态码 默认200 成功
     * @var int
     */
    protected $httpCode = FoundationResponse::HTTP_OK;

    /**
     * 获取状态码
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * 设置返回状态码
     * @param $httpCode
     * @return $this
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;
        return $this;
    }

    /**
     * 定义基础共同返回结构体
     * @param $data
     * @param array $header
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $header = [])
    {
        return Response::json($data, $this->getHttpCode(), $header);
    }

    /**
     * 定义成功消息返回
     * @param $message
     * @param string $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function message($message, $status = "success")
    {
        return $this->status($status, [
            'message' => $message
        ]);
    }

    /**
     * 服务出错500报错返回
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function internalError($message = "Internal Error!")
    {
        return $this->failed($message, FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * 定义成功返回
     * @param $data
     * @param string $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data, $status = "success")
    {
        return $this->status($status, compact('data'));
    }

    /**
     * 定义失败返回
     * @param $message
     * @param int $code
     * @param string $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function failed($message, $code = FoundationResponse::HTTP_BAD_REQUEST, $status = 'error')
    {
        return $this->setHttpCode($code)->message($message, $status);
    }

    /**
     * 定义成功返回状态
     * @param $status
     * @param array $data
     * @param null $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($status, array $data, $code = null)
    {
        if ($code) {
            $this->setHttpCode($code);
        }
        $status = [
            'status' => $status,
            'code' => $this->httpCode
        ];

        $data = array_merge($status, $data);
        return $this->respond($data);
    }


    /**
     * 定义404错误返回
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function notFond($message = 'Not Fond!')
    {
        return $this->failed($message, Foundationresponse::HTTP_NOT_FOUND);
    }

}
