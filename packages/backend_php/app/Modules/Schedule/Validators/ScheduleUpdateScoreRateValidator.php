<?php

namespace App\Modules\Schedule\Validators;

use App\Modules\Schedule\DTO\ScheduleUpdateScoreRateDTO;
use App\Validators\AbstractValidator;

class ScheduleUpdateScoreRateValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
            'advisor_score_rate' => 'required|numeric|min:1',
            'critical_score_rate' => 'required|numeric|min:1',
            'president_score_rate' => 'required|numeric|min:1',
            'secretary_score_rate' => 'required|numeric|min:1',
        ];
    }

    public function toDTO()
    {
        return ScheduleUpdateScoreRateDTO::fromRequest();
    }
}
