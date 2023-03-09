<?php

namespace App\Modules\Task\Validators;

use App\Modules\Task\DTO\TaskCommentAddDTO;
use App\Validators\AbstractValidator;

class TaskCommentAddValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
            'message' => 'required',
        ];
    }

    public function toDTO()
    {
        return TaskCommentAddDTO::fromRequest();
    }
}