<?php
namespace Laraquick\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Laraquick\Controllers\Traits\Response\Common;
use Laraquick\Controllers\Traits\Response\Web as WebResponse;

/**
 * A collection of api methods to assist in quick controller generation
 */
trait Web
{
    use Crud\Crud, WebResponse, Common;

    /**
     * Store method success response
     *
     * @param Model $data
     * @return Response
     */
    protected function storeResponse(Model $data)
    {
        return $this->success('Create successful');
    }

    /**
     * StoreMany method success response
     *
     * @param array $data
     * @return Response
     */
    protected function storeManyResponse(array $data)
    {
        return $this->success('Create many successful', 201);
    }

    /**
     * Update method success response
     *
     * @param Model $data
     * @return Response
     */
    protected function updateResponse(Model $data)
    {
        return $this->success('Update successful');
    }

    /**
     * Destroy method success response
     *
     * @param Model $data
     * @return Response
     */
    protected function destroyResponse(Model $data)
    {
        return $this->success('Delete successful');
    }

    /**
     * ForceDestroy method success response
     *
     * @param Model $data
     * @return Response
     */
    protected function forceDestroyResponse(Model $data)
    {
        return $this->success('Permanent delete successful');
    }

    /**
     * DestroyMany method success response
     *
     * @param int $deletedCount
     * @return Response
     */
    protected function destroyManyResponse($deletedCount)
    {
        return $this->success("Deleted $deletedCount item(s) successfully");
    }

    /**
     * RestoreDestroyed method success response
     *
     * @param Model $data
     * @return Response
     */
    protected function restoreDestroyedResponse(Model $data)
    {
        return $this->success('Restoration successful');
    }
}
