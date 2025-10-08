<?php

namespace Laraquick\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Laraquick\Controllers\Traits\Response\Api as ApiResponse;
use Laraquick\Controllers\Traits\Response\Common;

/**
 * A collection of api methods to assist in quick controller generation
 */
trait Api
{
    use Crud\Crud, ApiResponse, Common;

    protected function modelResource(): string
    {
        return '';
    }

    /**
     * Index method success response
     *
     * @param mixed $data
     * @return JsonResponse
     */
    protected function indexResponse(LengthAwarePaginator | array $data)
    {
        if (!empty($this->modelResource())) {
            if (is_array($data)) {
                foreach ($data as &$item) {
                    $item = app($this->modelResource(), ['resource' => $item]);
                }
            } else {
                $data->getCollection()->transform(fn($item) => app($this->modelResource(), ['resource' => $item]));
            }
        }

        return $this->paginatedList(is_array($data) ? $data : $data->toArray());
    }

    /**
     * StoreMany method success response
     *
     * @param array $data
     * @return JsonResponse
     */
    protected function storeManyResponse(array $data)
    {
        if (!empty($this->modelResource())) {
            foreach ($data as &$item) {
                $item = app($this->modelResource(), ['resource' => $item]);
            }
        }

        return $this->success($data, Response::HTTP_CREATED);
    }

    /**
     * Store method success response
     *
     * @param Model $model
     * @return JsonResponse
     */
    protected function storeResponse(Model $model)
    {
        return $this->success(
            !empty($this->modelResource()) ? app($this->modelResource(), ['resource' => $model]) : $model,
            Response::HTTP_CREATED
        );
    }

    /**
     * Show method success response
     *
     * @param Model $model
     * @return JsonResponse
     */
    protected function showResponse(Model $model)
    {
        return $this->success(
            !empty($this->modelResource()) ? app($this->modelResource(), ['resource' => $model]) : $model,
            Response::HTTP_OK
        );
    }

    /**
     * Update method success response
     *
     * @param Model $model
     * @return JsonResponse
     */
    protected function updateResponse(Model $model)
    {
        return $this->success(
            !empty($this->modelResource()) ? app($this->modelResource(), ['resource' => $model]) : $model,
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Destroy method success response
     *
     * @param Model $model
     * @return JsonResponse
     */
    protected function destroyResponse(Model $model)
    {
        return $this->success(
            !empty($this->modelResource()) ? app($this->modelResource(), ['resource' => $model]) : $model,
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * ForceDestroy method success response
     *
     * @param Model $model
     * @return JsonResponse
     */
    protected function forceDestroyResponse(Model $model)
    {
        return $this->success(
            !empty($this->modelResource()) ? app($this->modelResource(), ['resource' => $model]) : $model,
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * DestroyMany method success response
     *
     * @param int $deletedCount
     * @return JsonResponse
     */
    protected function destroyManyResponse($deletedCount)
    {
        return $this->success("$deletedCount item(s) deleted successfully", Response::HTTP_ACCEPTED);
    }

    /**
     * RestoreDestroyed method success response
     *
     * @param Model $model
     * @return JsonResponse
     */
    protected function restoreDestroyedResponse(Model $model)
    {
        return $this->success(
            !empty($this->modelResource()) ? app($this->modelResource(), ['resource' => $model]) : $model,
            Response::HTTP_ACCEPTED
        );
    }
}
