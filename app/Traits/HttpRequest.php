<?php


namespace App\Traits;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

trait HttpRequest
{

    protected function get($endpoint, $query = [], $headers = [])
    {
        return $this->request('get', $endpoint, [
            'headers' => $headers,
            'query'   => $query,
        ]);
    }

    protected function post($endpoint, $params = [], $headers = [])
    {
        return $this->request('post', $endpoint, [
            'headers'     => $headers,
            'form_params' => $params
        ]);
    }

    protected function postJson($endpoint, $params = [], $headers = [])
    {
        return $this->request('post', $endpoint, [
            'headers' => $headers,
            'json'    => $params
        ]);
    }

    protected function postBody($endpoint, $body = '', $headers = [])
    {
        return $this->request('post', $endpoint, [
            'headers' => $headers,
            'body'    => $body
        ]);
    }

    protected function postFile($endpoint, $params = [], $headers = [])
    {
        return $this->request('post', $endpoint, [
            'multipart' => []
        ]);
    }

    /**
     * 请求获取结果
     *
     * @param $method
     * @param $endpoint
     * @param $options
     *
     * @return mixed
     */
    protected function request($method, $endpoint, $options)
    {
        return $this->getHttpClient($this->getBaseOptions())->{$method}($endpoint, $options);
    }

    /**
     * 获取基础配置
     *
     * @return array
     */
    protected function getBaseOptions()
    {
        return [
            'base_uri' => method_exists($this, 'getBaseUri') ? $this->getBaseUri() : '',
            'timeout'  => method_exists($this, 'getTimeout') ? $this->getTimeout() : 10,
        ];
    }

    /**
     * 获取Http Client
     *
     * @param array $options
     *
     * @return Client
     */
    protected function getHttpClient($options = [])
    {
        return new Client($options);
    }

    protected function unwrapResponse(ResponseInterface $response)
    {

        $contentType = $response->getHeaderLine('Content-Type');
        $contents = $response->getBody()->getContents();

        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($contents, true);
        } elseif (false !== stripos($contentType, 'xml')) {
            return json_decode(json_encode(simplexml_load_string($contents)), true);
        }

        return $contents;
    }

}
