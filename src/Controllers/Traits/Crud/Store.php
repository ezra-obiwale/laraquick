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
     * @return void
     */
    protected function rollbackStore()
    {
    }

    /**
     * Store a newly stored resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if ($resp = $this->checkRequestData($data, $this->validationRules($data)))
            return $resp;

        $model = $this->storeModel();

        try {
            DB::beginTransaction();
            if ($resp = $this->beforeStore($data)) return $resp;

            $data = is_object($model)
                ? $model->create($data)
                : $model::create($data);

            if (!$data) {
                throw new \Exception(500);
            }
        }
        catch (\Exception $ex) {
            Log::error('Store: ' . $ex->getMessage(), [$data]);
            $this->rollbackStore();
            DB::rollback();
            return $this->storeFailedError();
        }

        if ($resp = $this->beforeStoreResponse($data)) {
            return $resp;
        }

        DB::commit();
        return $this->storeResponse($data);
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