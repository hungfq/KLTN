<?php

namespace App\Modules\Task\Validators;

use App\Modules\Task\DTO\TaskStoreDTO;
use App\Validators\AbstractValidator;

class TaskStoreValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'topicId' => 'required',
            'code' => 'nullable',
            'title' => 'required',
            'description' => 'nullable',
            'status' => 'required',
            'assignTo' => 'nullable',
        ];
    }

    public function toDTO()
    {
        return TaskStoreDTO::fromRequest();
    }
}
