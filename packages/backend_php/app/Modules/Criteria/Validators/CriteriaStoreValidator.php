<?php

namespace App\Modules\Criteria\Validators;

use App\Modules\Criteria\DTO\CriteriaStoreDTO;
use App\Validators\AbstractValidator;

class CriteriaStoreValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'title' => 'required',
            'description' => 'nullable',
        ];
    }

    public function toDTO()
    {
        return CriteriaStoreDTO::fromRequest();
    }
}
