<?php
namespace Laraquick\Controllers\Traits\Response;

use Illuminate\Http\Response;

trait Web
{

    /**
     * Called when an action is successfully processed.
     *
     * @param string $message
     * @return Response
     */
    protected function success($status)
    {
        return back()->withStatus($status);
    }

    /**
     * Called when an error occurs while performing an action
     *
     * @param string $message
     * @param mixed $errors
     * @param integer $code
     * @return Response
     */
    protected function error($message, $errors = null, $code = 400)
    {
        $back = back()->withInput()->withMessage($message);
        if ($errors) {
            $back->withErrors($errors);
        }
        return $back;
    }
}
