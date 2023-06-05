<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;

class ScheduleShowAction
{
    public function handle($id)
    {
        $query = Schedule::query()
            ->where('schedules.id', $id)
            ->with(['students']);

        $query->addSelect([
            $query->qualifyColumn('*'),
            'uc.name as created_by_name',
            'uu.name as updated_by_name',
        ]);

        $query->leftJoin('users as uc', 'uc.id', '=', $query->qualifyColumn('created_by'))
            ->leftJoin('users as uu', 'uu.id', '=', $query->qualifyColumn('updated_by'));


        return $query->first();
    }
}