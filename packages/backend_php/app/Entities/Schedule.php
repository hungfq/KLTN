<?php

namespace App\Entities;

use App\Entities\Traits\CreatedByRelationshipTrait;
use App\Entities\Traits\UpdatedByRelationshipTrait;

class Schedule extends BaseSoftModel
{
    protected $guarded = ['id'];

    use CreatedByRelationshipTrait, UpdatedByRelationshipTrait;

    public function topics()
    {
        return $this->hasMany(Topic::class, 'schedule_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'schedule_students', 'schedule_id', 'student_id');
    }

    public function criteria()
    {
        return $this->belongsToMany(Criteria::class, 'schedule_criteria', 'schedule_id', 'criteria_id');
    }
}