<?php
namespace Laraquick\Helpers;

use GuzzleHttp\Client;

class Http
{

    private static $client;

    private static function client()
    {
        if (!self::$client) self::$client = new Client;
        return self::$client;
    }

    private static function processResponse($resp)
    {
        return json_decode(strval($resp->getBody()));
    }

    private static function req($method, array $args)
    {
        array_unshift($args, $method);
        return call_user_func_array([get_called_class(), 'request'], $args);
    }

    public static function request($method, $url, $_ = null)
    {
        $args = func_get_args();
        array_shift($args);
        $resp = call_user_func_array([self::client(), $method], $args);
        return self::processResponse($resp);
    }

    public static function __callStatic($method, $args)
    {
        return self::req($method, $args);
    }

}
