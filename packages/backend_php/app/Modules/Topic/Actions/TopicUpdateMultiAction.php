<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Schedule;
use App\Entities\Topic;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Topic\DTO\TopicUpdateMultiDTO;

class TopicUpdateMultiAction
{
    public TopicUpdateMultiDTO $dto;

    /***
     * @param TopicUpdateMultiDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->updateTopics();
    }

    protected function checkData()
    {
        foreach ($this->dto->topic_ids as $topicId) {
            $topic = Topic::find($topicId);
            if (!$topic) {
//            throw new UserException("Topic not found!");
                throw new UserException('Đề tài không tồn tại trong hệ thống!', 400);
            }
        }

        $critical = User::find($this->dto->critical_id);
        if (!$critical) {
//            throw new UserException("User not found!");
            throw new UserException('GVPB không tồn tại trong hệ thống!', 400);
        }

        $schedule = Schedule::find($this->dto->schedule_id);
        if (!$schedule) {
//            throw new UserException("Schedule not found!");
            throw new UserException('Đợt đăng ký không tồn tại trong hệ thống!', 400);
        }

        return $this;
    }

    protected function updateTopics()
    {
        Topic::query()
            ->where('critical_id', $this->dto->critical_id)
            ->where('schedule_id', $this->dto->schedule_id)
            ->update([
                'critical_id' => null,
            ]);

        Topic::query()
            ->whereIn('id', $this->dto->topic_ids)
            ->update([
                'critical_id' => $this->dto->critical_id,
            ]);

        return $this;
    }
}
