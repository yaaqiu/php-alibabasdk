<?php

namespace alibabasdk;

use alibabasdk\utils\Request;

class AlibabaClient
{

    /**
     * @var
     */
    private $baseUrl = 'http://gw.open.1688.com/openapi/';

    /**
     * @var
     */
    private $appKey;

    /**
     * @var
     */
    private $appSecret;

    /**
     * @var
     */
    private $accessToken;

    /**
     * @var
     */
    private $postData;

    /**
     * 初始化
     */
    public function __construct($appKey, $appSecret, $accessToken)
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
    }

    /**
     * 获取签名
     * @throws AlibabaException
     */
    public function getSign(string $apiInfo, array $params): string
    {

        if (empty($apiInfo)) {
            throw new AlibabaException('api must afferent!');
        }

        $arr = explode(':', $apiInfo);
        $spaceName = $arr[0];
        $arr = explode('-', $arr[1]);
        $version = $arr[1];
        $apiName = $arr[0];
        $apiInfo = "param2/{$version}/{$spaceName}/{$apiName}/";

        $appKey = $this->appKey;
        $appSecret = $this->appSecret;
        $apiInfo .= $appKey;
        $this->baseUrl .= $apiInfo;

        $arr = [];
        foreach ($params as $key => &$param) {
            $param = is_string($param) ? $param : json_encode($param, JSON_UNESCAPED_UNICODE);
            $arr[] = $key . $param;
        }

        sort($arr);
        $signStr = join('', $arr);
        $signStr = $apiInfo . $signStr;

        $this->postData = $params;

        return strtoupper(bin2hex(hash_hmac("sha1", $signStr, $appSecret, true)));
    }

    /**
     * get请求
     * @param string $urlInfo
     * @param array $params
     * @return array
     * @throws AlibabaException
     */
    public function get(string $urlInfo, array $params = []): array
    {
        $params['access_token'] = $this->accessToken;
        $sign = $this->getSign($urlInfo, $params);
        $params['_aop_signature'] = $sign;
        $result = Request::get($this->baseUrl, $params);
        $result = json_decode($result, true);
        if (isset($result['error_message'])){
            throw new AlibabaException($result['error_message']);
        }
        if (isset($result['errorMsg'])){
            throw new AlibabaException($result['errorMsg']);
        }
        return $result;
    }


    /**
     * post请求
     * @param string $urlInfo
     * @param array $params
     * @return array
     * @throws AlibabaException
     */
    public function post(string $urlInfo, array $params = []): array
    {
        $params['access_token'] = $this->accessToken;
        $sign = $this->getSign($urlInfo, $params);
        $this->postData['_aop_signature'] = $sign;

        if ((!empty($this->postData)) && (is_array($this->postData))) {
            foreach ($this->postData as $key => $val) {
                if (is_array($val)) {
                    $this->postData[$key] = serialize($val);
                }
            }
        }

        $result = Request::post($this->baseUrl, $this->postData);
        $result = json_decode($result, true);
        if (isset($result['error_message'])){
            throw new AlibabaException($result['error_message']);
        }
        if (isset($result['errorMsg'])){
            throw new AlibabaException($result['errorMsg']);
        }
        return $result;
    }
}