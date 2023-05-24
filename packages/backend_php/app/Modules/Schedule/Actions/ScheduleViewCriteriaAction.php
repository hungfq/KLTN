<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Entities\ScheduleCriteria;
use App\Exceptions\UserException;

class ScheduleViewCriteriaAction
{
    public function handle($id)
    {
        $schedule = Schedule::find($id);
        if (!$schedule) {
            throw new UserException("Schedule not found!");
        }

        $query = ScheduleCriteria::query()
            ->select([
                'schedule_criteria.*',
                'criteria.title',
                'criteria.description',
            ])
            ->join('criteria', function ($q) {
                $q->on('schedule_criteria.criteria_id', '=', 'criteria.id')
                    ->where('criteria.deleted', 0);
            })
            ->where('schedule_criteria.schedule_id', $schedule->id);

        return $query->get();
    }
}