<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Entities\User;
use App\Exceptions\UserException;
use App\Modules\Schedule\DTO\ScheduleStoreDTO;

class ScheduleStoreAction
{
    public ScheduleStoreDTO $dto;
    public $schedule;
    public $studentIds;

    /***
     * @param ScheduleStoreDTO $dto
     * @return void
     */
    public function handle($dto)
    {
        $this->dto = $dto;

        $this->checkData()
            ->createSchedule();
    }

    protected function checkData()
    {
        $isExists = Schedule::where('code', $this->dto->code)
            ->exists();
        if ($isExists) {
//            throw new UserException("Schedule code already exists!");
            throw new UserException('Mã đợt đã tồn tại trong hệ thống!', 400);
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

    protected function createSchedule()
    {
        $this->schedule = Schedule::create($this->dto->all());
        $this->schedule->students()->sync($this->studentIds);
        $this->schedule->save();
        return $this;
    }
}
