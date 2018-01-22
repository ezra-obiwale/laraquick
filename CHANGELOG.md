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