<?php

namespace App\Modules\User\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class UserViewDTO extends FlexibleDataTransferObject
{
    public $search;
    public $type;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'search' => $request->input('search'),
            'type' => $request->input('type'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}