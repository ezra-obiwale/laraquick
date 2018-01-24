<?php

namespace Laraquick\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Helper {

    /**
     * A shortcut to withoutGlobalScope()
     *
     * @param string|array $attributes
     * @return Builder
     */
    public function without($attributes)
    {
        return $this->withoutGlobalScope($attributes);
    }
	
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