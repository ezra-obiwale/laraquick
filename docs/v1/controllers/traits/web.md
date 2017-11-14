# Web Trait

<p class="tip">
This trait uses the [Crud trait](/controllers/traits/crud).
</p>

This trait implements some response methods of the [Crud trait](/controllers/traits/crud).
It is meant to be used on controllers meant for web endpoints.

## Implemented Methods

### storeResponse ( `...` )

<p class="tip no-bg">
    protected function storeResponse ( `Illuminate\Database\Eloquent\Model` $data ) : `Illuminate\Http\RedirectResponse`
</p>

Redirects to the previous page with a status message.

### updateResponse ( `...` )

<p class="tip no-bg">
    protected function updateResponse ( `Illuminate\Database\Eloquent\Model` $data ) : `Illuminate\Http\RedirectResponse`
</p>

Redirects to the previous page with a status message.

### deleteResponse ( `...` )

<p class="tip no-bg">
    protected function deleteResponse ( `Illuminate\Database\Eloquent\Model` $data ) : `Illuminate\Http\RedirectResponse`
</p>

Redirects to the previous page with a status message.

### deleteManyResponse ( `...` )

<p class="tip no-bg">
    protected function deleteManyResponse ( `integer` $data ) : `Illuminate\Http\RedirectResponse`
</p>

Redirects to the previous page with a status message bearing the number of successfully delete items.

## Overriden Methods

### error ( `...` )

<p class="tip no-bg">
    protected function error ( `string` $message, `mixed` $errors = null, `integer` $code = 400 ) : `Illuminate\Http\RedirectResponse`
</p>

Redirects to the previous page with a status message and errors, if available.

This covers for all error messages coming from the [Crud trait](/controllers/traits/crud).