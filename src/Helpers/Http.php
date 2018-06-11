<?php
namespace Laraquick\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Log;

/**
 * @method mixed DELETE($url, $_ = null)
 * @method mixed GET($url, $_ = null)
 * @method mixed PATCH($url, $_ = null)
 * @method mixed POST($url, $_ = null)
 * @method mixed PUT($url, $_ = null)
 */
class Http
{

    private static $client;
    private static $response;

    public static function client()
    {
        if (!static::$client) static::$client = new Client;
        return static::$client;
    }

    private static function processResponse()
    {
        return json_decode(strval(static::$response->getBody()), true);
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
        $args[1]['http_errors'] = false;

        static::$response = call_user_func_array([static::client(), $method], $args);

        return static::processResponse();
    }
    
    public static function requestAsync($method, $url, $_ = null) {
        $args = func_get_args();
        array_shift($args);
        $args[1]['http_errors'] = false;
        return call_user_func_array([static::client(), $method . 'Async'], $args);
    }

    public static function hasErrors() {
        return static::getStatusCode() >= 400;
    }

    public static function getStatusCode() {
        return static::$response ? static::$response->getStatusCode() : 0;
    }

    public static function rawResponse() {
        return static::$response;
    }

    public static function response() {
        return static::processResponse();
    }

    public static function __callStatic($method, $args)
    {
        return static::req($method, $args);
    }

}
