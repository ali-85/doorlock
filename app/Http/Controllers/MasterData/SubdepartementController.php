<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Models\msubdepartement as msubdept;
use App\Models\mdepartement as mdept;

class SubdepartementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $depts = msubdept::join('mdepartements as mdept', 'mdept.id', '=', 'msubdepartements.departement_id')
                ->get(['msubdepartements.*', 'mdept.nama as namadepartemen']);
            return DataTables::of($depts)
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
            return view('pages.masterdata.subdepartment', [
                'depts' => mdept::all()
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
        $input = $request->only(['nama', 'departement_id', 'createdBy', 'updatedBy']);
        $validator = Validator::make($input, msubdept::rules(), [], msubdept::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $create = msubdept::create($input);
            if ($create) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sub Departemen berhasil dibuat!'
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
        $subdept = msubdept::find($id);
        if ($subdept) {
            return response()->json([
                'status' => 'error',
                'data' => $subdept
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Subdepartemen tidak ditemukan'
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
        $input = $request->only(['nama', 'departement_id', 'updatedBy']);
        $validator = Validator::make($input, msubdept::rules(), [], msubdept::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $subdept = msubdept::find($id);
            if ($subdept) {
                $subdept->update($input);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sub Departemen berhasil diubah!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sub Departemen tidak ditemukan!'
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
        $subdept = msubdept::find($id);
        if ($subdept) {
            $subdept->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Subdepartemen berhasil dihapus!'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Subdepartemen tidak ditemukan!'
            ], 404);
        }
    }
}
