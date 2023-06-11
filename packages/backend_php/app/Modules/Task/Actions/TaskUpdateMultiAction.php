<?php

namespace App\Modules\Task\Actions;

use App\Entities\Task;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use App\Modules\Task\DTO\TaskUpdateMultiDTO;
use Illuminate\Support\Collection;

class TaskUpdateMultiAction
{
    public TaskUpdateMultiDTO $dto;
    public $userIds = [];
    public $topicId = null;

    /***
     * @param TaskUpdateMultiDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        foreach ($this->dto->tasks as $task) {
            $this->checkAndUpdateOneTask($task);
        }

        $this->sendUpdateTaskRequest();
    }

    protected function checkAndUpdateOneTask($data)
    {
        $task = Task::find(data_get($data, '_id'));
        if (!$task) {
//            throw new UserException('Task not found');
            throw new UserException('Nhiệm vụ không tồn tại trong hệ thống!', 400);
        }
        $this->topicId = data_get($task, 'topic_id');

        $task->update([
            'status' => data_get($data, 'status'),
            'code' => data_get($data, 'code'),
            'title' => data_get($data, 'title'),
            'description' => data_get($data, 'description') ?: "",
            'assignee_id' => data_get($data, 'assignTo'),
        ]);

        $members = data_get($task, 'topic.students', []);
        $ids = $members->pluck('id');

        $ids[] = data_get($task, 'topic.lecturer_id');
        if ($ids instanceof Collection) {
            $ids = $ids->toArray();
        }
        foreach ($ids as $id) {
            $this->userIds[$id] = $id;
        }
    }

    protected function sendUpdateTaskRequest()
    {
        Socket::sendUpdateTaskRequest($this->userIds, $this->topicId);
    }
}
