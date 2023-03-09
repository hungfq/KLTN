<?php

namespace App\Modules\Task\Actions;

use App\Entities\Task;

class TaskShowAction
{
    public function handle($id)
    {
        $query = Task::query()
            ->where('tasks.id', $id)
            ->with(['topic', 'assignee', 'comments']);
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