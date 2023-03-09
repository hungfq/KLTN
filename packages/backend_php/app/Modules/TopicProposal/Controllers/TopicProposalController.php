<?php

namespace App\Modules\TopicProposal\Controllers;

use App\Http\Controllers\ApiController;
use App\Modules\TopicProposal\Actions\TopicProposalDeleteAction;
use App\Modules\TopicProposal\Actions\TopicProposalStoreAction;
use App\Modules\TopicProposal\Actions\TopicProposalUpdateAction;
use App\Modules\TopicProposal\Actions\TopicProposalViewAction;
use App\Modules\TopicProposal\DTO\TopicProposalViewDTO;
use App\Modules\TopicProposal\Transformers\TopicProposalViewTransformer;
use App\Modules\TopicProposal\Validators\TopicProposalStoreValidator;
use App\Modules\TopicProposal\Validators\TopicProposalUpdateValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TopicProposalController extends ApiController
{
    public function view(TopicProposalViewAction $action, TopicProposalViewTransformer $transformer)
    {
        $results = $action->handle(TopicProposalViewDTO::fromRequest());

        if ($results instanceof Collection) {
            return $this->response->collection($results, $transformer);
        }

        return $this->response->paginator($results, $transformer);
    }

    public function store(TopicProposalStoreValidator $validator, TopicProposalStoreAction $action)
    {
        $validator->validate($this->request->all());

        DB::transaction(function () use ($action, $validator) {
            $action->handle(
                $validator->toDTO()
            );
        });

        return $this->responseSuccess();
    }

    public function update($id, TopicProposalUpdateValidator $validator, TopicProposalUpdateAction $action)
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

    public function delete($id, TopicProposalDeleteAction $action)
    {
        DB::transaction(function () use ($action, $id) {
            $action->handle($id);
        });

        return $this->responseSuccess();
    }
}