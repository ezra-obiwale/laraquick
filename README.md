# laraquick

A collection of classes to be extended/used in laravel api applications for quick
development.

## Introduction

The library contains traits with well documented methods that should be used by
controllers and models to enhance coding speed.

## Installation

```
composer require d-scribe/laraquick
```

## Dependencies

### v1.*

- PHP               >=      7.0
- Laravel           -       ~5.5
- Guzzle            -       ~6.0

### v0.*

- PHP               >=     5.6.0
- Laravel           -      5.4.*
- Laravel Fractal   -      ^4.0
- Guzzle            -       ~6.0

## Example

An example controller for a `Book` model is:

```php
use App\Book;
use Laraquick\Controllers\Traits\Api;

class BookController extends Controller {

    use Api;

    protected function model() {
        return Book::class;
    }

    protected function validationRules(array $data, $id = null) {
        return [
            'title' => 'required|max:200',
            'author' => 'required|max:50',
            'genre' => 'required'
        ];
    }
}

```

And with just the above, the controller would take care of listing (w/ pagination),
and all `CRUD` operations and give the right JSON responses.

## API Documentation

Coming soon
