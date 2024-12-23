<?php

namespace Laraquick\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Helper
{
    /**
     * A shortcut to withoutGlobalScope()
     *
     * @param string|array $attributes
     * @return Builder
     */
    public function without($attributes): Builder
    {
        return $this->withoutGlobalScopes(!is_array($attributes) ? [$attributes] : $attributes);
    }

    /**
     * Excludes the given values from being selected from the database
     * Thanks to Ikechi Michael (@mykeels)
     *
     * @param Builder $query
     * @param string|array $value
     * @return Builder
     */
    public function scopeExcept($query, $value): Builder
    {
        $defaultColumns = ['id', 'created_at', 'updated_at'];

        if (in_array('deleted_at', $this->dates)) {
            $defaultColumns[] = 'deleted_at';
        }

        if (is_string($value)) {
            $value = [$value];
        }

        return $query->select(array_diff(array_merge($defaultColumns, $this->fillable), (array) $value));
    }

    /**
     * Removes timestamps from query
     *
     * @return self
     */
    public function scopeWithoutTimestamps($query): self
    {
        $this->timestamps = false;

        return $query;
    }

    public function toArray(): array
    {
        $fillable = $this->fillable ?? [];
        $appends = $this->appends ?? [];
        $relations = array_keys($this->relations ?? []);
        $counts = array_keys($this->withCount ?? []);
        $withArray = $this->withArray ?? [];

        $relations = array_map(fn ($relation) => Str::snake($relation), $relations);
        $withArray = array_map(fn ($with) => Str::snake($with), $withArray);

        array_unshift($fillable, 'id');
        $array = collect(parent::toArray())
            // Show only fillables, appends and relations
            ->only(array_merge($fillable, $appends, $relations, $counts, $withArray))
            ->all();

        // remove nulls
        if (property_exists($this, 'arrayWithoutNulls') && $this->arrayWithoutNulls) {
            $array = array_filter($array);
        }

        return $array;
    }
}
