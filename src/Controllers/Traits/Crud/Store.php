<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;

use DB;
use Log;

/**
 * Methods for storing a resource
 *
 */
trait Store
{
    
    /**
     * Create a model not set error response
     *
     * @return Response
     */
    abstract protected function modelNotSetError();

    /**
     * The model to use in the store method.
     *
     * @return mixed
     */
    abstract protected function storeModel();

    /**
     * Called when store action fails
     *
     * @return Response
     */
    abstract protected function storeFailedError();

    /**
     * Called after validation but before store method is called
     *
     * @param array $data
     * @return mixed The response to send or null
     */
    protected function beforeStore(array &$data)
    {
    }

    /**
     * Called when an error occurs in a store operation
     *
     * @param array $data The data from the request
     * @param Model $model The created model
     * @return void
     */
    protected function rollbackStore(array $data, Model $model)
    {
    }

    /**
     * Store a newly stored resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if ($resp = $this->validateRequest()) {
            return $resp;
        }

        $data = $request->all();
        $model = $this->storeModel();
        if (!$model) {
            logger()->error('Store model undefined');
            return $this->modelNotSetError();
        }

        try {
            DB::beginTransaction();
            if ($resp = $this->beforeStore($data)) {
                return $resp;
            }

            $item = is_object($model)
                ? $model->create($data)
                : $model::create($data);

            if (!$item) {
                throw new \Exception('Create method returned falsable', null, 500);
            }

            if ($resp = $this->beforeStoreResponse($item)) {
                return $resp;
            }
        } catch (\Exception $ex) {
            Log::error('Store: ' . $ex->getMessage(), $data);
            try {
                $this->rollbackStore($data, $item);
            }
            catch (\Exception $ex) {
                Log::error('Rollback: ' . $ex->getMessage());
            }
            DB::rollback();
            return $this->storeFailedError();
        }

        DB::commit();
        return $this->storeResponse($item);
    }

    /**
     * Called on success but before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeStoreResponse(Model &$data)
    {
    }

    /**
     * Called for the response to method store()
     *
     * @param Model $data
     * @return Response|array
     */
    abstract protected function storeResponse(Model $data);
}
