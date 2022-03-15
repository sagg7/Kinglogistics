<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Excel;

class IncomeErrorsExport implements FromArray, ShouldAutoSize, WithTitle
{
    use Exportable;

    private $errors;

    private $writerType = Excel::XLSX;

    public function title(): string
    {
        return 'Income Excel Errors';
    }

    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->errors;
    }
}
