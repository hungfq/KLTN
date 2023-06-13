<?php

namespace App\Modules\Committee\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CommitteeViewDTO extends FlexibleDataTransferObject
{
    public $schedule_id;
    public $search;
    public $president_id;
    public $secretary_id;
    public $critical_id;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'schedule_id' => $request->input('schedule_id'),
            'search' => $request->input('search'),
            'president_id' => $request->input('president_id'),
            'secretary_id' => $request->input('secretary_id'),
            'critical_id' => $request->input('critical_id'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}