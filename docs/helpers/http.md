# HTTP Helper

A wrapper around [GuzzleHttp](http://docs.guzzlephp.org) for quicker and easier use.

## Methods

### request ( `...` )

<p class="tip no-bg">
    public static function request(`string` $method, `string` $url, $_ = null) : `mixed`
</p>

A shortcut to [GuzzleHttp's `$client->request()` method](http://docs.guzzlephp.org/en/stable/quickstart.html#making-a-request).

Parameters are the same except that `$url` is the full url to the target endpoint.

The method returns the actual data that was returned by the request. The full raw
response is at [method rawResponse( )](#rawresponse-).

### hasError ( )

<p class="tip no-bg">
    public static function hasError ( ) : `boolean`
</p>

Indicates whether the request returns with an error or not. This is based on the
[response's status code](#getstatuscode-).

### getStatusCode ( )

<p class="tip no-bg">
    public static function getStatusCode ( ) : `integer`
</p>

The status code of response.

### rawResponse ( )

<p class="tip no-bg">
    public static function rawResponse ( ) : `GuzzleHttp\Psr7\Response`
</p>

The raw response object.

### client ( )

<p class="tip no-bg">
    public static function client ( ) : `GuzzleHttp\Client`
</p>

## Magic Methods

All methods on [`GuzzleHttp\Client`](http://docs.guzzlephp.org/en/stable/quickstart.html#sending-requests)
are magically available too.