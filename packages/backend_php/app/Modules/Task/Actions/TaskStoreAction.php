<?php

namespace App\Modules\Task\Actions;

use App\Entities\Task;
use App\Entities\Topic;
use App\Exceptions\UserException;
use App\Modules\Task\DTO\TaskStoreDTO;

class TaskStoreAction
{
    public TaskStoreDTO $dto;

    /***
     * @param TaskStoreDTO $dto
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
        $topic = Topic::query()->find($this->dto->topic_id);
        if (!$topic) {
            throw new UserException('Topic not found');
        }

        $existsCode = Task::query()
            ->where('topic_id', $this->dto->topic_id)
            ->where('code', $this->dto->code)
            ->exists();
        if ($existsCode) {
            throw new UserException('Code already exists');
        }

        return $this;
    }

    protected function createTopic()
    {
        Task::create($this->dto->all());
        return $this;
    }
}
