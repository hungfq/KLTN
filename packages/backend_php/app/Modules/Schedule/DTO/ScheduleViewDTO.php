<?php

namespace App\Modules\Schedule\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ScheduleViewDTO extends FlexibleDataTransferObject
{
    public $search;
    public $is_approve_time;
    public $is_student;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'search' => $request->input('search'),
            'is_approve_time' => $request->input('is_approve_time'),
            'is_student' => $request->input('is_student'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}