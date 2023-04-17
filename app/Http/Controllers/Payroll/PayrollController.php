<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\collectAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\leaveAndAbsence as leaveAbsence;
use Illuminate\Support\Facades\Validator;

class PayrollController extends Controller
{
    protected $rules = [
        'jam_masuk' => 'required',
        'jam_Keluar' => 'required',
        'keterangan' => 'required',
    ];
    protected $attributes = [
        'jam_masuk' => 'Jam Masuk',
        'jam_Keluar' => 'Jam Keluar',
        'keterangan' => 'Keterangan',
    ];
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
    public function store(Request $request)
    {
        $input = $request->only(['user_id', 'jam_masuk', 'jam_Keluar', 'keterangan', 'keterangan_detail', 'createdBy', 'updatedBy']);
        $validator = Validator::make($input, $this->rules, [], $this->attributes);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->fails()
            ], 401);
        } else {
            $input['uid'] = 1;
            $create = collectAttendance::create($input);
            if ($create) {
                if ($request->has('leave_id')) {
                    $result = [];
                    $leave_id = $request->leave_id;
                    foreach ($leave_id as $key => $val) {
                        $result[] = [
                            'collect_attendance_id' => $create->id,
                            'leave_absence_id' => $val
                        ];
                    }
                    DB::table('tr_payroll')->insert($result);
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Absensi berhasil ditambah manual'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'internal server error'
                ], 500);
            }
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
    public function update(Request $request, $id)
    {
        $input = $request->only(['jam_masuk', 'jam_Keluar', 'keterangan', 'keterangan_detail', 'updatedBy']);
        $validator = Validator::make($input, $this->rules, [], $this->attributes);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->fails()
            ], 401);
        } else {
            $input['uid'] = 1;
            $data = collectAttendance::find($id);
            if ($data) {
                $data->update($input);
                if ($request->has('leave_id')) {
                    DB::table('tr_payroll')->where('collect_attendance_id', $id)->delete();
                    $result = [];
                    $leave_id = $request->leave_id;
                    foreach ($leave_id as $key => $val) {
                        $result[] = [
                            'collect_attendance_id' => $data->id,
                            'leave_absence_id' => $val
                        ];
                    }
                    DB::table('tr_payroll')->insert($result);
                } else {
                    DB::table('tr_payroll')->where('collect_attendance_id', $id)->delete();
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Absensi berhasil diubah'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
        }
    }
}
