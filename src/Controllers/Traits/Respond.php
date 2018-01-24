<?php
namespace Laraquick\Controllers\Traits;

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
            "message" => $message
        ];
        if ($errors) $resp["errors"] = $errors;
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
    protected function paginatedList(array $items, $code = 200, array $meta = []) {
        $resp['data'] = array_key_exists('data', $items) ? $items['data'] : $items;
        if (request()->query('length') != 'all' && count($resp['data'])) {
            unset($items['data']);
            $meta['pagination'] = $items;
            $resp['meta'] = $meta;
        }
        return response()->json($resp, $code);
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
     * Called when create action fails
     *
     * @return Response
     */
    protected function storeFailedError()
    {
        return $this->error('Create failed', null, 500);
    }

    /**
     * Error message for when an update action fails
     *
     * @return Response
     */
    protected function updateFailedError()
    {
        return $this->error('Update failed', null, 500);
    }

    /**
     * Called when a delete action fails
     *
     * @return Response
     */
    protected function destroyFailedError()
    {
        return $this->error('Delete failed', null, 500);
    }

    /**
     * Called when a restore deleted action fails
     *
     * @return Response
     */
    protected function restoreFailedError()
    {
        return $this->error('Restoration failed', null, 500);
    }

    /**
     * Create a 404 not found error response
     *
     * @return Response
     */
    protected function notFoundError()
    {
        return $this->error('Resource not found', null, 404);
    }
}