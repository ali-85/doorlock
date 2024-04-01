<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleHasMenu extends Model
{
    protected $table = 'role_has_menu';

    public function roles()
    {
        return $this->hasMany(RoleModel::class, 'role_id');
    }
    public function submenu()
    {
        return $this->belongsTo(Submenu::class, 'submenu_id');
    }
}
