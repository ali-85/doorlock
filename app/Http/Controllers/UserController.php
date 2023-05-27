<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getProfile(){
        $user = User::select('roles.name AS role_name', 'users.*')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->find(Auth::user()->id);
        return view('pages.profile', [
            'user' => $user
        ]);
    }
    public function putResetPassword(){
        # code...
    }
}
