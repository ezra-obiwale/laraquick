# Api Trait

<p class="tip">
This trait uses the [Crud trait](/controllers/traits/crud).
</p>

This trait implements the response methods of the [Crud trait](/controllers/traits/crud).
It is meant to be used on controllers meant for API endpoints.

## Implemented Methods

### indexResponse ( `...` )

<p class="tip no-bg">
    protected function indexResponse ( `mixed` $data ) : `Illuminate\Http\Response`
</p>

Returns the [Respond trait's paginatedList](/controllers/traits/respond#paginatedlist-)
method response after applying it to the `$data`.

### storeResponse ( `...` )

<p class="tip no-bg">
    protected function storeResponse ( `Illuminate\Database\Eloquent\Model` $data ) : `Illuminate\Http\Response`
</p>

Returns `$data` with response code 201

### showResponse ( `...` )

<p class="tip no-bg">
    protected function showResponse ( `Illuminate\Database\Eloquent\Model` $data ) : `Illuminate\Http\Response`
</p>

Returns `$data` with response code 200

### updateResponse ( `...` )

<p class="tip no-bg">
    protected function updateResponse ( `Illuminate\Database\Eloquent\Model` $data ) : `Illuminate\Http\Response`
</p>

Returns `$data` with response code 202

### deleteResponse ( `...` )

<p class="tip no-bg">
    protected function deleteResponse ( `Illuminate\Database\Eloquent\Model` $data ) : `Illuminate\Http\Response`
</p>

Returns `$data` with response code 202