<?php

namespace App\Modules\Schedule\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ScheduleUpdateStudentDTO extends FlexibleDataTransferObject
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