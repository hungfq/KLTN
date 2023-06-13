<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Schedule\DTO\ScheduleViewStudentDTO;

class ScheduleViewStudentAction
{
    /**
     * @param ScheduleViewStudentDTO $dto
     */
    public function handle($dto)
    {
        $schedule = Schedule::find($dto->id);
        if (!$schedule) {
//            throw new UserException("Schedule not found!");
            throw new UserException('Đợt đăng ký không tồn tại trong hệ thống!', 400);
        }

        $studentIds = [];
        foreach (data_get($schedule, 'students', []) as $student) {
            $studentIds[] = $student->id;
        }

        $query = User::query()
            ->whereIn('id', $studentIds);

        $ignoreIds = $dto->ignore_ids;

        if ($dto->not_in_any_proposal) {
            $proposalStudentIds = [];
            foreach ($schedule->topicProposals as $topicProposal) {
                foreach ($topicProposal->students as $student) {
                    $proposalStudentIds[] = $student->id;
                }
            }

            $query->whereNotIn('id', array_diff($proposalStudentIds, $ignoreIds));
        }

        if ($dto->not_in_any_topic) {
            $topicStudentIds = [];
            foreach ($schedule->topics as $topic) {
                foreach ($topic->students as $student) {
                    $topicStudentIds[] = $student->id;
                }
            }
            $query->whereNotIn('id', array_diff($topicStudentIds, $ignoreIds));
        }

        if ($dto->limit) {
            return $query->paginate($dto->limit);
        }

        return $query->get();
    }
}