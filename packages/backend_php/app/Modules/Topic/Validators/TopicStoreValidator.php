<?php

namespace App\Modules\Topic\Validators;

use App\Modules\Topic\DTO\TopicStoreDTO;
use App\Validators\AbstractValidator;

class TopicStoreValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'code' => 'required',
            'title' => 'required',
            'description' => 'nullable',
            'limit' => 'required',
            'thesisDefenseDate' => 'nullable',
            'scheduleId' => 'nullable',
            'lecturerId' => 'nullable',
            'criticalLecturerId' => 'nullable',
            'advisorLecturerGrade' => 'nullable',
            'criticalLecturerGrade' => 'nullable',
            'committeePresidentGrade' => 'nullable',
            'committeeSecretaryGrade' => 'nullable',
            'students' => 'nullable|array',
        ];
    }

    public function toDTO()
    {
        return TopicStoreDTO::fromRequest();
    }
}
