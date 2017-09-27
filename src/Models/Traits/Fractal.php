<?php
namespace Laraquick\Models\Traits;

use Spatie\Fractalistic\ArraySerializer;

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
        })->serializeWith(new ArraySerializer());
    }

    /**
     * Fractalize many models of this class
     *
     * @param Traversable|array $items
     * @return void
     */
    public static function fractality($items)
    {
        return fractal($items, function ($item) {
            return $item->transform();
        })->serializeWith(new ArraySerializer());
    }

    abstract public function transform();
}