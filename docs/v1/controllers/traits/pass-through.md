# PassThrough Trait

A collection of methods to help pass requests to another server. The
controller serves as a passage to the actual server that's to deal with the request.

It uses the [Api trait](/v1/controllers/traits/api). Since the request would be passed
along, there won't be any need for the [Api trait's abstract method model( )](/v1/controllers/traits/api#model-).
The method has therefore been implemented. 

The [abstract method validationRules ( `...` )](/v1/controllers/traits/api#validationrules-)
must however still be implemented as validations can be made on this server before
passing on the action processing server.

<p class="tip">
This trait also uses the [Http helper class](/v1/helpers/http).
</p>

## Properties

### $responseStatusCode

<p class="tip no-bg">
    protected $responseStatusCode : `integer`
</p>

Holds the response code of the response gotten from the processing server.

## Abstract methods

### headers ( )

<p class="tip no-bg">
    protected headers ( ) : `array`
</p>

The method should return an array of headers to send with all requests to the
processing server.

### toUrl ( )

<p class="tip no-bg">
    protected toUrl ( ) : `string`
</p>

The method should return the url to server endpoint to process the resource request.

## Other Header Methods

### indexHeaders ( )

<p class="tip no-bg">
    protected indexHeaders ( ) : `array`
</p>

The method should return an array of headers to send with the request to fetch all
models from the the processing server. This defaults to [method headers( )](#headers-).

### createHeaders ( )

<p class="tip no-bg">
    protected createHeaders ( ) : `array`
</p>

The method should return an array of headers to send with the request to create a
new resource on the processing server. This defaults to [method headers( )](#headers-).

### showHeaders ( )

<p class="tip no-bg">
    protected showHeaders ( ) : `array`
</p>

The method should return an array of headers to send with the request to fetch a
resource model from the the processing server. This defaults to [method headers( )](#headers-).

### updateHeaders ( )

<p class="tip no-bg">
    protected updateHeaders ( ) : `array`
</p>

The method should return an array of headers to send with the request to update a
model resource on the processing server. This defaults to [method headers( )](#headers-).

### deleteHeaders ( )

<p class="tip no-bg">
    protected deleteHeaders ( ) : `array`
</p>

The method should return an array of headers to send with the request to delete a
model resource from the processing server. This defaults to [method headers( )](#headers-).

## Endpoint Methods

### index ( )

<p class="tip no-bg">
    public function index ( ) : `mixed`
</p>

Called when fetching a list of models.

### store ( `...` )

<p class="tip no-bg">
    public function store( `Illuminate\Http\Request` $request ) : `mixed`
</p>

Called when storing/creating a new model.

### show ( `...` )

<p class="tip no-bg">
    public function show ( `mixed` $id ) : `mixed`
</p>

Called when retrieving a single model.

### update ( `...` )

<p class="tip no-bg">
    public function update ( `Illuminate\Http\Request` $request, `mixed` $id ) : `mixed`
</p>

Called when updating a model.

### destroy ( `...` )

<p class="tip no-bg">
    public function destroy ( `mixed` $id ) : `mixed`
</p>

Called when deleting a model.


## Other Methods

### httpRequest ( `...` )

<p class="tip no-bg">
    protected function httpRequest ( `string` $method, `string` $url, `array` $options = [] ) : `mixed`
</p>

A shortcut to [Http request( `...` )](/v1/helpers/http#request-).

### httpResponse ( )

<p class="tip no-bg">
    protected function httpResponse ( ) : `mixed`
</p>

A shortcut to [Http rawResponse( `...` )](/v1/helpers/http#rawresponse-).

### httpStatusCode ( )

<p class="tip no-bg">
    protected function httpStatusCode ( ) : `mixed`
</p>

A shortcut to [Http getStatusCode( `...` )](/v1/helpers/http#getstatuscode-).

### methodMap ( )

<p class="tip no-bg">
    protected function methodMap ( `string` $action = null ) : `array`
</p>

The method should return an array of `actions => HTTP method`. Default is:

```php
    return [
        'index' => 'GET',
        'create' => 'POST',
        'show' => 'GET',
        'update' => 'PUT',
        'delete' => 'DELETE'
    ];
```

If parameter `$action` is provided though, the method should return the
corresponding METHOD for the given action.