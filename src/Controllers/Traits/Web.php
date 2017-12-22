<?php
namespace Laraquick\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;

use DB;

trait Web
{
    use Crud;
    
    protected function error($message, $errors = null, $code = 400)
    {
        $back = back()->withMessage($message);
        if ($errors) {
            $back->withErrors($errors);
        }
        return $back;
    }

    protected function storeResponse(Model $data)
    {
        return back()->withStatus('Create successful');
    }

    protected function updateResponse(Model $data)
    {
        return back()->withStatus('Update successful');
    }

    protected function destroyResponse(Model $data)
    {
        return back()->withStatus('Delete successful');
    }
    
    protected function forceDestroyResponse(Model $data)
    {
        return back()->withStatus('Permanent delete successful');
    }
    
    protected function destroyManyResponse($deletedCount)
    {
        return back()->withStatus("Deleted $deletedCount item(s) successfully");
    }    
    
    protected function restoreDestroyedResponse(Model $data)
    {
        return back()->withStatus('Restoration successful');
    }
}
