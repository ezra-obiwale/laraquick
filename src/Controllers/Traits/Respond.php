<?php
namespace Laraquick\Controllers\Traits;

use Illuminate\Http\Response;

trait Respond
{
    /**
     * Called on a successful action
     *
     * @param mixed $data
     * @param integer $code
     * @return Response
     */
    protected function success($data, $code = 200, $meta = null)
    {
        $resp = [
            "status" => true,
            "data" => $data
        ];
        if ($meta) $resp['meta'] = $meta;
        return response()->json($resp, $code);
    }

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
            "status" => false,
            "message" => $message
        ];
        if ($errors) $resp["errors"] = $errors;
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
    protected function createFailedError()
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
    protected function deleteFailedError()
    {
        return $this->error('Delete failed', null, 500);
    }

    /**
     * Create a 404 not found error response
     *
     * @return Response
     */
    final protected function notFoundError()
    {
        return $this->error('Resource not found', null, 404);
    }
}