<?php

namespace App\Modules\Schedule\Actions\Helpers;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ScheduleGradeExport implements FromView, WithColumnWidths, ShouldAutoSize
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
            'D' => 10,
            'E' => 60,
            'F' => 20,
            'G' => 10,
            'H' => 25,
            'I' => 10,
            'J' => 10,
            'K' => 25,
            'L' => 10,
        ];
    }

    public function view(): View
    {
        return view($this->fileName, $this->data);
    }
}




