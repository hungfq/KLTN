<?php

namespace App\Modules\Committee\Validators;

use App\Modules\Committee\DTO\CommitteeImportTopicDTO;
use App\Validators\AbstractValidator;

class
CommitteeImportTopicValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'file' => [
                'file',
                'mimes:xls,xlsx',
                'required',
            ],
        ];
    }

    public function toDTO()
    {
        return CommitteeImportTopicDTO::fromRequest();
    }
}
