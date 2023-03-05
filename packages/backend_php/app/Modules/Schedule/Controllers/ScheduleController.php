<?php

namespace App\Modules\Schedule\Controllers;

use App\Http\Controllers\ApiController;
use App\Modules\Schedule\Actions\ScheduleDeleteAction;
use App\Modules\Schedule\Actions\ScheduleStoreAction;
use App\Modules\Schedule\Actions\ScheduleUpdateAction;
use App\Modules\Schedule\Actions\ScheduleViewAction;
use App\Modules\Schedule\Actions\ScheduleViewStudentAction;
use App\Modules\Schedule\DTO\ScheduleViewDTO;
use App\Modules\Schedule\Transformers\ScheduleViewStudentTransformer;
use App\Modules\Schedule\Transformers\ScheduleViewTransformer;
use App\Modules\Schedule\Validators\ScheduleStoreValidator;
use App\Modules\Schedule\Validators\ScheduleUpdateValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScheduleController extends ApiController
{
    public function view(ScheduleViewAction $action, ScheduleViewTransformer $transformer)
    {
        $results = $action->handle(ScheduleViewDTO::fromRequest());

        if ($results instanceof Collection) {
            return $this->response->collection($results, $transformer);
        }

        return $this->response->paginator($results, $transformer);
    }

    public function store(ScheduleStoreValidator $validator, ScheduleStoreAction $action)
    {
        $validator->validate($this->request->all());

        DB::transaction(function () use ($action, $validator) {
            $action->handle(
                $validator->toDTO()
            );
        });

        return $this->responseSuccess();
    }

    public function viewStudent($id, ScheduleViewStudentAction $action, ScheduleViewStudentTransformer $transformer)
    {
        $results = $action->handle($id);

        if ($results instanceof Collection) {
            return $this->response->collection($results, $transformer);
        }

        return $this->response->paginator($results, $transformer);
    }

    public function update($id, ScheduleUpdateValidator $validator, ScheduleUpdateAction $action)
    {
        $this->request->merge([
            'id' => $id
        ]);

        $validator->validate($this->request->all());

        DB::transaction(function () use ($action, $validator) {
            $action->handle(
                $validator->toDTO()
            );
        });

        return $this->responseSuccess();
    }

    public function delete($id, ScheduleDeleteAction $action)
    {

        DB::transaction(function () use ($action, $id) {
            $action->handle($id);
        });

        return $this->responseSuccess();
    }
}