<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Modules\Topic\DTO\TopicViewDTO;
use Illuminate\Support\Facades\Auth;

class TopicStudentShowResultAction
{
    /**
     * @param $dto TopicViewDTO
     */
    public function handle($dto)
    {
        $query = Topic::query()
            ->with(['schedule', 'lecturer', 'critical'])
            ->whereHas('students', function ($q) {
                $q->where('id', Auth::id());
            });

        $query->addSelect([
            $query->qualifyColumn('*'),
            'uc.name as created_by_name',
            'uu.name as updated_by_name',
        ]);

        $query->leftJoin('users as uc', 'uc.id', '=', $query->qualifyColumn('created_by'))
            ->leftJoin('users as uu', 'uu.id', '=', $query->qualifyColumn('updated_by'));

        if ($dto->limit) {
            return $query->paginate($dto->limit);
        }

        return $query->get();
    }
}