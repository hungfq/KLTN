<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Topic;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Topic\DTO\TopicStoreDTO;

class TopicStoreAction
{
    public TopicStoreDTO $dto;
    public $topic;
    public $studentIds;

    /***
     * @param TopicStoreDTO $dto
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
        foreach (data_get($this->dto, 'students', []) as $studentCode) {
            $student = User::where('code', $studentCode)->first();
            if (!$student) {
                throw new UserException("Student not found!");
            }
            $this->studentIds[] = $student->id;
        }

        return $this;
    }

    protected function createTopic()
    {
        $this->topic = Topic::create($this->dto->all());
        $this->topic->students()->sync($this->studentIds);
        $this->topic->save();
        return $this;
    }
}
