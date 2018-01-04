<?php

namespace Laraquick\Models\Traits;

trait Helper {
	
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