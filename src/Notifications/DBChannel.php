<?php

namespace Laraquick\Notifications;

use Illuminate\Notifications\Notification;

class DBChannel
{

  public function send($notifiable, Notification $notification)
  {
    $data = $notification->toDatabase($notifiable);

    $data['id'] = $notification->id;
    $data['type'] = get_class($notification);
    $data['read_at'] = null;

    return $notifiable->routeNotificationFor('database')->create($data);
  }

}