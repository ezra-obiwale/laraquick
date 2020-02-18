<?php

namespace Laraquick\Helpers\Excel;

use Maatwebsite\Excel\Concerns\ToCollection as MToCollection;
use Illuminate\Support\Collection;

class ToCollection extends Import implements MToCollection
{
    
    /**
     * Called on each sheet
     *
     * @param Collection $rows
     * @return Model
     */
    public function collection(Collection $rows) : Model
    {
        if ($this->each) {
            return call_user_func($this->each, $rows);
        }
    }
}
