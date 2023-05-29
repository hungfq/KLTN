<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Modules\Schedule\Transformers\ScheduleViewTransformer;

class ScheduleStudentViewTodayAction
{
    public function handle()
    {
        $proposal = Schedule::query()
            ->with(['students'])
            ->whereDate('proposal_start', '<=', date('Y-m-d H:i:s', time()))
            ->whereDate('proposal_end', '>=', date('Y-m-d H:i:s', time()))
            ->get();

        $approve = Schedule::query()
            ->with(['students'])
            ->whereDate('approve_start', '<=', date('Y-m-d H:i:s', time()))
            ->whereDate('approve_end', '>=', date('Y-m-d H:i:s', time()))
            ->get();

        $register = Schedule::query()
            ->with(['students'])
            ->whereDate('register_start', '<=', date('Y-m-d H:i:s', time()))
            ->whereDate('register_end', '>=', date('Y-m-d H:i:s', time()))
            ->get();

        $mark = Schedule::query()
            ->with(['students'])
            ->whereDate('mark_start', '<=', date('Y-m-d H:i:s', time()))
            ->whereDate('mark_end', '>=', date('Y-m-d H:i:s', time()))
            ->get();

        $transform = new ScheduleViewTransformer();
        $proposalTransform = $proposal->map(function ($dtl) use ($transform) {
            return $transform->transform($dtl);
        });
        $approveTransform = $approve->map(function ($dtl) use ($transform) {
            return $transform->transform($dtl);
        });
        $registerTransform = $register->map(function ($dtl) use ($transform) {
            return $transform->transform($dtl);
        });
        $markTransform = $mark->map(function ($dtl) use ($transform) {
            return $transform->transform($dtl);
        });

        return [
            'proposal' => $proposalTransform,
            'approve' => $approveTransform,
            'register' => $registerTransform,
            'mark' => $markTransform,
        ];
    }
}