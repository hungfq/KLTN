<?php

namespace App\Modules\Topic\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicStoreDTO extends FlexibleDataTransferObject
{
    public $code;
    public $title;
    public $description;
    public $limit;
    public $thesis_defense_date;
    public $schedule_id;
    public $lecturer_id;
    public $critical_id;
//    public $lecturer_approved;
//    public $critical_approved;
    public $lecturer_grade;
    public $critical_grade;
    public $committee_president_grade;
    public $committee_secretary_grade;

    public $students;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'code' => $request->input('code'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'limit' => $request->input('limit'),
            'thesis_defense_date' => $request->input('thesisDefenseDate'),
            'schedule_id' => $request->input('scheduleId'),
            'lecturer_id' => $request->input('lecturerId'),
            'critical_id' => $request->input('criticalLecturerId'),
//            'lecturer_approved' => false,
//            'critical_approved' => false,
            'lecturer_grade' => $request->input('advisorLecturerGrade'),
            'critical_grade' => $request->input('criticalLecturerGrade'),
            'committee_president_grade' => $request->input('committeePresidentGrade'),
            'committee_secretary_grade' => $request->input('committeeSecretaryGrade'),

            'students' => $request->input('students'),
        ]);
    }
}