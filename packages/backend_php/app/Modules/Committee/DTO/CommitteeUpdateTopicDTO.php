<?php

namespace App\Modules\Committee\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CommitteeUpdateTopicDTO extends FlexibleDataTransferObject
{
    public $id;
    public $topics;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'topics' => $request->input('topics'),
        ]);
    }
}