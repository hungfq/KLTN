<?php

namespace App\Modules\Task\Actions;

use App\Entities\Task;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use App\Modules\Task\DTO\TaskUpdateDTO;
use Illuminate\Support\Collection;

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
            ->updateTask();
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

    protected function updateTask()
    {
        $this->task->update($this->dto->all());
        $members = data_get($this->task, 'topic.students', []);
        $ids = $members->pluck('id');

        $ids[] = data_get($this->task, 'topic.lecturer_id');
        if ($ids instanceof Collection) {
            $ids = $ids->toArray();
        }
        Socket::sendUpdateTaskRequest($ids, $this->task->topic_id);

        return $this;
    }
}
