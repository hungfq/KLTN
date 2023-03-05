<?php

namespace App\Modules\Topic\Transformers;

use League\Fractal\TransformerAbstract;

class TopicViewTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $students = [];
        foreach (data_get($model, 'students', []) as $student) {
            $students[] = data_get($student, 'code');
        }
        return [
            '_id' => $model->id,
            'code' => $model->code,
            'title' => $model->title,
            'description' => $model->description,
            'current' => $model->students()->count(),
            'limit' => $model->limit,
            'scheduleId' => [
                '_id' => data_get($model, 'schedule.id'),
                'code' => data_get($model, 'schedule.code'),
                'name' => data_get($model, 'schedule.name'),
            ],
            'lecturerId' => [
                '_id' => data_get($model, 'lecturer.id'),
                'code' => data_get($model, 'lecturer.code'),
                'name' => data_get($model, 'lecturer.name'),
            ],
            'criticalLecturerId' => [
                '_id' => data_get($model, 'critical.id'),
                'code' => data_get($model, 'critical.code'),
                'name' => data_get($model, 'critical.name'),
            ],
            'criticalLecturerApprove' => (bool)$model->critical_approved,
            'advisorLecturerApprove' => (bool)$model->lecturer_approved,
            'advisorLecturerGrade' => $model->lecturer_grade,
            'criticalLecturerGrade' => $model->critical_grade,
            'committeePresidentGrade' => $model->committee_president_grade,
            'committeeSecretaryGrade' => $model->committee_secretary_grade,
            'thesisDefenseDate' => $model->thesis_defense_date,
            'students' => $students,
            'created_at' => $model->created_at,
            'created_by_name' => data_get($model, 'createdBy.name'),
            'updated_at' => $model->updated_at,
            'updated_by_name' => data_get($model, 'updatedBy.name'),
        ];
    }
}