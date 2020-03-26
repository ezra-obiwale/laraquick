<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laraquick\Helpers\DB;

/**
 * Methods for updating a resource
 *
 */
trait Update
{
    use Authorize;

    /**
     * Create a 404 not found error response
     *
     * @param string $message The message to send with a 404 status code
     * @return Response
     */
    abstract protected function notFoundError($message = "Resource not found");

    /**
     * Error message for when an update action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    abstract protected function updateFailedError($message = 'Update failed');

    /**
     * Create a model not set error response
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    abstract protected function modelNotSetError($message = 'Model not set for action');

    /**
     * The model to use in the update method.
     *
     * @return mixed
     */
    abstract protected function updateModel();

    /**
     * Called after validation but before update method is called
     *
     * @param array $data The data to update the model with
     * @param Model $model The model to be updated
     * @return mixed The response to send or null
     */
    protected function beforeUpdate(array &$data, Model $model)
    {
    }

    /**
     * Called when an error occurs in a update operation
     *
     * @param array $data The data from the request
     * @param Model $model The model from the id
     * @return void
     */
    protected function rollbackUpdate(array &$data, Model $model)
    {
    }

    /**
     * Update the specified resource.
     * @param  Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if ($resp = $this->validateRequest()) {
            return $resp;
        }

        $model = $this->updateModel();

        $item = is_object($model)
            ? $model->find($id)
            : $model::find($id);

        if (!$item) {
            return $this->notFoundError();
        }

        $this->authorizeMethod('update', [$model, $item]);

        $data = $request->only(array_keys($this->validationRules($request->all(), $id)));

        return DB::transaction(
            function () use (&$data, &$item) {
                if ($resp = $this->beforeUpdate($data, $item)) {
                    return $resp;
                }

                $result = $item->update(Arr::only($data, $item->getFillable()));

                if (!$result) {
                    throw new \Exception('Update method returned falsable');
                }

                if ($resp = $this->beforeUpdateResponse($item)) {
                    return $resp;
                }
                return $this->updateResponse($item);
            },
            function ($ex) use ($data, $item) {
                $message = $ex->getMessage();
                try {
                    $this->rollbackUpdate($data, $item);
                } catch (\Exception $ex) {
                    $message = $ex->getMessage();
                }
                return $this->updateFailedError($message);
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param Model $model
     * @return mixed The response to send or null
     */
    protected function beforeUpdateResponse(Model $model)
    {
    }

    /**
     * Called for the response to method update()
     *
     * @param Model $model
     * @return Response|array
     */
    abstract protected function updateResponse(Model $model);
}
