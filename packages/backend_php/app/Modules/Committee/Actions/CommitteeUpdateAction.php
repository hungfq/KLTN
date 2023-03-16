<?php

namespace App\Modules\Committee\Actions;

use App\Entities\Committee;
use App\Entities\Role;
use App\Entities\Topic;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Committee\DTO\CommitteeUpdateDTO;

class CommitteeUpdateAction
{
    public CommitteeUpdateDTO $dto;
    public $committee;

    /***
     * @param CommitteeUpdateDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->updateCommittee()
            ->updateTopics();
    }

    protected function checkData()
    {
        $this->committee = Committee::find($this->dto->id);

        if ($this->dto->president_id) {
            $president = User::role(Role::ROLE_LECTURER)->find($this->dto->president_id);
            if (!$president) {
                throw new UserException("President not found!");
            }
            $this->committee->president_id = $this->dto->president_id;
        }

        if ($this->dto->secretary_id) {
            $secretary = User::role(Role::ROLE_LECTURER)->find($this->dto->secretary_id);
            if (!$secretary) {
                throw new UserException("Secretary not found!");
            }
            $this->committee->secretary_id = $this->dto->secretary_id;
        }

        if ($this->dto->critical_id) {
            $critical = User::role(Role::ROLE_LECTURER)->find($this->dto->critical_id);
            if (!$critical) {
                throw new UserException("Critical not found!");
            }
            $this->committee->critical_id = $this->dto->critical_id;
        }

        return $this;
    }

    protected function updateCommittee()
    {
        if ($this->dto->code) {
            $this->committee->code = $this->dto->code;
        }
        if ($this->dto->name) {
            $this->committee->name = $this->dto->name;
        }
        $this->committee->save();

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
            $topic->save();
        }

        return $this;
    }
}
