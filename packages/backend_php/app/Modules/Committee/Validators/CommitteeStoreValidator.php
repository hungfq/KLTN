<?php

namespace App\Modules\Committee\Validators;

use App\Modules\Committee\DTO\CommitteeStoreDTO;
use App\Validators\AbstractValidator;

class CommitteeStoreValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'schedule_id' => 'required',
//            'code' => 'required',
            'name' => 'required',
            'committeePresidentId' => 'nullable',
            'committeeSecretaryId' => 'nullable',
            'criticalLecturerId' => 'nullable',
            'topics' => 'nullable|array',
        ];
    }

    public function toDTO()
    {
        return CommitteeStoreDTO::fromRequest();
    }
}
