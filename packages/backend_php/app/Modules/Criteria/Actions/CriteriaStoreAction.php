<?php

namespace App\Modules\Criteria\Actions;

use App\Entities\Criteria;
use App\Modules\Criteria\DTO\CriteriaStoreDTO;

class CriteriaStoreAction
{
    public CriteriaStoreDTO $dto;

    /***
     * @param CriteriaStoreDTO $dto
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->createCriteria();
    }

    protected function createCriteria()
    {
        Criteria::create($this->dto->all());
        return $this;
    }
}
