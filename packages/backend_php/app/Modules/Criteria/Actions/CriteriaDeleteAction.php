<?php

namespace App\Modules\Criteria\Actions;

use App\Entities\Criteria;
use App\Exceptions\UserException;

class CriteriaDeleteAction
{
    public $criteria;

    public function handle($id)
    {
        $this->criteria = Criteria::query()->find($id);
        if (!$this->criteria) {
            throw new UserException('Criteria not found');
        }

        $this->criteria->delete();
    }
}
