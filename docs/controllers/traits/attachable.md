# Attachable Trait

Shortcut methods to making working with related many-to-many models faster. It is
intended to be used with the [Api trait](/controllers/traits/api).

<p class="tip">
    See [Laravel Doc](https://laravel.com/docs/5.5/eloquent-relationships#updating-many-to-many-relationships)
</p>

<p class="tip">
    These methods are meant to be called from within custom endpoint methods and
    not used directly as the parameters would need to be customized.

    For ready-made endpoint methods, use the [Pivotable trait](/controllers/traits/pivotable).
</p>

## Route Methods

### attach ( `...` )

<p class="tip no-bg">
    public function attach ( `mixed` $id, `string` $relation, `string` $paramKey = 'items' ) : `Illuminate\Http\Response`
</p>

Called when `id`'s of existing models need to be attached to another model.

### detach ( `...` )

<p class="tip no-bg">
    public function detach ( `mixed` $id, `string` $relation, `string` $paramKey = 'items' ) : `Illuminate\Http\Response`
</p>

Called when `id`'s of existing models need to be detached to another model.

### sync ( `...` )

<p class="tip no-bg">
    public function sync ( `mixed` $id, `string` $relation, `string` $paramKey = 'items' ) : `Illuminate\Http\Response`
</p>

Called when `id`'s of existing models need to be synced to another model.

### Parameters

All three methods have the same parameters:

#### $id

The id of the base model, e.g. User

#### $relation

The name of the relation method on the base model, e.g. books

#### $paramKey

The key on the request data that holds the `id` or array of `id`'s of the relation
models to attach to the base model.

## Model Methods

### attachModel ( )

<p class="tip no-bg">
    protected function attachModel ( ) : string
</p>

The base model to be used for when attaching. Defaults to [Api trait's method model ( )](/controllers/traits/api#model-).

### detachModel ( )

<p class="tip no-bg">
    protected function detachModel ( ) : string
</p>

The base model to be used for when detaching. Defaults to [Api trait's method model ( )](/controllers/traits/api#model-).

### syncModel ( )

<p class="tip no-bg">
    protected function syncModel ( ) : string
</p>

The base model to be used for when synching. Defaults to [Api trait's method model ( )](/controllers/traits/api#model-).