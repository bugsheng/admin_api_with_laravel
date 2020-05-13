<?php

namespace App\Exceptions;

use App\Traits\BaseResponseTrait;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Prophecy\Exception\Doubler\MethodNotFoundException;

class Handler extends ExceptionHandler
{
    use BaseResponseTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     *  Report or log an exception.
     *
     * @param Exception $exception
     *
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $response = $this->baseFailed('Not Found!');
            return response()->json($response, 404);
        }

        if ($exception instanceof MethodNotFoundException) {
            $response = $this->baseFailed('Not Found!');
            return response()->json($response, 404);
        }

        return parent::render($request, $exception);
    }

    /**
     * 未登录授权自定义异常返回
     *
     * @param \Illuminate\Http\Request $request
     * @param AuthenticationException  $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $response = $this->baseFailed('登录失效，请先登录');
        return response()->json($response, 401);
    }

    /**
     * FormRequest表单验证失败自定义返回
     *
     * @param \Illuminate\Http\Request $request
     * @param ValidationException      $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        $message = $exception->getMessage();
        if ($message == 'The given data was invalid.') {
            $message = '验证失败';
        }

        $response = $this->baseFailed($message, $exception->errors());
        return response()->json($response, $exception->status);
    }
}
