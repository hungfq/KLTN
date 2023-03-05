<?php

namespace App\Modules\Topic\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicViewDTO extends FlexibleDataTransferObject
{
    public $search;
    public $lecturerId;
    public $criticalId;
    public $scheduleId;
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
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}