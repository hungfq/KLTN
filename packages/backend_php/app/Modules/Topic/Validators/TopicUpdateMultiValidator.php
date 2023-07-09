<?php

namespace App\Modules\Topic\Validators;

use App\Modules\Topic\DTO\TopicUpdateMultiDTO;
use App\Validators\AbstractValidator;

class TopicUpdateMultiValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'critical_id' => 'required',
            'topic_ids' => 'required|array',
        ];
    }

    public function toDTO()
    {
        return TopicUpdateMultiDTO::fromRequest();
    }
}
