<?php

namespace App\Modules\Schedule\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ScheduleImportStudentDTO extends FlexibleDataTransferObject
{
    public $id;
    public $file;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'file' => $request->file('file'),
        ]);
    }
}