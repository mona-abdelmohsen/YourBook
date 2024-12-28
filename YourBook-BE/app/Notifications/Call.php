<?php

namespace App\Notifications;

use App\Models\Auth\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class Call extends Notification
{
    use Queueable;
    private array $data;
    private User|Authenticatable $fromUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->fromUser = auth()->user();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    /**
     * @param $notifiable
     * @return FcmMessage
     */
    public function toFcm($notifiable): FcmMessage
    {

        return (new FcmMessage(notification: new FcmNotification(
            title: 'Incoming call',
            body: 'incoming call from: '.$this->fromUser->getAttribute('name')
        )))->data([
            'type' => 'notification',
            'data' => json_encode($this->data),
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {

        return [
            'parameters' => $this->data,
            'message' => 'incoming call from: '.$this->fromUser->getAttribute('name')
        ];
    }

    /**
     * Get the notification's database type.
     *
     * @param object $notifiable
     * @return string
     */
    public function databaseType(object $notifiable): string
    {
        return 'call';
    }

    public function broadcastType(): string
    {
        return 'call';
    }

    public function broadcastAs(): string
    {
        return 'notification';
    }
}
