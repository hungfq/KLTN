<?php

namespace App\Modules\Criteria\Actions;

use App\Entities\Criteria;
use App\Exceptions\UserException;
use App\Modules\Criteria\DTO\CriteriaUpdateDTO;

class CriteriaUpdateAction
{
    public CriteriaUpdateDTO $dto;
    public $criteria;

    /***
     * @param CriteriaUpdateDTO $dto
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->updateCriteria();
    }

    protected function checkData()
    {
        $this->criteria = Criteria::query()->find($this->dto->id);
        if (!$this->criteria) {
//            throw new UserException('Criteria not found');
            throw new UserException('Tiêu chí không tồn tại!', 400);
        }

        return $this;
    }

    protected function updateCriteria()
    {
        $this->criteria->update($this->dto->all());
        return $this;
    }
}
