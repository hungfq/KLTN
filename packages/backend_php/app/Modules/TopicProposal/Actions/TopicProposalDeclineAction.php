<?php

namespace App\Modules\TopicProposal\Actions;

use App\Entities\TopicProposal;
use App\Exceptions\UserException;

class TopicProposalDeclineAction
{
    public $id;
    public $topicProposal;

    public function handle($id)
    {
        $this->id = $id;

        $this->checkData()
            ->addNotification()
            ->deleteProposal();
    }

    protected function checkData()
    {
        $this->topicProposal = TopicProposal::query()->find($this->id);
        if (!$this->topicProposal) {
            throw new UserException('Topic proposal not found');
        }

        return $this;
    }

    protected function addNotification()
    {
        $title = data_get($this->topicProposal, 'title');

        $this->topicProposal->students->each(function ($student) use ($title) {
            $student->notifications()->create([
                'title' => 'DUYỆT YÊU CẦU HƯỚNG DẪN',
                'message' => "Đề tài $title đã bị từ chối.",
            ]);
        });

        return $this;
    }

    protected function deleteProposal()
    {
        $this->topicProposal->delete();

        return $this;
    }
}
