<?php

namespace App\Entities;

use App\Entities\Traits\CreatedByRelationshipTrait;
use App\Entities\Traits\UpdatedByRelationshipTrait;

class TopicProposal extends BaseSoftModel
{
    protected $guarded = ['id'];

    use CreatedByRelationshipTrait, UpdatedByRelationshipTrait;

    const STATUS_LECTURER_PENDING = 'LECTURER';
    const STATUS_LECTURER_APPROVE = 'ADMIN';

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'topic_proposal_students', 'topic_id', 'student_id');
    }
}