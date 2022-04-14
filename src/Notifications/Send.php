<?php

namespace Laraquick\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
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
     * Function to create the mail message. Receives the notifiable as the only parameter.
     *
     * @var callable
     */
    protected $mailMessageCreator;

    /**
     * The array of data for saving to the database.
     *
     * @var array
     */
    protected $databaseData = [];

    /**
     * The array of data to use for broadcasting.
     *
     * @var array
     */
    protected $broadcastData = [];

    /**
     * The broadcast channels to send the event on
     *
     * @var array
     */
    protected $broadcastChannels = [];

    /**
     * The broadcast message to send on event channels
     *
     * @var BroadcastMessage
     */
    protected $broadcastMessage;

    /**
     * Sets the function to create the broadcast message. Receives the notifiable as the only parameter.a2
     *
     * @var function
     */
    protected $broadcastMessageCreator;

    /**
     * Create a new notification instance
     *
     * @param MailMessage $mailMessage The MailMessage object
     * @param array $databaseData The array of data for saving to the database.
     * @param array $broadcastData The array of data to use for broadcasting.
     *
     */
    public function __construct(MailMessage $mailMessage = null, array $databaseData = [], array $broadcastData = [])
    {
        $this->mailMessage = $mailMessage;
        $this->databaseData = $databaseData;
        $this->broadcastData = $broadcastData;
    }

    /**
     * Sets the mail message
     *
     * @param MailMessage $mailMessage
     * @return self
     */
    public function setMailMessage(MailMessage $mailMessage)
    {
        $this->mailMessage = $mailMessage;

        return $this;
    }

    /**
     * Gets the mail message
     *
     * @return MailMessage
     */
    public function getMailMessage()
    {
        return $this->mailMessage;
    }

    /**
     * Sets the array of data for saving to the database.
     *
     * @param array $data
     * @return self
     */
    public function setDatabaseData(array $data)
    {
        $this->databaseData = $data;

        return $this;
    }

    /**
     * Sets the array of data to use for broadcasting.
     *
     * @param array $data
     * @return self
     */
    public function setBroadcastData(array $data)
    {
        $this->broadcastData = $data;

        return $this;
    }

    /**
     * Sets the channels the event should be broadcast on
     *
     * @param array $channels
     * @return self
     */
    public function setBroadcastChannels(array $channels)
    {
        $this->broadcastChannels = $channels;

        return $this;
    }

    /**
     * Sets the broadcast message to be sent to the event channels
     *
     * @param BroadcastMessage $message
     * @return self
     */
    public function setBroadcastMessage(BroadcastMessage $message)
    {
        $this->broadcastMessage = $message;

        return $this;
    }

    /**
     * Sets the function to create the broadcast message object. The function receives the notifiable as the only parameter.
     *
     * @param callable $creator
     * @return self
     */
    public function createsBroadcastMessage(callable $creator)
    {
        $this->broadcastMessageCreator = $creator;

        return $this;
    }

    /**
     * Sets a function to create the mail message object. The function receives the notifiable as the only parameter.
     *
     * @param callable $creator
     * @return self
     */
    public function createMailMessage(callable $creator)
    {
        $this->mailMessageCreator = $creator;

        return $this;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = [];

        if ($this->toMail($notifiable)) {
            $via[] = 'mail';
        }

        if (count($this->databaseData)) {
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
        return $this->mailMessageCreator ? call_user_func($this->mailMessageCreator, $notifiable) : $this->mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return $this->databaseData;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->broadcastData;
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param mixed $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return $this->broadcastMessageCreator ? call_user_func($this->broadcastMessageCreator, $notifiable) : $this->broadcastMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return $this->broadcastChannels;
    }
}
