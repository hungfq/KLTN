<?php

namespace App\Modules\TopicProposal\Transformers;

use League\Fractal\TransformerAbstract;

class TopicProposalShowTransformer extends TransformerAbstract
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
            'scheduleId' => $model->schedule_id,
            'schedule' => [
                '_id' => data_get($model, 'schedule.id'),
                'code' => data_get($model, 'schedule.code'),
                'name' => data_get($model, 'schedule.name'),
            ],
            'lecturerId' => [
                '_id' => data_get($model, 'lecturer.id'),
                'code' => data_get($model, 'lecturer.code'),
                'name' => data_get($model, 'lecturer.name'),
            ],
            'list_students' => data_get($model, 'students'),
            'students' => $students,
            'created_at' => $model->created_at,
            'createdBy' => $model->created_by,
            'created_by_name' => data_get($model, 'created_by_name'),
            'updated_at' => $model->updated_at,
            'updated_by_name' => data_get($model, 'created_by_name'),
        ];
    }
}