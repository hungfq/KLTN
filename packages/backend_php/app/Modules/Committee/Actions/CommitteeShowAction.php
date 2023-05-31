<?php

namespace App\Modules\Committee\Actions;

use App\Entities\Committee;

class
CommitteeShowAction
{
    public function handle($id)
    {
        $query = Committee::query()
            ->where('committees.id', $id)
            ->with(['president', 'secretary', 'critical', 'topics']);

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