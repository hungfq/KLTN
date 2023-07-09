<?php

namespace App\Modules\Schedule\Actions\Helpers;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ScheduleTopicExport implements FromView, WithColumnWidths, ShouldAutoSize
{
    public function __construct($data, $fileName = 'ExcelTemplate')
    {
        $this->data = $data;
        $this->fileName = $fileName;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 10,
            'C' => 25,
            'D' => 15,
            'E' => 50,
            'F' => 10,
            'G' => 25,
            'H' => 10,
            'I' => 25,
        ];
    }

    public function view(): View
    {
        return view($this->fileName, $this->data);
    }
}




