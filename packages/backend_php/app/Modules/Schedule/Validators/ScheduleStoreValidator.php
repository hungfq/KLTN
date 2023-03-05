<?php

namespace App\Modules\Schedule\Validators;

use App\Modules\Schedule\DTO\ScheduleStoreDTO;
use App\Validators\AbstractValidator;

class ScheduleStoreValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
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
        return ScheduleStoreDTO::fromRequest();
    }
}
