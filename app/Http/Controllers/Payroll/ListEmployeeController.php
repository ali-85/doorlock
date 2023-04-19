<?php
namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\memployee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ListEmployeeController extends Controller
{
    public function getIndex(Request $request)
    {
        if ($request->wantsJson()) {
            $data = memployee::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn_show =
                        '<a class="btn btn-primary" href="' .
                        route('payroll.employee.index', $row->id) .
                        '" type="button"><i class="icon-eye"></i></a>';
                    $btn = '<div class="btn-group">' . $btn_show . '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            return view('pages.payroll.list-employee');
        }
    }
    public function getDownloadPdf(Request $request)
    {
        $data = DB::table('v_payroll AS vp')
            ->selectRaw(
                "vp.id, vp.nama, vp.basic_salary, SUM(vp.salary) AS salary, vp.pembayaran,
                5000*SUM(vp.lembur) AS lembur, SUM(vp.lembur) AS jumlah_lembur, 20000*SUM(vp.lembur2) AS lembur2,
                JSON_ARRAYAGG(JSON_OBJECT('category', laa.category, 'remark', laa.remark, 'value', laa.value_1A)) AS insentif"
            )
            ->leftJoin(
                'tr_payroll AS trp',
                'trp.collect_attendance_id',
                '=',
                'vp.id'
            )
            ->leftJoin(
                'leave_and_absences AS laa',
                'laa.id',
                '=',
                'trp.leave_absence_id'
            )
            ->whereBetween(DB::raw('DATE(vp.jam_masuk)'), [
                $request->start,
                $request->akhir,
            ])
            ->groupBy('vp.user_id')
            ->get();
        $pdf = Pdf::loadView('export.salary_slip', [
            'data' => $data,
            'pekan' => $request->start,
        ]);
        return $pdf->stream();
    }
}
