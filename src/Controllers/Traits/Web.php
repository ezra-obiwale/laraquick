<?php
namespace Laraquick\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;

use DB;

trait Web
{
    use Crud;

    protected function storeResponse($data)
    {
        return back()->withStatus('Create successful');
    }

    protected function updateResponse(Model $data)
    {
        return back()->withStatus('Update successful');
    }

    protected function deleteResponse(Model $data)
    {
        return back()->withStatus('Delete successful');
    }
    
    protected function deleteManyResponse($deletedCount)
    {
        return back()->withStatus("Deleted $deletedCount item(s) successfully");
    }

    protected function error($message, $errors = null, $code = 400)
    {
        $back = back()->withMessage($message);
        if ($errors) {
            $back->withErrors($errors);
        }
        return $back;
    }    
}
