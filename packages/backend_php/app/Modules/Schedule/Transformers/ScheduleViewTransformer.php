<?php

namespace App\Modules\Schedule\Transformers;

use League\Fractal\TransformerAbstract;

class ScheduleViewTransformer extends TransformerAbstract
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
            'name' => $model->name,
            'description' => $model->description,
            'startDate' => $model->start_date,
            'deadline' => $model->end_date,
            'startProposalDate' => $model->proposal_start,
            'endProposalDate' => $model->proposal_end,
            'startApproveDate' => $model->approve_start,
            'endApproveDate' => $model->approve_end,
            'endRegisterDate' => $model->register_end,
            'startRegisterDate' => $model->register_start,
            'students' => $students,
            'created_at' => $model->created_at,
            'created_by_name' => data_get($model, 'created_by_name'),
            'updated_at' => $model->updated_at,
            'updated_by_name' => data_get($model, 'updated_by_name'),
        ];
    }
}