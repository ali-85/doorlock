<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Golongan extends Model
{
    use HasFactory;

    protected $table = 'golongans';

    protected $fillable = [
        'nama',
        'createdBy',
        'updatedBy',
    ];
    public function karyawan() : HasMany
    {
        return $this->hasMany(memployee::class);
    }
}
