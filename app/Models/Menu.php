<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'mmenus';

    protected $guarded = ['id'];

    public function submenus()
    {
        return $this->hasMany(Submenu::class);
    }

    public static function rules()
    {
        return [
            'title' => 'required|max:128',
            'icon' => 'required|max:128',
            'url' => 'nullable|max:128',
            'route' => 'nullable|max:128',
        ];
    }

    public static function attributes(){
        return [
            'title' => 'Title',
            'icon' => 'Icon',
            'url' => 'URL',
            'route' => 'Route'
        ];
    }
}
