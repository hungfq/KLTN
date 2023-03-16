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
            'advisorLecturerGrade' => 'nullable|numeric|min:0|max:10',
            'criticalLecturerGrade' => 'nullable|numeric|min:0|max:10',
            'committeePresidentGrade' => 'nullable|numeric|min:0|max:10',
            'committeeSecretaryGrade' => 'nullable|numeric|min:0|max:10',
            'students' => 'nullable|array',
        ];
    }

    public function toDTO()
    {
        return TopicUpdateDTO::fromRequest();
    }
}
