<?php

namespace App\Modules\Committee\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CommitteeViewDTO extends FlexibleDataTransferObject
{
    public $search;
    public $presidentId;
    public $secretaryId;
    public $criticalId;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'search' => $request->input('search'),
            'presidentId' => $request->input('committeePresidentId'),
            'secretaryId' => $request->input('committeeSecretaryId'),
            'criticalId' => $request->input('criticalLecturerId'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}