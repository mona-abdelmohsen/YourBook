<?php

namespace App\Notifications;

use App\Models\Auth\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class FriendRequestAccepted extends Notification
{
    use Queueable;

    private User $friend;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $friend)
    {
        $this->friend = $friend;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', FcmChannel::class];
    }

    /**
     * @param $notifiable
     * @return FcmMessage
     */
    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(notification: new FcmNotification(
            title: 'Friend Reqest',
            body: $this->friend->name.' has accepted your friend request! 🎉 You are now connected'
        )))->data([
            'type' => 'notification',
            'data'  => json_encode([
                'from_user_id'  => $this->friend->id,
            ]),
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->friend->name.' has accepted your friend request! 🎉 You are now connected.')
            ->greeting('Dear '.auth()->user()->name)
            ->line('We are excited to inform you that '.$this->friend->name.' has accepted your friend request on YourBook! 🎉 You are now connected and can start enjoying all the benefits of being friends on our platform.')
            ->line('Thanks for being a part of our community!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'parameters'  => [
                'from_user_id'  => $this->friend->id,
            ],
            'message'   => $this->friend->name.' has accepted your friend request! 🎉 You are now connected.',
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
        return 'friend-request-accepted';
    }

    public function broadcastType(): string
    {
        return 'friend-request-accepted';
    }

    public function broadcastAs(): string
    {
        return 'notification';
    }
}
