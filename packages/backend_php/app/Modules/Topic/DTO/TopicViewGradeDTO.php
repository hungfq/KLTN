<?php

namespace App\Modules\Topic\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicViewGradeDTO extends FlexibleDataTransferObject
{
    public $id;
    public $student_id;
    public $type;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'student_id' => $request->input('student_id'),
            'type' => $request->input('type'),
        ]);
    }
}