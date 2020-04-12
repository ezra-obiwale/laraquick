<?php

namespace Laraquick\Controllers\Traits\Response;

use Illuminate\Http\JsonResponse;

trait Api
{
    /**
     * Called when an action is successfully processed.
     *
     * @param mixed $response
     * @param integer $code
     * @param array $meta
     * @return JsonResponse
     */
    protected function success($response = null, $code = 200, array $meta = [])
    {
        $resp = [
            'status' => is_array($response) && array_key_exists('status', $response)
             ? $response['status'] : 'ok'
        ];

        if (!is_null($response)) {
            if (!is_array($response) && !is_object($response)) {
                $resp['message'] = $this->translate($response);
            } elseif ($response !== null) {
                $resp['data'] = $response;
            }
        }

        if (count($meta)) {
            $resp['meta'] = $meta;
        }

        return response()->json($resp, $code);
    }

    /**
     * Called when an error occurs while performing an action
     *
     * @param string $message
     * @param mixed $errors
     * @param integer $code
     * @return JsonResponse
     */
    protected function error($message, $errors = null, $code = 400)
    {
        $resp = [
            "status" => "error",
            "message" => $this->translate($message)
        ];

        if ($errors !== null) {
            $resp["errors"] = $errors;
        }

        return response()->json($resp, $code);
    }
}
