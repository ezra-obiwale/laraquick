<?php

namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;

/**
 * Methods for fetching a resource
 *
 */
trait Show
{
    /**
     * Create a 404 not found error response
     *
     * @param string $message The message to send with a 404 status code
     * @return Response
     */
    abstract protected function notFoundError($message = 'Resource not found');

    /**
     * Create a model not set error response
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    abstract protected function modelNotSetError($message = 'Model not set for action');

    /**
     * The model to use in the show method.
     *
     * @return mixed
     */
    abstract protected function showModel();

    /**
     * Single
     *
     * Gets the specified item.
     *
     * @urlParam id integer|string required The id of the item to get.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $model = $this->showModel();

        if (!$model) {
            return $this->modelNotSetError('Show model undefined');
        }

        $item = $this->find($model, $id);

        if (!$item) {
            return $this->notFoundError();
        }

        if (method_exists($this, 'authorizeMethod')) {
            $this->authorizeMethod('show', [$model, $item]);
        }

        if ($resp = $this->beforeShowResponse($item)) {
            return $resp;
        }

        return $this->showResponse($item);
    }

    /**
     * Called when the model is found but before sending the response
     *
     * @param Model $model
     * @return mixed The response to send or null
     */
    protected function beforeShowResponse(Model $model)
    {
    }

    /**
     * Called for the response to method show()
     *
     * @param Model $model
     * @return Response|array
     */
    abstract protected function showResponse(Model $model);
}
