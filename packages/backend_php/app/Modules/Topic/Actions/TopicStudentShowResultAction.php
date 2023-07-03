<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Modules\Topic\DTO\TopicViewDTO;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TopicStudentShowResultAction
{
    /**
     * @param $dto TopicViewDTO
     */
    public function handle($dto)
    {
        $query = Topic::query()
            ->with([
                'schedule',
                'lecturer',
                'critical',
                'committee.president',
                'committee.secretary',
                'committee.critical',
            ])
            ->whereHas('students', function ($q) {
                $q->where('id', Auth::id());
            });

        $query->addSelect([
            $query->qualifyColumn('*'),
            'uc.name as created_by_name',
            'uu.name as updated_by_name',
        ]);

        if ($dto->is_in_progress) {
            $query->whereHas('schedule', function ($q) {
                $now = Carbon::now('UTC')->format('Y-m-d H:i:s');
                $q->where('proposal_start', '<=', $now)
                    ->where('mark_start', '>=', $now);
            });
        }

        $query->leftJoin('users as uc', 'uc.id', '=', $query->qualifyColumn('created_by'))
            ->leftJoin('users as uu', 'uu.id', '=', $query->qualifyColumn('updated_by'));

        if ($dto->limit) {
            return $query->paginate($dto->limit);
        }

        return $query->get();
    }
}