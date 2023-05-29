<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\collectAttendance as collect;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = collect::join('memployees', 'memployees.id', '=', 'collect_attendances.user_id')
                ->orderBy('id', 'DESC')->limit(1000)->get(['collect_attendances.*', 'memployees.nama']);
            return DataTables::of($data)
                ->editColumn('jam_keluar', function ($row) {
                    return empty($row->jam_Keluar)?'Belum keluar':$row->jam_Keluar;
                })
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
            return view('pages.report.absence');
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
        $data = collect::select(
                'memployees.nama', 'collect_attendances.jam_masuk', 'collect_attendances.jam_masuk_photo_path', 'collect_attendances.jam_Keluar_photo_path',
                'collect_attendances.jam_Keluar', 'collect_attendances.keterangan', 'collect_attendances.keterangan_detail'
                )
            ->join('memployees', 'memployees.id', '=', 'collect_attendances.user_id')
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
