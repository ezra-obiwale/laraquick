<?php
namespace Laraquick\Models\Traits;

use Iterator;

trait Fractal
{
    /**
     * Transform the model with fractal
     *
     * @return void
     */
    final public function fractalize()
    {
        return fractal($this, function () {
            return $this->transform();
        });
    }

    /**
     * Fractalize many models of this class
     *
     * @param Iterator $items
     * @return void
     */
    public static function fractality(Iterator $items)
    {
        return collect($items)->map([self, 'fractalize']);
    }

    abstract public function transform();
}