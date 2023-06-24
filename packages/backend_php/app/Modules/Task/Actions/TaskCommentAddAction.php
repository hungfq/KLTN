<?php

namespace App\Modules\Task\Actions;

use App\Entities\Task;
use App\Entities\TaskComment;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use App\Modules\Task\DTO\TaskCommentAddDTO;
use Illuminate\Support\Collection;

class TaskCommentAddAction
{
    public TaskCommentAddDTO $dto;
    public $task;

    /***
     * @param TaskCommentAddDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->createComment();
    }

    protected function checkData()
    {
        $this->task = Task::query()->find($this->dto->id);
        if (!$this->task) {
//            throw new UserException('Task not found');
            throw new UserException('Nhiệm vụ không tồn tại trong hệ thống!', 400);
        }

        return $this;
    }

    protected function createComment()
    {
        $comment = new TaskComment();
        $comment->message = $this->dto->message;
        $this->task->comments()->save($comment);

        $members = data_get($this->task, 'topic.students', []);
        $ids = $members->pluck('id');

        $ids[] = data_get($this->task, 'topic.lecturer_id');
        if ($ids instanceof Collection) {
            $ids = $ids->toArray();
        }
        Socket::sendUpdateTaskInfoRequest($ids, $this->task->id);
        return $this;
    }
}
