<?php

namespace App\Modules\Topic\Controllers;

use App\Http\Controllers\ApiController;
use App\Modules\Topic\Actions\TopicDeleteAction;
use App\Modules\Topic\Actions\TopicStoreAction;
use App\Modules\Topic\Actions\TopicUpdateAction;
use App\Modules\Topic\Actions\TopicViewAction;
use App\Modules\Topic\DTO\TopicViewDTO;
use App\Modules\Topic\Transformers\TopicViewTransformer;
use App\Modules\Topic\Validators\TopicStoreValidator;
use App\Modules\Topic\Validators\TopicUpdateValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TopicController extends ApiController
{
    public function view(TopicViewAction $action, TopicViewTransformer $transformer)
    {
        $results = $action->handle(TopicViewDTO::fromRequest());

        if ($results instanceof Collection) {
            return $this->response->collection($results, $transformer);
        }

        return $this->response->paginator($results, $transformer);
    }

    public function store(TopicStoreValidator $validator, TopicStoreAction $action)
    {
        $validator->validate($this->request->all());

        DB::transaction(function () use ($action, $validator) {
            $action->handle(
                $validator->toDTO()
            );
        });

        return $this->responseSuccess();
    }


    public function update($id, TopicUpdateValidator $validator, TopicUpdateAction $action)
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

    public function delete($id, TopicDeleteAction $action)
    {

        DB::transaction(function () use ($action, $id) {
            $action->handle($id);
        });

        return $this->responseSuccess();
    }
}