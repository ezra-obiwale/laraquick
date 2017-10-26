<?php

namespace Laraquick\Controllers\Traits;

/**
 * Simplifies add, removing and syncing many-to-many relations
 * 
 */
trait Pivotable {

    use Attachable;

    /**
     * Set the relation name of the model to be used
     * by methods addItems, removeItems and updateItems
     *
     * @return string
     */
    protected function relation() {
        return 'items';
    }

    /**
     * Set the paramKey to be used by methods
     * addItems, removeItems and updateItems
     *
     * @return string
     */
    protected function paramKey()
    {
        return 'items';
    }

    /**
     * Attaches a list of items to the object at the given id
     *
     * @param int $id
     * @return void
     */
    public function addItems($id)
    {
        return $this->attach($id, $this->relation(), $this->paramKey());
    }

    /**
     * Detaches a list of items from the object at the given id
     *
     * @param int $id
     * @return void
     */
    public function removeItems($id)
    {
        return $this->detach($id, $this->relation(), $this->paramKey());
    }

    /**
     * Syncs a list of items with existing attached items on the object at the given id
     *
     * @param int $id
     * @return void
     */
    public function updateItems($id)
    {
        return $this->sync($id, $this->relation(), $this->paramKey());
    }
}