<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Modules\Topic\DTO\TopicMarkDTO;
use Illuminate\Support\Facades\Auth;

class TopicMarkAction
{
    public TopicMarkDTO $dto;
    public $topic;
    public $studentIds;

    /**
     * @param TopicMarkDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->updateMark();
    }

    protected function checkData()
    {
        $this->topic = Topic::find($this->dto->id);

        if (!$this->topic) {
//            throw new UserException("Topic not found!");
            throw new UserException('Đề tài không tồn tại trong hệ thống!', 400);
        }

        return $this;
    }

    protected function updateMark()
    {
        if ($this->dto->lecturer_grade) {
            if (data_get($this->topic, 'lecturer_id') != Auth::id()) {
//                throw new UserException("You not are lecturer this topic");
                throw new UserException('Bạn không phải GVHD của đề tài này!', 400);
            }

            $this->topic->lecturer_grade = $this->dto->lecturer_grade;
        }

        if ($this->dto->critical_grade) {
            if (data_get($this->topic, 'critical_id') != Auth::id()) {
//                throw new UserException("You not are critical this topic");
                throw new UserException('Bạn không phải GVPB của đề tài này!', 400);
            }

            $this->topic->critical_grade = $this->dto->critical_grade;
        }

        if ($this->dto->committee_president_grade) {
            if (data_get($this->topic, 'committee.president_id') != Auth::id()) {
//                throw new UserException("You not are committee president this topic");
                throw new UserException('Bạn không Chủ tịch hội đồng của đề tài này!', 400);
            }

            $this->topic->committee_president_grade = $this->dto->committee_president_grade;
        }

        if ($this->dto->committee_secretary_grade) {
            if (data_get($this->topic, 'committee.secretary_id') != Auth::id()) {
//                throw new UserException("You not are committee secretary this topic");
                throw new UserException('Bạn không Thư ký hội đồng của đề tài này!', 400);
            }

            $this->topic->committee_secretary_grade = $this->dto->committee_secretary_grade;
        }

        $this->topic->save();

        return $this;
    }
}
