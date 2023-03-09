<?php

namespace App\Modules\Task\Validators;

use App\Modules\Task\DTO\TaskUpdateDTO;
use App\Validators\AbstractValidator;

class TaskUpdateValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
            'code' => 'nullable',
            'title' => 'required',
            'description' => 'nullable',
            'status' => 'required',
            'assignTo' => 'nullable',
        ];
    }

    public function toDTO()
    {
        return TaskUpdateDTO::fromRequest();
    }
}
