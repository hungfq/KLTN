<?php

namespace App\Modules\Schedule\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ScheduleUpdateDTO extends FlexibleDataTransferObject
{
    public $id;
    public $code;
    public $name;
    public $description;
    public $start_date;
    public $end_date;
    public $proposal_start;
    public $proposal_end;
    public $approve_start;
    public $approve_end;
    public $register_start;
    public $register_end;
//    public $advisor_score_rate;
//    public $critical_score_rate;
//    public $president_score_rate;
//    public $secretary_score_rate;
    public $students;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'start_date' => $request->input('startDate'),
            'end_date' => $request->input('deadline'),
            'proposal_start' => $request->input('startProposalDate'),
            'proposal_end' => $request->input('endProposalDate'),
            'approve_start' => $request->input('startApproveDate'),
            'approve_end' => $request->input('endApproveDate'),
            'register_start' => $request->input('startRegisterDate'),
            'register_end' => $request->input('endRegisterDate'),
//            'advisor_score_rate' => $request->input('advisor_score_rate'),
//            'critical_score_rate' => $request->input('critical_score_rate'),
//            'president_score_rate' => $request->input('president_score_rate'),
//            'secretary_score_rate' => $request->input('secretary_score_rate'),
            'students' => $request->input('students'),
        ]);
    }
}