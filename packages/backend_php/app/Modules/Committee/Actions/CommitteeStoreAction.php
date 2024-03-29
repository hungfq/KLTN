<?php

namespace App\Modules\Committee\Actions;

use App\Entities\Committee;
use App\Entities\Role;
use App\Entities\Schedule;
use App\Entities\Topic;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Committee\DTO\CommitteeStoreDTO;

class CommitteeStoreAction
{
    public CommitteeStoreDTO $dto;
    public $committee;
    public $schedule;

    /***
     * @param CommitteeStoreDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->createCommittee()
            ->syncTopics();
    }

    protected function checkData()
    {
        if ($this->dto->president_id) {
            $president = User::role(Role::ROLE_LECTURER)->find($this->dto->president_id);
            if (!$president) {
//                throw new UserException("President not found!");
                throw new UserException("Chủ tịch hội đồng không ồn tại!", 400);
            }
        }

        if ($this->dto->secretary_id) {
            $secretary = User::role(Role::ROLE_LECTURER)->find($this->dto->secretary_id);
            if (!$secretary) {
//                throw new UserException("Secretary not found!");
                throw new UserException("Thư ký hội đồng không ồn tại!", 400);
            }
        }

        if ($this->dto->critical_id) {
            $critical = User::role(Role::ROLE_LECTURER)->find($this->dto->critical_id);
            if (!$critical) {
//                throw new UserException("Critical not found!");
                throw new UserException("GVPB không ồn tại!", 400);
            }
        }

        $this->schedule = Schedule::find($this->dto->schedule_id);
        if (!$this->schedule) {
//                throw new UserException("Schedule not found!");
            throw new UserException("Đợt đăng ký không ồn tại!", 400);
        }

        return $this;
    }

    protected function createCommittee()
    {
        $this->dto->code = Committee::generateCommitteeCode($this->schedule);
        $this->committee = Committee::create($this->dto->all());

        return $this;
    }

    protected function syncTopics()
    {
        foreach (data_get($this->dto, 'topics', []) as $topicId) {
            $topic = Topic::find($topicId);
            if (!$topic) {
//                throw new UserException("Topic not found!");
                throw new UserException("Đề tài không ồn tại trong hệ thống!", 400);
            }

            $topic->committee_id = $this->committee->id;
            $topic->save();
        }

        return $this;
    }
}
