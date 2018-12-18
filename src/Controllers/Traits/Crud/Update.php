<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Laraquick\Helpers\DB;

/**
 * Methods for updating a resource
 *
 */
trait Update
{

    /**
     * Create a 404 not found error response
     *
     * @return Response
     */
    abstract protected function notFoundError($message = null);

    /**
     * Error message for when an update action fails
     *
     * @return Response
     */
    abstract protected function updateFailedError();
    
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
     * Update the specified resource in storage.
     * @param  Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        if ($resp = $this->validateRequest($this->validationRules($data, $id), $this->validationMessages($data, $id))) {
            return $resp;
        }

        $model = $this->updateModel();

        $item = is_object($model)
            ? $model->find($id)
            : $model::find($id);
        if (!$item) {
            return $this->notFoundError();
        }

        return DB::transaction(
            function () use (&$data, &$item) {
                if ($resp = $this->beforeUpdate($data, $item)) {
                    return $resp;
                }
    
                $result = $item->update(array_only($data, $item->getFillable()));
    
                if (!$result) {
                    throw new \Exception('Update method returned falsable', null, 500);
                }
    
                if ($resp = $this->beforeUpdateResponse($item)) {
                    return $resp;
                }
                return $this->updateResponse($item);
            },
            function ($ex) use ($data, $item) {
                logger()->error('Update: ' . $ex->getMessage(), $data);
                try {
                    $this->rollbackUpdate($data, $item);
                } catch (\Exception $ex) {
                    logger()->error('Rollback: ' . $ex->getMessage());
                }
                $this->rollbackUpdate($data, $item);
                return $this->updateFailedError();
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeUpdateResponse(Model &$data)
    {
    }

    /**
     * Called for the response to method update()
     *
     * @param Model $data
     * @return Response|array
     */
    abstract protected function updateResponse(Model $data);
}
