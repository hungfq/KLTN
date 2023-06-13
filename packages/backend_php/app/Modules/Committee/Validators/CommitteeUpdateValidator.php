<?php

namespace App\Modules\Committee\Validators;

use App\Modules\Committee\DTO\CommitteeUpdateDTO;
use App\Validators\AbstractValidator;

class CommitteeUpdateValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
            'schedule_id' => 'required',
//            'code' => 'nullable',
            'name' => 'nullable',
            'committeePresidentId' => 'nullable',
            'committeeSecretaryId' => 'nullable',
            'criticalLecturerId' => 'nullable',
//            'topics' => 'nullable|array',
        ];
    }

    public function toDTO()
    {
        return CommitteeUpdateDTO::fromRequest();
    }
}
