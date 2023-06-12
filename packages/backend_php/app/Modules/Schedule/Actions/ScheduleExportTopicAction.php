<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Exceptions\UserException;
use App\Modules\Schedule\Actions\Helpers\ScheduleTopicExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleExportTopicAction
{
    public function handle($id)
    {
        $schedule = Schedule::with([
            'topics.students',
            'topics.lecturer',
            'topics.critical',
        ])->find($id);
        if (!$schedule) {
//            throw new UserException("Schedule not found!");
            throw new UserException('Đợt đăng ký không tồn tại!', 400);
        }

        $today = Carbon::now();
        $locationTime = "TP.HCM, ngày " . $today->day . " tháng " . $today->month . " năm " . $today->year;

        $topics = $schedule->topics;

        return Excel::download(new ScheduleTopicExport([
            'schedule' => $schedule,
            'topics' => $topics,
            'locationTime' => $locationTime,
        ], 'ScheduleTopicReportExcel'), "ScheduleTopicReport.xlsx");
    }
}