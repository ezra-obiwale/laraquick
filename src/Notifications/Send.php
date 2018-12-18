<?php

namespace Laraquick\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class Send extends Notification
{
    use Queueable;

    /**
     * The mail message object
     *
     * @var MailMessage
     */
    protected $mailMessage;
    /**
     * The array to return in the toDatabase method
     *
     * @var array
     */
    protected $toDatabase;
    /**
     * The array to return in the toArray method
     *
     * @var array
     */
    protected $toArray;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    /**
     * Create a new notification instance
     *
     * @param MailMessage $mailMessage The MailMessage object
     * @param array $toDatabase The array to return in the toDatabase method
     * @param array $toArray The array to return in the toArray method
     *
     */
    public function __construct(MailMessage $mailMessage, array $toDatabase = null, array $toArray = null)
    {
        $this->mailMessage = $mailMessage;
        $this->toDatabase = $toDatabase;
        $this->toArray = $toArray;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['mail'];
        if (($this->toDatabase && count($this->toDatabase))
            || ($this->toArray && count($this->toArray))) {
            $via[] = 'database';
        }
        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return $this->mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return $this->toDatabase ?: [];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->toArray ?: [];
    }
}
