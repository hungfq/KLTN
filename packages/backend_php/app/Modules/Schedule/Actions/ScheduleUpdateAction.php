<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Schedule\DTO\ScheduleUpdateDTO;

class ScheduleUpdateAction
{
    public ScheduleUpdateDTO $dto;
    public $schedule;
    public $studentIds;

    /***
     * @param ScheduleUpdateDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->update();
    }

    protected function checkData()
    {
        $this->schedule = Schedule::find($this->dto->id);
        if (!$this->schedule) {
//            throw new UserException("Schedule not found!");
            throw new UserException('Đợt đăng ký không tồn tại trong hệ thống!', 400);
        }

        foreach (data_get($this->dto, 'students', []) as $studentCode) {
            $student = User::where('code', $studentCode)->first();
            if (!$student) {
//                throw new UserException("Student not found!");
                throw new UserException('Sinh viên không tồn tại trong hệ thống!', 400);
            }
            $this->studentIds[] = $student->id;
        }

        return $this;
    }

    protected function update()
    {
        $this->schedule->update($this->dto->all());
        $this->schedule->students()->sync($this->studentIds);
        $this->schedule->save();
        return $this;
    }
}
