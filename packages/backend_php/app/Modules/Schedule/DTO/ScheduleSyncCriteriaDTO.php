<?php

namespace App\Modules\Schedule\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class
ScheduleSyncCriteriaDTO extends FlexibleDataTransferObject
{
    public $id;
    public $details;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'details' => $request->input('details'),
        ]);
    }
}