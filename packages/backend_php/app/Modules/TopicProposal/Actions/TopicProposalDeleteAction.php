<?php

namespace App\Modules\TopicProposal\Actions;

use App\Entities\TopicProposal;
use App\Exceptions\UserException;

class TopicProposalDeleteAction
{
    public $topicProposal;

    public function handle($id)
    {
        $this->topicProposal = TopicProposal::find($id);
        if (!$this->topicProposal) {
//            throw new UserException('Topic proposal not found');
            throw new UserException('Đề xuất không tồn tại trong hệ thống!', 400);
        }

        $this->topicProposal->delete();
    }
}
