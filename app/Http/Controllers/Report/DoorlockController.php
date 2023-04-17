<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DoorlockReport;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DoorlockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = DoorlockReport::join('doorlock_devices as dd', 'dd.uid', '=', 'doorlock_reports.uid')
                ->join('memployees', 'memployees.id', '=', 'doorlock_reports.user_id')
                ->orderBy('id', 'DESC')
                ->get(['dd.name as namadevice', 'memployees.nama', 'doorlock_reports.*']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn_show =
                        '<button class="btn btn-primary" onclick="show(' .
                        $row->id .
                        ')" type="button"><i class="icon-eye"></i></button>';
                    $btn =
                        '<div class="btn-group">' .
                        $btn_show .
                        '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            return view('pages.report.doorlock');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = DoorlockReport::select(
                'memployees.nama', 'dd.uid AS uid_device', 'dd.name AS nama_device',
                'doorlock_reports.keterangan', 'doorlock_reports.remark_log', 'doorlock_reports.count_access'
            )
            ->join('doorlock_devices as dd', 'dd.uid', '=', 'doorlock_reports.uid')
            ->join('memployees', 'memployees.id', '=', 'doorlock_reports.user_id')
            ->find($id);
        if ($data) {
            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Absensi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
