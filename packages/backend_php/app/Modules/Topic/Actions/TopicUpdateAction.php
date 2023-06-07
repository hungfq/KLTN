<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Role;
use App\Entities\Schedule;
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
            $studentAlreadyRegistered = Topic::query()
                ->where('id', '<>', $this->dto->id)
                ->where('schedule_id', $this->dto->schedule_id)
                ->whereHas('students', function ($q) use ($student) {
                    $q->where('id', data_get($student, 'id'));
                })->exists();
            if ($studentAlreadyRegistered) {
                throw new UserException('Some student already has register in another topic');
            }
            $this->studentIds[] = $student->id;
        }

        $schedule = Schedule::query()->find($this->dto->schedule_id);
        if (!$schedule) {
            throw new UserException("Schedule not found!");
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
