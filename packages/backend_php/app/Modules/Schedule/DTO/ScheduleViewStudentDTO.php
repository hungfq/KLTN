<?php

namespace App\Modules\Schedule\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ScheduleViewStudentDTO extends FlexibleDataTransferObject
{
    public $id;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}