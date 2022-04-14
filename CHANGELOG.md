# Changelog

## UNVERSIONED

### Added

### Updated

- Fixed toArray issue with snake/camel case relationships.
- Adds method `getMailMessage` to Notifications\Send.

### Removed

- Removed Helper/Http. Laravel now has an Http facade.
- Removed Events/WebSocket. Use [Laravel WebSockets](https://beyondco.de/docs/laravel-websockets/getting-started/introduction).
- Removed Helper::relationLoaded method. Laravel now has a method for that.

## Missed out on a couple of version. Will try to this file up-to-date going forward.

## 3.8.0

Added Notfications DBChannel

## 3.6.2

- Fixed issue with `before{method}Response` in PassThrough's respond method

## 3.6.0

- Added DB helper class to create full text indexes
- Added Searchable trait to provide methods for full text searches

## 3.5.10

- Ensure `0` and empty strings are allowed as success and error messages

## 3.5.7

- Updated parameters for method `rollbackStore()`
- Updated method `rollbackDestroy()`
- Catch and log exceptions thrown in `rollbackDestroy()`.

## 3.5.6

- Catch and log exceptions thrown in methods `rollbackStore()` and `rollbackUpdate()`.

## 3.5.5

- Updated methods `rollbackStore()` and `rollbackUpdate()`.

## 3.4.1

- Fixed typo

## 3.4.0

- Added method `validateData()`
- Method `validateRequest()` now only takes parameters `$rules` and `$messages`.
- Implemented using custom validation messages
- Deprecated method `checkRequestData()` should be replaced with new method `validateData()`
- Removed parameter `$ignoreStrict` entirely from method `validateData()`

## 3.3.4

Deprecated method `checkRequestData()` in favour of `validateRequest()`

## 3.3.3

- Ensured exceptions in `beforeResponse` methods are caught
- Ensured method `rollback` is called on the caught errors

## 3.3.1

Fixed notFoundError consistency issue'

## 3.3.0

- Exempted `$hidden` attributes in model helper trait
- Added method `validationMessages()` to the validation trait
- Used `validationMessages()` in both storing and updating.
- Allowed custom `notFoundError` messages
- Allowed success method in api to have zero params

## 3.2.0

- Check that model methods do not return falsable
- The model being updated is now passed as the second parameter to `beforeUpdate`

## 3.1.0

`Laraquick\Models\Traits\Helper` now has an `except` scope method to remove provided
columns from selection. Thanks [@mykeels](https://github.com/mykeels).

## 3.0.5

Response method `paginatedList()` now takes a third parameter, an array
of custom meta fields.

## 3.0.4

Created Dud model

PassThrough Trait:
- Updated methed names with *create* to *store*
- Updated method names with *delete* to *destroy*
- Ensured using validation rules work as expected

## 3.0.3

Made attach/detach/sync error message more custom to the parameter key or relation
name

Added methods `prepareAttachItems`, `prepareDetachItems`, `prepareSyncItems` to **Attachable**

## 3.0.2

Converted **Attachable** public methods' `$relation` to camelCase if it doesn't exist
on the model in the default form.

## 3.0.1

Created model helper trait

## 3.0.0

Changed response structure to:

```javascript
{
	"status": "ok", // or "error", if the request failed
	"message": "...", // [optional] holds a string description of the status
	"data": "", // [optional] holds the retrieved/processed data if any
	"errors": [] // [optional] holds the errors encountered while processing the request
}
```