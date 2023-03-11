<?php

namespace App\Modules\Schedule\Transformers;

use League\Fractal\TransformerAbstract;

class ScheduleViewWithTopicTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $topics = [];
        foreach (data_get($model, 'topics', []) as $topic) {
            $topics[] = [
                '_id' => data_get($topic, 'id'),
                'code' => data_get($topic, 'code'),
                'title' => data_get($topic, 'title'),
            ];
        }
        return [
            '_id' => $model->id,
            'code' => $model->code,
            'name' => $model->name,
            'topics' => $topics,
        ];
    }
}