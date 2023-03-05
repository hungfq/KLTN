<?php

namespace App\Modules\Schedule\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ScheduleViewDTO extends FlexibleDataTransferObject
{
    public $search;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'search' => $request->input('search'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}