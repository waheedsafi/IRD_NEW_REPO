<?php

namespace App\Repositories\Notification;

use App\Jobs\SendNotificationJob;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function SendNotification($request, $record)
    {
        $token = $request->bearerToken();
        // SendNotificationJob::dispatch($token, $record);
    }
}
