<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;

class TopicShowStudentAction
{
    public function handle($id)
    {
        return Topic::query()
            ->where('topics.id', $id)
            ->first()
            ->students()
            ->get();
    }
}