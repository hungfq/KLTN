<?php

namespace App\Modules\Criteria\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CriteriaViewDTO extends FlexibleDataTransferObject
{
    public $title;
    public $description;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}