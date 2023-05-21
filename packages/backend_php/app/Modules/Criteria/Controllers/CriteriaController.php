<?php

namespace App\Modules\Criteria\Controllers;

use App\Http\Controllers\ApiController;
use App\Modules\Criteria\Actions\CriteriaCommentAddAction;
use App\Modules\Criteria\Actions\CriteriaCommentDeleteAction;
use App\Modules\Criteria\Actions\CriteriaShowAction;
use App\Modules\Criteria\Actions\CriteriaStoreAction;
use App\Modules\Criteria\Actions\CriteriaUpdateAction;
use App\Modules\Criteria\Actions\CriteriaViewAction;
use App\Modules\Criteria\DTO\CriteriaViewDTO;
use App\Modules\Criteria\Transformers\CriteriaShowTransformer;
use App\Modules\Criteria\Transformers\CriteriaViewTransformer;
use App\Modules\Criteria\Validators\CriteriaCommentAddValidator;
use App\Modules\Criteria\Validators\CriteriaStoreValidator;
use App\Modules\Criteria\Validators\CriteriaUpdateValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CriteriaController extends ApiController
{
    public function view(CriteriaViewAction $action, CriteriaViewTransformer $transformer)
    {
        $results = $action->handle(CriteriaViewDTO::fromRequest());

        if ($results instanceof Collection) {
            return $this->response->collection($results, $transformer);
        }

        return $this->response->paginator($results, $transformer);
    }

    public function store(CriteriaStoreValidator $validator, CriteriaStoreAction $action)
    {
        $validator->validate($this->request->all());

        DB::transaction(function () use ($action, $validator) {
            $action->handle(
                $validator->toDTO()
            );
        });

        return $this->responseSuccess();
    }

    public function show($id, CriteriaShowAction $action, CriteriaShowTransformer $transformer)
    {
        $results = $action->handle($id);

        return $this->response->item($results, $transformer);
    }

    public function update($id, CriteriaUpdateValidator $validator, CriteriaUpdateAction $action)
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

    public function addComment($id, CriteriaCommentAddValidator $validator, CriteriaCommentAddAction $action)
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

    public function deleteComment($commentId, CriteriaCommentDeleteAction $action)
    {
        DB::transaction(function () use ($action, $commentId) {
            $action->handle($commentId);
        });

        return $this->responseSuccess();
    }
}