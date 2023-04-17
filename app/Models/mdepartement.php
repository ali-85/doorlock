<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class mdepartement extends Model
{
    use HasFactory;

    protected $table = 'mdepartements';

    protected $fillable = [
        'nama',
        'createdBy',
        'updatedBy',
    ];

    public function subDepartement() : HasMany
    {
        return $this->hasMany(msubdepartement::class);
    }

    public function karyawan() : HasMany
    {
        return $this->hasMany(memployee::class);
    }
    public function DoorLockDevice() : HasMany
    {
        return $this->hasMany(doorlockDevices::class);
    }
    public function AttendanceDevice() : HasMany
    {
        return $this->hasMany(attendanceDevice::class);
    }

    public static function rules()
    {
        return [
            'nama' => 'required'
        ];
    }

    public static function attributes()
    {
        return [
            'nama' => 'Nama Departemen'
        ];
    }
}
