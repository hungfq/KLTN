<?php

namespace App\Modules\Topic\Transformers;

use League\Fractal\TransformerAbstract;

class TopicStudentShowGradeTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'criteria_id' => $model->criteria_id,
            'title' => $model->title,
            'description' => $model->description,
            'lecturer_grade' => $model->lecturer_grade,
            'critical_grade' => $model->critical_grade,
            'president_grade' => $model->president_grade,
            'secretary_grade' => $model->secretary_grade,
        ];
    }
}