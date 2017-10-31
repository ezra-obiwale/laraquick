<?php
namespace Laraquick\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Exception;

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
        if (!self::$client) self::$client = new Client;
        return self::$client;
    }

    private static function processResponse()
    {
        return json_decode(strval(self::$response->getBody()), true);
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

        self::$response = call_user_func_array([self::client(), $method], $args);

        return self::processResponse();
    }

    public static function hasErrors() {
        return self::getStatusCode() >= 400;
    }

    public static function getStatusCode() {
        return self::$response->getStatusCode();
    }

    public static function rawResponse() {
        return self::$response;
    }

    public static function __callStatic($method, $args)
    {
        return self::req($method, $args);
    }

}
