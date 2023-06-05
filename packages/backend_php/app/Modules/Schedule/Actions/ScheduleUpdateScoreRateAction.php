<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Exceptions\UserException;
use App\Modules\Schedule\DTO\ScheduleUpdateScoreRateDTO;

class
ScheduleUpdateScoreRateAction
{
    public ScheduleUpdateScoreRateDTO $dto;
    public $schedule;
    public $studentIds;

    /***
     * @param ScheduleUpdateScoreRateDTO $dto
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
            throw new UserException("Schedule not found!");
        }

        return $this;
    }

    protected function update()
    {
        $this->schedule->update($this->dto->all());
        return $this;
    }
}
