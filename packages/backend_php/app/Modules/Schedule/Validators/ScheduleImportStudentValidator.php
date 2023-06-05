<?php

namespace App\Modules\Schedule\Validators;

use App\Modules\Schedule\DTO\ScheduleImportStudentDTO;
use App\Validators\AbstractValidator;

class
ScheduleImportStudentValidator extends AbstractValidator
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
        return ScheduleImportStudentDTO::fromRequest();
    }
}
