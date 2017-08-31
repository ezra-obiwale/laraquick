<?php

namespace Laraquick\Controllers\Traits;

use Illuminate\Http\Response;

trait Respond {
    /**
     * Called on a successful action
     *
     * @param mixed $data
     * @param integer $code
     * @return Response
     */
    protected function success($data, $code = 200)
    {
        return response()->json([
            "status" => true,
            "data" => $data
        ], $code);
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
     * Error message for when a resource is not found
     *
     * @return string
     */
    protected function notFoundMessage() {
        return 'Resource not found';
    }

    /**
     * Error message for when validation fails
     *
     * @return string
     */
    protected function validationErrorMessage() {
        return 'Validation error';
    }

    /**
     * Error message for when create action fails
     *
     * @return string
     */
    protected function createFailedMessage() {
        return 'Create failed';
    }

    /**
     * Error message for when an update action fails
     *
     * @return string
     */
    protected function updateFailedMessage() {
        return 'Update failed';
    }

    /**
     * Error message for when a delete action fails
     *
     * @return string
     */
    protected function deleteFailedMessage() {
        return 'Delete failed';
    }
    
    /**
     * Create a 404 not found error response
     *
     * @return void
     */
    final protected function notFound()
    {
        return $this->error($this->notFoundMessage(), null, 404);
    }
}