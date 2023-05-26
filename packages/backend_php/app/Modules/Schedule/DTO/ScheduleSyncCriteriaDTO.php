<?php

namespace App\Modules\Schedule\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class
ScheduleSyncCriteriaDTO extends FlexibleDataTransferObject
{
    public $id;
    public $advisor_score_rate;
    public $critical_score_rate;
    public $president_score_rate;
    public $secretary_score_rate;
    public $details;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'advisor_score_rate' => $request->input('advisor_score_rate'),
            'critical_score_rate' => $request->input('critical_score_rate'),
            'president_score_rate' => $request->input('president_score_rate'),
            'secretary_score_rate' => $request->input('secretary_score_rate'),
            'details' => $request->input('details'),
        ]);
    }
}