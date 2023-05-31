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
    public $mark_start;
    public $mark_end;
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
            'start_date' => date('Y-m-d H:i:s', strtotime($request->input('startDate'))),
            'end_date' => date('Y-m-d H:i:s', strtotime($request->input('deadline'))),
            'proposal_start' => date('Y-m-d H:i:s', strtotime($request->input('startProposalDate'))),
            'proposal_end' => date('Y-m-d H:i:s', strtotime($request->input('endProposalDate'))),
            'approve_start' => date('Y-m-d H:i:s', strtotime($request->input('startApproveDate'))),
            'approve_end' => date('Y-m-d H:i:s', strtotime($request->input('endApproveDate'))),
            'register_start' => date('Y-m-d H:i:s', strtotime($request->input('startRegisterDate'))),
            'register_end' => date('Y-m-d H:i:s', strtotime($request->input('endRegisterDate'))),
            'mark_start' => date('Y-m-d H:i:s', strtotime($request->input('mark_start'))),
            'mark_end' => date('Y-m-d H:i:s', strtotime($request->input('mark_end'))),
            'students' => $request->input('students'),
        ]);
    }
}