<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Libraries\Helpers;
use App\Modules\Schedule\DTO\ScheduleViewDTO;

class ScheduleViewAction
{
    /**
     * @param $dto ScheduleViewDTO
     */
    public function handle($dto)
    {
        $query = Schedule::query()
            ->with(['students']);

        $query->addSelect([
            $query->qualifyColumn('*'),
            'uc.name as created_by_name',
            'uu.name as updated_by_name',
        ]);

        $query->leftJoin('users as uc', 'uc.id', '=', $query->qualifyColumn('created_by'))
            ->leftJoin('users as uu', 'uu.id', '=', $query->qualifyColumn('updated_by'));

        if ($search = $dto->search) {
            $query->where('code', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%");
        }

        if ($dto->is_approve_time) {
            $query->whereDate('approve_start', '<=', date('Y-m-d H:i:s', time()))
                ->whereDate('approve_end', '>=', date('Y-m-d H:i:s', time()));
        }

        Helpers::sortBuilder($query, $dto->toArray(), [
            'created_by_name' => 'uc.name',
            'updated_by_name' => 'uu.name',
        ]);

        if ($dto->limit) {
            return $query->paginate($dto->limit);
        }

        return $query->get();
    }
}