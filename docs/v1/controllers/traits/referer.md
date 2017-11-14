# Referer Trait

A collection of methods for verifying the referer of a request.

## Methods

### verifyReferer ( `...` )

<p class="tip no-bg">
    protected function verifyReferer ( `string` $url ) : `null`
</p>

Verifies that the given url has the same origin as the request referer.
If they it doesn't, an exception is thrown.

### originFromUrl ( `...` )

<p class="tip no-bg">
    protected function originFromUrl ( `string` $url) : `string`
</p>

Fetches the origin from a url string.