<?php

namespace App\Modules\Task\Actions;

use App\Entities\Task;
use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Libraries\Socket;
use App\Modules\Task\DTO\TaskStoreDTO;
use Illuminate\Support\Collection;

class TaskStoreAction
{
    public TaskStoreDTO $dto;
    public $task;

    /***
     * @param TaskStoreDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->createTask();
    }

    protected function checkData()
    {
        $topic = Topic::query()->find($this->dto->topic_id);
        if (!$topic) {
//            throw new UserException('Topic not found');
            throw new UserException('Đề tài không tồn tại trong hệ thống!', 400);
        }

        $existsCode = Task::query()
            ->where('topic_id', $this->dto->topic_id)
            ->where('code', $this->dto->code)
            ->exists();
        if ($existsCode) {
//            throw new UserException('Code already exists');
            throw new UserException('Mã đã tồn tại trong hệ thống!', 400);
        }

        if (!$this->dto->code) {
            $this->dto->code = Task::generateTaskCode($topic);
        }

        return $this;
    }

    protected function createTask()
    {
        $this->task = Task::create($this->dto->all());
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
