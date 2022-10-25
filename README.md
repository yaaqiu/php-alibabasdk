# 1688 SDK

## 安装

> 运行环境要求PHP7.1+

```shell
$ composer require yaaqiu/php-alibabasdk
```

## 快速使用

 ```php
<?php

 use alibabasdk\AlibabaOauth;
 use alibabasdk\AlibabaClient;
 use alibabasdk\AlibabaException;
 
 // 授权获取access_token
 $data = new AlibabaOauth('your appKey','your appSecret','your redirectUri');
 
 // 接口调用
 $params = [
    'page'     => 1,
    'pageSize' => 20
 ];

 $client = new AlibabaClient('your appKey', 'your appSecret', 'your accessToken');
 
 $api = 'com.alibaba.p4p:alibaba.cps.op.searchCybOffers-1';
 
 try {
    $client->post($api,$params); // post
    $client->get($api,$params);  // get
 }catch (AlibabaException $e){
    echo $e->getMessage();
 }


 ```
