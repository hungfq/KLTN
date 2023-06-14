<?php

namespace App\Entities;

use App\Entities\Traits\CreatedByRelationshipTrait;
use App\Entities\Traits\UpdatedByRelationshipTrait;
use Illuminate\Support\Facades\DB;

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

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public static function generateCommitteeCode($schedule)
    {
        $prefix = data_get($schedule, 'code') . '-HD-';
        $defaultNum = $prefix . "001";

        $committee = DB::table(Committee::getTableName())
            ->where('schedule_id', data_get($schedule, 'id'))
            ->where('code', 'LIKE', "$prefix%")
            ->orderBy('code', 'desc')
            ->first();

        if (!$committee) {
            return $defaultNum;
        }

        $arr = explode('-', $committee->code);
        $lastNum = (int)$arr[count($arr) - 1];

        return sprintf('%s%03d', $prefix, $lastNum + 1);
    }
}