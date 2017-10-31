# Api Trait

Adds all necessary methods for a complete CRUD operation on the given model.
It uses the [Respond](/controllers/traits/respond) trait.

## Usage

```php
// BookController.php

use Laraquick\Controllers\Traits\Api;
use App\Book;

class BookController {
    use Api;

}

// api.php

Route::resource('books', 'App\Http\Controllers\BookController');
```

## Properties

### $validator

<p class="tip no-bg">
    @var Illuminate\Support\Facades\Validator
</p>

The validator object used in [validation](#checkrequestdata-).

## Abstract Methods

The following methods MUST be implemented in the controller:

### model ( )

<p class="tip no-bg">
    protected function model ( ) : `string`
</p>

The fully qualified class name of the entity on which the controller is based.

```php

public function model ( ) {
    return Book::class;
}

```

### validationRules ( `...` )

<p class="tip no-bg">
    protected function validationRules(`array` $data, $id=null): `array`
</p>

The array of rules for which to validate data when creating and updating the model.

Parameter `id` would hold the id of the model being updated when an update operation
is being carried out.

```php
public function validationRules(array $data, $id=null) {
    return [
        'title' => 'required',
        'isbn' => 'required',
        'author_id' => 'required|exists:user,id'
    ];
}
```

## Other Model Methods

Other model methods are available for further customization of the each model
used for each operation. By default, they all return [method model ( )](#model-):

### indexModel ( )

<p class="tip no-bg">
    protected function indexModel ( ) : `string`
</p>

Called when getting the list of models.

```php
public function indexModel ( ) {
    return Book::with('author');
}
```

### storeModel ( )

<p class="tip no-bg">
    public function storeModel ( ) : `string`
</p>

Called when creating a model instance.

```php
public function storeModel ( ) {
    return $this->model ( );
}
```

### updateModel ( )

<p class="tip no-bg">
    protected function updateModel ( ) : `string`
</p>

Called when updating a model instance.

```php
public function updateModel ( ) {
    return $this->model ( );
}
```

### deleteModel ( )

<p class="tip no-bg">
    protected function deleteModel ( ) : `string`
</p>

Called when deleting a model instance.

```php
public function deleteModel ( ) {
    return $this->model ( );
}
```

## Endpoint Methods

### index ( )

<p class="tip no-bg">
    public function index ( ) : `Illuminate\Http\Response`
</p>

Called when fetching a list of models. The lists are paginated by default.

The number of items per page in the pagination can be determined by the `GET`
query `length=10`. If this is not provided, [method defaultPaginationLength](#defaultpaginationlength-)
is used. It defaults to 15.

### store ( `...` )

<p class="tip no-bg">
    public function store(`Illuminate\Http\Request` $request) : `Illuminate\Http\Response`
</p>

Called when storing/creating a new model.

### show ( `...` )

<p class="tip no-bg">
    public function show(`mixed` $id) : `Illuminate\Http\Response`
</p>

Called when retrieving a single model.

### update ( `...` )

<p class="tip no-bg">
    public function update(`Illuminate\Http\Request` $request, `mixed` $id) : `Illuminate\Http\Response`
</p>

Called when updating a model.

### destroy ( `...` )

<p class="tip no-bg">
    public function destroy(`mixed` $id) : `Illuminate\Http\Response`
</p>

## Validation Methods

### checkRequestData ( `...` )

<p class="tip no-bg">
    protected function checkRequestData(`array` $data, `array` $rules, $ignoreStrict = false) : `Illuminate\Http\Response | null`
</p>

Called to validate the given data with the given rules.

Parameter `$ignoreStrict` is useful to exclude a validation from using strict
rules when [method strictValidation](#strictvalidation-) is set to `true`.

### strictValidation ( )

<p class="tip no-bg">
    protected function strictValidation ( ) : `boolean`
</p>

If `true`, it indicates that only the keys provided in [validation rules](#checkrequestdata-)
should available in the data. If more than these are available, an exception is thrown.

## Other Methods

### beforeIndexResponse ( `...` )

<p class="tip no-bg">
    beforeIndexResponse(`mixed` &$data) : `mixed | null`
</p>

Called before sending the response for [fetching a list of objects](#index-).

The parameter is an object of either `Illuminate\Pagination\Paginator` or `Illuminate\Database\Eloquent\Collection`.
The latter is only returned if there's a get query `?length=all`.

If the method returns anything not equivalent to `null`, it becomes the sent response.

### beforeCreate ( `...` )

<p class="tip no-bg">
    protected function beforeCreate(`array` &$data) : `Illuminate\Http\Response | null`
</p>

Called before a create/store operation is done on the validated data. The data
can be manipulated as it is passed by reference.

If the method returns anything not equivalent to `null`, it becomes the sent response.

### beforeCreateResponse ( `...` )

<p class="tip no-bg">
    protected function beforeCreateResponse(`Illuminate\Database\Eloquent\Model` &$data) : `Illuminate\Http\Response | null`
</p>

Called before the success response of the create/store operation is sent.
Parameter `$data` is the created model.

If the method returns anything not equivalent to `null`, it becomes the sent response.

### beforeUpdate ( `...` )

<p class="tip no-bg">
    protected function beforeUpdate(`array` &$data) : `Illuminate\Http\Response | null`
</p>

Called before an update operation is done on the validated data. The data can be
manipulated as it is passed by reference.

If the method returns anything not equivalent to `null`, it becomes the sent response.

### beforeUpdateResponse ( `...` )

<p class="tip no-bg">
    protected function beforeUpdateResponse(`Illuminate\Database\Eloquent\Model` &$data) : `Illuminate\Http\Response | null`
</p>

Called before the success response of the update operation is sent.
Parameter `$data` is the updated model.

If the method returns anything not equivalent to `null`, it becomes the sent response.

### beforeDelete ( `...` )

<p class="tip no-bg">
    protected function beforeDelete(`Illuminate\Database\Eloquent\Model` &$data) : `Illuminate\Http\Response | null`
</p>

Called before a delete/destory operation. Parameter `$data` is the model to be deleted.

If the method returns anything not equivalent to `null`, it becomes the sent response.

### beforeDeleteResponse ( `...` )

<p class="tip no-bg">
    protected function beforeDeleteResponse(`Illuminate\Database\Eloquent\Model` &$data) : `Illuminate\Http\Response | null`
</p>

Called before the success response on the delete operation is sent. Parameter `$data`
is the delete model.

If the method returns anything not equivalent to `null`, it becomes the sent response.

### defaultPaginationLength ( )

<p class="tip no-bg">
    protected function defaultPaginationLength ( ) : `integer`
</p>

The default pagination length for [listing paginated models](#index-). The default
is 15.