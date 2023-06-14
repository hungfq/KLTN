<?php

namespace App\Modules\User\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class UserViewDTO extends FlexibleDataTransferObject
{
    public $search;
    public $type;
    public $is_active;
    public $not_done_any_topic;
    public $limit;
    public $sort;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'search' => $request->input('search'),
            'type' => $request->input('type'),
            'is_active' => $request->input('is_active'),
            'not_done_any_topic' => $request->input('not_done_any_topic'),
            'limit' => $request->input('limit'),
            'sort' => $request->input('sort'),
        ]);
    }
}