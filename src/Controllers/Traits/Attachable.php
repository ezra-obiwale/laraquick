<?php

namespace Laraquick\Controllers\Traits;

use Illuminate\Http\Response;
use Log;

/**
 * Shortcuts for many-to-many attachments
 * 
 * @see https://laravel.com/docs/5.5/eloquent-relationships#updating-many-to-many-relationships
 */
trait Attachable
{

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
     * Fetches a paginated list of related items
     *
     * @param mixed $id
     * @param string $relation
     * @return Response
     */
    public function attached($id, $relation) {
        $model = $this->attachModel();
        $model = is_object($model) 
            ? $model->findOrFail($id)
            : $model::findOrFail($id);
        $list = $model->$relation()->simplePaginate();
        return $this->paginatedList($list->toArray());
    }

    /**
     * Attaches a list of items to the object at the given id
     *
     * @param int $id
     * @param string $relation
     * @param string $paramKey
     * @return Response
    */
    public function attach($id, $relation, $paramKey = null)
    {
        $paramKey = $paramKey ?: $relation;
        if (!$this->validate(request(), [
            $paramKey => 'required'
        ]))
            return $this->error($this->validationErrorMessage(), $this->validator->errors());
        $model = $this->attachModel();
        $model = is_object($model)
            ? $model->find($id)
            : $model::find($id);
        if (!$model) return $this->notFoundError();
        try {
            $items = request()->input($paramKey);
            $model->$relation()->syncWithoutDetaching($items);
            return response()->json([
                'status' => 'ok'
            ]);
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
    public function detach($id, $relation, $paramKey = null)
    {
        $paramKey = $paramKey ?: $relation;
        if (!$this->validate(request(), [
            $paramKey => 'required'
        ]))
            return $this->error($this->validationErrorMessage(), $this->validator->errors());
        $model = $this->detachModel();
        $model = is_object($model)
            ? $model->find($id)
            : $model::find($id);
        if (!$model) return $this->notFoundError();
        try {
            $items = request()->input($paramKey);
            $model->$relation()->detach($items);
            return response()->json([
                'status' => 'ok'
            ]);
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
    public function sync($id, $relation, $paramKey = null)
    {
        $paramKey = $paramKey ?: $relation;
        if (!$this->validate(request(), [
            $paramKey => 'required'
        ]))
            return $this->error($this->validationErrorMessage(), $this->validator->errors());
        $model = $this->syncModel();
        $model = is_object($model)
            ? $model->find($id)
            : $model::find($id);
        if (!$model) return $this->notFoundError();
        try {
            $items = request()->input($paramKey);
            $resp = $model->$relation()->sync($items);
            $resp['added'] = $resp['attached'];
            $resp['removed'] = $resp['detached'];
            unset($resp['attached']);
            unset($resp['detached']);
            return response()->json([
                'status' => 'ok'
            ]);
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error('Something went wrong. Are you sure the items exists?');
        }
    }

}
