<?php

namespace App\Modules\Task\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TaskUpdateDTO extends FlexibleDataTransferObject
{
    public $id;
    public $code;
    public $status;
    public $title;
    public $description;
    public $assignee_id;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'status' => $request->input('status'),
            'code' => $request->input('code'),
            'title' => $request->input('title'),
            'description' => $request->input('description') ?: "",
            'assignee_id' => $request->input('assignTo'),
        ]);
    }
}