<?php

namespace App\Modules\Schedule\Actions;

use App\Entities\Schedule;
use App\Exceptions\UserException;
use App\Modules\Schedule\DTO\ScheduleSyncCriteriaDTO;

class ScheduleSyncCriteriaAction
{
    /**
     * @param ScheduleSyncCriteriaDTO $dto
     */
    public function handle($dto)
    {
        $schedule = Schedule::find($dto->id);
        if (!$schedule) {
            throw new UserException("Schedule not found!");
        }

        $details = $dto->details;
        $criteriaIds = array_column($details, 'criteria_id');
        $schedule->scheduleCriteria()->whereNotIn('criteria_id', $criteriaIds)->delete();
        foreach ($details as $detail) {
            if ($criteria = $schedule->scheduleCriteria()
                ->where('criteria_id', data_get($detail, 'criteria_id'))
                ->first()
            ) {
                $criteria->update([
                    'score_rate' => data_get($detail, 'score_rate'),
                ]);
            } else {
                $schedule->scheduleCriteria()->create([
                    'criteria_id' => data_get($detail, 'criteria_id'),
                    'score_rate' => data_get($detail, 'score_rate'),
                ]);
            }
        }
    }
}