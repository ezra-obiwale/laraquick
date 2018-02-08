<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;

/**
 * Methods for fetching a resource
 *
 */
trait Show
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
     * The model to use in the show method.
     *
     * @return mixed
     */
    abstract protected function showModel();

    /**
     * Fetch a resource
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $model = $this->showModel();
		if (!$model) {
			logger()->error('Show model undefined');
			return $this->modelNotSetError();
		}
        $item = is_object($model)
            ? $model->find($id)
            : $model::find($id);

        if (!$item) return $this->notFoundError();

        if ($resp = $this->beforeShowResponse($item)) return $resp;
        return $this->showResponse($item);
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
     * Called for the response to method show()
     *
     * @param Model $data
     * @return Response|array
     */
    abstract protected function showResponse(Model $data);
    
    
}