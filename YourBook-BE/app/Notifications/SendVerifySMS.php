<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
//use Illuminate\Notifications\Messages\VonageMessage;

class SendVerifySMS extends Notification
{
    public function __construct()
    {
        //
    }

    public function via($notifiable): array
    {
        return ['array'];
    }

    public function toX($notifiable)
    {
//        return (new VonageMessage())
//            ->content("Your verification code is {$notifiable->mobile_verify_code}");
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }

}
