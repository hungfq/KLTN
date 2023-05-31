<?php

namespace App\Modules\Topic\Validators;

use App\Modules\Topic\DTO\TopicUpdateStudentDTO;
use App\Validators\AbstractValidator;

class TopicUpdateStudentValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
            'students' => 'nullable|array',
        ];
    }

    public function toDTO()
    {
        return TopicUpdateStudentDTO::fromRequest();
    }
}
