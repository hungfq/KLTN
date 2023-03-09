<?php

namespace App\Modules\Task\Actions;

use App\Entities\Task;
use App\Exceptions\UserException;
use App\Modules\Task\DTO\TaskUpdateDTO;

class TaskUpdateAction
{
    public TaskUpdateDTO $dto;
    public $task;

    /***
     * @param TaskUpdateDTO $dto
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
        $this->task->update($this->dto->all());
        return $this;
    }
}
