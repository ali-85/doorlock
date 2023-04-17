<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\OutMonitoring as OutModel;

class OutMonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = outModel::select('out_monitoring.id', 'memploye_id','nama', DB::raw('SUM(TIMESTAMPDIFF(MINUTE, tmstart, tmend)) as minutes, DATE(tmstart) as tglfilter, COUNT(memploye_id) as frekuensi'))->join('memployees','memployees.id','=','out_monitoring.memploye_id')
            ->get();
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
            return view('pages.report.out-monitoring');
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
        $data = outModel::select('nama', DB::raw('SUM(TIMESTAMPDIFF(MINUTE, tmstart, tmend)) as minutes, DATE(tmstart) as tgl, COUNT(memploye_id) as frekuensi'))->groupBy('tgl')->join('memployees','memployees.id','=','out_monitoring.memploye_id')->find($id);
        if ($data) {
            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Report tidak ditemukan'
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
