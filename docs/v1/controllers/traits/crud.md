# Crud Trait

Adds all necessary methods for a complete CRUD operation on the given model.
It uses the [Respond](/v1/controllers/traits/respond) trait.

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
    protected $validator : `Illuminate\Support\Facades\Validator`
</p>

The validator object used in [validation](#checkrequestdata-).

## Abstract Methods

The following methods MUST be implemented in the controller:

### indexResponse ( `...` )

<p class="tip no-bg">
    protected function indexResponse ( `mixed` $data ) : `mixed`
</p>

Called to process the result of a listings and return the appropriate response
to send.

### storeResponse ( `...` )

<p class="tip no-bg">
    protected function storeResponse ( `Illuminate\Database\Eloquent\Model` $data ) : `mixed`
</p>

Called to process the result of a create operation and return the appropriate response
to send.

### showResponse ( `...` )

<p class="tip no-bg">
    protected function showResponse ( `Illuminate\Database\Eloquent\Model` $data ) : `mixed`
</p>

Called to process the result of a single fetch and return the appropriate response
to send.

### updateResponse ( `...` )

<p class="tip no-bg">
    protected function updateResponse ( `Illuminate\Database\Eloquent\Model` $data ) : `mixed`
</p>

Called to process the result of an update operation and return the appropriate response
to send.

### deleteResponse ( `...` )

<p class="tip no-bg">
    protected function deleteResponse ( `Illuminate\Database\Eloquent\Model` $data ) : `mixed`
</p>

Called to process the result of a delete operation and return the appropriate response
to send.

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

### searchModel ( `...` )

<p class="tip no-bg">
    protected function searchModel ( `string` $query ) : `string`
</p>

Called when getting the list of models and [searchQueryParam()](#searchqueryparam-)
exists in the `GET` query. Defaults to [indexModel()][#indexmodel-].

```php
public function indexModel ( ) {
    return Book::with('author');
}
```

### storeModel ( )

<p class="tip no-bg">
    public function storeModel ( ) : `string`
</p>

Called when creating a model instance. Defaults to [indexModel()][#indexmodel-].

```php
public function storeModel ( ) {
    return $this->model ( );
}
```

### updateModel ( )

<p class="tip no-bg">
    protected function updateModel ( ) : `string`
</p>

Called when updating a model instance. Defaults to [indexModel()][#indexmodel-].

```php
public function updateModel ( ) {
    return $this->model ( );
}
```

### deleteModel ( )

<p class="tip no-bg">
    protected function deleteModel ( ) : `string`
</p>

Called when deleting a model instance. Defaults to [indexModel()][#indexmodel-].

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

If `GET` query parameter specified in [searchQueryParam()](#searchqueryparam-)
exists, [searchModel()](#searchmodel-) is called with the query and the returned
model is used instead of [indexModel](#indexmodel-).

Also, if `GET` query parameter specified in [sortParam()](#sortparam-)
exists, the sorting instruction in the value is applied on the model used.
The format for sorting is `column:direction,column:direction,...`, e.g.
`created_at:desc,first_name:asc,last_name:desc`.

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

Called when deleting a model.

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

### rollbackCreate ( )

<p class="tip no-bg">
    protected function rollbackCreate( ) : `null`
</p>

Called when there's an issue with creating a resource.

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

### rollbackUpdate ( )

<p class="tip no-bg">
    protected function rollbackUpdate( ) : `null`
</p>

Called when there's an issue with updating a resource.

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

### beforeDeleteMany ( `...` )

<p class="tip no-bg">
    protected function beforeDeleteMany( `array` &$data) : `Illuminate\Http\Response | null`
</p>

Called before a delete/destory many operation. Parameter `$data` request data.

If the method returns anything not equivalent to `null`, it becomes the sent response.

### rollbackDelete ( )

<p class="tip no-bg">
    protected function rollbackDelete( ) : `null`
</p>

Called when there's an issue with deleting a resource.

### beforeDeleteResponse ( `...` )

<p class="tip no-bg">
    protected function beforeDeleteResponse(`Illuminate\Database\Eloquent\Model` &$data) : `Illuminate\Http\Response | null`
</p>

Called before the success response on the delete operation is sent. Parameter `$data`
is the delete model.

If the method returns anything not equivalent to `null`, it becomes the sent response.

### beforeDeleteManyResponse ( `...` )

<p class="tip no-bg">
    protected function beforeDeleteManyResponse(`integer` $deletedCount) : `Illuminate\Http\Response | null`
</p>

Called before the success response on the delete many operation is sent. Parameter `$deletedCount`
is the number of items successfully deleted.

If the method returns anything not equivalent to `null`, it becomes the sent response.

### defaultPaginationLength ( )

<p class="tip no-bg">
    protected function defaultPaginationLength ( ) : `integer`
</p>

The default pagination length for [listing paginated models](#index-). The default
is 15.

### searchQueryParam ( )

<p class="tip no-bg">
    protected function searchQueryParam ( ) : `string`
</p>

The parameter in the `GET` query which holds the string to search with.

### sortParam ( )

<p class="tip no-bg">
    protected function sortParam ( ) : `string`
</p>

The parameter in the `GET` query which holds the string to sort by.
The expected format of the value in the query is `column:direction,column:direction,...`.