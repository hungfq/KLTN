<?php

namespace App\Modules\Topic\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicUpdateMultiDTO extends FlexibleDataTransferObject
{
    public $critical_id;
    public $topic_ids;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'critical_id' => $request->input('critical_id'),
            'topic_ids' => $request->input('topic_ids'),
        ]);
    }
}
