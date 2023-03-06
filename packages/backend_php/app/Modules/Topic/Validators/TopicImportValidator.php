<?php

namespace App\Modules\Topic\Validators;

use App\Modules\Topic\DTO\TopicImportDTO;
use App\Validators\AbstractValidator;

class TopicImportValidator extends AbstractValidator
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
        return TopicImportDTO::fromRequest();
    }
}
