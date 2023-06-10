<?php

namespace App\Modules\Task\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TaskUpdateMultiDTO extends FlexibleDataTransferObject
{
    public $tasks;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'tasks' => $request->input('tasks'),
        ]);
    }
}