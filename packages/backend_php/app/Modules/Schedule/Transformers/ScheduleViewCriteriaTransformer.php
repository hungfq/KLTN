<?php

namespace App\Modules\Schedule\Transformers;

use League\Fractal\TransformerAbstract;

class ScheduleViewCriteriaTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'id' => $model->id,
            'criteria_id' => $model->criteria_id,
            'title' => $model->title,
            'description' => $model->description,
            'score_rate' => $model->score_rate,
        ];
    }
}