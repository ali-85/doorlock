<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Models\dataLocation as location;

class DataLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = location::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn_edit = '<button class="btn btn-info" onclick="edit('.$row->id.')" type="button"><i class="icon-pencil"></i></button>';
                    $btn_delete = '<button class="btn btn-danger" onclick="destroy('.$row->id.')" type="button"><i class="icon-trash"></i></button>';
                    $btn = '<div class="btn-group">'.$btn_edit.$btn_delete.'</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            return view('pages.device.data-location');
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
        $input = $request->only(['name', 'createdBy', 'updatedBy']);
        $validator = Validator::make($input, location::rules(), [], location::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $create = location::create($input);
            if ($create) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data Lokasi berhasil ditambah'
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
        $dept = location::find($id);
        if ($dept) {
            return response()->json([
                'status' => 'success',
                'data' => $dept
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Lokasi tidak ditemukan!'
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
        $input = $request->only(['name', 'updatedBy']);
        $validator = Validator::make($input, location::rules(), [], location::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $dept = location::find($id);
            if ($dept) {
                $dept->update($input);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Departemen berhasil diubah!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Departemen tidak ditemukan!'
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
        $dept = location::find($id);
        if ($dept) {
            $dept->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Data Lokasi berhasil dihapus!'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Lokasi tidak ditemukan!'
            ], 404);
        }
    }
}
