<?php

namespace App\Modules\Topic\Actions;

use App\Entities\Role;
use App\Entities\Topic;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Topic\DTO\TopicUpdateStudentDTO;

class TopicUpdateStudentAction
{
    public TopicUpdateStudentDTO $dto;
    public $topic;
    public $studentIds;

    /***
     * @param TopicUpdateStudentDTO $dto
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
//            throw new UserException("Topic not found!");
            throw new UserException('Đề tài không tồn tại trong hệ thống!', 400);
        }

        foreach (data_get($this->dto, 'students', []) as $studentCode) {
            $student = User::role(Role::ROLE_STUDENT)->where('code', $studentCode)->first();
            if (!$student) {
//                throw new UserException("Student not found!");
                throw new UserException('Sinh viên không tồn tại trong hệ thống!', 400);
            }
            $studentAlreadyRegistered = Topic::query()
                ->where('id', '<>', $this->dto->id)
                ->where('schedule_id', $this->topic->schedule_id)
                ->whereHas('students', function ($q) use ($student) {
                    $q->where('id', data_get($student, 'id'));
                })->exists();
            if ($studentAlreadyRegistered) {
//                throw new UserException('Some student already has register in another topic');
                throw new UserException(sprintf('Sinh viên %s đã đăng ký đề tài khác!', data_get($student, 'code')), 400);
            }

            $this->studentIds[] = $student->id;
        }

        return $this;
    }

    protected function updateTopic()
    {
        $this->topic->students()->sync($this->studentIds);
        $this->topic->save();
        return $this;
    }
}
