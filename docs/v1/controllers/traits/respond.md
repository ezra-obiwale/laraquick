# Respond Trait

A collection of methods for quick `Illuminate\Http\Response` responses.

## Methods

### createFailedError ( )

<p class="tip no-bg">
    protected function createFailedError ( ) : `Illuminate\Http\Response`
</p>

The error response for when creation of a new resource fails.

### deleteFailedError ( )

<p class="tip no-bg">
    protected function deleteFailedError ( ) : `Illuminate\Http\Response`
</p>

The error response for when deleting a resource fails.

### error ( `...` )

<p class="tip no-bg">
    protected function error ( `string` $message, `mixed` $errors = null, `integer` $code ) : `Illuminate\Http\Response`
</p>

Creates an error response from the given parameters.

### notFoundError ( )

<p class="tip no-bg">
    protected function notFoundError ( ) : `Illuminate\Http\Response`
</p>

The error response for a target resource doesn't exist.

### paginatedList ( `...` )

<p class="tip no-bg">
    protected function paginatedList ( `array` $items, `integer` $code = 200 ) : `Illuminate\Http\Response`
</p>

The response for a paginated array of items. The array must contain key `data`.
Other keys aside this are put into `meta->pagination`.

### validationError ( `...` )

<p class="tip no-bg">
    protected function validationError ( `mixed` $errors ) : `Illuminate\Http\Response`
</p>

The error response for when validation fails.
