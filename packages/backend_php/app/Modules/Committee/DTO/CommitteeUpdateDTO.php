<?php

namespace App\Modules\Committee\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CommitteeUpdateDTO extends FlexibleDataTransferObject
{
    public $id;
    public $schedule_id;
//    public $code;
    public $name;
    public $president_id;
    public $secretary_id;
    public $critical_id;
//    public $topics;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'schedule_id' => $request->input('schedule_id'),
//            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'president_id' => $request->input('committeePresidentId'),
            'secretary_id' => $request->input('committeeSecretaryId'),
            'critical_id' => $request->input('criticalLecturerId'),
//            'topics' => $request->input('topics'),
        ]);
    }
}