<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class attendanceDevice extends Model
{
    use HasFactory;

    protected $table = 'attendance_devices';

    protected $fillable = [
        'uid',
        'name',
        'departement_id',
        'location_id',
        'mode',
        'createdBy',
        'updatedBy',
    ];

    public function Location() : BelongsTo
    {
        return $this->belongsTo(dataLocation::class,'location_id');
    }
    public function Departement() : BelongsTo
    {
        return $this->belongsTo(mdepartement::class, 'departement_id');
    }
    public static function rules($id = false)
    {
        if ($id) {
            return [
                'uid' => 'required|unique:attendance_devices,uid,' . $id,
                'name' => 'required',
                'departement_id' => 'required',
                'location_id' => 'required',
            ];
        } else {
            return [
                'uid' => 'required|unique:attendance_devices,uid,' . $id,
                'name' => 'required',
                'departement_id' => 'required',
                'location_id' => 'required',
            ];
        }
    }
    public static function attributes()
    {
        return [
            'uid' => 'Device ID',
            'name' => 'Nama Device',
            'departement_id' => 'Departement',
            'location_id' => 'Lokasi',
        ];
    }
}
