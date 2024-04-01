<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Models\RequestPayment;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Excel;
use App\Exports\SalaryTransferExport;

class RequestPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = RequestPayment::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('start_date', function ($row) {
                    return date('d-m-Y', strtotime($row->start_date));
                })
                ->editColumn('end_date', function ($row) {
                    return date('d-m-Y', strtotime($row->end_date));
                })
                ->addColumn('status', function($row){
                    if ($row->approvedBy) {
                        $status = '<span class="label label-success">Disetujui oleh : '.$row->approvedBy.'</span>';
                    } elseif ($row->rejectedBy) {
                        $status = '<span class="label label-danger">Ditolak oleh : '.$row->rejectedBy.'</span>';
                    } else {
                        $status = '<span class="label label-primary">Belum disetujui</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    if ($row->approvedBy) {
                        $btn_print = '<a href="'.route('request.download.pdf', $row->id).'" target="_new" class="btn btn-danger text-white"><i class="far fa-file-pdf"></i></a>';
                        $btn_excel = '<a href="'.route('request.download.excel', $row->id).'" target="_new" class="btn btn-success text-white"><i class="fa-solid fa-file-excel"></i></a>';
                        $action = '<div class="btn-group">' .
                            $btn_print . $btn_excel.
                            '</div>';
                    } elseif ($row->rejectedBy) {
                        $action = 'Not Available';
                    } else {
                        $btn_edit = '<button class="btn btn-success text-white" onclick="edit('.$row->id.')" type="button"><i class="icon-note"></i></button>';
                        $btn_hapus = '<button class="btn btn-danger text-white" onclick="destroy('.$row->id.')" type="button"><i class="icon-trash"></i></button>';
                        $action = '<div class="btn-group">' .
                            $btn_edit . $btn_hapus.
                            '</div>';
                    }
                    return $action;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } else {
            return view('pages.payroll.request-payment');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDownloadExcel($id)
    {
        $payment = RequestPayment::find($id);
        return Excel::download(new SalaryTransferExport($payment->start_date, $payment->end_date, $payment->payment_mode), 'Salary_Transfer_'.$payment->start_date.'_'.$payment->payment_mode.'.xlsx');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->only([
            'payment_mode', 'start_date', 'end_date',
            'createdBy', 'updatedBy'
        ]);
        $validator = Validator::make($input, RequestPayment::rules(), [], RequestPayment::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $create = RequestPayment::create($input);
            if ($create) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Request berhasil dibuat'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'internal server error'
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDownloadPDF($id)
    {
        $payment = RequestPayment::find($id);
        $data = DB::table('v_payroll AS vp')
            ->selectRaw(
                "vp.id, vp.nama, vp.basic_salary, SUM(vp.salary) AS salary, vp.pembayaran, COUNT(vp.jam_masuk) AS hari_kerja,
                CASE WHEN DAYNAME(vp.jam_masuk) = 'Sunday' THEN 7500*SUM(vp.lembur) ELSE 5000*SUM(vp.lembur) END AS lembur,
                SUM(vp.lembur2) AS lembur2, SUM(lembur3) AS lembur3, SUM(vp.lembur) AS jumlah_lembur,
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
                $payment->start_date,
                $payment->end_date,
            ])
            ->where('vp.payment_mode', $payment->payment_mode)
            ->groupBy('vp.user_id')
            ->get();
        $pdf = Pdf::loadView('export.salary_slip', [
            'data' => $data,
            'pekan' => $payment->start_date,
        ]);
        return $pdf->stream();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = RequestPayment::find($id);
        if ($data) {
            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->only([
            'payment_mode', 'start_date', 'end_date', 'updatedBy'
        ]);
        $validator = Validator::make($input, RequestPayment::rules(), [], RequestPayment::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $data = RequestPayment::find($id);
            if ($data) {
                $data->update($input);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Request berhasil diubah'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = RequestPayment::find($id);
        if ($data) {
            $data->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Request berhasil dihapus'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }
}
