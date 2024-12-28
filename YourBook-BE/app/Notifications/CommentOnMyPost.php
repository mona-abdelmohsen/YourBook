<?php

namespace App\Notifications;

use App\Models\Auth\User;
use App\Models\Posts\Comment;
use App\Models\Posts\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;


class CommentOnMyPost extends Notification
{
    use Queueable;

    private User $friend;
    private Post $post;
    private Comment $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $friend, Post $post, Comment $comment)
    {
        $this->friend = $friend;
        $this->post = $post;
        $this->comment = $comment;
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
            title: 'New Comment',
            body: 'You have received a new comment from '.$this->friend->name.' on your post! Check it out now.'
            )))->data([
                'type' => 'notification',
                'data'  => json_encode([
                    'from_user_id'  => $this->friend->id,
                    'post_id'   => $this->post->id,
                    'comment_id'    => $this->comment->id,
                ]),
            ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Comment from '.$this->friend->name.' on Your Post')
            ->line('We wanted to let you know that '.$this->friend->name.' has left a new comment on your post on YourBook. It is always great to receive feedback and engage with our community!')
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
                'post_id'   => $this->post->id,
                'comment_id'    => $this->comment->id,
            ],
            'message'   => 'You have received a new comment from '.$this->friend->name.' on your post! Check it out now.'
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
        return 'comment-on-my-post';
    }

    public function broadcastType(): string
    {
        return 'comment-on-my-post';
    }

    public function broadcastAs(): string
    {
        return 'notification';
    }
}
