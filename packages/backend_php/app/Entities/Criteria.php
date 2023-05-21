<?php

namespace App\Entities;

use App\Entities\Traits\CreatedByRelationshipTrait;
use App\Entities\Traits\UpdatedByRelationshipTrait;

class Criteria extends BaseSoftModel
{
    protected $table = 'criteria';
    protected $guarded = ['id'];

    use CreatedByRelationshipTrait, UpdatedByRelationshipTrait;

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_criteria', 'criteria_id', 'schedule_id',);
    }
}