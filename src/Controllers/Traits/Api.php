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

    /**
     * Called when an action is successfully processed.
     *
     * @param mixed $response
     * @param integer $code
     * @return Response
     */
    protected function success($response = null, $code = 200)
    {
        $resp = [
            'status' => is_array($response) && array_key_exists('status', $response)
             ? $response['status'] : 'ok'
        ];
        if (is_array($response)) {
            $resp = array_merge($resp, $response);
        } elseif (is_string($response)) {
            $resp['message'] = $response;
        } elseif ($response !== null) {
            $resp['data'] = $response;
        }
        
        return response()->json($resp, $code);
    }

    protected function indexResponse($data)
    {
        return $this->paginatedList($data->toArray());
    }

    protected function storeResponse(Model $data)
    {
        return $this->success($data, 201);
    }

    protected function showResponse(Model $data)
    {
        return $this->success($data, 200);
    }

    protected function updateResponse(Model $data)
    {
        return $this->success($data, 202);
    }

    protected function destroyResponse(Model $data)
    {
        return $this->success($data, 202);
    }

    protected function forceDestroyResponse(Model $data)
    {
        return $this->success($data, 202);
    }

    protected function destroyManyResponse($deletedCount)
    {
        return $this->success("$deletedCount item(s) deleted successfully", 202);
    }
    
    protected function restoreDestroyedResponse(Model $data)
    {
        return $this->success($data, 202);
    }
}
