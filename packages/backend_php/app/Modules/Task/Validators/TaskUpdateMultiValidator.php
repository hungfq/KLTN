<?php

namespace App\Modules\Task\Validators;

use App\Modules\Task\DTO\TaskUpdateMultiDTO;
use App\Validators\AbstractValidator;

class TaskUpdateMultiValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'tasks' => 'array|required',
            'tasks.*.id' => 'required',
            'tasks.*.code' => 'nullable',
            'tasks.*.title' => 'required',
            'tasks.*.description' => 'nullable',
            'tasks.*.status' => 'required',
            'tasks.*.assignTo' => 'nullable',
        ];
    }

    public function toDTO()
    {
        return TaskUpdateMultiDTO::fromRequest();
    }
}
