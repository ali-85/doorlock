<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use App\Models\User;
use App\Models\RoleModel as Role;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $accounts = User::join('roles', 'roles.id', '=', 'users.role_id')
                ->get(['roles.name as role_name', 'users.*']);
            return DataTables::of($accounts)
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
            return view('pages.masterdata.account', [
                'roles' => Role::all()
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
        $input = $request->only(['role_id', 'name', 'username', 'email', 'password']);
        $validator = Validator::make($input, User::rules(), User::messages(), User::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            if ($request->has('profile_photo')) {
                $profile = $request->profile_photo;
                $naming = date('YmdHi').'_'.preg_replace('/\s+/', '_', $profile->getClientOriginalName());
                $path = public_path().'/dist/profiles/';
                $input['profile_photo'] = $naming;
                $profile->move($path, $naming);
            }
            $input['password'] = Hash::make($input['password']);
            $create = User::create($input);
            if ($create) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Akun berhasil dibuat!'
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
        $akun = User::find($id);
        if ($akun) {
            return response()->json([
                'status' => 'success',
                'data' => $akun
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun tidak ditemukan!'
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
        $input = $request->only(['role_id', 'name', 'username', 'email']);
        $validator = Validator::make($input, User::rules($id), User::messages(), User::attributes());
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'fields' => $validator->errors()
            ], 401);
        } else {
            $akun = User::find($id);
            if($akun){
                if ($request->has('profile_photo')) {
                    if ($akun->profile_photo != 'default.jpg') {
                        $profile_photo = public_path('/dist/profiles/'.$akun->profile_photo);
                        unlink($profile_photo);
                    }
                    $profile = $request->profile_photo;
                    $naming = date('YmdHi').'_'.preg_replace('/\s+/', '_', $profile->getClientOriginalName());
                    $path = public_path().'/dist/profiles/';
                    $input['profile_photo'] = $naming;
                    $profile->move($path, $naming);
                }
                $akun->update($input);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Akun berhasil diubah!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akun tidak ditemukan!'
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
        $akun = User::find($id);
        if ($akun) {
            if ($akun->profile_photo != 'default.jpg') {
                $profile_photo = public_path('/dist/profiles/'.$akun->profile_photo);
                unlink($profile_photo);
            }
            $akun->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Akun berhasil dihapus!'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun tidak ditemukan!'
            ], 404);
        }
    }
}
