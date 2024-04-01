<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submenu extends Model
{
    protected $table = 'msubmenus';
    protected $guarded = ['id'];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public static function rules()
    {
        return [
            'menu_id' => 'required',
            'submenuTitle' => 'required|max:128',
            'submenuIcon' => 'required|max:128',
            'submenuUrl' => 'required|max:128',
            'submenuRoute' => 'required|max:128',
        ];
    }
    public static function attributes()
    {
        return [
            'menu_id' => 'Menu',
            'submenuTitle' => 'Submenu Title',
            'submenuIcon' => 'Submenu Icon',
            'submenuUrl' => 'Submenu URL',
            'submenuRoute' => 'Submenu Route',
        ];
    }
}
