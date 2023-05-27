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
        'appends' => [],
        'fields' => [],
        'filters' => [],
        'includes' => [],
        'sorts' => [],
    ];

    /**
     * Create a model not set error response
     *
     * @return Response
     */
    abstract protected function modelNotSetError($message = 'Model not set');

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
    protected function defaultPaginationLength(): int
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
     * Set alowed appends
     *
     * @return array|string
     */
    protected function allowedAppends()
    {
        return $this->allowed['appends'] ?? [];
    }

    /**
     * Set alowed fields
     *
     * @return array|string
     */
    protected function allowedFields()
    {
        return $this->allowed['fields'] ?? [];
    }

    /**
     * Set allowed filters
     *
     * @return array|string
     */
    protected function allowedFilters()
    {
        return $this->allowed['filters'] ?? [];
    }

    /**
     * Set allowed includes
     *
     * @return array|string
     */
    protected function allowedIncludes()
    {
        return $this->allowed['includes'] ?? [];
    }

    /**
     * Set allowed sorts
     *
     * @return array|string
     */
    protected function allowedSorts()
    {
        return $this->allowed['sorts'] ?? [];
    }

    /**
     * Set default sort
     *
     * @return string
     */
    protected function defaultSort()
    {
    }

    private function isValid($param): bool
    {
        return $param && ((is_array($param) && count($param)) || is_string($param));
    }

    private function build($model)
    {
        $createBuilder = function ($builder) use ($model) {
            if ($builder) {
                return $builder;
            }

            return QueryBuilder::for($model);
        };

        $builder = null;

        if ($this->isValid($this->allowedAppends())) {
            $builder = $createBuilder($builder);
            $builder->allowedAppends($this->allowedAppends());
        }

        if ($this->isValid($this->allowedFields())) {
            $builder = $createBuilder($builder);
            $builder->allowedFields($this->allowedFields());
        }

        if ($this->isValid($this->allowedFilters())) {
            $builder = $createBuilder($builder);
            $builder->allowedFilters($this->allowedFilters());
        }

        if ($this->isValid($this->allowedIncludes())) {
            $builder = $createBuilder($builder);
            $builder->allowedIncludes($this->allowedIncludes());
        }

        if ($this->isValid($this->defaultSort())) {
            $builder = $createBuilder($builder);
            $builder->defaultSort($this->defaultSort());
        }

        if ($this->isValid($this->allowedSorts())) {
            $builder = $createBuilder($builder);
            $builder->allowedSorts($this->allowedSorts());
        }

        if (!$builder) {
            $builder = $model;
        }

        return $builder;
    }

    /**
     * List
     *
     * Get a list of items
     *
     * @return Response
     */
    public function index()
    {
        $model = $this->indexModel();

        if (!$model) {
            logger()->error('Index model undefined');

            return $this->modelNotSetError();
        }

        $length = (int) request('length', $this->defaultPaginationLength());

        $model = $this->build($model);

        if ($length === -1) {
            $data = is_object($model) ? $model->get() : $model::all();
        } else {
            $data = is_object($model) ? $model->paginate($length) : $model::paginate($length);
        }

        if ($resp = $this->beforeIndexResponse($data)) {
            return $resp;
        }

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
     *
     * List (deleted)
     *
     * Get a list of items marked as deleted
     *
     * @return Response
     */
    public function trashedIndex()
    {
        $model = $this->indexModel();

        if (!$model) {
            logger()->error('Index model undefined');

            return $this->modelNotSetError();
        }

        $length = (int) request('length', $this->defaultPaginationLength());

        $model = $this->build($model);

        if ($length === -1) {
            $data = is_object($model) ?
                $model->onlyTrashed()->get() :
                $model::onlyTrashed()->all();
        } else {
            $data = is_object($model) ?
                $model->onlyTrashed()->paginate($length) :
                $model::onlyTrashed()->paginate($length);
        }

        if ($resp = $this->beforeTrashedIndexResponse($data)) {
            return $resp;
        }

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
        return $this->indexResponse($data);
    }
}
