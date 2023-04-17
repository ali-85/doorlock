<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Models\leaveAndAbsence as leaveAbsence;

class LeaveAbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = leaveAbsence::all();
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
            return view('pages.attendance.leave-absence');
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
        $input = $request->all();
        $validator = Validator::make($input, [
            'category' => 'required',
            'remark' => 'required'
        ], [], []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $create = leaveAbsence::create($input);
            if ($create) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Leave Absence berhasil ditambahkan'
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
        $data = leaveAbsence::find($id);
        if ($data) {
            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Leave absence tidak ditemukan'
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
        $input = $request->except(['createdBy', '_method']);
        $validator = Validator::make($input, [
            'category' => 'required',
            'remark' => 'required'
        ], [], []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $data = leaveAbsence::find($id);
            if ($data) {
                $data->update($input);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Leave absence berhasil diubah'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Leave absence tidak ditemukan'
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
        $data = leaveAbsence::find($id);
        if ($data) {
            $data->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Leave absence berhasil dihapus'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Leave absence tidak ditemukan'
            ], 404);
        }
    }
}
