<?php

namespace App\Http\Controllers\masterData;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $menus = Menu::latest();
            return DataTables::of($menus)
                ->addIndexColumn()
                ->editColumn('icon', function ($row) {
                    return '<i class="' . $row->icon . '"></i>';
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
                ->rawColumns(['icon', 'action'])
                ->make(true);
        } else {
            $data = [
                'title' => 'Menu',
            ];
            return view('pages.masterdata.menu', $data);
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
            'title',
            'icon',
            'url',
            'route',
            'created_by',
            'updated_by',
        ]);
        $validator = Validator::make(
            $input,
            Menu::rules(),
            [],
            Menu::attributes()
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $validator->errors(),
                ],
                400
            );
        } else {
            $create = Menu::create($input);
            if ($create) {
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Menu berhasil ditambah',
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
        $data = Menu::find($id);
        if ($data) {
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $data,
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan',
                ],
                404
            );
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
            'title',
            'icon',
            'url',
            'route',
            'updated_by',
        ]);
        $validator = Validator::make(
            $input,
            Menu::rules(),
            [],
            Menu::attributes()
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $validator->errors(),
                ],
                400
            );
        } else {
            $data = Menu::find($id);
            if ($data) {
                $data->update($input);
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Menu berhasil diubah',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Data tidak ditemukan',
                    ],
                    404
                );
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
        $data = Menu::find($id);
        if ($data) {
            $data->delete();
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Menu berhasil dihapus',
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan',
                ],
                404
            );
        }
    }
}
