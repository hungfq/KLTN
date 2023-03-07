<?php

namespace App\Modules\Committee\Controllers;

use App\Http\Controllers\ApiController;
use App\Modules\Committee\Actions\CommitteeDeleteAction;
use App\Modules\Committee\Actions\CommitteeStoreAction;
use App\Modules\Committee\Actions\CommitteeUpdateAction;
use App\Modules\Committee\Actions\CommitteeViewAction;
use App\Modules\Committee\DTO\CommitteeViewDTO;
use App\Modules\Committee\Transformers\CommitteeViewTransformer;
use App\Modules\Committee\Validators\CommitteeStoreValidator;
use App\Modules\Committee\Validators\CommitteeUpdateValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommitteeController extends ApiController
{
    public function view(CommitteeViewAction $action, CommitteeViewTransformer $transformer)
    {
        $results = $action->handle(CommitteeViewDTO::fromRequest());

        if ($results instanceof Collection) {
            return $this->response->collection($results, $transformer);
        }

        return $this->response->paginator($results, $transformer);
    }

    public function store(CommitteeStoreValidator $validator, CommitteeStoreAction $action)
    {
        $validator->validate($this->request->all());

        DB::transaction(function () use ($action, $validator) {
            $action->handle(
                $validator->toDTO()
            );
        });

        return $this->responseSuccess();
    }

    public function update($id, CommitteeUpdateValidator $validator, CommitteeUpdateAction $action)
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

    public function delete($id, CommitteeDeleteAction $action)
    {

        DB::transaction(function () use ($action, $id) {
            $action->handle($id);
        });

        return $this->responseSuccess();
    }
}