<?php

namespace App\Modules\Criteria\Actions;

use App\Entities\Criteria;
use App\Libraries\Helpers;
use App\Modules\Criteria\DTO\CriteriaViewDTO;

class CriteriaViewAction
{
    /**
     * @param $dto CriteriaViewDTO
     */
    public function handle($dto)
    {
        $query = Criteria::query();

        $query->addSelect([
            $query->qualifyColumn('*'),
            'uc.name as created_by_name',
            'uu.name as updated_by_name',
        ]);

        $query->leftJoin('users as uc', 'uc.id', '=', $query->qualifyColumn('created_by'))
            ->leftJoin('users as uu', 'uu.id', '=', $query->qualifyColumn('updated_by'));

        if ($title = $dto->title) {
            $query->where('title', $title);
        }

        if ($description = $dto->description) {
            $query->where('description', $description);
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