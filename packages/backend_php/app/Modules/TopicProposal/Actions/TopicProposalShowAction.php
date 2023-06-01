<?php

namespace App\Modules\TopicProposal\Actions;

use App\Entities\TopicProposal;

class TopicProposalShowAction
{
    public function handle($id)
    {
        $query = TopicProposal::query()
            ->where('topic_proposals.id', $id)
            ->with([
                'students',
                'schedule',
                'lecturer',
            ]);

        $query->addSelect([
            $query->qualifyColumn('*'),
            'uc.name as created_by_name',
            'uu.name as updated_by_name',
        ]);

        $query->leftJoin('users as uc', 'uc.id', '=', $query->qualifyColumn('created_by'))
            ->leftJoin('users as uu', 'uu.id', '=', $query->qualifyColumn('updated_by'));

        return $query->first();
    }
}