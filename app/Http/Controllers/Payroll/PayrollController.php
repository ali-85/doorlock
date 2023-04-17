<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\collectAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\leaveAndAbsence as leaveAbsence;

class PayrollController extends Controller
{
    public function getIndex(Request $request, $id)
    {
        if ($request->wantsJson()) {
            $data = DB::table('v_payroll')->where('user_id', $id)->orderBy('jam_masuk', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('lembur', function ($row) {
                    return $row->lembur.' Jam';
                })
                ->addColumn('action', function ($row) {
                    $btn_note = '<button class="btn btn-success text-white" onclick="edit('.$row->id.')" type="button"><i class="icon-note"></i></button>';
                    $btn =
                        '<div class="btn-group">' .
                        $btn_note .
                        '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            return view('pages.payroll.payroll', [
                'leaves' => leaveAbsence::all()
            ]);
        }
    }
    public function edit($id)
    {
        $data = collectAttendance::find($id);
        $payrolls = DB::table('tr_payroll')->where('collect_attendance_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'payrolls' => $payrolls
        ], 200);
    }
}
