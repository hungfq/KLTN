<?php

namespace App\Modules\Topic\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicViewDTO extends FlexibleDataTransferObject
{
    public $search;
    public $lecturerId;
    public $criticalId;
    public $scheduleId;
    public $schedule_ids;
    public $is_lecturer_approve;
    public $is_critical_approve;
    public $as_least_lecturer_approve;
    public $is_lecturer_mark;
    public $is_critical_mark;
    public $is_president_mark;
    public $is_secretary_mark;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'search' => $request->input('search'),
            'lecturerId' => $request->input('lecturerId'),
            'criticalId' => $request->input('criticalId'),
            'scheduleId' => $request->input('scheduleId'),
            'schedule_ids' => $request->input('schedule_ids'),
            'is_lecturer_approve' => $request->input('is_lecturer_approve'),
            'is_critical_approve' => $request->input('is_critical_approve'),
            'as_least_lecturer_approve' => $request->input('as_least_lecturer_approve'),
            'is_lecturer_mark' => $request->input('is_lecturer_mark'),
            'is_president_mark' => $request->input('is_president_mark'),
            'is_secretary_mark' => $request->input('is_secretary_mark'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}