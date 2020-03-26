<?php
namespace Laraquick\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Laraquick\Controllers\Traits\Response\Api as ApiResponse;
use Laraquick\Controllers\Traits\Response\Common;

/**
 * A collection of api methods to assist in quick controller generation
 */
trait Api
{
    use Crud\Crud, ApiResponse, Common;

    /**
     * Index method success response
     *
     * @param mixed $data
     * @return JsonResponse
     */
    protected function indexResponse($data)
    {
        return $this->paginatedList($data->toArray());
    }

    /**
     * StoreMany method success response
     *
     * @param array $data
     * @return JsonResponse
     */
    protected function storeManyResponse(array $data)
    {
        return $this->success($data, 201);
    }

    /**
     * Store method success response
     *
     * @param Model $data
     * @return JsonResponse
     */
    protected function storeResponse(Model $data)
    {
        return $this->success($data, 201);
    }

    /**
     * Show method success response
     *
     * @param Model $data
     * @return JsonResponse
     */
    protected function showResponse(Model $data)
    {
        return $this->success($data, 200);
    }

    /**
     * Update method success response
     *
     * @param Model $data
     * @return JsonResponse
     */
    protected function updateResponse(Model $data)
    {
        return $this->success($data, 202);
    }

    /**
     * Destroy method success response
     *
     * @param Model $data
     * @return JsonResponse
     */
    protected function destroyResponse(Model $data)
    {
        return $this->success($data, 202);
    }

    /**
     * ForceDestroy method success response
     *
     * @param Model $data
     * @return JsonResponse
     */
    protected function forceDestroyResponse(Model $data)
    {
        return $this->success($data, 202);
    }

    /**
     * DestroyMany method success response
     *
     * @param int $deletedCount
     * @return JsonResponse
     */
    protected function destroyManyResponse($deletedCount)
    {
        return $this->success("$deletedCount item(s) deleted successfully", 202);
    }

    /**
     * RestoreDestroyed method success response
     *
     * @param Model $data
     * @return JsonResponse
     */
    protected function restoreDestroyedResponse(Model $data)
    {
        return $this->success($data, 202);
    }
}
