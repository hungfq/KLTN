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
            'start_date' => $request->input('startDate') ? date('Y-m-d H:i:s', strtotime($request->input('startDate'))) : null,
            'end_date' => $request->input('deadline') ? date('Y-m-d H:i:s', strtotime($request->input('deadline'))) : null,
            'proposal_start' => $request->input('startProposalDate') ? date('Y-m-d H:i:s', strtotime($request->input('startProposalDate'))) : null,
            'proposal_end' => $request->input('endProposalDate') ? date('Y-m-d H:i:s', strtotime($request->input('endProposalDate'))) : null,
            'approve_start' => $request->input('startApproveDate') ? date('Y-m-d H:i:s', strtotime($request->input('startApproveDate'))) : null,
            'approve_end' => $request->input('endApproveDate') ? date('Y-m-d H:i:s', strtotime($request->input('endApproveDate'))) : null,
            'register_start' => $request->input('startRegisterDate') ? date('Y-m-d H:i:s', strtotime($request->input('startRegisterDate'))) : null,
            'register_end' => $request->input('endRegisterDate') ? date('Y-m-d H:i:s', strtotime($request->input('endRegisterDate'))) : null,
            'mark_start' => $request->input('mark_start') ? date('Y-m-d H:i:s', strtotime($request->input('mark_start'))) : null,
            'mark_end' => $request->input('mark_end') ? date('Y-m-d H:i:s', strtotime($request->input('mark_end'))) : null,
            'students' => $request->input('students'),
        ]);
    }
}