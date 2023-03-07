<?php

namespace App\Modules\Committee\Actions;

use App\Entities\Committee;
use App\Entities\Role;
use App\Entities\Topic;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Committee\DTO\CommitteeStoreDTO;

class CommitteeStoreAction
{
    public CommitteeStoreDTO $dto;
    public $committee;

    /***
     * @param CommitteeStoreDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->createCommittee()
            ->updateTopics();
    }

    protected function checkData()
    {
        if ($this->dto->president_id) {
            $president = User::role(Role::ROLE_LECTURER)->find($this->dto->president_id);
            if (!$president) {
                throw new UserException("President not found!");
            }
        }

        if ($this->dto->secretary_id) {
            $secretary = User::role(Role::ROLE_LECTURER)->find($this->dto->secretary_id);
            if (!$secretary) {
                throw new UserException("Secretary not found!");
            }
        }

        if ($this->dto->critical_id) {
            $critical = User::role(Role::ROLE_LECTURER)->find($this->dto->critical_id);
            if (!$critical) {
                throw new UserException("Critical not found!");
            }
        }

        return $this;
    }

    protected function createCommittee()
    {
        $this->committee = Committee::create($this->dto->all());

        return $this;
    }

    protected function updateTopics()
    {
        foreach (data_get($this->dto, 'topics', []) as $topicId) {
            $topic = Topic::find($topicId);
            if (!$topic) {
                throw new UserException("Topic not found!");
            }

            $topic->committee_id = $this->committee->id;
        }

        return $this;
    }
}
