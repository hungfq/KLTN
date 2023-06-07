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
        if (!$this->committee) {
//            throw new UserException("Committee not found!");
            throw new UserException("Hội đồng không ồn tại!", 400);
        }

        if ($this->dto->president_id) {
            $president = User::role(Role::ROLE_LECTURER)->find($this->dto->president_id);
            if (!$president) {
//                throw new UserException("President not found!");
                throw new UserException("Chủ tịch hội đồng không ồn tại!", 400);
            }
            $this->committee->president_id = $this->dto->president_id;
        }

        if ($this->dto->secretary_id) {
            $secretary = User::role(Role::ROLE_LECTURER)->find($this->dto->secretary_id);
            if (!$secretary) {
//                throw new UserException("Secretary not found!");
                throw new UserException("Thư ký hội đồng không ồn tại!", 400);
            }
            $this->committee->secretary_id = $this->dto->secretary_id;
        }

        if ($this->dto->critical_id) {
            $critical = User::role(Role::ROLE_LECTURER)->find($this->dto->critical_id);
            if (!$critical) {
//                throw new UserException("Critical not found!");
                throw new UserException("GVPB không ồn tại!", 400);
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
        foreach (data_get($this->committee, 'topics', []) as $topic) {
            $topic->committee_id = null;
            $topic->save();
        }

        foreach (data_get($this->dto, 'topics', []) as $topicId) {
            $topic = Topic::find($topicId);
            if (!$topic) {
//                throw new UserException("Topic not found!");
                throw new UserException("Đề tài không tồn tại!", 400);
            }

            $topic->committee_id = $this->committee->id;
            $topic->save();
        }

        return $this;
    }
}
