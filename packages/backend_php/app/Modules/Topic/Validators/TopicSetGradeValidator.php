<?php

namespace App\Modules\Topic\Validators;

use App\Entities\Grade;
use App\Modules\Topic\DTO\TopicSetGradeDTO;
use App\Validators\AbstractValidator;
use Illuminate\Validation\Rule;

class
TopicSetGradeValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required|integer',
            'type' => 'required|string|'. Rule::in(array_keys(Grade::type())),
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
