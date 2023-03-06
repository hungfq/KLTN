<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Modules\Schedule\DTO\ScheduleViewDTO;

class ScheduleViewAction
{
    /**
     * @param $dto ScheduleViewDTO
     */
    public function handle($dto)
    {
        $query = Schedule::query()
            ->with(['students', 'createdBy', 'updatedBy'])
            ->orderBy('created_at', 'DESC');

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