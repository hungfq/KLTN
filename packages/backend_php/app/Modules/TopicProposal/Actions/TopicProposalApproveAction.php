<?php

namespace App\Modules\TopicProposal\Actions;

use App\Entities\Topic;
use App\Entities\TopicProposal;
use App\Exceptions\UserException;
use App\Libraries\Socket;

class TopicProposalApproveAction
{
    public $id;
    public $studentIds;
    public $topicProposal;

    public function handle($id)
    {
        $this->id = $id;

        $this->checkData()
            ->createTopic()
            ->addNotification()
            ->deleteProposal();
    }

    protected function checkData()
    {
        $this->topicProposal = TopicProposal::query()->find($this->id);
        if (!$this->topicProposal) {
            throw new UserException('Topic proposal not found');
        }

        $this->studentIds = $this->topicProposal->students->map(function ($student) {
            return $student->id;
        });

        $studentAlreadyRegistered = Topic::query()
            ->where('schedule_id', $this->topicProposal->schedule_id)
            ->whereHas('students', function ($q) {
                $q->whereIn('id', $this->studentIds);
            })->exists();
        if ($studentAlreadyRegistered) {
            throw new UserException('Some student already has register in another topic');
        }

        return $this;
    }

    protected function createTopic()
    {
        $topicData = $this->topicProposal->replicate();
        $topicData = $topicData->toArray();
        $topicData['code'] = Topic::generateTopicCode($this->topicProposal->schedule);
        $topic = Topic::create($topicData);
        $topic->students()->sync($this->studentIds);


        return $this;
    }

    protected function addNotification()
    {
        $title = data_get($this->topicProposal, 'title');

        $this->topicProposal->students->each(function ($student) use ($title) {
            $data = [
                'title' => 'DUYỆT YÊU CẦU',
                'message' => "Đề tài $title đã được chấp thuận.",
            ];
            $student->notifications()->create($data);
            Socket::sendUpdateNotificationRequest([data_get($student, 'id')], $data);
        });

        return $this;
    }

    protected function deleteProposal()
    {
        $this->topicProposal->delete();

        return $this;
    }
}
