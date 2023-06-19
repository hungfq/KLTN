<?php

namespace App\Modules\Schedule\Controllers;

use App\Http\Controllers\ApiController;
use App\Modules\Schedule\Actions\ScheduleDeleteAction;
use App\Modules\Schedule\Actions\ScheduleExportGradeAction;
use App\Modules\Schedule\Actions\ScheduleExportTopicAction;
use App\Modules\Schedule\Actions\ScheduleImportStudentAction;
use App\Modules\Schedule\Actions\ScheduleShowAction;
use App\Modules\Schedule\Actions\ScheduleStoreAction;
use App\Modules\Schedule\Actions\ScheduleStudentViewTodayAction;
use App\Modules\Schedule\Actions\ScheduleSyncCriteriaAction;
use App\Modules\Schedule\Actions\ScheduleUpdateAction;
use App\Modules\Schedule\Actions\ScheduleUpdateScoreRateAction;
use App\Modules\Schedule\Actions\ScheduleUpdateStudentAction;
use App\Modules\Schedule\Actions\ScheduleViewAction;
use App\Modules\Schedule\Actions\ScheduleViewCriteriaAction;
use App\Modules\Schedule\Actions\ScheduleViewStudentAction;
use App\Modules\Schedule\Actions\ScheduleViewWithTopicAction;
use App\Modules\Schedule\DTO\ScheduleExportGradeDTO;
use App\Modules\Schedule\DTO\ScheduleViewDTO;
use App\Modules\Schedule\DTO\ScheduleViewStudentDTO;
use App\Modules\Schedule\DTO\ScheduleViewWithTopicDTO;
use App\Modules\Schedule\Transformers\ScheduleShowTransformer;
use App\Modules\Schedule\Transformers\ScheduleViewCriteriaTransformer;
use App\Modules\Schedule\Transformers\ScheduleViewStudentTransformer;
use App\Modules\Schedule\Transformers\ScheduleViewTransformer;
use App\Modules\Schedule\Transformers\ScheduleViewWithTopicTransformer;
use App\Modules\Schedule\Validators\ScheduleImportStudentValidator;
use App\Modules\Schedule\Validators\ScheduleStoreValidator;
use App\Modules\Schedule\Validators\ScheduleSyncCriteriaValidator;
use App\Modules\Schedule\Validators\ScheduleUpdateScoreRateValidator;
use App\Modules\Schedule\Validators\ScheduleUpdateStudentValidator;
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

    public function viewWithTopic(ScheduleViewWithTopicAction $action, ScheduleViewWithTopicTransformer $transformer)
    {
        $results = $action->handle(ScheduleViewWithTopicDTO::fromRequest());

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
        $this->request->merge([
            'id' => $id
        ]);

        $results = $action->handle(ScheduleViewStudentDTO::fromRequest());

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

    public function show($id, ScheduleShowAction $action, ScheduleShowTransformer $transformer)
    {
        $results = $action->handle($id);

        return $this->response->item($results, $transformer);
    }

    public function updateStudent($id, ScheduleUpdateStudentValidator $validator, ScheduleUpdateStudentAction $action)
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

    public function updateScoreRate($id, ScheduleUpdateScoreRateValidator $validator, ScheduleUpdateScoreRateAction $action)
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

    public function studentViewToday(ScheduleStudentViewTodayAction $action)
    {
        return $action->handle();
    }

    public function viewCriteria($id, ScheduleViewCriteriaAction $action, ScheduleViewCriteriaTransformer $transformer)
    {
        $results = $action->handle($id);

        if ($results instanceof Collection) {
            return $this->response->collection($results, $transformer);
        }

        return $this->response->paginator($results, $transformer);
    }

    public function syncCriteria($id, ScheduleSyncCriteriaValidator $validator, ScheduleSyncCriteriaAction $action)
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

    public function importStudent($id, ScheduleImportStudentAction $action, ScheduleImportStudentValidator $validator)
    {
        $this->request->merge([
            'id' => $id
        ]);

        $validator->validate($this->request->all());

        $result = DB::transaction(function () use ($action, $validator) {
            return $action->handle($validator->toDTO());
        });

        return $result === true ? $this->responseSuccess() : $result;
    }

    public function exportTopic($id, ScheduleExportTopicAction $action)
    {
        return $action->handle($id);
    }

    public function exportGrade($id, ScheduleExportGradeAction $action)
    {
        $this->request->merge([
            'id' => $id
        ]);

        return $action->handle(ScheduleExportGradeDTO::fromRequest());
    }
}