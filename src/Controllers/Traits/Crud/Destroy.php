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
     * @return Response
     */
    abstract protected function notFoundError($message = null);
    
    /**
     * Create a model not set error response
     *
     * @return Response
     */
    abstract protected function modelNotSetError();

    /**
     * Called when a delete action fails
     *
     * @return Response
     */
    abstract protected function destroyFailedError();
    
    /**
     * The model to use in the delete method.
     *
     * @return mixed
     */
    abstract protected function destroyModel();
    
    /**
     * Called when a restore deleted action fails
     *
     * @return Response
     */
    abstract protected function restoreFailedError();

    /**
     * Called when the model has been found but before deleting
     *
     * @param mixed $data
     * @return void
     */
    protected function beforeDestroy(Model &$data)
    {
    }

    /**
     * Called when an error occurs in a delete operation
     *
     * @param Model $model The model to be deleted
     * @return void
     */
    protected function rollbackDestroy(Model $model)
    {
    }

    /**
     * Deletes the specified resource.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $model = $this->destroyModel();
        if (!$model) {
            logger()->error('Destroy model undefined');
            return $this->modelNotSetError();
        }
        $item = is_object($model)
            ? $model->find($id)
            : $model::find($id);

        if (!$item) {
            return $this->notFoundError();
        }

        return DB::transaction(
            function () use (&$item) {
                $this->beforeDestroy($item);
                $result = $item->delete();
    
                if (!$result) {
                    throw new Exception('Delete method returned falsable', 500);
                }
                
                if ($resp = $this->beforeDestroyResponse($item)) {
                    return $resp;
                }
                return $this->destroyResponse($item);
            },
            function ($ex) use ($item) {
                logger()->error('Delete: ' . $ex->getMessage());
                try {
                    $this->rollbackDestroy($item);
                } catch (Exception $ex) {
                    logger()->error('Rollback: ' . $ex->getMessage());
                }
                return $this->destroyFailedError();
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeDestroyResponse(Model &$data)
    {
    }

    /**
     * Called for the response to method @see destroy()
     *
     * @param Model $data
     * @return Response|array
     */
    abstract protected function destroyResponse(Model $data);
    
    // ------------------ DESTROY MANY ---------------------------------

    /**
     * Called when the model has been found but before deleting
     *
     * @param mixed $data
     * @return void
     */
    protected function beforeDestroyMany(array &$data)
    {
    }

    /**
     * Called when an error occurs in a delete operation
     * @param array $ids The id of the models to be deleted
     * @return void
     */
    protected function rollbackDestroyMany(array $ids)
    {
    }
    /**
     * Deletes the specified resources.
     * @return Response
     */
    public function destroyMany(Request $request)
    {
        $model = $this->destroyModel();
        if (!$model) {
            logger()->error('Destroy model undefined');
            return $this->modelNotSetError();
        }
        $data = $request->all();
        if (!array_key_exists('ids', $data)) {
            throw new Exception('Ids not found');
        }
        return DB::transaction(
            function () use ($data, $model) {
                $this->beforeDestroyMany($data);
        
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
            function ($ex) {
                logger()->error('Rollback: ' . $ex->getMessage());
                $this->rollbackDestroyMany($data['ids']);
                return $this->destroyFailedError();
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param integer $deleteCount
     * @return mixed The response to send or null
     */
    protected function beforeDestroyManyResponse($deletedCount)
    {
    }
    
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
    protected function beforeForceDestroy(Model $model)
    {
    }

    /**
     * Called when an error occurs in a force delete operation
     *
     * @param Model $model
     * @return void
     */
    protected function rollbackForceDestroy(Model $model)
    {
    }

    /**
     * Force deletes the specified resource.
     * @param int $id
     * @return Response
     */
    public function forceDestroy($id)
    {
        $model = $this->destroyModel();
        if (!$model) {
            logger()->error('Destroy model undefined');
            return $this->modelNotSetError();
        }
        $item = is_object($model)
            ? $model->find($id)
            : $model::find($id);

        if (!$item) {
            return $this->notFoundError();
        }

        return DB::transaction(
            function () use (&$item) {
                $this->beforeForceDestroy($item);
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
                try {
                    $this->rollbackForceDestroy($item);
                } catch (Exception $ex) {
                    logger()->error('Rollback: ' . $ex->getMessage());
                }
                return $this->destroyFailedError();
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param mixed $model
     * @return mixed The response to send or null
     */
    protected function beforeForceDestroyResponse(Model $model)
    {
    }
    
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
     * @param Model $data
     * @return void
     */
    protected function beforeRestoreDestroyed(Model $data)
    {
    }

    /**
     * Called when an error occurs in a restore destroyed operation
     *
     * @param Model $model
     * @return void
     */
    protected function rollbackRestoreDestroyed(Model $model)
    {
    }

    public function restoreDestroyed($id)
    {
        $model = $this->destroyModel();
        if (!$model) {
            logger()->error('Destroy model undefined');
            return $this->modelNotSetError();
        }
        $item = is_object($model)
            ? $model->find($id)
            : $model::find($id);

        if (!$item) {
            return $this->notFoundError();
        }

        return DB::transaction(
            function () use (&$item) {
                $this->beforeRestoreDestroyed($item);
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
                try {
                    $this->rollbackRestoreDestroyed($item);
                } catch (Exception $ex) {
                    logger()->error('Rollback: ' . $ex->getMessage());
                }
                DB::rollback();
                return $this->restoreFailedError();
            }
        );
    }

    /**
     * Called on success but before sending the response
     *
     * @param Model $model
     * @return mixed The response to send or null
     */
    protected function beforeRestoreDestroyedResponse(Model $model)
    {
    }

    /**
     * Called for the response to method @see restoreDestroyed()
     *
     * @param Model $data
     * @return Response|array
     */
    abstract protected function restoreDestroyedResponse(Model $data);
}
