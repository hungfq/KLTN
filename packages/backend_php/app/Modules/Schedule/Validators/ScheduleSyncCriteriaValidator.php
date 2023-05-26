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
            'advisor_score_rate' => 'required|numeric|min:1',
            'critical_score_rate' => 'required|numeric|min:1',
            'president_score_rate' => 'required|numeric|min:1',
            'secretary_score_rate' => 'required|numeric|min:1',
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
