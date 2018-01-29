<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Http\Response;

/**
 * Methods for listing resources
 *
 */
trait Index
{
	
    /**
     * Create a model not set error response
     *
     * @return Response
     */
	abstract protected function modelNotSetError();
    /**
     * The model to use in the index method.
     *
     * @return mixed
     */
    abstract protected function indexModel();
    
    /**
     * The model to use in the index method when @see searchQueryParam() exists in the `GET` query.
     * 
     * It should return the model after the query conditions have been implemented.
     *
     * @return mixed
     */
    abstract protected function searchModel($query);

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
			if (!$model) {
				logger()->error('Search model undefined');
			}
        }
        else {
            $model = $this->indexModel();
			if (!$model) {
				logger()->error('Index model undefined');
			}
        }
		if (!$model) {
			return $this->modelNotSetError();
		}

        if ($sorter = request($this->sortParam())) {
            $model = $this->sort($sorter, $model);
        }

        $length = request('length', $this->defaultPaginationLength());
        if ($length == 'all')
            $data = is_object($model)
            ? $model->get()
            : $model::all();
        else
            $data = is_object($model)
            ? $model->simplePaginate($length)
            : $model::simplePaginate($length);
        if ($resp = $this->beforeIndexResponse($data)) return $resp;
        return $this->indexResponse($data);
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
     * Called for the response to method index()
     *
     * @param array $data
     * @return Response|array
     */
    abstract protected function indexResponse(array $data);

    // ------------------ TRASHED INDEX ---------------------
    
    /**
     * Display a listing of all soft deleted resources.
     * @return Response
     */
    public function trashedIndex()
    {
        if ($query = request($this->searchQueryParam())) {
            $model = $this->searchModel($query);
			if (!$model) {
				logger()->error('Search model undefined');
			}
        }
        else {
            $model = $this->indexModel();
			if (!$model) {
				logger()->error('Index model undefined');
			}
        }
		if (!$model) {
			return $this->modelNotSetError();
		}

        if ($sorter = request($this->sortParam())) {
            $model = $this->sort($sorter, $model);
        }

        $length = request('length', $this->defaultPaginationLength());
        if ($length == 'all')
            $data = is_object($model)
            ? $model->onlyTrashed()->all()
            : $model::onlyTrashed()->all();
        else
            $data = is_object($model)
            ? $model->simplePaginate($length)
            : $model::simplePaginate($length);
        if ($resp = $this->beforeIndexResponse($data)) return $resp;
        return $this->indexResponse($data);
    }

    /**
     * Called before sending the response
     *
     * @param mixed $data
     * @return mixed The response to send or null
     */
    protected function beforeTrashedIndexResponse(&$data)
    {
    }

    /**
     * Called for the response to method trashedIndex(). Defaults to @see indexResponse().
     *
     * @param array $data
     * @return Response|array
     */
     protected function trashedIndexResponse(array $data) 
     {
         return $this->indexResponse();
     }

}