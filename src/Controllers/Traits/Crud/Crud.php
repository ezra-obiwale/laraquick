<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;

use DB;

/**
 * A collection of methods to assist in quick controller generation for crud actions
 *
 */
trait Crud
{
    use Index, Store, Show, Update, Destroy, Validation, Respond;

    /**
     * Should return a static instance of the target model class or
     * the fully qualified name of the target modal class
     *
     * @return mixed
     */
    abstract protected function model();

    /**
     * The model to use in the index method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function indexModel()
    {
        return $this->model();
    }

    /**
     * The model to use in the index method when @see searchQueryParam() exists in the `GET` query.
     *
     * It should return the model after the query conditions have been implemented.
     * Defaults to @see indexModel()
     *
     * @return mixed
     */
    protected function searchModel($query)
    {
        return $this->indexModel();
    }
    
    /**
     * The model to use in the store method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function storeModel()
    {
        return $this->model();
    }

    /**
     * The model to use in the show method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function showModel()
    {
        return $this->model();
    }

    
    /**
     * The model to use in the update method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function updateModel()
    {
        return $this->model();
    }
    
    /**
     * The model to use in the delete method. Defaults to @see model()
     *
     * @return mixed
     */
    protected function destroyModel()
    {
        return $this->model();
    }
}
