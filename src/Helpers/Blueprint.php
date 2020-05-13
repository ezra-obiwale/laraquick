<?php

namespace Laraquick\Helpers;

use Illuminate\Database\Schema\Blueprint as iBlueprint;

class Blueprint extends iBlueprint
{
    public function fullText($columns, $name = null, $algorithm = null)
    {
        return $this->indexCommand('fullText', $columns, $name, $algorithm);
    }

    public function dropFullText($index)
    {
        return $this->dropIndexCommand('dropFullText', 'fullText', $index);
    }
}
