<?php

namespace App\Modules\Committee\Transformers;

use League\Fractal\TransformerAbstract;

class CommitteeShowTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $topics = [];
        foreach (data_get($model, 'topics', []) as $topic) {
            $topics[] = data_get($topic, 'id');
        }

        return [
            '_id' => $model->id,
            'schedule_id' => $model->schedule_id,
            'schedule_code' => data_get($model, 'schedule.code'),
            'schedule_name' => data_get($model, 'schedule.name'),
            'code' => $model->code,
            'name' => $model->name,
            'president_id' => data_get($model, 'president.id'),
            'president_code' => data_get($model, 'president.code'),
            'president_name' => data_get($model, 'president.name'),
            'secretary_id' => data_get($model, 'secretary.id'),
            'secretary_code' => data_get($model, 'secretary.code'),
            'secretary_name' => data_get($model, 'secretary.name'),
            'critical_id' => data_get($model, 'critical.id'),
            'critical_code' => data_get($model, 'critical.code'),
            'critical_name' => data_get($model, 'critical.name'),
            'topics' => $topics,
            'created_at' => $model->created_at,
            'created_by_name' => data_get($model, 'created_by_name'),
            'updated_at' => $model->updated_at,
            'updated_by_name' => data_get($model, 'created_by_name'),
            'list_topics' => data_get($model, 'topics')
        ];
    }
}