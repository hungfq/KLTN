<?php

namespace App\Entities;

use App\Entities\Traits\CreatedByRelationshipTrait;
use App\Entities\Traits\UpdatedByRelationshipTrait;

class Committee extends BaseSoftModel
{
    protected $guarded = ['id'];

    use CreatedByRelationshipTrait, UpdatedByRelationshipTrait;

    public function president()
    {
        return $this->belongsTo(User::class, 'president_id');
    }

    public function secretary()
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    public function critical()
    {
        return $this->belongsTo(User::class, 'critical_id');
    }

    public function topics()
    {
        return $this->hasMany(Topic::class, 'committee_id');
    }
}