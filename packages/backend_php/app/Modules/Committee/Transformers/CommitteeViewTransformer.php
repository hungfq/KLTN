<?php

namespace App\Modules\Committee\Transformers;

use League\Fractal\TransformerAbstract;

class CommitteeViewTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $topics = [];
        foreach (data_get($model, 'topics', []) as $topic) {
            $topics[] = data_get($topic, 'id');
        }
        return [
            '_id' => $model->id,
            'code' => $model->code,
            'name' => $model->name,
            'committeePresidentId' => [
                '_id' => data_get($model, 'president.id'),
                'code' => data_get($model, 'president.code'),
                'name' => data_get($model, 'president.name'),
            ],
            'committeeSecretaryId' => [
                '_id' => data_get($model, 'secretary.id'),
                'code' => data_get($model, 'secretary.code'),
                'name' => data_get($model, 'secretary.name'),
            ],
            'criticalLecturerId' => [
                '_id' => data_get($model, 'critical.id'),
                'code' => data_get($model, 'critical.code'),
                'name' => data_get($model, 'critical.name'),
            ],
            'topics' => $topics,
            'created_at' => $model->created_at,
            'created_by_name' => data_get($model, 'created_by_name'),
            'updated_at' => $model->updated_at,
            'updated_by_name' => data_get($model, 'created_by_name'),
            
        ];
    }
}