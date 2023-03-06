<?php

namespace App\Modules\Topic\DTO;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class TopicImportDTO extends FlexibleDataTransferObject
{
    public $file;

    public static function fromRequest($request = null)
    {
        $request = $request ?? app('request');

        return new self([
            'file' => $request->file('file'),
        ]);
    }
}