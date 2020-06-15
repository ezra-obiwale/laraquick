<?php

namespace Laraquick\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notifiable as NotificationsNotifiable;

trait Notifiable {

    use NotificationsNotifiable;

    /**
     * Get the entity's notifications.
     *
     * @return MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(config('laraquick.classes.database_notification', DatabaseNotification::class), 'notifiable')->orderBy('created_at', 'desc');
    }
}
