<?php

namespace App\Modules\Task\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TaskCommentAddDTO extends FlexibleDataTransferObject
{
    public $id;
    public $message;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'message' => $request->input('message'),
        ]);
    }
}