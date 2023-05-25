<?php

namespace App\Modules\Topic\Transformers;

use App\Entities\Grade;
use League\Fractal\TransformerAbstract;

class TopicViewGradeTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'student_id' => $model->student_id,
            'student_name' => $model->student_name,
            'criteria_id' => $model->criteria_id,
            'title' => $model->title,
            'description' => $model->description,
            'score_rate' => $model->score_rate,
            'score' => $model->score,
            'type' => $model->type,
            'type_name' => data_get(Grade::type(), $model->type),
            'graded_by' => $model->graded_by,
            'graded_by_name' => $model->graded_by_name,
        ];
    }
}