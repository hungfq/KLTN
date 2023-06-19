<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Exceptions\UserException;
use App\Modules\Schedule\Actions\Helpers\ScheduleGradeExport;
use App\Modules\Schedule\DTO\ScheduleExportGradeDTO;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleExportGradeAction
{
    public ScheduleExportGradeDTO $dto;

    /**
     * @param ScheduleExportGradeDTO $dto
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        switch ($dto->export_type) {
            case "COMMITTEE":
                return $this->handleByCommittee();
            default:
                return $this->handleBySchedule();
        }
    }

    protected function handleBySchedule()
    {
        $schedule = Schedule::with([
            'topics.students',
            'topics.lecturer',
            'topics.critical',
        ])->find($this->dto->id);
        if (!$schedule) {
//            throw new UserException("Schedule not found!");
            throw new UserException('Đợt đăng ký không tồn tại!', 400);
        }

        $today = Carbon::now();
        $locationTime = "TP.HCM, ngày " . $today->day . " tháng " . $today->month . " năm " . $today->year;

        $topics = $schedule->topics;

        return Excel::download(new ScheduleGradeExport([
            'schedule' => $schedule,
            'topics' => $topics,
            'locationTime' => $locationTime,
        ], 'ScheduleGradeReportExcel'), "ScheduleGradeReport.xlsx");
    }

    protected function handleByCommittee()
    {
        return 2;
    }
}