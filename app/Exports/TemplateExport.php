<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnFormatting
{
    use Exportable;

    private $data;
    private $headers;
    private $formats;
    private $styles;

    public function __construct(array $construct)
    {
        $this->data = $construct["data"];
        $this->headers = $construct["headers"] ?? [];
        $this->formats = $construct["formats"] ?? [];
        $this->styles = $construct["styles"] ?? [];
    }

    public function array(): array
    {
        return $this->data;
    }

    public function columnFormats(): array
    {
        return $this->formats;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function styles(Worksheet $sheet)
    {
        return [
                // Style the first row as bold text.
                1 => ["font" => ["bold" => true]],
            ] + $this->styles;
    }
}
