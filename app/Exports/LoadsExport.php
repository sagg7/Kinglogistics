<?php

namespace App\Exports;

use App\Models\Broker;
use App\Models\ShipperInvoice;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoadsExport implements FromArray, ShouldAutoSize, WithStyles, WithColumnWidths, WithTitle
{
    use Exportable;

    private $result;

    /**
     * Optional Writer Type
     */
    private $writerType;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function title(): string
    {
        return 'DISPATCH LOADS REPORTS';
    }

    public function styles(Worksheet $sheet)
    {

    }

    public function columnWidths(): array
    {
        return [
            'A' => 20
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        $headers = [
            [
                "Name",
                "Total",
                "1",
                "2",
                "3",
                "4",
                "5",
                "6",
                "7",
                "8",
                "9",
                "10",
                "11",
                "12",
                "13",
                "14",
                "15",
                "16",
                "17",
                "18",
                "19",
                "20",
                "21",
            ],
        ];
        $content = [];
        $drivers = $this->result;
        foreach ($drivers as $driver){
            $array = [];
            $cont = 0;
            foreach ($driver->loads as $load){
                if ($load)
                    $array[] = $load->customer_reference;
                    $cont++;
            }
            array_unshift($array, $cont);
            array_unshift($array, $driver->name);
            if ($cont > 0)
                $content[] = $array;
        }
        return array_merge($headers, $content);
    }
}
