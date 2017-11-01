<?php
namespace Laraquick\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;

use DB;

/**
 * A colletion of methods to assist in quick controller generation
 *
 */
trait Api
{
    use Crud;

    protected function indexResponse($data)
    {
        return $this->paginatedList($data->toArray());
    }

    protected function storeResponse(Model $data)
    {
        return response()->json($data, 201);
    }

    protected function showResponse(Model $data)
    {
        return response()->json($item, 200);
    }

    protected function updateResponse(Model $data)
    {
        return response()->json($item, 202);
    }

    protected function deleteResponse(Model $data)
    {
        return response()->json($item, 202);
    }
}
