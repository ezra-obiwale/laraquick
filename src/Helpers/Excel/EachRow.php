<?php

namespace Laraquick\Helpers\Excel;

use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class EachRow extends Import implements OnEachRow
{

    /**
     * Called on each row
     *
     * @param Row $row
     * @return void
     */
    public function onRow(Row $row)
    {
        if ($this->each) {
            return call_user_func($this->each, $row);
        }
    }
}
