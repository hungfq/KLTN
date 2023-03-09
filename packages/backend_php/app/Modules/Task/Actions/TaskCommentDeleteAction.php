<?php

namespace App\Modules\Task\Actions;

use App\Entities\TaskComment;
use App\Exceptions\UserException;

class TaskCommentDeleteAction
{
    public function handle($id)
    {
        $comment = TaskComment::query()->find($id);
        if (!$comment) {
            throw new UserException('Comment not found');
        }

        $comment->delete();
    }
}
