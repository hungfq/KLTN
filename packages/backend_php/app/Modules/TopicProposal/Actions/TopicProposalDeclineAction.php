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
        // TODO:

        return $this;
    }

    protected function deleteProposal()
    {
        $this->topicProposal->delete();

        return $this;
    }
}
