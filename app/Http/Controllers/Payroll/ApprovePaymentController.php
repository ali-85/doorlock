<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\RequestPayment as Payment;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ApprovePaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = Payment::orderBy('id', 'DESC')->get();
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
                    if ($row->approvedBy != NULL || $row->rejectedBy != NULL) {
                        $action = 'Not Available';
                    } else {
                        $btn_approve = '<button class="btn btn-success btn-sm text-white" onclick="approve('.$row->id.')" type="button"><i class="icon-check"></i> Approve</button>';
                        $btn_reject = '<button class="btn btn-danger btn-sm text-white" onclick="reject('.$row->id.')" type="button"><i class="fas fa-times-circle"></i> Reject</button>';
                        $action = '<div class="btn-group">' .
                            $btn_approve. $btn_reject.
                            '</div>';
                    }
                    return $action;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } else {
            return view('pages.payroll.approve-payment');
        }
    }
    public function approve(Request $request, $id)
    {
        $data = Payment::find($id);
        if ($data) {
            if ($request->status == 'approve') {
                $data->update([
                    'approvedBy' => Auth::user()->name
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Request berhasil diApprove'
                ], 200);
            } else {
                $data->update([
                    'rejectedBy' => Auth::user()->name
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Request berhasil diReject'
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }
}
