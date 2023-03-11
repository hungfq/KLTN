<?php

namespace App\Modules\Topic\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicViewDTO extends FlexibleDataTransferObject
{
    public $search;
    public $lecturerId;
    public $criticalId;
    public $scheduleId;
    public $is_lecturer_approve;
    public $is_critical_approve;
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
            'is_lecturer_approve' => $request->input('is_lecturer_approve'),
            'is_critical_approve' => $request->input('is_critical_approve'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}