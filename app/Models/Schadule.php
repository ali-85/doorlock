<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Schadule extends Model
{
    use HasFactory;

    protected $table = 'schadules';

    protected $fillable = [
        'nama',
        'tanggal_awal',
        'tanggal_akhir',
    ];

    public function karyawan() : BelongsToMany
    {
        return $this->belongsToMany(
            memployee::class,
            'schadules_meemployes',
            'schadules_id',
            'memployes_id'
        );
    }
    public function doorlock() : BelongsToMany
    {
        return $this->belongsToMany(
            doorlockDevices::class,
            'schadules_doorlock',
            'schadules_id',
            'doorlock_id'
        );
    }

    public static function rules()
    {
        return [
            'nama' => 'required',
            'tanggal_awal' => 'required',
            'tanggal_akhir' => 'required',
        ];
    }
    public static function attributes()
    {
        return [
            'nama' => 'Kegiatan',
            'tanggal_awal' => 'Awal',
            'tanggal_akhir' => 'Akhir',
        ];
    }
}
