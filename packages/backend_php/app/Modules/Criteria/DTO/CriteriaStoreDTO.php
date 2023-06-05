<?php

namespace App\Modules\Criteria\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CriteriaStoreDTO extends FlexibleDataTransferObject
{
    public $title;
    public $description;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ]);
    }
}