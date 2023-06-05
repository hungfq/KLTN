<?php

namespace App\Modules\Topic\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicSetGradeDTO extends FlexibleDataTransferObject
{
    public $id;
    public $type;
    public $details;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'id' => $request->input('id'),
            'type' => $request->input('type'),
            'details' => $request->input('details'),
        ]);
    }
}