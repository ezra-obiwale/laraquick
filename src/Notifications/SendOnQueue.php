<?php

namespace Laraquick\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;

class SendOnQueue extends Send implements ShouldQueue
{
}
