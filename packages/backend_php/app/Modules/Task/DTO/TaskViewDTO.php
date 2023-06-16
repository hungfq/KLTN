<?php

namespace App\Modules\Task\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TaskViewDTO extends FlexibleDataTransferObject
{
    public $topic_id;
    public $assignee_id;
    public $status;
    public $from_time;
    public $to_time;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'topic_id' => $request->input('topicId'),
            'assignee_id' => $request->input('studentId'),
            'status' => $request->input('statusTask'),
            'from_time' => $request->input('from_time'),
            'to_time' => $request->input('to_time'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}