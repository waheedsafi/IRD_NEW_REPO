<?php

namespace App\Repositories\Notification;

interface NotificationRepositoryInterface
{
    /**
     * Retrieve NGO data when registeration is completed.
     * 
     *
     * @param Illuminate\Http\Request $request
     * @param mix $record
     * @return mix
     */
    public function SendNotification($request, $record);
}
