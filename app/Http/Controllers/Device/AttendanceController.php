<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Models\attendanceDevice as attendance;
use App\Models\dataLocation as location;
use App\Models\mdepartement as mdept;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = attendance::with('location')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('namalokasi', function ($row) {
                    return $row->location->name;
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
                ->rawColumns(['namalokasi', 'action'])
                ->make(true);
        } else {
            return view('pages.device.attendance', [
                'locations' => location::all('id', 'name'),
                'depts' => mdept::all(['id', 'nama']),
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
        $input = $request->only(['uid', 'name', 'departement_id', 'location_id', 'mode', 'createdBy', 'updatedBy']);
        $validator = Validator::make(
            $input,
            attendance::rules(),
            [],
            attendance::attributes()
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'fields' => $validator->errors(),
                ],
                401
            );
        } else {
            $create = attendance::create($input);
            if ($create) {
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Device berhasil ditambah',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'internal server error',
                    ],
                    500
                );
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
        $device = attendance::find($id);
        if ($device) {
            return response()->json([
                'status' => 'success',
                'data' => $device
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance Device tidak ditemukan!'
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
        $input = $request->only(['uid', 'name', 'departement_id', 'location_id', 'mode', 'updatedBy']);
        $validator = Validator::make(
            $input,
            attendance::rules($id),
            [],
            attendance::attributes()
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'fields' => $validator->errors(),
                ],
                401
            );
        } else {
            $device = attendance::find($id);
            if ($device) {
                $device->update($input);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Device berhasil diubah!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Attendance Device tidak ditemukan!'
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
        $device = attendance::find($id);
        if ($device) {
            $device->delete();
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
