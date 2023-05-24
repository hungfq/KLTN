<?php

namespace App\Modules\Schedule\Validators;

use App\Modules\Schedule\DTO\ScheduleSyncCriteriaDTO;
use App\Validators\AbstractValidator;

class
ScheduleSyncCriteriaValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required|integer',
            'details' => 'required|array',
            'details.*.criteria_id' => 'required|integer',
            'details.*.score_rate' => 'required|numeric|min:1',
        ];
    }

    public function toDTO()
    {
        return ScheduleSyncCriteriaDTO::fromRequest();
    }
}
