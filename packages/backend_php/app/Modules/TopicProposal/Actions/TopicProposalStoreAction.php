<?php

namespace App\Modules\TopicProposal\Actions;

use App\Entities\Role;
use App\Entities\TopicProposal;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use App\Modules\TopicProposal\DTO\TopicProposalStoreDTO;

class TopicProposalStoreAction
{
    public TopicProposalStoreDTO $dto;
    public $topic;
    public $studentIds;
    public $lecturer;

    /***
     * @param TopicProposalStoreDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->createTopic()
            ->addNotification();
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

        if ($lecturerId = $this->dto->lecturer_id) {
            $this->lecturer = User::role(Role::ROLE_LECTURER)->find($lecturerId);
            if (!$this->lecturer) {
                throw new UserException("Lecturer not found!");
            }
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

    protected function addNotification()
    {
        if ($this->lecturer) {
            $this->lecturer->notifications()->create([
                'title' => 'YÊU CẦU HƯỚNG DẪN',
                'message' => "Bạn được yêu cầu hướng dẫn trong một đề tài.",
            ]);
            Socket::sendUpdateNotificationRequest([data_get($this->lecturer, 'id')]);
        }
    }
}
