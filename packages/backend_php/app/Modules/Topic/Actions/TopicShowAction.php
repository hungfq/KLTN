<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;

class TopicShowAction
{
    public function handle($id)
    {
        $query = Topic::query()
            ->where('topics.id', $id)
            ->with(['students', 'schedule', 'lecturer', 'critical']);
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