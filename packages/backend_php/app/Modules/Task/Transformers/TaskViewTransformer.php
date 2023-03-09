<?php

namespace App\Modules\Task\Transformers;

use League\Fractal\TransformerAbstract;

class TaskViewTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            '_id' => $model->id,
            'status' => $model->status,
            'code' => $model->code,
            'title' => $model->title,
            'description' => $model->description,
            'topicId' => $model->topic_id,
            'assignTo' => $model->assignee_id,
            'assignToFilter' => [
                '_id' => data_get($model, 'assignee.id'),
                'code' => data_get($model, 'assignee.code'),
                'name' => data_get($model, 'assignee.name'),
            ],
            'created_at' => $model->created_at,
            'created_by_name' => data_get($model, 'created_by_name'),
            'createdBy' => data_get($model, 'created_by_name'),
            'updated_at' => $model->updated_at,
            'updatedBy' => data_get($model, 'created_by_name'),
            'updated_by_name' => data_get($model, 'created_by_name'),
        ];
    }
}