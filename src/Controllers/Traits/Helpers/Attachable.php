<?php

namespace Laraquick\Controllers\Traits\Helpers;

use Illuminate\Http\Response;
use Log;
use Illuminate\Database\Eloquent\Model;

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
    protected function attachModel()
    {
        return $this->model();
    }

    /**
     * The model to use in the detach method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function detachModel()
    {
        return $this->model();
    }

    /**
     * The model to use in the sync method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function syncModel()
    {
        return $this->model();
    }

    /**
     * Treats the relation string
     *
     * @param Model $model
     * @param string $relation
     * @return void
     */
    private function treatRelation(Model $model, &$relation)
    {
        if (!method_exists($model, $relation)) {
            // change relation to camel case
            $relation = camel_case(str_replace('-', '_', $relation));
        }
    }

    /**
     * Prepares the items to attach to the model on the given relation
     *
     * @param mixed $items
     * @param Model $model
     * @param string $relation
     * @return void
     */
    protected function prepareAttachItems($items, Model $model, $relation)
    {
        return $items;
    }

    /**
     * Prepares the items to detach to the model on the given relation
     *
     * @param mixed $items
     * @param Model $model
     * @param string $relation
     * @return void
     */
    protected function prepareDetachItems($items, Model $model, $relation)
    {
        return $items;
    }

    /**
     * Prepares the items to sync to the model on the given relation
     *
     * @param mixed $items
     * @param Model $model
     * @param string $relation
     * @return void
     */
    protected function prepareSyncItems($items, Model $model, $relation)
    {
        return $items;
    }

    /**
     * Fetches a paginated list of related items
     *
     * @param mixed $id
     * @param string $relation
     * @return Response
     */
    public function attached($id, $relation)
    {
        $model = $this->attachModel();
        $model = is_object($model)
            ? $model->find($id)
            : $model::find($id);

        if (!$model) {
            return $this->notFoundError();
        }

        $this->treatRelation($model, $relation);
        $list = $model->$relation()->paginate();

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

        $items = $this->validateRequest([
            $paramKey => 'required|array'
        ]);

        $model = $this->attachModel();
        $model = is_object($model)
            ? $model->find($id)
            : $model::find($id);

        if (!$model) {
            return $this->notFoundError();
        }

        try {
            $this->treatRelation($model, $relation);
            $model->$relation()->syncWithoutDetaching($this->prepareAttachItems($items, $model, $relation));

            return $this->success($model->load($relation)->$relation);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->error('Something went wrong. Are you sure the ' . str_replace('_', ' ', $paramKey) . ' exists?');
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

        $items = $this->validateRequest([
            $paramKey => 'required|array'
        ]);

        $model = $this->detachModel();
        $model = is_object($model)
            ? $model->find($id)
            : $model::find($id);

        if (!$model) {
            return $this->notFoundError();
        }

        try {
            $this->treatRelation($model, $relation);
            $_items = $model->$relation()->find($items);
            $model->$relation()->detach($this->prepareDetachItems($items, $model, $relation));

            return $this->success($_items);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->error('Something went wrong. Are you sure the ' . str_replace('_', ' ', $paramKey) . ' exists?');
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

        $items = $this->validateRequest([
            $paramKey => 'required|array'
        ]);

        $model = $this->syncModel();
        $model = is_object($model)
            ? $model->find($id)
            : $model::find($id);

        if (!$model) {
            return $this->notFoundError();
        }

        try {
            $this->treatRelation($model, $relation);

            $resp = $model->$relation()->sync($this->prepareSyncItems($items, $model, $relation));
            $resp['added'] = $resp['attached'];
            $resp['removed'] = $resp['detached'];
            unset($resp['attached']);
            unset($resp['detached']);

            return $this->success($model->load($relation)->$relation);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error('Something went wrong. Are you sure the ' . str_replace('_', ' ', $paramKey) . ' exists?');
        }
    }
}
