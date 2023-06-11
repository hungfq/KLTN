<?php

namespace App\Modules\Committee\Validators;

use App\Modules\Committee\DTO\CommitteeUpdateTopicDTO;
use App\Validators\AbstractValidator;

class CommitteeUpdateTopicValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
            'topics' => 'nullable|array',
        ];
    }

    public function toDTO()
    {
        return CommitteeUpdateTopicDTO::fromRequest();
    }
}
