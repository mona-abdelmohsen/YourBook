<?php

namespace App\Notifications;

use App\Models\Auth\User;
use App\Models\Stories\Story;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;


class ReactOnMyStory extends Notification
{
    use Queueable;

    private User $friend;
    private Story $story;
    private string $reaction;
    /**
     * Create a new notification instance.
     */
    public function __construct(User $friend, Story $story, string $reaction)
    {
        $this->friend = $friend;
        $this->story = $story;
        $this->reaction = $reaction;
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
            title: 'New Reaction',
            body: $this->friend->name.' has reacted to your Story! 🎉 Check it out now'
        )))->data([
            'type' => 'notification',
            'data'  => json_encode([
                'from_user_id'  => $this->friend->id,
                'story_id'   => $this->story->id,
                'reaction'    => $this->reaction,
            ]),
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Reaction on Your Story')
            ->line('Exciting news! '.$this->friend->name.' has reacted to your Story on YourBook. It is fantastic to see your content resonating with others.')
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
                'story_id'   => $this->story->id,
                'reaction'    => $this->reaction,
            ],
            'message'   => $this->friend->name.' has reacted to your Story! 🎉 Check it out now',
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
        return 'react-on-my-story';
    }

    public function broadcastType(): string
    {
        return 'react-on-my-story';
    }

    public function broadcastAs(): string
    {
        return 'notification';
    }
}
