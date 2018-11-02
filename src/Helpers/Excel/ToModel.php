<?php

namespace Laraquick\Helpers\Excel;

use Maatwebsite\Excel\Concerns\ToModel as MToModel;
use Illuminate\Database\Eloquent\Model;

class ToModel implements MToModel
{
    
    /**
     * Called on each row
     *
     * @param array $row
     * @return Model
     */
    public function model(array $row) : Model
    {
        if ($this->each) {
            return call_user_func($this->each, $row);
        }
    }

}