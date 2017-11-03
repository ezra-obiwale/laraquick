<?php
namespace Laraquick\Controllers\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;

use DB;

/**
 * A collection of methods to assist in quick controller generation for crud actions
 *
 */
trait Crud
{

    use Respond;

    /**
     * Validator instance
     *
     * @var Illuminate\Support\Facades\Validator
     */
    protected $validator;

    /**
     * Should return a static instance of the target model class or
     * the fully qualified name of the target modal class
     *
     * @return mixed
     */
    abstract protected function model();

    /**
     * Should return the validation rules for when using @see store() and @see update().
     *
     * @param array $data The data being validated
     * @param mixed $id Id of the model being updated, if such were the case
     * @return array
     */
    abstract protected function validationRules(array $data, $id = null);

    /**
     * Called for the response to method index()
     *
     * @param array $data
     * @return Response|array
     */
    abstract protected function indexResponse(array $data);

    /**
     * Called for the response to method store()
     *
     * @param Model $data
     * @return Response|array
     */
    abstract protected function storeResponse(Model $data);

    /**
     * Called for the response to method show()
     *
     * @param Model $data
     * @return Response|array
     */
    abstract protected function showResponse(Model $data);

    /**
     * Called for the response to method update()
     *
     * @param Model $data
     * @return Response|array
     */
    abstract protected function updateResponse(Model $data);

    /**
     * Called for the response to method delete()
     *
     * @param Model $data
     * @return Response|array
     */
    abstract protected function deleteResponse(Model $data);

    /**
     * Called for the response to method deleteMany()
     *
     * @param integer $deletedCount
     * @return Response|array
     */
    abstract protected function deleteManyResponse($deleteCount);

    /**
     * Indicates whether validation should be strict and throw errors if unwanted
     * values exists
     *
     * @return boolean
     */
    protected function strictValidation()
    {
        return false;
    }

    /**
     * Checks the request data against validation rules
     *
     * @param array $data
     * @param array $rules
     * @param boolean $ignoreStrict Indicates whether to ignore strict validation
     * @return void
     */
    protected function checkRequestData(array $data, array $rules, $ignoreStrict = false)
    {
        $this->validator = Validator::make($data, $rules);
        if ($this->validator->fails())
            return $this->validationError($this->validator->errors());

        if (!$ignoreStrict && $this->strictValidation()) {
            $left_overs = collection($data)->except(array_keys($rules));
            if ($left_overs->count())
                return $this->error('Too many parameters', null, 406);
        }
    }

    /**
     * The model to use in the index method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function indexModel()
    {
        return $this->model();
    }

    /**
     * The model to use in the index method when @see searchQueryParam() exists in the `GET` query.
     * 
     * It should return the model after the query conditions have been implemented.
     * Defaults to @see model()
     *
     * @return mixed
     */
    protected function searchModel($query)
    {
        return $this->indexModel();
    }

    /**
     * The `GET` parameter that would hold the search query
     *
     * @return string
     */
    protected function searchQueryParam()
    {
        return 'query';
    }

    /**
     * The `GET` parameter that would hold the search query
     *
     * @return string
     */
    protected function sortParam()
    {
        return 'sort';
    }

    /**
     * Sets the default pagination length
     *
     * @return integer
     */
    protected function defaultPaginationLength()
    {
        return 15;
    }

    /**
     * The model to use in the store method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function storeModel()
    {
        return $this->model();
    }

    /**
     * The model to use in the update method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function updateModel()
    {
        return $this->model();
    }

    /**
     * The model to use in the show method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function showModel()
    {
        return $this->model();
    }

    /**
     * The model to use in the delete method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function deleteModel()
    {
        return $this->model();
    }

    /**
     * Called before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeIndexResponse(&$data)
    {
    }

    /**
     * Called after validation but before create method is called
     *
     * @param array $data
     * @return mixed The response to send or null
     */
    protected function beforeCreate(array &$data)
    {
    }

    /**
     * Called when an error occurs in a create operation
     *
     * @return void
     */
    protected function rollbackCreate()
    {
    }

    /**
     * Called on success but before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeCreateResponse(Model &$data)
    {
    }

    /**
     * Called on success but before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeCreateManyResponse(Model &$data)
    {
    }

    /**
     * Called when the model is found but before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeShowResponse(Model &$data)
    {
    }

    /**
     * Called after validation but before update method is called
     *
     * @param array $data
     * @return mixed The response to send or null
     */
    protected function beforeUpdate(array &$data)
    {
    }

    /**
     * Called when an error occurs in a update operation
     *
     * @return void
     */
    protected function rollbackUpdate()
    {
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
     * Called when the model has been found but before deleting
     *
     * @param mixed $data
     * @return void
     */
    protected function beforeDelete(Model &$data)
    {
    }

    /**
     * Called when an error occurs in a delete operation
     *
     * @return void
     */
    protected function rollbackDelete()
    {
    }

    /**
     * Called on success but before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeDeleteResponse(Model &$data)
    {
    }

    /**
     * Called when the model has been found but before deleting
     *
     * @param mixed $data
     * @return void
     */
    protected function beforeDeleteMany(array &$data)
    {
    }

    /**
     * Called on success but before sending the response
     *
     * @param integer $deleteCount
     * @return mixed The response to send or null
     */
    protected function beforeDeleteManyResponse($deletedCount)
    {
    }

    /**
     * Applies sort to the given model based on the given string
     *  
     * @param string $string Format is column:direction,column:direction
     * @param string $model
     * @return string
     */
    private function sort($string, $model)
    {
        $isObject = is_object($model);
        foreach (explode(',', $string) as $sorter) {
            $sorter = trim($sorter);
            if (!$sorter) continue;

            $parts = explode(':', $sorter);
            $col = trim($parts[0]);
            $dir = count($parts) > 1
                ? trim($parts[1])
                : 'asc';

            $model = $isObject
                ? $model->orderBy($col, $dir)
                : $model::orderBy($col, $dir);
        }
        return $model;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($query = request($this->searchQueryParam())) {
            $model = $this->searchModel($query);
        }
        else {
            $model = $this->indexModel();
        }

        if ($sorter = request($this->sortParam())) {
            $model = $this->sort($sorter, $model);
        }

        $length = request('length', $this->defaultPaginationLength());
        if ($length == 'all')
            $data = is_object($model)
            ? $model->all()
            : $model::all();
        else
            $data = is_object($model)
            ? $model->simplePaginate($length)
            : $model::simplePaginate($length);
        if ($resp = $this->beforeIndexResponse($data)) return $resp;
        return $this->indexResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if ($resp = $this->checkRequestData($data, $this->validationRules($data)))
            return $resp;

        $model = $this->storeModel();

        DB::beginTransaction();
        if ($resp = $this->beforeCreate($data)) return $resp;

        $data = is_object($model)
            ? $model->create($data)
            : $model::create($data);

        if (!$data) {
            $this->rollbackCreate();
            DB::rollback();
            return $this->createFailedError();
        }

        if ($resp = $this->beforeCreateResponse($data)) {
            return $resp;
        }

        DB::commit();
        return $this->storeResponse($data);
    }

    /**
     * Fetch a resource
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $model = $this->showModel();
        $item = is_object($model)
            ? $model->find($id)
            : $model::find($id);

        if (!$item) return $this->notFoundError();

        if ($resp = $this->beforeShowResponse($item)) return $resp;
        return $this->showResponse($item);
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
        if ($resp = $this->checkRequestData($data, $this->validationRules($data, $id)))
            return $resp;

        $model = $this->updateModel();

        $item = is_object($model)
            ? $model->find($id)
            : $model::find($id);
        if (!$item) return $this->notFoundError();

        DB::beginTransaction();
        if ($resp = $this->beforeUpdate($data)) return $resp;

        $result = $item->update($data);

        if (!$result) {
            $this->rollbackUpdate();
            DB::rollback();
            return $this->updateFailedError();
        }

        if ($resp = $this->beforeUpdateResponse($item)) {
            return $resp;
        }
        DB::commit();
        return $this->updateResponse($item);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $model = $this->model();
        $item = is_object($model)
            ? $model->find($id)
            : $model::find($id);

        if (!$item) return $this->notFoundError();

        DB::beginTransaction();
        $this->beforeDelete($item);
        $result = $item->delete();

        if (!$result) {
            $this->rollbackDelete();
            DB::rollback();
            return $this->deleteFailedError();
        }

        if ($resp = $this->beforeDeleteResponse($item)) {
            return $resp;
        }
        DB::commit();
        return $this->deleteResponse($item);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroyMany(Request $request)
    {
        $model = $this->model();
        $data = $request->all();
        if (!array_key_exists('ids', $data)) {
            throw new \Exception('Ids not found');
        }
        DB::beginTransaction();
        $this->beforeDeleteMany($data);
        $result = is_object($model)
            ? $model->whereIn('id', $data['ids'])->delete()
            : $model::whereIn('id', $data['ids'])->delete();

        if (!$result) {
            $this->rollbackDelete();
            DB::rollback();
            return $this->deleteFailedError();
        }

        if ($resp = $this->beforeDeleteManyResponse($result)) {
            return $resp;
        }
        DB::commit();
        return $this->deleteManyResponse($result);
    }
}
