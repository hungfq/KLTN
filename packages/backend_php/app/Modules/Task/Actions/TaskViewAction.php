<?php

namespace App\Modules\Task\Actions;

use App\Entities\Task;
use App\Libraries\Helpers;
use App\Modules\Task\DTO\TaskViewDTO;

class TaskViewAction
{
    /**
     * @param $dto TaskViewDTO
     */
    public function handle($dto)
    {
        $query = Task::query()
            ->with(['assignee', 'comments']);

        $query->addSelect([
            $query->qualifyColumn('*'),
            'uc.name as created_by_name',
            'uu.name as updated_by_name',
        ]);

        $query->leftJoin('users as uc', 'uc.id', '=', $query->qualifyColumn('created_by'))
            ->leftJoin('users as uu', 'uu.id', '=', $query->qualifyColumn('updated_by'));

        if ($status = $dto->status) {
            $query->where('status', $status);
        }

        if ($topicId = $dto->topic_id) {
            $query->where('topic_id', $topicId);
        }

        if ($assigneeId = $dto->assignee_id) {
            $query->where('assignee_id', $assigneeId);
        }

        if ($fromTime = $dto->from_time) {
            $query->where('tasks.created_at', '>=', $fromTime);
        }

        if ($toTime = $dto->to_time) {
            $query->where('tasks.created_at', '<=', $toTime);
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