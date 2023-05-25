<?php

namespace App\Modules\Schedule\Validators;

use App\Modules\Schedule\DTO\ScheduleUpdateStudentDTO;
use App\Validators\AbstractValidator;

class ScheduleUpdateStudentValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
            'students' => 'nullable|array',
        ];
    }

    public function toDTO()
    {
        return ScheduleUpdateStudentDTO::fromRequest();
    }
}
