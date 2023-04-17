<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mholiday extends Model
{
    use HasFactory;
    protected $fillable = [
        'txtdescription', 'dtday', 'createdBy', 'updatedBy'
    ];
    public static function rules()
    {
        return [
            'txtdescription' => 'required|max:128',
            'dtday' => 'required'
        ];
    }
    public static function attributes()
    {
        return [
            'txtdescription' => 'Deskripsi',
            'dtday' => 'Tanggal'
        ];
    }
}
