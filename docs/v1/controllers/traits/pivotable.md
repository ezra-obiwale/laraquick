# Pivotable Trait

This trait adds endpoint-ready methods for using the [Attachable trait](/v1/controllers/traits/attachable).

<p class="tip">
This trait uses the [Attachable trait](/v1/controllers/traits/attachable).
</p>

## Endpoint Methods

### addItems ( `...` )

<p class="tip no-bg">
    protected function addItems ( `mixed` $id ) : `Illuminate\Http\Response`
</p>

This method attaches the `id` or `id`'s on [the data key](#paramkey-) to the
base model.

Paramater `$id` is the `id` of the base model to operate on.

### removeItems ( `...` )

<p class="tip no-bg">
    protected function removeItems ( `mixed` $id ) : `Illuminate\Http\Response`
</p>

This method detaches the `id` or `id`'s on [the data key](#paramkey-) from the
base model.

Paramater `$id` is the `id` of the base model to operate on.

### updateItems ( `...` )

<p class="tip no-bg">
    protected function updateItems ( `mixed` $id ) : `Illuminate\Http\Response`
</p>

This method syncs the `id` or `id`'s on [the data key](#paramkey-) to the
base model.

Paramater `$id` is the `id` of the base model to operate on.

## Other Methods

### relation ( )

<p class="tip no-bg">
    protected function relation ( ) : `string`
</p>

The relation on the base model on which to process items.

### paramKey ( )

<p class="tip no-bg">
    protected function paramKey ( ) : `string`
</p>

The data key which holds the `id` or `id`'s of relation models to process items.