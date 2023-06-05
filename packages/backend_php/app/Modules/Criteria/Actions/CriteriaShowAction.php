<?php

namespace App\Modules\Criteria\Actions;

use App\Entities\Criteria;

class CriteriaShowAction
{
    public $criteria;

    public function handle($id)
    {
        return Criteria::query()->find($id);
    }
}
