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

class ShipperInvoiceExport implements FromArray, ShouldAutoSize, WithStyles, WithColumnWidths, WithTitle
{
    use Exportable;

    private $broker;
    private $invoice;

    /**
     * Optional Writer Type
     */
    private $writerType;

    public function __construct($invoice_id, $writerType = Excel::XLSX)
    {
        $this->broker = Broker::findOrFail(1);
        $this->invoice = ShipperInvoice::with([
            'shipper:id,name',
            'loads.driver.truck',
        ])
            ->findOrFail($invoice_id);

        $this->writerType = $writerType;
        switch ($writerType) {
            default:
            case Excel::XLSX:
                $format = "." . strtolower(Excel::XLSX);
                break;
            case Excel::MPDF:
                $format = ".pdf";
                break;
        }
        $this->fileName = "Shipper Invoice - " . $this->invoice->shipper->name ."  - " . $this->invoice->date->format('m-d-Y') . $format;
    }

    public function title(): string
    {
        return 'SUMMARY INVOICE';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:H1')
            ->mergeCells('A2:H2')
            ->mergeCells('A3:H3')
            ->mergeCells('A4:H4')
            ->mergeCells('A5:H5');

        for ($i = 1; $i < 48; $i++) {
            if ($i === 6)
                $sheet->getRowDimension($i)->setRowHeight(24.082);
            else
                $sheet->getRowDimension($i)->setRowHeight(12.041);
        }

        return [
            'A2:H47' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'A1:H47' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => [
                            'argb' => '000000'
                        ],
                    ]
                ],
                'font' => [
                    'size' => 9,
                ],
            ],
            'A1:h6' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFD9D9D9',
                    ],
                ],
            ],
            'G7:H47' => [
                'font' => [
                    'italic' => true,
                ]
            ],
            'H7:H47' => [
                'numberFormat' => [
                    'formatCode' => NumberFormat::FORMAT_ACCOUNTING_USD,
                ]
            ],
            'A2' => [
                'font' => [
                    'bold' => true,
                    'size' => 10,
                ],
            ],
            1 => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ],
            6 => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            47 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFD9D9D9',
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 11,
            'B' => 20,
            'C' => 18,
            'D' => 11,
            'E' => 11,
            'F' => 9,
            'G' => 6,
            'H' => 10,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        $headers = [
            ["Date Invoiced: " . $this->invoice->date->format('m/d/Y')],
            [$this->broker->name],
            ["Account Payable: RTS Financial Services PO Box 840267 Dallas, TX 75284-0267"], // TODO: ADD PO BOX DATA
            ["WEEK ENDING: " . $this->invoice->date->endOfWeek()->format('m/d/Y')],
            ["INVOICE #: " . $this->invoice->id],
            [
                "LOAD DATE",
                "DRIVER",
                "WELL NAME",
                "Sand Ticket #",
                "Sandbox" . PHP_EOL . "Control",
                "BOL",
                "MILES",
                "RATE",
            ],
        ];
        $content = [];
        for ($i = 0; $i < 40; $i++) {
            $load = $this->invoice->loads[$i] ?? null;
            if ($load)
                $content[] = [
                    $load->date->format('m/d/Y'),
                    $load->driver->name,
                    $load->trip->name,
                    $load->customer_reference,
                    $load->control_number,
                    $load->bol,
                    $load->mileage,
                    $load->shipper_rate,
                ];
            else
                $content[] = ['','','','','','','',''];
        }
        $total = [['','','','','','','',$this->invoice->total]];
        return array_merge($headers, $content, $total);
    }
}
