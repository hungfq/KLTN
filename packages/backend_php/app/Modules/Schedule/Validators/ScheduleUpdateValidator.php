<?php

namespace App\Modules\Schedule\Validators;

use App\Modules\Schedule\DTO\ScheduleUpdateDTO;
use App\Validators\AbstractValidator;

class ScheduleUpdateValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
            'code' => 'required|string',
            'name' => 'required|string',
            'description' => 'nullable',
            'startDate' => 'nullable',
            'deadline' => 'nullable',
            'startProposalDate' => 'nullable',
            'endProposalDate' => 'nullable',
            'startApproveDate' => 'nullable',
            'endApproveDate' => 'nullable',
            'startRegisterDate' => 'nullable',
            'endRegisterDate' => 'nullable',
            'students' => 'nullable|array',
        ];
    }

    public function toDTO()
    {
        return ScheduleUpdateDTO::fromRequest();
    }
}
