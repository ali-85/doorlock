<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Schadule as schedule;
use App\Models\memployee;
use App\Models\doorlockDevices as doorlock;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = schedule::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn_edit =
                        '<button class="btn btn-info" onclick="edit(' .
                        $row->id .
                        ')" type="button"><i class="icon-pencil"></i></button>';
                    $btn_delete =
                        '<button class="btn btn-danger" onclick="destroy(' .
                        $row->id .
                        ')" type="button"><i class="icon-trash"></i></button>';
                    $btn =
                        '<div class="btn-group">' .
                        $btn_edit .
                        $btn_delete .
                        '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            return view('pages.device.schedule', [
                'employees' => memployee::all(),
                'doorlock' => doorlock::all()
            ]);
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
        $input = $request->only(['nama', 'tanggal_awal', 'tanggal_akhir', 'createdBy', 'updatedBy']);
        $validator = Validator::make($input, schedule::rules(), [], schedule::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $create = schedule::create($input);
            if ($create) {
                $employee_id = $request->employee_id;
                $doorlock_id = $request->doorlock_id;
                $employee = [];
                $doorlock = [];
                foreach ($employee_id as $key => $val) {
                    $employee[] = [
                        'schadules_id' => $create->id,
                        'memployes_id' => $val
                    ];
                }
                foreach ($doorlock_id as $key => $val) {
                    $doorlock[] = [
                        'schadules_id' => $create->id,
                        'doorlock_id' => $val
                    ];
                }
                DB::table('schadules_doorlock')->insert($doorlock);
                DB::table('schadules_meemployes')->insert($employee);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Schedule berhasil ditambah'
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = schedule::with('karyawan')->with('doorlock')->find($id);
        if ($data) {
            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal tidak ditemukan'
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
        $input = $request->only(['nama', 'tanggal_awal', 'tanggal_akhir', 'updatedBy']);
        $validator = Validator::make($input, schedule::rules(), [], schedule::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $data = schedule::find($id);
            if ($data) {
                $update = $data->update($input);
                if ($update) {
                    DB::table('schadules_doorlock')->where('schadules_id', $id)->delete();
                    DB::table('schadules_meemployes')->where('schadules_id', $id)->delete();
                    $employee_id = $request->employee_id;
                    $doorlock_id = $request->doorlock_id;
                    $employee = [];
                    $doorlock = [];
                    foreach ($employee_id as $key => $val) {
                        $employee[] = [
                            'schadules_id' => $id,
                            'memployes_id' => $val
                        ];
                    }
                    foreach ($doorlock_id as $key => $val) {
                        $doorlock[] = [
                            'schadules_id' => $id,
                            'doorlock_id' => $val
                        ];
                    }
                    DB::table('schadules_doorlock')->insert($doorlock);
                    DB::table('schadules_meemployes')->insert($employee);
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Schedule berhasil diubah'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'internal server error'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal tidak ditemukan'
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
        $data = schedule::find($id);
        if ($data) {
            DB::table('schadules_doorlock')->where('schadules_id', $id)->delete();
            DB::table('schadules_meemployes')->where('schadules_id', $id)->delete();
            $data->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Shedule berhasil dihapus'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal tidak ditemukan'
            ], 404);
        }
    }
}
