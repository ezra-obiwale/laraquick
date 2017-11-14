<?php
namespace Laraquick\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class WithSoftDeletes extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function toArray()
    {
        $fillable = $this->fillable;
        $fillable[] = 'id';
        return collect(parent::toArray())
            ->only($fillable)
            ->all();
    }
}
