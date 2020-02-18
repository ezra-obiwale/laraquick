<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Laraquick\Helpers\DB;
use Laraquick\Models\Dud;
use Exception;

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
     * The model to use in the storeMany method
     *
     * @return mixed
     */
    protected function storeManyModel()
    {
        return $this->storeModel();
    }

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
     * Save a new resource.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if ($resp = $this->validateRequest()) {
            return $resp;
        }

        $data = $request->only(array_keys($this->validationRules($request->all())));
        $model = $this->storeModel();
        if (!$model) {
            logger()->error('Store model undefined');
            return $this->modelNotSetError();
        }

        $item = null;
        return DB::transaction(
            function () use (&$data, $model, &$item) {
                if ($resp = $this->beforeStore($data)) {
                    return $resp;
                }

                $item = is_object($model)
                    ? $model->create($data)
                    : $model::create($data);

                if (!$item) {
                    throw new Exception('Create method returned falsable');
                }

                if ($resp = $this->beforeStoreResponse($item)) {
                    return $resp;
                }
                return $this->storeResponse($item);
            },
            function ($ex) use ($data, $item) {
                logger()->error('Store: ' . $ex->getMessage(), $data);
                try {
                    $this->rollbackStore($data, @$item ?: new Dud);
                } catch (Exception $ex) {
                    logger()->error('Rollback: ' . $ex->getMessage());
                }
                return $this->storeFailedError();
            }
        );
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

    /**
     * Called after validation but before store method is called
     *
     * @param array $data
     * @return mixed The response to send or null
     */
    protected function beforeStoreMany(array &$data)
    {
    }

    /**
     * Called when an error occurs in a storeMany operation
     *
     * @param array $data The data from the request
     * @param array $models The created model
     * @return void
     */
    protected function rollbackStoreMany(array $data, array $models)
    {
    }

    /**
     * Save many new resources.
     * @param  Request $request
     * @return Response
     */
    public function storeMany(Request $request)
    {
        $data = $request->only(array_keys($this->manyValidationRules($request->all())));
        if ($resp = $this->validateRequest($this->manyValidationRules($data))) {
            return $resp;
        }


        $model = $this->storeModel();
        if (!$model) {
            logger()->error('Store model undefined');
            return $this->modelNotSetError();
        }

        $items = [];
        return DB::transaction(
            function () use (&$data, $model, &$items) {
                if ($resp = $this->beforeStoreMany($data)) {
                    return $resp;
                }

                foreach ($data['many'] as $currentData) {
                    $item = is_object($model)
                        ? $model->create($data)
                        : $model::create($data);

                    if (!$item) {
                        throw new Exception('Create method returned falsable');
                    }
                    $items[] = $item;
                }

                if ($resp = $this->beforeStoreManyResponse($items)) {
                    return $resp;
                }
                return $this->storeManyResponse($items);
            },
            function ($ex) use ($data, $items) {
                logger()->error('Store: ' . $ex->getMessage(), $data);
                try {
                    $this->rollbackStoreMany($data, $items);
                } catch (Exception $ex) {
                    logger()->error('Rollback: ' . $ex->getMessage());
                }
                return $this->storeFailedError();
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param array $data
     * @return mixed The response to send or null
     */
    protected function beforeStoreManyResponse(array $data)
    {
    }

    /**
     * Called for the response to method storeMany()
     *
     * @param array $data
     * @return Response|array
     */
    abstract protected function storeManyResponse(array $data);
}
