<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Schedule\DTO\ScheduleViewStudentDTO;

class ScheduleViewStudentAction
{
    /**
     * @param ScheduleViewStudentDTO $dto
     */
    public function handle($dto)
    {
        $schedule = Schedule::find($dto->id);
        if (!$schedule) {
            throw new UserException("Schedule not found!");
        }

        $studentIds = [];
        foreach (data_get($schedule, 'students', []) as $student) {
            $studentIds[] = $student->id;
        }

        $query = User::query()
            ->whereIn('id', $studentIds);

        if ($dto->limit) {
            return $query->paginate($dto->limit);
        }

        return $query->get();
    }
}