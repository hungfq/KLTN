<?php

namespace App\Modules\User\Transformers;


use League\Fractal\TransformerAbstract;

class UserViewTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            '_id' => $model->id,
            'email' => $model->email,
            'code' => $model->code,
            'name' => $model->name,
            'gender' => $model->gender,
            'status' => $model->status,
            'picture' => $model->picture,
            'created_at' => $model->created_at,
            'created_by_name' => data_get($model, 'createdBy.name'),
            'updated_at' => $model->updated_at,
            'updated_by_name' => data_get($model, 'updatedBy.name'),
        ];
    }
}
