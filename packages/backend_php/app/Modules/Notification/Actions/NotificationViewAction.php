<?php

namespace App\Modules\Notification\Actions;

use App\Entities\Notification;
use App\Modules\Notification\DTO\NotificationViewDTO;
use Illuminate\Support\Facades\Auth;

class NotificationViewAction
{

    /**
     * @param NotificationViewDTO $dto
     */
    public function handle($dto)
    {
        $query = Notification::query()
            ->where('to_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($search = $dto->search) {
            $query->where('code', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%");
        }

        if ($dto->limit) {
            return $query->paginate($dto->limit);
        }

        return $query->get();
    }
}