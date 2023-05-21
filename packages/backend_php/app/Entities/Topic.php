<?php

namespace App\Entities;

use App\Entities\Traits\CreatedByRelationshipTrait;
use App\Entities\Traits\UpdatedByRelationshipTrait;
use Illuminate\Support\Facades\DB;

class Topic extends BaseSoftModel
{
    protected $guarded = ['id'];

    use CreatedByRelationshipTrait, UpdatedByRelationshipTrait;

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class, 'committee_id');
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function critical()
    {
        return $this->belongsTo(User::class, 'critical_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'topic_students', 'topic_id', 'student_id');
    }

    public static function generateTopicCode($schedule)
    {
        $defaultNum = data_get($schedule, 'code') . "-001";

        $topic = DB::table(Topic::getTableName())
            ->where('schedule_id', data_get($schedule, 'id'))
            ->where('code', 'LIKE', "%-%")
            ->orderBy('code', 'desc')
            ->first();

        if (!$topic) {
            return $defaultNum;
        }

        $arr =  explode('-', $topic->code);

        if (!isset($arr[1])) {
            return $defaultNum;
        }

        return sprintf('%s-%03d', $arr[0], ++$arr[1]);
    }

    public function criteria()
    {
        return $this->belongsToMany(Criteria::class, 'schedule_criteria', 'schedule_id', 'criteria_id');
    }

}