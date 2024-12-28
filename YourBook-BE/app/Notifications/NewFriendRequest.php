<?php

namespace App\Notifications;

use App\Models\Auth\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;


class NewFriendRequest extends Notification implements ShouldQueue
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

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(notification: new FcmNotification(
            title: 'New Friend Request',
            body: 'New Friend Request From: '.$this->friend->name
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
            ->subject('New Friend Request')
            ->greeting('Hello!')
            ->line('You have new friend request from: '.$this->friend->name)
            ->line('Thank you for using YourBook');
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
            'message'   => 'New Friend Request From: '.$this->friend->name
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
        return 'new-friend-request';
    }

    public function broadcastType(): string
    {
        return 'new-friend-request';
    }

    public function broadcastAs(): string
    {
        return 'notification';
    }
}
