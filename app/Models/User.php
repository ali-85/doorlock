<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'role_id',
        'name',
        'username',
        'email',
        'password',
        'profile_photo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function rules($id = false)
    {
        if ($id) {
            return [
                'name' => 'required',
                'username' => 'required|max:16|unique:users,username,' . $id,
                'email' => 'email|required|max:124|unique:users,email,' . $id
            ];
        } else {
            return [
                'name' => 'required',
                'username' => 'required|max:16|unique:users,username',
                'email' => 'email|required|max:124|unique:users,email'
            ];
        }
    }
    public static function messages()
    {
        return [
            'required'  => ':attribute wajib diisi.',
            'unique'    => ':attribute sudah digunakan',
            'email'    => 'Format Email tidak valid',
        ];
    }
    public static function attributes()
    {
        return [
            'name' => 'Nama',
            'username' => 'Username',
            'email' => 'Email'
        ];
    }
}
