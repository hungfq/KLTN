<?php

namespace App\Modules\TopicProposal\Actions;

use App\Entities\Role;
use App\Entities\TopicProposal;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\TopicProposal\DTO\TopicProposalStoreDTO;

class TopicProposalStoreAction
{
    public TopicProposalStoreDTO $dto;
    public $topic;
    public $studentIds;

    /***
     * @param TopicProposalStoreDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->createTopic();
    }

    protected function checkData()
    {
        foreach (data_get($this->dto, 'students', []) as $studentCode) {
            $student = User::role(Role::ROLE_STUDENT)->where('code', $studentCode)->first();
            if (!$student) {
                throw new UserException("Student not found!");
            }
            $this->studentIds[] = $student->id;
        }

        return $this;
    }

    protected function createTopic()
    {
        $this->topic = TopicProposal::create($this->dto->all());
        $this->topic->students()->sync($this->studentIds);
        $this->topic->save();
        return $this;
    }
}
