<?php
namespace Laraquick\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;

use DB;

/**
 * A collection of methods to assist in quick controller generation
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
        return response()->json($data, 200);
    }

    protected function updateResponse(Model $data)
    {
        return response()->json($data, 202);
    }

    protected function destroyResponse(Model $data)
    {
        return response()->json($data, 202);
    }

    protected function forceDestroyResponse(Model $data)
    {
        return response()->json($data, 202);
    }

    protected function destroyManyResponse($deletedCount)
    {
        return response()->json([
            "message" => "$deletedCount item(s) deleted successfully"
        ], 202);
    }
    
    protected function restoreDestroyedResponse(Model $data)
    {
        return response()->json($data, 202);
    }
}
