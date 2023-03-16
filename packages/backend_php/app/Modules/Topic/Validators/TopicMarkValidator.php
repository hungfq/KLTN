<?php

namespace App\Modules\Topic\Validators;

use App\Modules\Topic\DTO\TopicMarkDTO;
use App\Validators\AbstractValidator;

class TopicMarkValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
            'lecturer_grade' => 'nullable|numeric|min:0|max:10',
            'critical_grade' => 'nullable|numeric|min:0|max:10',
            'committee_president_grade' => 'nullable|numeric|min:0|max:10',
            'committee_secretary_grade' => 'nullable|numeric|min:0|max:10',
        ];
    }

    public function toDTO()
    {
        return TopicMarkDTO::fromRequest();
    }
}
