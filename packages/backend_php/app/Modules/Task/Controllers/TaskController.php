<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\ApiController;
use App\Modules\Task\Actions\TaskCommentAddAction;
use App\Modules\Task\Actions\TaskCommentDeleteAction;
use App\Modules\Task\Actions\TaskShowAction;
use App\Modules\Task\Actions\TaskStoreAction;
use App\Modules\Task\Actions\TaskUpdateAction;
use App\Modules\Task\Actions\TaskViewAction;
use App\Modules\Task\DTO\TaskViewDTO;
use App\Modules\Task\Transformers\TaskShowTransformer;
use App\Modules\Task\Transformers\TaskViewTransformer;
use App\Modules\Task\Validators\TaskCommentAddValidator;
use App\Modules\Task\Validators\TaskStoreValidator;
use App\Modules\Task\Validators\TaskUpdateValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskController extends ApiController
{
    public function view(TaskViewAction $action, TaskViewTransformer $transformer)
    {
        $results = $action->handle(TaskViewDTO::fromRequest());

        if ($results instanceof Collection) {
            return $this->response->collection($results, $transformer);
        }

        return $this->response->paginator($results, $transformer);
    }

    public function store(TaskStoreValidator $validator, TaskStoreAction $action)
    {
        $validator->validate($this->request->all());

        DB::transaction(function () use ($action, $validator) {
            $action->handle(
                $validator->toDTO()
            );
        });

        return $this->responseSuccess();
    }

    public function show($id, TaskShowAction $action, TaskShowTransformer $transformer)
    {
        $results = $action->handle($id);

        return $this->response->item($results, $transformer);
    }

    public function update($id, TaskUpdateValidator $validator, TaskUpdateAction $action)
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

    public function addComment($id, TaskCommentAddValidator $validator, TaskCommentAddAction $action)
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

    public function deleteComment($commentId, TaskCommentDeleteAction $action)
    {
        DB::transaction(function () use ($action, $commentId) {
            $action->handle($commentId);
        });

        return $this->responseSuccess();
    }
}