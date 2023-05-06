<?php

namespace App\Modules\Committee\Transformers;

use League\Fractal\TransformerAbstract;

class CommitteeShowTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            '_id' => $model->id,
            'code' => $model->code,
            'name' => $model->name,
            'president_id' => data_get($model, 'president.id'),
            'president_code' => data_get($model, 'president.code'),
            'president_name' => data_get($model, 'president.name'),
            'secretary_id' => data_get($model, 'secretary.id'),
            'secretary_code' => data_get($model, 'secretary.code'),
            'secretary_name' => data_get($model, 'secretary.name'),
            'lecturer_id' => data_get($model, 'critical.id'),
            'lecturer_code' => data_get($model, 'critical.code'),
            'lecturer_name' => data_get($model, 'critical.name'),
            'created_at' => $model->created_at,
            'created_by_name' => data_get($model, 'created_by_name'),
            'updated_at' => $model->updated_at,
            'updated_by_name' => data_get($model, 'created_by_name'),
        ];
    }
}