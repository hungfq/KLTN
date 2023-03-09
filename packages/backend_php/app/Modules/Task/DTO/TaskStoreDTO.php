<?php

namespace App\Modules\Task\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TaskStoreDTO extends FlexibleDataTransferObject
{
    public $topic_id;
    public $code;
    public $status;
    public $title;
    public $description;
    public $assignee_id;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'topic_id' => $request->input('topicId'),
            'status' => $request->input('status'),
            'code' => $request->input('code'),
            'title' => $request->input('title'),
            'description' => $request->input('description') ?: "",
            'assignee_id' => $request->input('assignTo'),
        ]);
    }
}