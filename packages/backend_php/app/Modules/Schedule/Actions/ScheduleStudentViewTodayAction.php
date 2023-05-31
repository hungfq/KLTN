<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Modules\Schedule\Transformers\ScheduleViewTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ScheduleStudentViewTodayAction
{
    public function handle()
    {
        $now = Carbon::now('UTC');

        $proposal = Schedule::query()
            ->with(['students'])
            ->whereHas('students', function ($q) {
                $q->where('id', '=', Auth::id());
            })
            ->whereDate('proposal_start', '<=', $now)
            ->whereDate('proposal_end', '>=', $now)
            ->get();

        $approve = Schedule::query()
            ->with(['students'])
            ->whereDate('approve_start', '<=', $now)
            ->whereDate('approve_end', '>=', $now)
            ->get();

        $register = Schedule::query()
            ->with(['students'])
            ->whereHas('students', function ($q) {
                $q->where('id', '=', Auth::id());
            })
            ->whereDate('register_start', '<=', $now)
            ->whereDate('register_end', '>=', $now)
            ->get();

        $mark = Schedule::query()
            ->with(['students'])
            ->whereDate('mark_start', '<=', $now)
            ->whereDate('mark_end', '>=', $now)
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