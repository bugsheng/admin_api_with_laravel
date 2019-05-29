<?php

namespace App\Http\Middleware;

use App\Traits\ProxyTrait;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class CheckForTokenRefresh
{
    use ProxyTrait;

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if($request->bearerToken()){
            return $next($request);
        }

        //如果存在refresh_token 则刷新token 刷新异常直接返回登录失效
        if(!$refresh_token = $request->header('Refresh-Token')){
            return $next($request);
        }

        try{
            $token = $this->refreshToken($guard, $refresh_token);

            $request->headers->set('Authorization', 'Bearer ' . $token['access_token']);

            //如果没拿到用户异常
            if(!$request->user($guard)){
                throw new AuthenticationException();
            }

            // 在响应头中返回新的 token
            return $this->setAuthenticationHeader($next($request), $token);

        }catch (AuthenticationException $e){
            throw $e;
        }
    }

    /**
     * @param null $guard
     * @param $refresh_token
     * @return mixed
     * @throws AuthenticationException
     */
    protected function refreshToken($guard = null, $refresh_token){

        $tokens = $this->getRefreshToken($guard, $refresh_token);
        if($tokens == false){
            throw new AuthenticationException();
        }

        //返回刷新后的授权信息
        return $tokens;

    }

    /**
     * Set the authentication header.
     *
     * @param  \Illuminate\Http\Response|\Illuminate\Http\JsonResponse  $response
     * @param  string|null  $token
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function setAuthenticationHeader($response, $token = null)
    {

        $response->headers->set('Refresh-Authentication', $token['access_token']);
        $response->headers->set('Refresh-Refresh-Token', $token['refresh_token']);
        $response->headers->set('Refresh-Authentication-Type', $token['token_type']);
        $response->headers->set('Refresh-Express-In', $token['expires_in']);
        $response->headers->set("Access-Control-Expose-Headers", "Refresh-Authentication");
        $response->headers->set("Access-Control-Expose-Headers", "Refresh-Refresh-Token");
        $response->headers->set("Access-Control-Expose-Headers", "Refresh-Authentication-Type");
        $response->headers->set("Access-Control-Expose-Headers", "Refresh-Express-In");

        return $response;
    }
}
