<?php

namespace App\Modules\Schedule\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ScheduleViewWithTopicDTO extends FlexibleDataTransferObject
{
    public $is_lecturer;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'is_lecturer' => $request->input('is_lecturer'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}