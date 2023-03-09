<?php

namespace App\Modules\Task\Transformers;

use League\Fractal\TransformerAbstract;

class TaskShowTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $comments = [];
        foreach (data_get($model, 'comments', []) as $comment) {
            $comments[] = [
                '_id' => $comment['id'],
                'message' => $comment['message'],
                'createdBy' => $comment['created_by'],
                'createdAt' => $comment['created_at'],
                'createdByFilter' => [
                    '_id' => data_get($comment, 'createdBy.id'),
                    'code' => data_get($comment, 'createdBy.code'),
                    'name' => data_get($comment, 'createdBy.name'),
                ],
            ];
        }
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
            'createdByFilter' => [
                '_id' => data_get($model, 'createdBy.id'),
                'code' => data_get($model, 'createdBy.code'),
                'name' => data_get($model, 'createdBy.name'),
            ],
            'comments' => $comments,
        ];
    }
}