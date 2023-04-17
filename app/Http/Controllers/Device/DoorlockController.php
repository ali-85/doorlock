<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\doorlockDevices as doorlock;
use App\Models\dataLocation as location;
use App\Models\mdepartement as mdept;
use App\Models\mpriset;

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
            $data = doorlock::with('location')->get();
            return DataTables::of($data)
                ->addColumn('namalokasi', function ($row) {
                    return $row->location->name;
                })
                ->addColumn('remark', function ($row) {
                    if ($row->access_mode == 1) {
                        $remark = 'Ya';
                    } else {
                        $remark = 'Tidak';
                    }
                    return $remark;
                })
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
                ->rawColumns(['namalokasi', 'remark', 'action'])
                ->make(true);
        } else {
            return view('pages.device.doorlock', [
                'locations' => location::all('id', 'name'),
                'depts' => mdept::all(['id', 'nama']),
                'prisets' => mpriset::all()
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
            'uid', 'name', 'departement_id', 'location_id',
            'type', 'access_type', 'createdBy', 'updatedBy'
        ]);
        $validator = Validator::make($input, doorlock::rules(), [], doorlock::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            if ($request->has('intactivemonitoring')) {
                $input['intactivemonitoring'] = 1;
            }
            if ($request->has('access_mode')) {
                $input['access_mode'] = 1;
            }
            $create = doorlock::create($input);
            if ($create) {
                if ($create->access_mode == 1) {
                    $result = [];
                    $priset_id = $request->priset_id;
                    foreach ($priset_id as $key => $val) {
                        $result[] = [
                            'doorlock_id' => $create->id,
                            'priset_id' => $val
                        ];
                    }
                    DB::table('doorlock_has_priset')->insert($result);
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Device berhasil ditambahkan !'
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
        $data = doorlock::with('remarks')->find($id);
        if ($data) {
            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Device tidak ditemukan!'
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
            'uid', 'name', 'departement_id', 'location_id',
            'type', 'access_type', 'updatedBy'
        ]);
        $validator = Validator::make($input, doorlock::rules($id), [], doorlock::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            if ($request->has('intactivemonitoring')) {
                $input['intactivemonitoring'] = 1;
            } else {
                $input['intactivemonitoring'] = 0;
            }
            if ($request->has('access_mode')) {
                $input['access_mode'] = 1;
            } else {
                $input['access_mode'] = 0;
            }
            $data = doorlock::find($id);
            if ($data) {
                $update = $data->update($input);
                if ($update) {
                    if (doorlock::find($id)->access_mode == 1) {
                        $result = [];
                        $priset_id = $request->priset_id;
                        foreach ($priset_id as $key => $val) {
                            $result[] = [
                                'doorlock_id' => $id,
                                'priset_id' => $val
                            ];
                        }
                        DB::table('doorlock_has_priset')
                            ->where('doorlock_id', $id)
                            ->delete();
                        DB::table('doorlock_has_priset')->insert($result);
                    } else {
                        DB::table('doorlock_has_priset')
                            ->where('doorlock_id', $id)
                            ->delete();
                    }
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Device berhasil diubah'
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
                    'message' => 'Device tidak ditemukan!'
                ], 404);
            }
            $create = doorlock::create($input);
            if ($create) {
                if ($create->access_mode == 1) {
                    $result = [];
                    $priset_id = $request->priset_id;
                    foreach ($priset_id as $key => $val) {
                        $result[] = [
                            'doorlock_id' => $create->id,
                            'priset_id' => $val
                        ];
                    }
                    DB::table('doorlock_has_priset')->insert($result);
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Device berhasil ditambahkan !'
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = doorlock::find($id);
        if ($data) {
            DB::table('doorlock_has_priset')->where('doorlock_id', $id)->delete();
            $data->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Device berhasil dihapus!'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Device tidak ditemukan!'
            ], 404);
        }
    }
}
