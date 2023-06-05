<?php

namespace App\Modules\Criteria\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CriteriaUpdateDTO extends FlexibleDataTransferObject
{
    public $id;
    public $title;
    public $description;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ]);
    }
}