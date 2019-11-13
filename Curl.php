<?php
/**
 * Created by PhpStorm.
 * User: ljt
 * Date: 2019/10/28
 * Time: 17:52
 */

class Curl
{
    /**
     * @param  string $url 请求的url
     * @param  integer $timeout 允许curl执行的时间
     * @param  boolean $type 返回时是否json_decode
     * @return mixed
     */
    public static function get($url, $timeout = 1000, $type = true)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 3000);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:' . $_SERVER['REMOTE_ADDR']));
        $res = curl_exec($curl);
        curl_close($curl);
        return $type ? json_decode($res, true) : $res;
    }

    /**
     * @param  string $url 请求的url
     * @param  array $data post请求的数据
     * @param  integer $timeout 允许执行的时间
     * @param  boolean $type 返回时是否json_decode
     * @return mixed
     */
    public static function post($url, $data, $timeout = 5000, $type = true)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 3000);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:' . $_SERVER['REMOTE_ADDR']));
        $res = curl_exec($curl);
        curl_close($curl);
        return $type ? json_decode($res, true) : $res;
    }

    //curl请求
    function curl_method($url, $data, $header = false, $method = "POST") {
        //初使化init方法
        $ch = curl_init();
        //指定URL
        curl_setopt($ch,CURLOPT_URL, $url);
        //设定请求后返回结果
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $data ));
                break;
            case 'GET': break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置请求体，提交数据包
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        //忽略证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //header头信息
        if ($header) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        //发送请求
        $output = curl_exec($ch);
        //关闭curl
        curl_close($ch);
        //返回数据
        return $output;
    }
}