<?php

namespace App\Modules\TopicProposal\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicProposalViewDTO extends FlexibleDataTransferObject
{
    public $search;
    public $lecturerId;
    public $scheduleId;
    public $created;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'search' => $request->input('search'),
            'lecturerId' => $request->input('lecturerId'),
            'scheduleId' => $request->input('scheduleId'),
            'created' => $request->input('created'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}