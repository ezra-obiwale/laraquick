<?php
namespace Laraquick\Controllers\Traits\Response;

use Illuminate\Http\Response;

trait Common
{

    /**
     * Called when return a list of paginated items.
     *
     * The data is extracted while the others are placed in meta.
     *
     * @param array $items
     * @param integer $code
     * @return JsonResponse
     */
    protected function paginatedList(array $items, $code = Response::HTTP_OK, array $meta = [])
    {
        $resp = array_key_exists('data', $items) ? $items['data'] : $items;

        if ((int) request()->query('length') !== -1 && count($resp)) {
            unset($items['data']);
            $meta['pagination'] = $items;
        }

        return $this->success($resp, $code, $meta);
    }

    /**
     * Called when validation fails
     *
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function validationError($errors)
    {
        return $this->error('Validation error', $errors);
    }

    /**
     * Should be called when an error occurred which is not a fault of the user's
     *
     * @param string $message The message to send with a 500 status code
     * @return JsonResponse
     */
    protected function serverError($message)
    {
        return $this->error($message, null, 500);
    }

    /**
     * Called when create action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return JsonResponse
     */
    protected function storeFailedError($message = 'Create failed')
    {
        return $this->serverError($message);
    }

    /**
     * Error message for when an update action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return JsonResponse
     */
    protected function updateFailedError($message = 'Update failed')
    {
        return $this->serverError($message);
    }

    /**
     * Called when a delete action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return JsonResponse
     */
    protected function destroyFailedError($message = 'Delete failed')
    {
        return $this->serverError($message);
    }

    /**
     * Called when a restore deleted action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return JsonResponse
     */
    protected function restoreFailedError($message = 'Restoration failed')
    {
        return $this->serverError($message);
    }

    /**
     * Create a 404 not found error response
     *
     * @param string $message The message to send with a 404 status code
     * @return JsonResponse
     */
    protected function notFoundError($message = 'Resource not found')
    {
        return $this->error($message, null, 404);
    }

    /**
     * Create a model not set error response
     *
     * @param string $message The message to send with a 500 status code
     * @return JsonResponse
     */
    protected function modelNotSetError($message = 'Model not set for action')
    {
        return $this->serverError($message);
    }

    /**
     * Translates a given text
     *
     * @param string $text
     * @return string
     */
    protected function translate(string $text): string
    {
        return trans($text);
    }
}
