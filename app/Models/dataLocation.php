<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class dataLocation extends Model
{
    use HasFactory;

    protected $table = 'data_locations';

    protected $fillable = [
        'name',
        'createdBy',
        'updatedBy',
    ];

    public function attendanceDevice(): HasMany
    {
        return $this->hasMany(attendanceDevice::class);
    }
    public function doorlockDevice(): HasMany
    {
        return $this->hasMany(doorlockDevices::class);
    }

    public static function rules()
    {
        return [
            'name' => 'required|max:128'
        ];
    }
    public static function attributes()
    {
        return [
            'name' => 'Lokasi'
        ];
    }
}
