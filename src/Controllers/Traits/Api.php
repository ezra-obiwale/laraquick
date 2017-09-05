<?php
namespace Laraquick\Controllers\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * A colletion of methods to assist in quick controller generation
 *
 */
trait Api
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
     * @param boolean $forUpdate Indicates whether the validation should be for update or not
     * @return array
     */
    abstract protected function validationRules($forUpdate = false);

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
     * @return void
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
    protected function beforeCreateResponse(&$data)
    {
    }

    /**
     * Called when the model is found but before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeShowResponse(&$data)
    {
    }

    /**
     * Called after validation but before update method is called
     *
     * @param array $data
     * @return void
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
    protected function beforeUpdateResponse(&$data)
    {
    }

    /**
     * Called when the model has been found but before deleting
     *
     * @param mixed $data
     * @return void
     */
    protected function beforeDelete(&$data)
    {
    }

    /**
     * Called on success but before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeDeleteResponse(&$data)
    {
    }

    /**
     * Validates request with validation rules
     *
     * @param array $data
     * @param array $rules
     * @return void
     */
    protected function isValid(array $data, array $rules)
    {
        $this->validator = Validator::make($data, $rules);
        return !$this->validator->fails();
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $model = $this->indexModel();
        $length = request()->query('length') ? : 50;
        $items = is_object($model)
            ? $model->simplePaginate($length)
            : $model::simplePaginate($length);
        if ($resp = $this->beforeIndexResponse($items)) return $resp;
        $resp = [];
        $items = $items->toArray();
        $resp['data'] = $items['data'];
        unset($items['data']);
        $resp['meta']['pagination'] = $items;
        return $this->success($resp);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (!$this->isValid($request->all(), $this->validationRules()))
            return $this->error($this->validationErrorMessage(), $this->validator->errors());

        $model = $this->storeModel();
        $data = $request->all();

        $this->beforeCreate($data);
        $data = is_object($model)
            ? $model->create($request->all())
            : $model::create($request->all());

        if (!$data) return $this->error($this->createFailedMessage(), null, 500);

        if ($resp = $this->beforeCreateResponse($data)) return $resp;
        return $this->success($data);
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

        if (!$item) return $this->notFound();

        if ($resp = $this->beforeShowResponse($item)) return $resp;
        return $this->success($item);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if (!$this->isValid($request->all(), $this->validationRules(true)))
            return $this->error($this->validationErrorMessage(), $this->validator->errors());
        $model = $this->updateModel();

        $item = is_object($model)
            ? $model->find($id)
            : $model::find($id);
        if (!$item) return $this->notFound();

        $data = $request->all();
        $this->beforeUpdate($data);
        $result = $item->update($data);

        if (!$result) return $this->error($this->updateFailedMessage(), null, 500);

        if ($resp = $this->beforeUpdateResponse($item)) return $resp;
        return $this->success($item);
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

        if (!$item) return $this->notFound();

        $this->beforeDelete($item);
        $result = $item->delete();

        if (!$result) return $this->error($this->deleteFailedMessage(), null, 500);

        if ($resp = $this->beforeDeleteResponse($item)) return $resp;
        return $this->success($item);
    }
}
