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