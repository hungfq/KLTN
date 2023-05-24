<?php

namespace App\Modules\Topic\Validators;

use App\Modules\Topic\DTO\TopicSetGradeDTO;
use App\Validators\AbstractValidator;

class
TopicSetGradeValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required|integer',
            'details' => 'required|array',
            'details.*.criteria_id' => 'required|integer',
            'details.*.student_id' => 'required|integer',
            'details.*.score' => 'required|numeric',
        ];
    }

    public function toDTO()
    {
        return TopicSetGradeDTO::fromRequest();
    }
}
