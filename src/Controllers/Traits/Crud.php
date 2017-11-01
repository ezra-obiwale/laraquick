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
     * Should return the validation rules
     *
     * @param array $data The data being validated
     * @param mixed $id Id of the model being updated, if such were the case
     * @return array
     */
    abstract protected function validationRules(array $data, $id = null);

    abstract protected function indexResponse(array $data);
    abstract protected function storeResponse(Model $data);
    abstract protected function showResponse(Model $data);
    abstract protected function updateResponse(Model $data);
    abstract protected function deleteResponse(Model $data);

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
     * Called on success but before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeCreateResponse(Model &$data)
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
     * Called on success but before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeDeleteResponse(Model &$data)
    {
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $model = $this->indexModel();
        $length = request()->query('length') ? : $this->defaultPaginationLength();
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

        if ($resp = $this->beforeCreate($data)) return $resp;

        DB::beginTransaction();
        $data = is_object($model)
            ? $model->create($data)
            : $model::create($data);

        if (!$data) {
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

        if ($resp = $this->beforeUpdate($data)) return $resp;

        DB::beginTransaction();
        $result = $item->update($data);

        if (!$result) {
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

        $this->beforeDelete($item);
        $result = $item->delete();
        
        if (!$result) {
            DB::rollback();
            return $this->deleteFailedError();
        }

        if ($resp = $this->beforeDeleteResponse($item)) {
            return $resp;
        }
        DB::commit();
        return $this->deleteResponse($item);
    }
}
