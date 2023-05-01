<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Libraries\Helpers;
use App\Modules\Topic\DTO\TopicViewDTO;
use Illuminate\Support\Facades\Auth;

class TopicViewAction
{
    /**
     * @param $dto TopicViewDTO
     */
    public function handle($dto)
    {
        $query = Topic::query()
            ->with(['students', 'schedule', 'lecturer', 'critical'])
            ->leftJoin('committees', 'committees.id', '=', 'topics.committee_id');

        $query->addSelect([
            $query->qualifyColumn('*'),
            'uc.name as created_by_name',
            'uu.name as updated_by_name',
        ]);

        $query->leftJoin('users as uc', 'uc.id', '=', $query->qualifyColumn('created_by'))
            ->leftJoin('users as uu', 'uu.id', '=', $query->qualifyColumn('updated_by'));

        if ($search = $dto->search) {
            $query->where('topics.code', 'LIKE', "%$search%")
                ->orWhere('topics.title', 'LIKE', "%$search%")
                ->orWhere('topics.description', 'LIKE', "%$search%");
        }

        if ($lecturerId = $dto->lecturerId) {
            $query->where('topics.lecturer_id', $lecturerId);
        }

        if ($scheduleId = $dto->scheduleId) {
            $query->where('schedule_id', $scheduleId);
        }

        if ($criticalId = $dto->criticalId) {
            $query->where('topics.critical_id', $criticalId);
        }

        if ($dto->is_lecturer_approve) {
            $query->where('lecturer_id', Auth::id())
                ->whereNull('lecturer_approved');
        }

        if ($dto->is_critical_approve) {
            $query->where('topics.critical_id', Auth::id())
                ->whereNull('critical_approved');
        }

        if ($dto->as_least_lecturer_approve) {
            $query->where('lecturer_approved', true);
        }

        if ($dto->is_lecturer_mark) {
            $query->where('topics.lecturer_id', Auth::id());
        }

        if ($dto->is_critical_mark) {
            $query->where('topics.critical_id', Auth::id());
        }

        if ($dto->is_president_mark) {
            $query->where('committees.president_id', Auth::id());
        }

        if ($dto->is_secretary_mark) {
            $query->where('committees.secretary_id', Auth::id());
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