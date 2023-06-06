<?php

namespace App\Modules\TopicProposal\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicProposalViewDTO extends FlexibleDataTransferObject
{
    public $search;
    public $lecturerId;
    public $scheduleId;
    public $is_created;
    public $is_lecturer;
    public $is_approve_time;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'search' => $request->input('search'),
            'lecturerId' => $request->input('lecturerId'),
            'scheduleId' => $request->input('scheduleId'),
            'is_created' => $request->input('is_created'),
            'is_lecturer' => $request->input('is_lecturer'),
            'is_approve_time' => $request->input('is_approve_time'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}