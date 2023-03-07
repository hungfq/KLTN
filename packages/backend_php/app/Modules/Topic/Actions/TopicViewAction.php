<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Libraries\Helpers;
use App\Modules\Topic\DTO\TopicViewDTO;

class TopicViewAction
{
    /**
     * @param $dto TopicViewDTO
     */
    public function handle($dto)
    {
        $query = Topic::query()
            ->with(['students', 'schedule', 'lecturer', 'critical']);

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

        if ($lecturerId = $dto->lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        }

        if ($scheduleId = $dto->scheduleId) {
            $query->where('schedule_id', $scheduleId);
        }

        if ($criticalId = $dto->criticalId) {
            $query->where('critical_id', $criticalId);
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