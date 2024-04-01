<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $fillable = ['name', 'guard_name'];

    public function hasMenu()
    {
        return $this->hasMany(RoleHasMenu::class, 'role_id');
    }

    public static function rules()
    {
        return [
            'name' => 'required',
        ];
    }
    public static function attributes()
    {
        return [
            'name' => 'Nama Role',
        ];
    }
}
