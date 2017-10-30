<?php

namespace Laraquick\Controllers\Traits;

use Illuminate\Http\Response;
use Log;

trait Attachable
{

    use Respond;

    /**
     * The model to use in the attach method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function attachModel() {
        return $this->model();
    }

    /**
     * The model to use in the detach method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function detachModel() {
        return $this->model();
    }

    /**
     * The model to use in the sync method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function syncModel() {
        return $this->model();
    }

    /**
     * Attaches a list of items to the object at the given id
     *
     * @param int $id
     * @param string $relation
     * @param string $paramKey
     * @return Response
    */
    public function attach($id, $relation, $paramKey = 'items')
    {
        if (!$this->validate(request(), [
            $paramKey => 'required|array'
        ]))
            return $this->error($this->validationErrorMessage(), $this->validator->errors());
        $model = $this->attachModel();
        $group = is_object($model)
            ? $model->find($id)
            : $model::find($id);
        if (!$group) return $this->notFound();
        try {
            $items = request()->input($paramKey);
            $group->$relation()->attach($items);
            return response()->json($group->$relation);
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error('Something went wrong. Are you sure the items exists?');
        }
    }

    /**
     * Detaches a list of items from the object at the given id
     *
     * @param int $id
     * @param string $relation
     * @param string $paramKey
     * @return Response
    */
    public function detach($id, $relation, $paramKey = 'items')
    {
        if (!$this->validate(request(), [
            $paramKey => 'required|array'
        ]))
            return $this->error($this->validationErrorMessage(), $this->validator->errors());
        $model = $this->detachModel();
        $group = is_object($model)
            ? $model->find($id)
            : $model::find($id);
        if (!$group) return $this->notFound();
        try {
            $items = request()->input($paramKey);
            $group->$relation()->detach($items);
            return response()->json($group->$relation);
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error('Something went wrong. Are you sure the items exists?');
        }
    }

    /**
     * Syncs a list of items with the existing attached items on the object at the given id
     *
     * @param int $id
     * @param string $relation
     * @param string $paramKey
     * @return Response
    */
    public function sync($id, $relation, $paramKey = 'items')
    {
        if (!$this->validate(request(), [
            $paramKey => 'required|array'
        ]))
            return $this->error($this->validationErrorMessage(), $this->validator->errors());
        $model = $this->syncModel();
        $group = is_object($model)
            ? $model->find($id)
            : $model::find($id);
        if (!$group) return $this->notFound();
        try {
            $items = request()->input($paramKey);
            $resp = $group->$relation()->sync($items);
            $resp['added'] = $resp['attached'];
            $resp['removed'] = $resp['detached'];
            unset($resp['attached']);
            unset($resp['detached']);
            return response()->json($group->$relation);
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error('Something went wrong. Are you sure the items exists?');
        }
    }

}
