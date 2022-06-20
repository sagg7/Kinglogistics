<?php

namespace App\Exports;

use App\Enums\CarrierPaymentEnum;
use App\Traits\Carrier\Payment\PaymentExportData;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CarrierPaymentExport implements FromArray, ShouldAutoSize, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    use Exportable, PaymentExportData;

    private $fileTitle;
    private $carrierPayment;
    private $bonuses;
    private $expenses;
    private $allRowsCount;
    private $totalsRowsCount;
    private $pageOrientation;
    private $paymentExportType;

    private $writerType;

    public function __construct($id, $writerType = Excel::XLSX)
    {
        $data = $this->getPaymentExportData($id);

        $title = $data['carrierPayment']->date->startOfWeek()->day . "-" . $data['carrierPayment']->date->endOfWeek()->day . " " . $data['carrierPayment']->date->format('F') . " " . $data['carrierPayment']->date->year;
        if ($data['carrierPayment']->status === CarrierPaymentEnum::CHARGES) {
            $title = "PAID CHARGES WEEK " . $data['carrierPayment']->date->startOfWeek()->day . "-" . $data['carrierPayment']->date->endOfWeek()->day . " " . $data['carrierPayment']->date->format('F') . " " . $data['carrierPayment']->date->year;
            $this->pageOrientation = PageSetup::ORIENTATION_PORTRAIT;
            $this->paymentExportType = CarrierPaymentEnum::CHARGES;
        } else {
            $title = "PAYMENT WEEK " . $title;
            $this->pageOrientation = PageSetup::ORIENTATION_LANDSCAPE;
            $this->paymentExportType = "payment";
        }

        $this->fileTitle = $title;
        $this->carrierPayment = $data['carrierPayment'];
        $this->bonuses = $data['bonuses'];
        $this->expenses = $data['expenses'];

        switch ($this->paymentExportType) {
            default:
            case "payment":
                // The +5 represents The two rows for the document title, the header of the main table, the subtotal and the total row
                $this->totalsRowsCount = count($this->bonuses) + count($this->expenses) + 2;
                $this->allRowsCount = count($this->carrierPayment->loads) + $this->totalsRowsCount + 3;
                break;
            case CarrierPaymentEnum::CHARGES:
                // The +5 represents The two rows for the document title, the header of the main table and the total row
                $this->totalsRowsCount = 1;
                $this->allRowsCount = count($this->expenses) + $this->totalsRowsCount + 3;
                break;
        }

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
        $this->fileName = "Payment - " . $this->carrierPayment->carrier->name ."  - " . $this->fileTitle . $format;
    }

    public function title(): string
    {
        return "PAYMENT SUMMARY";
    }

    public function styles(Worksheet $sheet)
    {

        $rowsTotalsStart = $this->allRowsCount - ($this->totalsRowsCount - 1);
        $rowsTotalsEnd = $this->allRowsCount;
        switch ($this->paymentExportType) {
            default:
            case "payment":
                $endColumn = "K";
                $customStyles = [
                    "A" . ($this->allRowsCount - ($this->totalsRowsCount - 1)) . ":J$this->allRowsCount" => [
                        'font' => [
                            'bold' => true,
                            'size' => 10,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        ],
                    ]
                ];
                for ($i = $rowsTotalsStart; $i <= $rowsTotalsEnd; $i++) {
                    $sheet->mergeCells("A$i:J$i");
                }
                break;
            case CarrierPaymentEnum::CHARGES:
                $endColumn = "B";
                $customStyles = [
                    "A$this->allRowsCount" => [
                        'font' => [
                            'bold' => true,
                            'size' => 10,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        ],
                    ]
                ];
                break;
        }

        $totalRowsAmount = $endColumn . ($this->allRowsCount - ($this->totalsRowsCount - 1)) . ":" . $endColumn . $this->allRowsCount;
        switch ($this->writerType) {
            default:
            case Excel::XLSX:
                $customStyles[$totalRowsAmount] =
                    [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_RIGHT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ];
                break;
            case Excel::MPDF:
                $customStyles[$totalRowsAmount] =
                    [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ];
                break;
        }

        $sheet->mergeCells("A1:". $endColumn. "1")
            ->mergeCells("A2:". $endColumn. "2");
        $sheet->getPageMargins()
            ->setLeft(.5)
            ->setRight(.5)
            ->setTop(.5)
            ->setBottom(.5)
            ->setHeader(.5);

        return array_merge([
            "A1:" . $endColumn . $this->allRowsCount => [
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
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            "A1:" . $endColumn . "3" => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFD9D9D9',
                    ],
                ],
            ],
            "A1:" . $endColumn . "1" => [
                'font' => [
                    'bold' => true,
                    'size' => 10,
                ],
            ],
                    "A3:" . $endColumn . "3" => [
                'font' => [
                    'bold' => true,
                    'size' => 10,
                ],
            ],
            $endColumn . "4:$endColumn" . (count($this->carrierPayment->loads) + count($this->bonuses) + 5) => [
                'numberFormat' => [
                    'formatCode' => NumberFormat::FORMAT_ACCOUNTING_USD,
                ]
            ],
            $endColumn . $this->allRowsCount => [
                'numberFormat' => [
                    'formatCode' => NumberFormat::FORMAT_ACCOUNTING_USD,
                ]
            ],
            "A" . ($this->allRowsCount - ($this->totalsRowsCount - 1)) . ":" . $endColumn . $this->allRowsCount => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFD9D9D9',
                    ],
                ],
            ],
        ] + $customStyles);
    }

    public function columnWidths(): array
    {
        switch ($this->paymentExportType) {
            default:
            case "payment":
                return [
                    "A" => 6,
                    "B" => 10,
                    "C" => 10,
                    "D" => 12,
                    "F" => 14,
                    "G" => 15,
                    "H" => 10,
                    "I" => 10,
                    "J" => 16,
                    "K" => 16,
                ];
            case CarrierPaymentEnum::CHARGES:
                return [
                    "A" => 52,
                    "B" => 52,
                ];
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $event->sheet
                    ->getPageSetup()
                    ->setOrientation($this->pageOrientation)
                    ->setFitToWidth(1);
            },
        ];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        switch ($this->paymentExportType) {
            default:
            case "payment":
            $headers = [
                [$this->carrierPayment->carrier->name],
                [$this->fileTitle],
                [
                    "#",
                    session('renames')->carrier ?? 'Carrier',
                    "Truck #",
                    "Load Date",
                    "Driver",
                    "Destination",
                    session('renames')->customer_reference ?? 'C Reference',
                    session('renames')->control_number ?? 'Control #',
                    session('renames')->bol ?? 'BOL',
                    "Miles",
                    "Rate",
                ],
            ];
            $content = [];
            foreach ($this->carrierPayment->loads as $key => $load) {
                $content[] = [
                    $key+1,
                    $this->carrierPayment->carrier->name,
                    $load->driver->truck->number ?? null,
                    $load->date->format('m/d/Y'),
                    $load->driver->name,
                    $load->trip->name,
                    $load->customer_reference,
                    $load->control_number,
                    $load->bol,
                    $load->mileage,
                    number_format($load->rate, 2),
                ];
            }
            $total = [];
            // Row Subtotal
            $total[] = [
                "Subtotal",'','','','','','','','','',$this->carrierPayment->gross_amount,
            ];
            foreach ($this->bonuses as $bonus) {
                $total[] = [
                    $bonus["name"],'','','','','','','','','',$bonus["amount"],
                ];
            }
            foreach ($this->expenses as $expense) {
                $total[] = [
                    $expense["name"],'','','','','','','','','',"$(" . number_format($expense["amount"], 2) . ")",
                ];
            }
            // Row Total
            $total[] = [
                "Total",'','','','','','','','','',$this->carrierPayment->total,
            ];
                break;
            case CarrierPaymentEnum::CHARGES:
                $headers = [
                    [$this->carrierPayment->carrier->name],
                    [$this->fileTitle],
                    [
                        "Description",
                        "Amount",
                    ],
                ];
                $content = [];
                foreach ($this->expenses as $expense) {
                    $content[] = [$expense["name"],$expense["amount"],];
                }
                // Row Total
                $total = [["Total", -$this->carrierPayment->total]];
                break;
        }

        return array_merge($headers, $content, $total);
    }
}
