<?php

namespace App\Modules\Criteria\Transformers;

use League\Fractal\TransformerAbstract;

class CriteriaViewTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'description' => $model->description,
            'created_at' => $model->created_at,
            'created_by_name' => data_get($model, 'created_by_name'),
            'updated_at' => $model->updated_at,
            'updated_by_name' => data_get($model, 'updated_by_name'),
        ];
    }
}