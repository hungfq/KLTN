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
//            throw new UserException('Criteria not found');
            throw new UserException('Tiêu chí không tồn tại!', 400);
        }

        $this->criteria->delete();
    }
}
