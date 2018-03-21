<?php
namespace Laraquick\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;

use DB;

trait Web
{
    use Crud;
	
    /**
     * Called when an action is successfully processed.
     *
     * @param string $message
     * @return Response
     */
	protected function success($status) {
		return back()->withStatus($status);
	}
    
    protected function error($message, $errors = null, $code = 400)
    {
        $back = back()->withInput()->withMessage($message);
        if ($errors) {
            $back->withErrors($errors);
        }
        return $back;
    }

    protected function storeResponse(Model $data)
    {
        return $this->success('Create successful');
    }

    protected function updateResponse(Model $data)
    {
        return $this->success('Update successful');
    }

    protected function destroyResponse(Model $data)
    {
        return $this->success('Delete successful');
    }
    
    protected function forceDestroyResponse(Model $data)
    {
        return $this->success('Permanent delete successful');
    }
    
    protected function destroyManyResponse($deletedCount)
    {
        return $this->success("Deleted $deletedCount item(s) successfully");
    }    
    
    protected function restoreDestroyedResponse(Model $data)
    {
        return $this->success('Restoration successful');
    }
}
