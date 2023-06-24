<?php

namespace App\Modules\Schedule\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ScheduleExportGradeDTO extends FlexibleDataTransferObject
{
    public $id;
    public $export_type;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'export_type' => $request->input('export_type'),
        ]);
    }
}