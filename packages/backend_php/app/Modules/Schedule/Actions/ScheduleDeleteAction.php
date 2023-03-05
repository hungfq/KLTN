<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Exceptions\UserException;
use App\Modules\Schedule\DTO\ScheduleUpdateDTO;

class ScheduleDeleteAction
{
    public $id;
    public $schedule;

    /***
     * @return void
     */
    public function handle($id)
    {
        $this->id = $id;

        $this->checkData()
            ->delete();
    }

    protected function checkData()
    {
        $this->schedule = Schedule::find($this->id);
        if (!$this->schedule) {
            throw new UserException("Schedule not found!");
        }

        return $this;
    }

    protected function delete()
    {
        $this->schedule->students()->sync([]);
        $this->schedule->delete();
        return $this;
    }
}
