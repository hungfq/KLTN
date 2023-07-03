<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Libraries\Helpers;
use App\Modules\Schedule\DTO\ScheduleViewWithTopicDTO;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ScheduleViewWithTopicAction
{
    /**
     * @param $dto ScheduleViewWithTopicDTO
     */
    public function handle($dto)
    {
        $now = Carbon::now('UTC')->format('Y-m-d H:i:s');

        $query = Schedule::query()
            ->with(['topics' => function ($q) use ($dto) {
                $q->when($dto->is_lecturer, function ($q1) {
                    $q1->where('lecturer_id', Auth::id());
                });
            }])
            ->whereHas('topics', function ($q) use ($dto) {
                $q->when($dto->is_lecturer, function ($q1) {
                    $q1->where('lecturer_id', Auth::id());
                });
            })
            ->where('proposal_start', '<=', $now)
            ->where('mark_start', '>=', $now);

        $query->addSelect([
            $query->qualifyColumn('*'),
            'uc.name as created_by_name',
            'uu.name as updated_by_name',
        ]);

        $query->leftJoin('users as uc', 'uc.id', '=', $query->qualifyColumn('created_by'))
            ->leftJoin('users as uu', 'uu.id', '=', $query->qualifyColumn('updated_by'));


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