<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\RoleHasMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Models\RoleModel as Role;
use App\Models\Submenu;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function __storeRoleMenu($role_id, $submenu_id, $update = false)
    {
        $result = [];
        foreach ($submenu_id as $key => $val) {
            $result[] = [
                'role_id' => $role_id,
                'submenu_id' => $val,
            ];
        }
        if ($update) {
            RoleHasMenu::where('role_id', $role_id)->delete();
            RoleHasMenu::insert($result);
        } else {
            RoleHasMenu::insert($result);
        }
        return true;
    }

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $roles = Role::with('hasMenu.submenu.menu')->latest();
            return DataTables::of($roles)
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
            $data = [
                'title' => 'Role',
                'submenus' => Submenu::with('menu')->get(),
            ];
            return view('pages.masterdata.role', $data);
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
        $validator = Validator::make(
            $input,
            role::rules(),
            [],
            role::attributes()
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
            $create = role::create($input);
            if ($create) {
                $this->__storeRoleMenu($create->id, $request->submenu_id);
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Role berhasil ditambah',
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
        $dept = Role::find($id);
        if ($dept) {
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $dept,
                    'submenus' => RoleHasMenu::where('role_id', $id)->pluck(
                        'submenu_id'
                    ),
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Role tidak ditemukan!',
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
        $input = $request->only(['name', 'guard_name']);
        $validator = Validator::make(
            $input,
            Role::rules(),
            [],
            Role::attributes()
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
            $dept = Role::find($id);
            if ($dept) {
                $dept->update($input);
                $this->__storeRoleMenu($id, $request->submenu_id, true);
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Role berhasil diubah!',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Role tidak ditemukan!',
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
        $dept = Role::find($id);
        if ($dept) {
            $dept->delete();
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Role berhasil dihapus!',
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Role tidak ditemukan!',
                ],
                404
            );
        }
    }
}
