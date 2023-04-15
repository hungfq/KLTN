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
            ->with(['president', 'secretary', 'critical', 'topics']);

        $query->addSelect([
            $query->qualifyColumn('*'),
            'uc.name as created_by_name',
            'uu.name as updated_by_name',
        ]);

        $query->leftJoin('users as uc', 'uc.id', '=', $query->qualifyColumn('created_by'))
            ->leftJoin('users as uu', 'uu.id', '=', $query->qualifyColumn('updated_by'));

        if ($search = $dto->search) {
            $query->where('committees.code', 'LIKE', "%$search%")
                ->orWhere('committees.name', 'LIKE', "%$search%");
        }

        if ($presidentId = $dto->presidentId) {
            $query->where('president_id', $presidentId);
        }

        if ($secretaryId = $dto->secretaryId) {
            $query->where('secretary_id', $secretaryId);
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