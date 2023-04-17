<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\memployee;
use App\Models\mdepartement as mdept;
use App\Models\msubdepartement as msubdept;
use App\Models\Golongan;
use App\Models\doorlockDevices as devdoorlock;
use App\Models\workingTime as shift;
use App\Models\mbank;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $employee = memployee::with('departement')
                ->with('subDepartement')
                ->get();
            return DataTables::of($employee)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn_edit = '<button class="btn btn-info" onclick="edit('.$row->id.')" type="button"><i class="icon-pencil"></i></button>';
                    $btn_delete = '<button class="btn btn-danger" onclick="destroy('.$row->id.')" type="button"><i class="icon-trash"></i></button>';
                    $btn = '<div class="btn-group">'.$btn_edit.$btn_delete.'</div>';
                    return $btn;
                })
                ->addColumn('namadepartement', function($row){
                    return $row->departement->nama;
                })
                ->addColumn('namasubdepartement', function($row){
                    return $row->subdepartement->nama;
                })
                ->rawColumns(['namadepartement', 'namasubdepartement','action'])
                ->make(true);
        } else {
            return view('pages.masterdata.employee', [
                'depts' => mdept::all(),
                'golongans' => Golongan::all(['id', 'nama']),
                'doorlock_devices' => devdoorlock::all(['id', 'name']),
                'shifts' => shift::all(['id', 'shift_name']),
                'banks' => mbank::all(['id', 'nama_bank', 'kode_bank'])
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
        $input = $request->only([
            'nip', 'rfid_number', 'fingerprint',
            'nama', 'job_title', 'noHandphone', 'email',
            'attendances_type', 'departement_id', 'subdepartement_id',
            'golongan_id', 'shiftcode_id', 'alamat', 'basic_salary',
            'transfer_type', 'bank_name', 'bank_account', 'credited_accont',
        ]);
        $validator = Validator::make($input, memployee::rules(), memployee::messages(), memployee::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            if ($request->has('intmonitoring')) {
                $input['intmonitoring'] = 1;
            }
            if ($request->has('profile_photo_path')) {
                $photo = $request->profile_photo_path;
                $naming = date('YmdHi').'_'.preg_replace('/\s+/', '_', $photo->getClientOriginalName());
                $path = public_path().'/files/';
                $input['profile_photo_path'] = $naming;
                $photo->move($path, $naming);
            }
            $create = memployee::create($input);
            if ($create) {
                $doorlockid = $request->doorlock_id;
                $result = [];
                foreach($doorlockid as $key => $val){
                    $result[] = [
                        'memployes_id' => $create->id,
                        'doorlock_id' => $val
                    ];
                }
                DB::table('doorlock_has_employees')->insert($result);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data Pegawai berhasil ditambah!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'internal server error!'
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
    public function getSubdepartement($id)
    {
        $subdept = msubdept::where('departement_id', $id)->get(['id', 'nama']);
        if ($subdept) {
            return response()->json([
                'status' => 'success',
                'data' => $subdept
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Sub Departement tidak ditemukan!'
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
        $employee = memployee::with('Doorlock')->find($id);
        if ($employee) {
            return response()->json([
                'status' => 'success',
                'data' => $employee
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Pegawai tidak ditemukan!'
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
            'nip', 'rfid_number', 'fingerprint',
            'nama', 'job_title', 'noHandphone', 'email',
            'attendances_type', 'departement_id', 'subdepartement_id',
            'golongan_id', 'shiftcode_id', 'alamat', 'basic_salary',
            'transfer_type', 'bank_name', 'bank_account', 'credited_accont',
        ]);
        $validator = Validator::make($input, memployee::rules($id), memployee::messages(), memployee::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $employee = memployee::with('Doorlock')->find($id);
            if ($employee) {
                if ($request->has('intmonitoring')) {
                    $input['intmonitoring'] = 1;
                }
                if ($request->has('profile_photo_path')) {
                    if ($employee->profile_photo_path != 'default.jpg') {
                        $profile_photo = public_path('/files/'.$employee->profile_photo_path);
                        unlink($profile_photo);
                    }
                    $photo = $request->profile_photo_path;
                    $naming = date('YmdHi').'_'.preg_replace('/\s+/', '_', $photo->getClientOriginalName());
                    $path = public_path().'/files/';
                    $input['profile_photo_path'] = $naming;
                    $photo->move($path, $naming);
                }
                $doorlockid = $request->doorlock_id;
                $result = [];
                foreach($doorlockid as $key => $val){
                    $result[] = [
                        'memployes_id' => $id,
                        'doorlock_id' => $val
                    ];
                }
                DB::table('doorlock_has_employees')->where('memployes_id', $id)->delete();
                DB::table('doorlock_has_employees')->insert($result);
                $employee->update($input);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data Pegawai berhasil diubah!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data Pegawai tidak ditemukan!'
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
        $employee = memployee::find($id);
        if ($employee) {
            if ($employee->profile_photo_path != 'default.jpg') {
                $profile_photo = public_path('/files/'.$employee->profile_photo_path);
                unlink($profile_photo);
            }
            DB::table('doorlock_has_employees')->where('memployes_id', $employee->id)->delete();
            $employee->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Data Pegawai berhasil dihapus!'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Pegawai tidak ditemukan!'
            ], 404);
        }
    }
}
