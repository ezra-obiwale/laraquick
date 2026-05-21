<?php

namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
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
     * @param string $type includes | filters | sorts | appends | fields
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
    protected function defaultSort() {}

    protected function indexPaginate(QueryBuilder $builder, int $length): Paginator | CursorPaginator | CursorPaginator
    {
        return $builder->paginate($length);
    }

    private function isValid($param): bool
    {
        return $param && !empty($param);
    }

    private function build($model): QueryBuilder
    {
        $builder = QueryBuilder::for($model);

        if ($this->isValid($this->allowedAppends())) {
            $builder->allowedAppends($this->allowedAppends());
        }

        if ($this->isValid($this->allowedFields())) {
            $builder->allowedFields($this->allowedFields());
        }

        if ($this->isValid($this->allowedFilters())) {
            $builder->allowedFilters($this->allowedFilters());
        }

        if ($this->isValid($this->allowedIncludes())) {
            $builder->allowedIncludes($this->allowedIncludes());
        }

        if ($this->isValid($this->defaultSort())) {
            $builder->defaultSort($this->defaultSort());
        }

        if ($this->isValid($this->allowedSorts())) {
            $builder->allowedSorts($this->allowedSorts());
        }

        return $builder;
    }

    /**
     * List
     *
     * Get a paginated list of items
     *
     * @queryParam length number This is the number of items to return per page. If not provided, the default is used. Set as "-1" to return all items at once. Example: 15
     * @queryParam page number This is the current page to be items to return. Example: 1
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

        $length = $this->getPaginationLength();

        $model = $this->build($model);

        if ($length === -1) {
            $data = $model->get();
        } else {
            $data = $this->indexPaginate($model, $length);
        }

        if ($resp = $this->beforeIndexResponse($data)) {
            return $resp;
        }

        return $this->indexResponse($data);
    }

    protected function getPaginationLength(): int
    {
        return (int) request('length', $this->defaultPaginationLength());
    }

    /**
     * Called before sending the response
     *
     * @param Paginator|CursorPaginator|Collection $data
     * @return mixed The response to send or null
     */
    protected function beforeIndexResponse(Paginator | CursorPaginator | Collection &$data) {}

    /**
     * Called for the response to method index()
     *
     * @param Paginator|CursorPaginator|Collection $data
     * @return Response|array
     */
    abstract protected function indexResponse(Paginator | CursorPaginator | Collection $data);


    // ------------------ TRASHED INDEX ---------------------

    /**
     *
     * List (deleted)
     *
     * Get a list of items marked as deleted
     *
     * @queryParam length number This is the number of items to return per page. If not provided, the default is used. Set as "-1" to return all items at once. Example: 15
     * @queryParam page number This is the current page to be items to return. Example: 1
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

        $length = $this->getPaginationLength();

        $model = $this->build($model);

        if ($length === -1) {
            $data = $model->onlyTrashed()->get();
        } else {
            $data = $this->indexPaginate($model->onlyTrashed(), $length);
        }

        if ($resp = $this->beforeTrashedIndexResponse($data)) {
            return $resp;
        }

        return $this->trashedIndexResponse($data);
    }

    /**
     * Called before sending the response
     *
     * @param Paginator|CursorPaginator|Collection $data
     * @return mixed The response to send or null
     */
    protected function beforeTrashedIndexResponse(Paginator|CursorPaginator|Collection &$data) {}

    /**
     * Called for the response to method trashedIndex(). Defaults to @see indexResponse().
     *
     * @param Paginator|CursorPaginator|Collection $data
     * @return Response|array
     */
    protected function trashedIndexResponse(Paginator|CursorPaginator|Collection $data)
    {
        return $this->indexResponse($data);
    }
}
