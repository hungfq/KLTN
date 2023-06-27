<?php

namespace App\Modules\Task\Actions;

use App\Entities\Task;
use App\Entities\TaskComment;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use Illuminate\Support\Collection;

class TaskCommentDeleteAction
{
    public $task;

    public function handle($id)
    {
        $comment = TaskComment::query()->find($id);

        if (!$comment) {
//            throw new UserException('Comment not found');
            throw new UserException('Nhận xét không tồn tại trong hệ thống!', 400);
        }
        $this->task = $comment->task;

        $comment->delete();

        $members = data_get($this->task, 'topic.students', []);
        $ids = $members->pluck('id');

        $ids[] = data_get($this->task, 'topic.lecturer_id');
        if ($ids instanceof Collection) {
            $ids = $ids->toArray();
        }
        Socket::sendUpdateTaskInfoRequest($ids, $this->task->id);
    }
}
