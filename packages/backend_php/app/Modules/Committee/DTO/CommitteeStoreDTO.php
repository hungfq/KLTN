<?php

namespace App\Modules\Committee\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CommitteeStoreDTO extends FlexibleDataTransferObject
{
    public $schedule_id;
    public $code;
    public $name;
    public $president_id;
    public $secretary_id;
    public $critical_id;
    public $defense_date;
    public $address;
    public $topics;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'schedule_id' => $request->input('schedule_id'),
//            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'president_id' => $request->input('committeePresidentId'),
            'secretary_id' => $request->input('committeeSecretaryId'),
            'critical_id' => $request->input('criticalLecturerId'),
            'defense_date' => $request->input('defense_date') ? date('Y-m-d H:i:s', strtotime($request->input('defense_date'))) : null,
            'address' => $request->input('address'),
            'topics' => $request->input('topics'),
        ]);
    }
}