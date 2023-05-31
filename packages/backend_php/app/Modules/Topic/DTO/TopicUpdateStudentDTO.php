<?php

namespace App\Modules\Topic\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicUpdateStudentDTO extends FlexibleDataTransferObject
{
    public $id;
    public $students;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'students' => $request->input('students'),
        ]);
    }
}