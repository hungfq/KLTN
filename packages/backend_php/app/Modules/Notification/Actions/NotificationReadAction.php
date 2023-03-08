<?php

namespace App\Modules\Notification\Actions;

use App\Entities\Notification;
use App\Exceptions\UserException;
use Illuminate\Support\Facades\Auth;

class NotificationReadAction
{
    public function handle($id)
    {
        $notify = Notification::query()
            ->where('id', $id)
            ->where('to_id', Auth::id())
            ->first();

        if (!$notify) {
            throw new UserException('Notification is not exist');
        }

        $notify->is_read = true;
        $notify->save();
    }
}