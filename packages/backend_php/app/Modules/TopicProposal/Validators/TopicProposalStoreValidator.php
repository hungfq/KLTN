<?php

namespace App\Modules\TopicProposal\Validators;

use App\Modules\TopicProposal\DTO\TopicProposalStoreDTO;
use App\Validators\AbstractValidator;

class TopicProposalStoreValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'code' => 'nullable',
            'title' => 'required',
            'description' => 'nullable',
            'limit' => 'required',
            'scheduleId' => 'nullable',
            'lecturerId' => 'nullable',

            'students' => 'nullable|array',
        ];
    }

    public function toDTO()
    {
        return TopicProposalStoreDTO::fromRequest();
    }
}
