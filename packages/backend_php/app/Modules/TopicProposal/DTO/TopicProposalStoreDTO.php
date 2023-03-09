<?php

namespace App\Modules\TopicProposal\DTO;

use App\Entities\TopicProposal;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicProposalStoreDTO extends FlexibleDataTransferObject
{
    public $code;
    public $title;
    public $description;
    public $limit;
    public $schedule_id;
    public $lecturer_id;
    public $status;
    public $students;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'code' => $request->input('code'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'limit' => $request->input('limit'),
            'schedule_id' => $request->input('scheduleId'),
            'lecturer_id' => $request->input('lecturerId'),
            'status' => $request->input('status') ?? TopicProposal::STATUS_LECTURER_PENDING,
            'students' => $request->input('students'),
        ]);
    }
}