<?php

namespace App\Modules\TopicProposal\Actions;

use App\Entities\TopicProposal;
use App\Libraries\Helpers;
use App\Modules\TopicProposal\DTO\TopicProposalViewDTO;
use Illuminate\Support\Facades\Auth;

class TopicProposalViewAction
{
    /**
     * @param TopicProposalViewDTO $dto
     */
    public function handle($dto)
    {
        $query = TopicProposal::query()
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

        if ($search = $dto->search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%");
            });
        }

        if ($lecturerId = $dto->lecturerId) {
            $query->where('lecturer_id', $lecturerId);
        }

        if ($scheduleId = $dto->scheduleId) {
            $query->where('schedule_id', $scheduleId);
        }

        if ($dto->is_created) {
            $query->where('topic_proposals.created_by', Auth::id());
        }

        if ($dto->is_lecturer) {
            $query->where('topic_proposals.lecturer_id', Auth::id());
        }

        Helpers::sortBuilder($query, $dto->toArray(), [
            'created_by_name' => 'uc.name',
            'updated_by_name' => 'uu.name',
        ]);

        if ($dto->limit) {
            return $query->paginate($dto->limit);
        }

        return $query->get();
    }
}