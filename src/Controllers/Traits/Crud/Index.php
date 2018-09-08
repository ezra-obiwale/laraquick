<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Methods for listing resources
 *
 */
trait Index
{

    protected $allowed = [
        'includes' => [],
        'filters' => [],
        'sorts' => [],
        'appends' => []
    ];
	
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
     * Sets the default pagination length
     *
     * @return integer
     */
    protected function defaultPaginationLength()
    {
        return 15;
    }

    /**
     * Set allowed types
     *
     * @param string $type includes | filters | sorts | appends
     * @param string|array $value
     * @return self
     */
    protected function allowed($type, $value)
    {
        $this->allowed[$type] = is_array($value) ? join(',', $value) : $value;
        return $this;
    }

    /**
     * Set allowed includes
     *
     * @return array|string
     */
    protected function allowedIncludes()
    {
        return [];
    }

    /**
     * Set allowed filters
     *
     * @return array|string
     */
    protected function allowedFilters()
    {
        return [];
    }

    /**
     * Set allowed sorts
     *
     * @return array|string
     */
    protected function allowedSorts()
    {
        return [];
    }

    /**
     * Set alowed appends
     *
     * @return array|string
     */
    protected function allowedAppends()
    {
        return [];
    }

    /**
     * Set default sort
     *
     * @return string
     */
    protected function defaultSort()
    {
        
    }

    private function isValid($param)
    {
        return $param && ((is_array($param) && count($param)) || is_string($param));
    }

    private function validArray($param, $allowedKey = null)
    {
        if ($this->isValid($param)) {
            // convert to string
            if (is_array($param)) {
                $param = join(',', $param);
            }
            // join with allowed array
            if ($allowedKey) {
                $param .= ',' . $this->allowed[$allowedKey];
            }
            // remove all spaces
            $param = str_replace(' ', '', $param);
            // convert to array
            $param = explode(',', $param);
            // return only unique values
            return array_unique($param);
        }
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $model = $this->indexModel();
		if (!$model) {
            logger()->error('Index model undefined');
			return $this->modelNotSetError();
        }
        
        $builder = QueryBuilder::for($model);

        if ($includes = $this->validArray($this->allowedIncludes())) {
            $builder->allowedIncludes($includes);
        }
        if ($filter = $this->validArray($this->allowedFilters())) {
            $builder->allowedFilters($filter);
        }
        if ($defaultSort = $this->validArray($this->defaultSort())) {
            $builder->defaultSort($defaultSort);
        }

        if ($sort = $this->validArray($this->allowedSorts())) {
            $builder->allowedSorts($sort);
        }

        $length = request('length', $this->defaultPaginationLength());
        if ($length == 'all') {
            $data = $builder->get();
        }
        else {
            $data = $builder->simplePaginate($length);
        }
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
        $model = $this->indexModel();
		if (!$model) {
            logger()->error('Index model undefined');
			return $this->modelNotSetError();
        }
        
        $builder = QueryBuilder::for($model);

        if ($includes = $this->validArray($this->allowedIncludes())) {
            $builder->allowedIncludes($includes);
        }
        if ($filter = $this->validArray($this->allowedFilters())) {
            $builder->allowedFilters($filter);
        }
        if ($defaultSort = $this->validArray($this->defaultSort())) {
            $builder->defaultSort($defaultSort);
        }

        if ($sort = $this->validArray($this->allowedSorts())) {
            $builder->allowedSorts($sort);
        }

        $length = request('length', $this->defaultPaginationLength());
        if ($length == 'all') {
            $data = $builder->onlyTrashed()->get();
        }
        else {
            $data = $builder->onlyTrashed()->simplePaginate($length);
        }
        if ($resp = $this->beforeTrashedIndexResponse($data)) return $resp;
        return $this->trashedIndexResponse($data);
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