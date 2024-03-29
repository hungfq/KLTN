<?php

namespace App\Entities;

use App\Entities\Traits\CreatedByRelationshipTrait;
use App\Entities\Traits\UpdatedByRelationshipTrait;

class Grade extends BaseSoftModel
{
    protected $guarded = ['id'];

    use CreatedByRelationshipTrait, UpdatedByRelationshipTrait;

    const TYPE_LECTURER = 'LT';
    const TYPE_CRITICAL = 'CR';
    const TYPE_PRESIDENT = 'PD';
    const TYPE_SECRETARY = 'SE';

    public static function type()
    {
        return [
            self::TYPE_LECTURER => 'Lecturer',
            self::TYPE_CRITICAL => 'Critical',
            self::TYPE_PRESIDENT => 'President',
            self::TYPE_SECRETARY => 'Secretary',
        ];
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function graded()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id');
    }

}