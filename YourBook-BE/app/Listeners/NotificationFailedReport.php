<?php

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\SendReport;

class NotificationFailedReport
{

    /**
     * Handle the event.
     */
    public function handle(NotificationFailed $event): void
    {
        $report = Arr::get($event->data, 'report');
        $target = $report->target();

        //$event->notifiable->notificationTokens()
        //    ->where('push_token', $target->value())
        //    ->delete();

        // Log the failed notification response
        logger()->error('Notification delivery failed', [
//            'notification' => $event->notification,
//            'notifiable' => $event->notifiable,
//            'channel' => $event->channel,
            'error' => $report->error()->getMessage()
        ]);

    }
}
