<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class doorlockDevices extends Model
{
    use HasFactory;

    protected $table = 'doorlock_devices';

    protected $fillable = [
        'uid',
        'name',
        'type',
        'access_type',
        'access_mode',
        'departement_id',
        'location_id',
        'intactivemonitoring',
        'createdBy',
        'updatedBy',
    ];

    public function privelage() : BelongsToMany
    {
        return $this->belongsToMany(
            memployee::class,
            'doorlock_has_employees',
            'doorlock_id',
            'memployes_id'
        );
    }
    public function remarks() : BelongsToMany
    {
        return $this->belongsToMany(
            mpriset::class,
            'doorlock_has_priset',
            'doorlock_id',
            'priset_id'
        );
    }
    public function Location() : BelongsTo
    {
        return $this->belongsTo(dataLocation::class,'location_id');
    }
    public function Departement() : BelongsTo
    {
        return $this->belongsTo(mdepartement::class, 'departement_id');
    }
    public function schadule() : BelongsToMany
    {
        return $this->belongsToMany(
            Schadule::class,
            'schadules_doorlock',
            'doorlock_id',
            'schadules_id',
        );
    }
    public static function rules($id = false){
        if ($id) {
            return [
                'uid' => 'required|unique:doorlock_devices,uid,' . $id,
                'name' => 'required',
                'type' => 'required',
                'access_type' => 'required',
                'departement_id' => 'required',
                'location_id' => 'required'
            ];
        } else {
            return [
                'uid' => 'required|unique:doorlock_devices,uid',
                'name' => 'required',
                'type' => 'required',
                'access_type' => 'required',
                'departement_id' => 'required',
                'location_id' => 'required'
            ];
        }
    }
    public static function attributes(){
        return [
            'uid' => 'ID Device',
            'name' => 'Nama Device',
            'type' => 'Tipe',
            'access_type' => 'Tipe Akses',
            'departement_id' => 'Departement',
            'location_id' => 'Lokasi'
        ];
    }
}
