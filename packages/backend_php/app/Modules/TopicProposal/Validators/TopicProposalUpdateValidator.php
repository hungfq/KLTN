<?php

namespace App\Modules\TopicProposal\Validators;

use App\Modules\TopicProposal\DTO\TopicProposalUpdateDTO;
use App\Validators\AbstractValidator;

class TopicProposalUpdateValidator extends AbstractValidator
{
    public function rules($params = [])
    {
        return [
            'id' => 'required',
//            'code' => 'nullable',
            'title' => 'required',
            'description' => 'nullable',
            'limit' => 'required',
//            'scheduleId' => 'nullable',
            'lecturerId' => 'nullable',

            'students' => 'nullable|array',
        ];
    }

    public function toDTO()
    {
        return TopicProposalUpdateDTO::fromRequest();
    }
}
