<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Models\RoleModel as Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $roles = Role::all();
            return DataTables::of($roles)
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
            return view('pages.masterdata.role');
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
        $input = $request->only(['name', 'guard_name']);
        $validator = Validator::make($input, role::rules(), [], role::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $create = role::create($input);
            if ($create) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Role berhasil ditambah'
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
        $dept = Role::find($id);
        if ($dept) {
            return response()->json([
                'status' => 'success',
                'data' => $dept
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Role tidak ditemukan!'
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
        $input = $request->only(['name', 'guard_name']);
        $validator = Validator::make($input, Role::rules(), [], Role::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $dept = Role::find($id);
            if ($dept) {
                $dept->update($input);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Role berhasil diubah!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role tidak ditemukan!'
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
        $dept = Role::find($id);
        if ($dept) {
            $dept->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Role berhasil dihapus!'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Role tidak ditemukan!'
            ], 404);
        }
    }
}
