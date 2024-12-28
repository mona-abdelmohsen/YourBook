<?php

namespace App\Notifications;

use App\Models\Auth\User;
use App\Traits\Mapping;
use Chatify\Facades\ChatifyMessenger as Chatify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewChatMessage extends Notification
{
    use Queueable, Mapping;

    private User $fromUser;
    private ?string $message;
    private ?array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $fromUser, string|null $message, array|null $data)
    {
        $this->fromUser = $fromUser;
        $this->message = $message;
        $this->data = $data;
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
        if($this->data['attachment']){
            $path = config('chatify.attachments.folder') . '/' . $this->data['attachment']->file;
            $fileUrl = $this->data['attachment']->url = Chatify::storage()->url($path);
        }
        return (new FcmMessage(notification: new FcmNotification(
            title: $this->fromUser->name,
            body: $this->message
        )))->data([
            'type' => 'message',
            'data' => json_encode([
                'from_id'  => $this->fromUser->id,
                'name'  => $this->fromUser->name,
                'avatar' => $this->fromUser->avatar? url('storage/avatars/'.basename($this->fromUser->avatar)): null,
                'message'   => $this->message,
                'attachment_url' => $fileUrl ?? null,
                'messageData'   => $this->data,
            ]),
        ]);
    }

}
