<?php

namespace App\Modules\Committee\Actions;

use App\Entities\Committee;
use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Modules\Committee\DTO\CommitteeUpdateTopicDTO;

class CommitteeUpdateTopicAction
{
    public CommitteeUpdateTopicDTO $dto;
    public $committee;

    /***
     * @param CommitteeUpdateTopicDTO $dto
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
        $this->committee = Committee::find($this->dto->id);
        if (!$this->committee) {
//            throw new UserException("Committee not found!");
            throw new UserException("Hội đồng không ồn tại!", 400);
        }

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
