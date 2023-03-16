<?php

namespace App\Modules\Topic\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicMarkDTO extends FlexibleDataTransferObject
{
    public $id;
    public $lecturer_grade;
    public $critical_grade;
    public $committee_president_grade;
    public $committee_secretary_grade;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'lecturer_grade' => $request->input('lecturer_grade'),
            'critical_grade' => $request->input('critical_grade'),
            'committee_president_grade' => $request->input('committee_president_grade'),
            'committee_secretary_grade' => $request->input('committee_secretary_grade'),
        ]);
    }
}