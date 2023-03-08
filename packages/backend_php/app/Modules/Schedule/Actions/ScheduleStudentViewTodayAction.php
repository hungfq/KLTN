<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Modules\Schedule\Transformers\ScheduleViewTransformer;

class ScheduleStudentViewTodayAction
{
    public function handle()
    {
//        dd(date('Y-m-d H:i:s', time()));
        $proposal = Schedule::query()
            ->whereDate('proposal_start', '<=', date('Y-m-d H:i:s', time()))
            ->whereDate('proposal_end', '>=', date('Y-m-d H:i:s', time()))
            ->get();

        $register = Schedule::whereDate('register_start', '<=', date('Y-m-d H:i:s', time()))
            ->whereDate('register_end', '>=', date('Y-m-d H:i:s', time()))
            ->get();

        $transform = new ScheduleViewTransformer();
        $proposalTransform = $proposal->map(function ($dtl) use ($transform) {
            return $transform->transform($dtl);
        });
        $registerTransform = $register->map(function ($dtl) use ($transform) {
            return $transform->transform($dtl);
        });

        return [
            'proposal' => $proposalTransform,
            'register' => $registerTransform,
        ];
    }
}