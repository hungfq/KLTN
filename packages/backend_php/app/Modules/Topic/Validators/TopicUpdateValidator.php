<?php

namespace App\Modules\Topic\Validators;

use App\Modules\Topic\DTO\TopicUpdateDTO;
use App\Validators\AbstractValidator;

class TopicUpdateValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
            'code' => 'nullable',
            'title' => 'required',
            'description' => 'nullable',
            'limit' => 'required',
            'thesisDefenseDate' => 'nullable',
            'scheduleId' => 'required',
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
        return TopicUpdateDTO::fromRequest();
    }
}
