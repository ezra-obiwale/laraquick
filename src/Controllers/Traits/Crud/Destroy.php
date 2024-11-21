<?php

namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Laraquick\Helpers\DB;
use Exception;

/**
 * Methods for destorying a single resource
 *
 */
trait Destroy
{
    /**
     * Create a 404 not found error response
     *
     * @param string $message The message to send with a 404 status code
     * @return Response
     */
    abstract protected function notFoundError($message = "Resource not found");

    /**
     * Create a model not set error response
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    abstract protected function modelNotSetError($message = 'Model not set for action');

    /**
     * Called when a delete action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    abstract protected function destroyFailedError($message = 'Delete failed');

    /**
     * Called when a restore deleted action fails
     *
     * @param string $message The message to send with a 500 status code
     * @return Response
     */
    abstract protected function restoreFailedError($message = 'Restoration failed');

    /**
     * The model to use in the delete method.
     *
     * @return mixed
     */
    abstract protected function destroyModel();

    /**
     * Called when the model has been found but before deleting
     *
     * @param Model $model
     * @return void
     */
    protected function beforeDestroy(Model $model) {}

    /**
     * Called when an error occurs in a delete operation
     *
     * @param Model $model The model to be deleted
     * @return void
     */
    protected function rollbackDestroy(Model $model) {}

    /**
     * Delete
     *
     * Deletes the specified item.
     *
     * @urlParam id integer|string required The id of the item to delete.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $model = $this->destroyModel();

        if (!$model) {
            return $this->modelNotSetError('Destroy model undefined');
        }

        $item = $this->find($model, $id);

        if (!$item) {
            return $this->notFoundError();
        }

        if (method_exists($this, 'authorizeMethod')) {
            $this->authorizeMethod('destroy', [$model, $item]);
        }

        return DB::transaction(
            function () use (&$item) {
                if ($resp = $this->beforeDestroy($item)) {
                    return $resp;
                }

                $result = $item->delete();

                if (!$result) {
                    throw new Exception('Delete method returned falsable');
                }

                if ($resp = $this->beforeDestroyResponse($item)) {
                    return $resp;
                }

                return $this->destroyResponse($item);
            },
            function ($ex) use ($item) {
                $message = $ex->getMessage();

                try {
                    $this->rollbackDestroy($item);
                } catch (Exception $ex) {
                    $message = $ex->getMessage();
                }

                return $this->destroyFailedError($message);
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param Model $model
     * @return mixed The response to send or null
     */
    protected function beforeDestroyResponse(Model $model) {}

    /**
     * Called for the response to method @see destroy()
     *
     * @param Model $model
     * @return Response|array
     */
    abstract protected function destroyResponse(Model $model);

    // ------------------ DESTROY MANY ---------------------------------

    /**
     * Called when the model has been found but before deleting
     *
     * @param array $data
     * @return void
     */
    protected function beforeDestroyMany(array &$data) {}

    /**
     * Called when an error occurs in a delete operation
     * @param array $ids The id of the models to be deleted
     * @return void
     */
    protected function rollbackDestroyMany(array $ids) {}
    /**
     * Delete many
     *
     * Deletes the specified items.
     *
     * @bodyParams ids string[]|integer[] required Array of ids of items to delete.
     *
     * @return Response
     */
    public function destroyMany(Request $request)
    {
        $model = $this->destroyModel();

        if (!$model) {
            return $this->modelNotSetError('Destroy model undefined');
        }

        if (method_exists($this, 'authorizeMethod')) {
            $this->authorizeMethod('destroyMany', [$model]);
        }

        $data = $request->all();

        if (!array_key_exists('ids', $data)) {
            throw new Exception('Ids not found');
        }

        return DB::transaction(
            function () use (&$data, $model) {
                if ($resp = $this->beforeDestroyMany($data)) {
                    return $resp;
                }

                $result = is_object($model)
                    ? $model->whereIn('id', $data['ids'])->delete()
                    : $model::whereIn('id', $data['ids'])->delete();

                if (!$result) {
                    throw new Exception('Delete failed');
                }

                if ($resp = $this->beforeDestroyManyResponse($result, $data['ids'])) {
                    return $resp;
                }

                return $this->destroyManyResponse($result);
            },
            function ($ex) use ($data) {
                $message = $ex->getMessage();

                try {
                    $this->rollbackDestroyMany($data['ids']);
                } catch (\Exception $ex) {
                    $message = $ex->getMessage();
                }

                return $this->destroyFailedError($message);
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param integer $deleteCount
     * @return mixed The response to send or null
     */
    protected function beforeDestroyManyResponse($deletedCount) {}

    /**
     * Called for the response to method destroyMany()
     *
     * @param integer $deletedCount
     * @return Response|array
     */
    abstract protected function destroyManyResponse($deleteCount);

    // -------------------- FORCE DESTROY ------------------------------

    /**
     * Called when the model has been found but before force deleting
     *
     * @param Model $model
     * @return void
     */
    protected function beforeForceDestroy(Model $model) {}

    /**
     * Called when an error occurs in a force delete operation
     *
     * @param Model $model
     * @return void
     */
    protected function rollbackForceDestroy(Model $model) {}

    /**
     * Delete (permanently)
     *
     * Permanently deletes the specified item.
     *
     * @urlParam id integer|string required The id of the item to delete.
     *
     *  @param int $id
     * @return Response
     */
    public function forceDestroy($id)
    {
        $model = $this->destroyModel();

        if (!$model) {
            return $this->modelNotSetError('Destroy model undefined');
        }

        $item = $this->find($model, $id);

        if (!$item) {
            return $this->notFoundError();
        }

        if (method_exists($this, 'authorizeMethod')) {
            $this->authorizeMethod('forceDestroy', [$model, $item]);
        }

        return DB::transaction(
            function () use (&$item) {
                if ($resp = $this->beforeForceDestroy($item)) {
                    return $resp;
                }

                $result = $item->forceDelete();

                if (!$result) {
                    throw new Exception('Force delete failed');
                }

                if ($resp = $this->beforeForceDestroyResponse($item)) {
                    return $resp;
                }

                return $this->forceDestroyResponse($item);
            },
            function ($ex) use ($item) {
                $message = $ex->getMessage();

                try {
                    $this->rollbackForceDestroy($item);
                } catch (Exception $ex) {
                    $message = $ex->getMessage();
                }

                return $this->destroyFailedError($message);
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param mixed $model
     * @return mixed The response to send or null
     */
    protected function beforeForceDestroyResponse(Model $model) {}

    /**
     * Called for the response to method @see forceDestroy()
     *
     * @param Model $model
     * @return Response|array
     */
    protected function forceDestroyResponse(Model $model)
    {
        return $this->destroyResponse($model);
    }

    // ---------------- RESTORE DESTROYED -------------------------

    /**
     * Called when the model has been found but before restoring a deleted resource
     *
     * @param Model $model
     * @return void
     */
    protected function beforeRestoreDestroyed(Model $model) {}

    /**
     * Called when an error occurs in a restore destroyed operation
     *
     * @param Model $model
     * @return void
     */
    protected function rollbackRestoreDestroyed(Model $model) {}

    /**
     * Restore
     *
     * Undeletes the specified item which was previously marked as deleted.
     *
     * @urlParam id integer|string required The id of the item to undelete.
     *
     * @param int $id
     * @return Response
     */
    public function restoreDestroyed($id)
    {
        $model = $this->destroyModel();

        if (!$model) {
            return $this->modelNotSetError('Destroy model undefined');
        }

        $item = $this->find($model, $id);

        if (!$item) {
            return $this->notFoundError();
        }

        return DB::transaction(
            function () use (&$item) {
                if ($resp = $this->beforeRestoreDestroyed($item)) {
                    return $resp;
                }

                $result = $item->restore();

                if (!$result) {
                    throw new Exception('Restore failed');
                }

                if ($resp = $this->beforeRestoreDestroyedResponse($item)) {
                    return $resp;
                }

                return $this->restoreDestroyedResponse($item);
            },
            function ($ex) use ($item) {
                $message = $ex->getMessage();

                try {
                    $this->rollbackRestoreDestroyed($item);
                } catch (Exception $ex) {
                    $message = $ex->getMessage();
                }

                return $this->restoreFailedError($message);
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param Model $model
     * @return mixed The response to send or null
     */
    protected function beforeRestoreDestroyedResponse(Model $model) {}

    /**
     * Called for the response to method @see restoreDestroyed()
     *
     * @param Model $model
     * @return Response|array
     */
    abstract protected function restoreDestroyedResponse(Model $model);
}
