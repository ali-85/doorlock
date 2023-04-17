<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $fillable = [
        'name', 'guard_name'
    ];

    public static function rules()
    {
        return [
            'name' => 'required'
        ];
    }
    public static function attributes()
    {
        return [
            'name' => 'Nama Role'
        ];
    }
}
