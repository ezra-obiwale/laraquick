<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laraquick\Helpers\DB;
use Laraquick\Models\Dud;
use Exception;

/**
 * Methods for storing a resource
 *
 */
trait Store
{
    use Authorize;


    /**
     * Create a model not set error response
     *
     * @return Response
     */
    abstract protected function modelNotSetError($message = 'Model not set for action');

    /**
     * Called when store action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    abstract protected function storeFailedError($message = 'Create failed');

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
        $model = $this->storeModel();

        $this->authorizeMethod('store', [$model]);

        $model = $this->storeModel();

        if (!$model) {
            return $this->modelNotSetError('Store model undefined');
        }

        $data = $this->validateRequest();
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
                $message = $ex->getMessage();

                try {
                    $this->rollbackStore($data, @$item ?: new Dud);
                } catch (Exception $ex) {
                    $message = $ex->getMessage();
                }

                return $this->storeFailedError($message);
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param Model $model
     * @return mixed The response to send or null
     */
    protected function beforeStoreResponse(Model $model)
    {
    }

    /**
     * Called for the response to method store()
     *
     * @param Model $model
     * @return Response|array
     */
    abstract protected function storeResponse(Model $model);

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
        $model = $this->storeModel();

        if (!$model) {
            return $this->modelNotSetError('Store model undefined');
        }

        $this->authorizeMethod('storeMany', [$model]);

        $rules = $this->manyValidationRules($request->all());
        $messages = $this->manyValidationMessages($request->all());

        $ruleKeys = array_keys($rules);

        $data = $this->getManyValues($request->many, $ruleKeys);

        if ($resp = $this->validateRequest($rules, $messages)) {
            return $resp;
        }

        $items = [];

        return DB::transaction(
            function () use (&$data, $model, &$items) {
                if ($resp = $this->beforeStoreMany($data)) {
                    return $resp;
                }

                foreach ($data as $currentData) {
                    $item = is_object($model)
                        ? $model->create($currentData)
                        : $model::create($currentData);

                    if (!$item) {
                        throw new Exception('Create failed');
                    }

                    $items[] = $item;
                }

                if ($resp = $this->beforeStoreManyResponse($items)) {
                    return $resp;
                }

                return $this->storeManyResponse($items);
            },
            function ($ex) use ($data, $items) {
                $message = $ex->getMessage();

                try {
                    $this->rollbackStoreMany($data, $items);
                } catch (Exception $ex) {
                    $message = $ex->getMessage();
                }

                return $this->storeFailedError($message);
            }
        );
    }

    private function getManyValues($many, $ruleKeys)
    {
        $items = [];

        $keysString = str_replace('many.*.', '', join('/', $ruleKeys));
        $ruleKeys = explode('/', $keysString);

        foreach ($many as $item) {
            $items[] = Arr::only($item, $ruleKeys);
        }

        return $items;
    }

    /**
     * Called on success but before sending the response
     *
     * @param array $data
     * @return mixed The response to send or null
     */
    protected function beforeStoreManyResponse(array &$data)
    {
    }

    /**
     * Called for the response to method storeMany()
     *
     * @param array $data
     * @return Response|array
     */
    abstract protected function storeManyResponse(array &$data);
}
