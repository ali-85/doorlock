<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use DateTime;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class EmployeeAbsenceExport implements
    FromView,
    WithEvents,
    WithProperties,
    WithColumnWidths
{
    use RegistersEventListeners;
    use Exportable;
    protected $start, $end;
    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }
    public function view(): View
    {
        $dates = [];
        $begin = new DateTime($this->start);
        $finish = new DateTime($this->end);
        for ($i = $begin; $i <= $finish; $i->modify('+1 day')) {
            array_push($dates, $i->format('Y-m-d'));
        }
        $data = DB::table('v_payroll')
            ->selectRaw(
                "nama, SUM(lembur) AS lembur, SUM(lembur2) AS lembur2,
                JSON_ARRAYAGG(JSON_OBJECT('tanggal', DATE(jam_masuk), 'masuk', TIME(jam_masuk), 'keluar', TIME(jam_keluar))) AS tanggal"
            )
            ->whereBetween(DB::raw('DATE(jam_masuk)'), [
                date('Y-m-d', strtotime($this->start)),
                date('Y-m-d', strtotime($this->end)),
            ])
            ->groupBy('user_id')
            ->get();
        return view('export.absensi_excel', [
            'dates' => $dates,
            'data' => $data,
        ]);
    }
    public function properties(): array
    {
        return [
            'creator' =>
                env('APP_SYSTEM', 'Door Lock Access & Payroll Systems') .
                env('APP_NAME', 'PT Cahaya Sukses Plastindo'),
            'lastModifiedBy' =>
                env('APP_SYSTEM', 'Door Lock Access & Payroll Systems') .
                env('APP_NAME', 'PT Cahaya Sukses Plastindo'),
            'title' => 'Payroll Report : ' . $this->start,
            'description' =>
                'Latest Payroll at Payroll Systems' .
                env('APP_NAME', 'PT Cahaya Sukses Plastindo'),
            'subject' => 'payroll',
            'keywords' => 'payroll,export,spreadsheet',
            'category' => 'payroll',
            'company' => env('APP_NAME', 'PT Cahaya Sukses Plastindo'),
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 23,
            'D' => 23,
            'E' => 23,
            'F' => 23,
            'G' => 23,
            'H' => 23,
            'I' => 23,
            'J' => 23,
            'K' => 23,
            'L' => 23,
            'M' => 23,
            'N' => 23,
            'O' => 23,
            'P' => 23,
            'Q' => 23,
            'R' => 23,
            'S' => 23,
            'T' => 23,
            'U' => 23,
            'V' => 23,
            'W' => 23,
            'X' => 23,
            'Y' => 23,
            'Z' => 23,
            'AA' => 23,
            'AB' => 23,
            'AC' => 23,
            'AD' => 23,
            'AE' => 23,
            'AF' => 23,
            'AG' => 23,
            'AH' => 23,
            'AI' => 23,
        ];
    }
    public static function afterSheet(AfterSheet $event)
    {
        $range = 'A1:AZ1';
        $centering = [
            'alignment' => [
                'horizontal' =>
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' =>
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
        $sheet = $event->sheet->getDelegate();
        $sheet
            ->getStyle($range)
            ->getFont()
            ->setSize(11)
            ->setBold(true);
        $sheet
            ->getStyle($range)
            ->getFont()
            ->getColor()
            ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet
            ->getStyle($range)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('002060');
        $sheet->getStyle(1)->applyFromArray($centering);
    }
}
