<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2018/9/29
 * Time: 16:48
 */

namespace App\Traits;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

/**
 * 授权Trait
 * Trait ProxyTrait
 * @package App\Traits
 */
trait ProxyTrait
{

    /**
     * 授权获取token
     * @param string $guard
     * @param string $login_name
     * @param string $login_password
     * @return bool|mixed
     */
    public function authenticate($guard = '',$login_name, $login_password)
    {
        $client = new Client();

        try {
            $url = request()->root() . '/api/oauth/token';

            if ($guard) {
                $params = array_merge(config('passport.proxy'), [
                    'username' => $login_name,
                    'password' => $login_password,
                    'provider' => $guard
                ]);
            } else {
                $params = array_merge(config('passport.proxy'), [
                    'username' => $login_name,
                    'password' => $login_password,
                ]);
            }

            $respond = $client->request('POST', $url, ['form_params' => $params]);
        } catch (RequestException  $exception) {
            return false;
        } catch (GuzzleException $exception) {
            return false;
        } catch (\Exception $exception) {
            return false;
        }

        if ($respond->getStatusCode() == 200) {
            return json_decode($respond->getBody()->getContents(), true);
        }
        return false;
    }

    /**
     * 刷新token
     * @param $refresh_token
     * @return bool|mixed
     */
    public function getRefreshToken($refresh_token)
    {
        $client = new Client();

        try {
            $url = request()->root() . '/api/oauth/token';

            $params = array_merge(config('passport.refresh_token'), [
                'refresh_token' => $refresh_token,
            ]);

            $respond = $client->request('POST', $url, ['form_params' => $params]);
        } catch (RequestException  $exception) {
            return false;
        } catch (GuzzleException $exception) {
            return false;
        } catch (\Exception $exception) {
            return false;
        }

        if ($respond->getStatusCode() === 200) {
            return json_decode($respond->getBody(), true);
        }
        return false;
    }

}
