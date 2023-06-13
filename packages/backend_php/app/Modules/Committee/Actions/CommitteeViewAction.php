<?php

namespace App\Modules\Committee\Actions;

use App\Entities\Committee;
use App\Libraries\Helpers;
use App\Modules\Committee\DTO\CommitteeViewDTO;

class CommitteeViewAction
{
    /**
     * @param $dto CommitteeViewDTO
     */
    public function handle($dto)
    {
        $query = Committee::query()
            ->with(['president', 'secretary', 'critical', 'topics', 'schedule']);

        $query->addSelect([
            $query->qualifyColumn('*'),
            'uc.name as created_by_name',
            'uu.name as updated_by_name',
        ]);

        $query->leftJoin('users as uc', 'uc.id', '=', $query->qualifyColumn('created_by'))
            ->leftJoin('users as uu', 'uu.id', '=', $query->qualifyColumn('updated_by'));

        if ($search = $dto->search) {
            $query->where(function ($q) use ($search) {
                $q->where('committees.code', 'LIKE', "%$search%")
                    ->orWhere('committees.name', 'LIKE', "%$search%");
            });
        }

        if ($president_id = $dto->president_id) {
            $query->where('president_id', $president_id);
        }

        if ($secretary_id = $dto->secretary_id) {
            $query->where('secretary_id', $secretary_id);
        }

        if ($critical_id = $dto->critical_id) {
            $query->where('critical_id', $critical_id);
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