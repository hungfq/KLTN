<?php

namespace App\Modules\Task\Actions;

use App\Entities\Task;
use App\Entities\TaskComment;
use App\Exceptions\UserException;
use App\Modules\Task\DTO\TaskCommentAddDTO;

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
            ->createTopic();
    }

    protected function checkData()
    {
        $this->task = Task::query()->find($this->dto->id);
        if (!$this->task) {
            throw new UserException('Task not found');
        }

        return $this;
    }

    protected function createTopic()
    {
        $comment = new TaskComment();
        $comment->message = $this->dto->message;
        $this->task->comments()->save($comment);
        return $this;
    }
}
