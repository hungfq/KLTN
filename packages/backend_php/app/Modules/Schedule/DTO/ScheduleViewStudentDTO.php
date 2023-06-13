<?php

namespace App\Modules\Schedule\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ScheduleViewStudentDTO extends FlexibleDataTransferObject
{
    public $id;
    public $ignore_ids;
    public $not_in_any_proposal;
    public $not_in_any_topic;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'ignore_ids' => $request->input('ignore_ids') ?? [],
            'not_in_any_proposal' => $request->input('not_in_any_proposal'),
            'not_in_any_topic' => $request->input('not_in_any_topic'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}