<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Role;
use App\Entities\Topic;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Topic\DTO\TopicUpdateDTO;

class TopicUpdateAction
{
    public TopicUpdateDTO $dto;
    public $topic;
    public $studentIds;

    /***
     * @param TopicUpdateDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->updateTopic();
    }

    protected function checkData()
    {
        $this->topic = Topic::find($this->dto->id);
        if (!$this->topic) {
            throw new UserException("Topic not found!");
        }

        foreach (data_get($this->dto, 'students', []) as $studentCode) {
            $student = User::role(Role::ROLE_STUDENT)->where('code', $studentCode)->first();
            if (!$student) {
                throw new UserException("Student not found!");
            }
            $this->studentIds[] = $student->id;
        }

        return $this;
    }

    protected function updateTopic()
    {
        $this->topic->update($this->dto->all());
        $this->topic->students()->sync($this->studentIds);
        $this->topic->save();
        return $this;
    }
}
