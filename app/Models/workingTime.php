<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class workingTime extends Model
{
    use HasFactory;

    protected $table = 'working_times';

    protected $fillable = [
        'shift_name',
        'jam_masuk',
        'jam_keluar',
        'createdBy',
        'updatedBy',
    ];

    public function karyawan() : HasMany
    {
        return $this->hasMany(memployee::class);
    }
}
