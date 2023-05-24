<?php

namespace App\Entities;

use App\Entities\Traits\CreatedByRelationshipTrait;
use App\Entities\Traits\UpdatedByRelationshipTrait;

class
ScheduleCriteria extends BaseSoftModel
{
    protected $table = 'schedule_criteria';

    protected $guarded = ['id'];

    use CreatedByRelationshipTrait, UpdatedByRelationshipTrait;

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id',);
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id',);
    }
}