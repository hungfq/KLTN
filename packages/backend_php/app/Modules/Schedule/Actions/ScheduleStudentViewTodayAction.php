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
        $now = Carbon::now('UTC')->format('Y-m-d H:i:s');

        $proposal = Schedule::query()
            ->with(['students'])
            ->whereHas('students', function ($q) {
                $q->where('id', '=', Auth::id());
            })
            ->where('proposal_start', '<=', $now)
            ->where('proposal_end', '>=', $now)
            ->get();

        $approve = Schedule::query()
            ->with(['students'])
            ->where('approve_start', '<=', $now)
            ->where('approve_end', '>=', $now)
            ->get();

        $register = Schedule::query()
            ->with(['students'])
            ->whereHas('students', function ($q) {
                $q->where('id', '=', Auth::id());
            })
            ->where('register_start', '<=', $now)
            ->where('register_end', '>=', $now)
            ->get();

        $mark = Schedule::query()
            ->with(['students'])
            ->where('mark_start', '<=', $now)
            ->where('mark_end', '>=', $now)
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