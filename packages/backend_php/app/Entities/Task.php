<?php

namespace App\Entities;

use App\Entities\Traits\CreatedByRelationshipTrait;
use App\Entities\Traits\UpdatedByRelationshipTrait;
use Illuminate\Support\Facades\DB;

class Task extends BaseSoftModel
{
    protected $guarded = ['id'];

    use CreatedByRelationshipTrait, UpdatedByRelationshipTrait;

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id');
    }

    public static function generateTaskCode($topic)
    {
        $defaultNum = data_get($topic, 'code') . "-001";

        $task = DB::table(Task::getTableName())
            ->where('topic_id', data_get($topic, 'id'))
            ->where('code', 'LIKE', "%-%")
            ->orderBy('code', 'desc')
            ->first();

        $arr = explode('-', $task->code);

        if (!isset($arr[2])) {
            return $defaultNum;
        }

        return sprintf('%s-%03d-%03d', $arr[0], $arr[1], ++$arr[2]);
    }
}