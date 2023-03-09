<?php

namespace App\Modules\TopicProposal\Actions;

use App\Entities\Role;
use App\Entities\TopicProposal;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\TopicProposal\DTO\TopicProposalUpdateDTO;

class TopicProposalUpdateAction
{
    public TopicProposalUpdateDTO $dto;
    public $topicProposal;
    public $studentIds;

    /***
     * @param TopicProposalUpdateDTO $dto
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->updateTopic();
    }

    protected function checkData()
    {
        $this->topicProposal = TopicProposal::find($this->dto->id);
        if (!$this->topicProposal) {
            throw new UserException('Topic proposal not found');
        }

        foreach (data_get($this->dto, 'students', []) as $studentCode) {
            $student = User::role(Role::ROLE_STUDENT)->where('code', $studentCode)->first();
            if (!$student) {
                throw new UserException("Student not found!");
            }
            $this->studentIds[] = $student->id;
        }

        return $this;
    }

    protected function updateTopic()
    {
        $this->topicProposal->update($this->dto->all());
        $this->topicProposal->students()->sync($this->studentIds);
        $this->topicProposal->save();
        return $this;
    }
}
