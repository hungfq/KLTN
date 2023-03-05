<?php

namespace App\Modules\Schedule\Transformers;

use League\Fractal\TransformerAbstract;

class ScheduleViewStudentTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            '_id' => $model->id,
            'code' => $model->code,
            'name' => $model->name,
            'email' => $model->email,
            'gender' => $model->gender,
            'picture' => $model->picture,
        ];
    }
}