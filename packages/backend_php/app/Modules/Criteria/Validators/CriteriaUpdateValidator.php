<?php

namespace App\Modules\Criteria\Validators;

use App\Modules\Criteria\DTO\CriteriaUpdateDTO;
use App\Validators\AbstractValidator;

class CriteriaUpdateValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
            'title' => 'required',
            'description' => 'nullable',
        ];
    }

    public function toDTO()
    {
        return CriteriaUpdateDTO::fromRequest();
    }
}
