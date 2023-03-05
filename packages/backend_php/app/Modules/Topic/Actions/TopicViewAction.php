<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Modules\Topic\DTO\TopicViewDTO;

class TopicViewAction
{
    /**
     * @param $dto TopicViewDTO
     */
    public function handle($dto)
    {
        $query = Topic::query()
            ->with(['students', 'schedule', 'lecturer', 'critical', 'createdBy', 'updatedBy'])
            ->orderBy('created_at', 'DESC');

        if ($search = $dto->search) {
            $query->where('code', $search)
                ->orWhere('name', $search);
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

        if ($dto->limit) {
            return $query->paginate($dto->limit);
        }

        return $query->get();
    }
}