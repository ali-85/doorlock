<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SalaryTransferExport implements FromView, WithEvents, WithProperties, WithColumnWidths, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use RegistersEventListeners;
    use Exportable;
    protected $start, $end, $mode;
    public function __construct($start, $end, $mode){
        $this->start = $start;
        $this->end = $end;
        $this->mode = $mode;
    }
    public function view(): View
    {
        $data = DB::table('v_payroll AS vp')
            ->selectRaw("vp.id, vp.nama, memp.nip,
                CASE WHEN memp.bank_account <> 'NULL' THEN bank.nama_bank ELSE 'CASH' END AS transfer_type, memp.credited_accont,
                memp.bank_name, memp.basic_salary*SUM(vp.jam_kerja) AS salary,
                CASE WHEN DAYNAME(vp.jam_masuk) = 'Sunday' THEN 7500*SUM(vp.lembur) ELSE 5000*SUM(vp.lembur) END AS lembur,
                CASE WHEN DAYNAME(vp.jam_masuk) = 'Sunday' THEN 20000*SUM(vp.lembur2) ELSE 0 END AS lembur2,
                (SELECT transaction_id FROM mrequest_payment WHERE start_date = '2023-04-13' AND end_date = '2023-04-19') AS transaction_id")
            ->join('memployees AS memp', 'memp.id', '=', 'vp.user_id')
            ->leftJoin('mbanks AS bank', 'bank.id', '=', 'memp.bank_account')
            ->where('memp.payment_mode', $this->mode)
            ->where('memp.transfer_type', 1)
            ->whereBetween(DB::raw('DATE(vp.jam_masuk)'), [$this->start, $this->end])
            ->groupBy('vp.user_id')
            ->get();
        return view('export.salary_transfer', [
            'data' => $data
        ]);
    }
    public function properties(): array
    {
        return [
            'creator'        => env('APP_SYSTEM','Door Lock Access & Payroll Systems'). env('APP_NAME','PT Cahaya Sukses Plastindo'),
            'lastModifiedBy' => env('APP_SYSTEM','Door Lock Access & Payroll Systems') . env('APP_NAME','PT Cahaya Sukses Plastindo'),
            'title'          => 'Payroll Report : ' . $this->start,
            'description'    => 'Latest Payroll at Payroll Systems' . env('APP_NAME','PT Cahaya Sukses Plastindo'),
            'subject'        => 'payroll',
            'keywords'       => 'payroll,export,spreadsheet',
            'category'       => 'payroll',
            'company'        => env('APP_NAME','PT Cahaya Sukses Plastindo'),
        ];
    }
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_GENERAL,
            'H' => NumberFormat::FORMAT_NUMBER,
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 9,
            'B' => 20,
            'C' => 12,
            'D' => 17,
            'E' => 61,
            'F' => 23,
            'G' => 28,
            'H' => 20,
            'I' => 9,
            'J' => 83,
            'K' => 19,
            'L' => 18,
            'M' => 23,

        ];
    }
    public static function afterSheet(AfterSheet $event)
    {
        $range = 'A1:M1';
        $centering = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ];
        $sheet = $event->sheet->getDelegate();
        $sheet->getStyle($range)->getFont()->setSize(11)->setBold(true);
        $sheet->getStyle($range)->getFont()
            ->getColor()
            ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle($range)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('000000');
        $sheet->getStyle(1)->applyFromArray($centering);
        $sheet->setAutoFilter('A1:M1');
    }
}
