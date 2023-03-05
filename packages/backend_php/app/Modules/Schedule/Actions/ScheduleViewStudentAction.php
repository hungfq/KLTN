<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Entities\User;
use App\Exceptions\UserException;

class ScheduleViewStudentAction
{
    public function handle($id)
    {
        $schedule = Schedule::find($id);
        if (!$schedule) {
            throw new UserException("Schedule not found!");
        }

        $studentIds = [];
        foreach (data_get($schedule, 'students', []) as $student) {
            $studentIds[] = $student->id;
        }

        $query = User::query()
            ->whereIn('id', $studentIds);

        return $query->get();
    }
}