<?php
namespace Laraquick\Helpers;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Laraquick\Jobs\AsyncCall;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

/**
 * @method mixed delete($url, $_ = null)
 * @method mixed deleteAsync($url, $_ = null)
 * @method mixed get($url, $_ = null)
 * @method mixed getAsync($url, $_ = null)
 * @method mixed patch($url, $_ = null)
 * @method mixed patchAsync($url, $_ = null)
 * @method mixed post($url, $_ = null)
 * @method mixed postAsync($url, $_ = null)
 * @method mixed put($url, $_ = null)
 * @method mixed putAsync($url, $_ = null)
 */
class Http
{
    private static $client;
    private static $response;

    /**
     * The Guzzle clinet
     *
     * @return Client
     */
    public static function client()
    {
        if (!self::$client) {
            self::$client = new Client;
        }

        return self::$client;
    }

    /**
     * Process json response
     *
     * @param mixed $response The response object
     * @param boolean $toArray Indicate whether to parse to an array
     * @return stdClass|array
     */
    public static function processJsonResponse($response, $toArray = true)
    {
        return json_decode(strval($response->getBody()), $toArray);
    }

    private static function req($method, array $args)
    {
        array_unshift($args, $method);

        return call_user_func_array([get_called_class(), 'request'], $args);
    }

    /**
     * Send a request
     *
     * This takes the same parameters as Guzzle's request method.
     *
     * @param string $method Any request method supported by Guzzle
     * @param string $url The url to send the request to
     * @param mixed $_
     * @return mixed
     */
    public static function request($method, $url, $_ = null)
    {
        $args = func_get_args();
        array_shift($args);
        $args[1]['http_errors'] = false;

        self::$response = call_user_func_array([static::client(), $method], $args);

        return static::processJsonResponse(self::$response);
    }

    /**
     * Send a request asynchronously
     *
     * This takes the same parameters as Guzzle's request method.
     * An additional callback is accepted as the last parameter. This receives the
     * response of the request.
     *
     * Aysnc request uses the queue system. It must therefore be set up properly.
     *
     * @param string $method Any request method supported by Guzzle
     * @param string $url The url to send the request to
     * @param mixed $_
     * @param callable $callback @see call_user_func()
     * @return mixed
     */
    public static function requestAsync($method, $url, $_ = null)
    {
        $args = func_get_args();
        $callback = array_pop($args);

        if (!is_callable($callback)) {
            $args[] = $callback;
            $callback = null;
        }

        AsyncCall::dispatch([self::class, 'request', $args], $args, [self::class, 'asyncCallback'], [$callback], ['request-async']);
    }

    /**
     * Processes async responses and passes it to the callback
     *
     * @param mixed $response
     * @param callable $callback
     * @return void
     */
    public static function asyncCallback($response, callable $callback = null)
    {
        if ($callback) {
            call_user_func($callback, self::processJsonResponse($response));
        }
    }

    /**
     * Indicates whether the response to the last request has status code less than or equal to 400
     *
     * @return boolean
     */
    public static function hasErrors()
    {
        return static::getStatusCode() >= 400;
    }

    /**
     * Fetches the status code of the last response to the last request
     *
     * @return void
     */
    public static function getStatusCode()
    {
        return self::$response ? self::$response->getStatusCode() : 0;
    }

    /**
     * Fetch the response without being parsed
     *
     * @return void
     */
    public static function rawResponse()
    {
        return self::$response;
    }

    /**
     * The last processed response
     *
     * @return mixed
     */
    public static function response()
    {
        return static::processJsonResponse(self::$response);
    }

    /**
     * Package the response into a Response object
     *
     * @return Response
     */
    public static function respond() : JsonResponse
    {
        return response()->json(static::response(), static::getStatusCode());
    }

    /**
     * Provides shortcuts to request methods e.b. POST, GET, DELETE
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return static::req($method, $args);
    }
}
