<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;

trait Respond
{

    /**
     * Called when an error occurs while performing an action
     *
     * @param string $message
     * @param mixed $errors
     * @param integer $code
     * @return Response
     */
    protected function error($message, $errors = null, $code = 400)
    {
        $resp = [
            "status" => "error",
            "message" => $message
        ];
        if ($errors !== null) {
            $resp["errors"] = $errors;
        }
        return response()->json($resp, $code);
    }

    /**
     * Called when return a list of paginated items.
     *
     * The data is extracted while the others are placed in meta.
     *
     * @param array $items
     * @param integer $code
     * @return void
     */
    protected function paginatedList(array $items, $code = 200, array $meta = [])
    {
        $resp['data'] = array_key_exists('data', $items) ? $items['data'] : $items;
        if (request()->query('length') != 'all' && count($resp['data'])) {
            unset($items['data']);
            $meta['pagination'] = $items;
        }
        return $this->success($resp, $code, $meta);
    }

    /**
     * Called when validation fails
     *
     * @param mixed $errors
     * @return Response
     */
    protected function validationError($errors)
    {
        return $this->error('Validation error', $errors);
    }

    /**
     * Should be called when an error occurred which is not a fault of the user's
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    protected function serverError($message)
    {
        return $this->error($message, null, 500);
    }

    /**
     * Called when create action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    protected function storeFailedError($message = 'Create failed')
    {
        return $this->serverError($message);
    }

    /**
     * Error message for when an update action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    protected function updateFailedError($message = 'Update failed')
    {
        return $this->serverError($message);
    }

    /**
     * Called when a delete action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    protected function destroyFailedError($message = 'Delete failed')
    {
        return $this->serverError($message);
    }

    /**
     * Called when a restore deleted action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    protected function restoreFailedError($message = 'Restoration failed')
    {
        return $this->serverError($message);
    }

    /**
     * Create a 404 not found error response
     *
     * @param string $message The message to send with a 404 status code
     * @return Response
     */
    protected function notFoundError($message = 'Resource not found')
    {
        return $this->error($message, null, 404);
    }
    
    /**
     * Create a model not set error response
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    protected function modelNotSetError($message = 'Model not set for action')
    {
        return $this->serverError($message);
    }
}
