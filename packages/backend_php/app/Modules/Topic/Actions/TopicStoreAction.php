<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Role;
use App\Entities\Schedule;
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
            $student = User::role(Role::ROLE_STUDENT)->where('code', $studentCode)->first();
            if (!$student) {
//                throw new UserException("Student not found!");
                throw new UserException('Sinh viên không tồn tại trong hệ thống!', 400);
            }

            $studentAlreadyRegistered = Topic::query()
                ->where('schedule_id', $this->dto->schedule_id)
                ->whereHas('students', function ($q) use ($student) {
                    $q->where('id', data_get($student, 'id'));
                })->exists();
            if ($studentAlreadyRegistered) {
//                throw new UserException('Some student already has register in another topic');
                throw new UserException(sprintf('Sinh viên %s đã đăng ký đề tài khác!', data_get($student, 'code')), 400);
            }
            $this->studentIds[] = $student->id;
        }

        $schedule = Schedule::query()->find($this->dto->schedule_id);
        if (!$schedule) {
//            throw new UserException("Schedule not found!");
            throw new UserException('Đợt đăng ký không tồn tại trong hệ thống!', 400);
        }

        if (!$this->dto->code) {
            $this->dto->code = Topic::generateTopicCode($schedule);
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
