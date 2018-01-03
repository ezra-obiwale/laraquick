<?php
namespace Laraquick\Models;

use Illuminate\Database\Eloquent\Model as iModel;

abstract class Model extends iModel
{
	
    public function toArray()
    {
        $fillable = $this->fillable;
        $fillable[] = 'id';
		// Show only fillables
        $array = collect(parent::toArray())
            ->only($fillable)
            ->all();
		// Add loaded relations
		foreach (array_keys($this->relations) as $relation) {
			$array[$relation] = $this->$relation;
		}
		return $array;
    }

}
