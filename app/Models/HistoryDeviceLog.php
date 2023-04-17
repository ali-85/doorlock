<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryDeviceLog extends Model
{
    use HasFactory;

    protected $table = 'history_device_logs';

    protected $fillable = [
        'uid',
        'user_id',
        'keterangan',
        'is_attendance',
        'createdBy',
    ];

    public function karyawan() : BelongsTo
    {
        return $this->belongsTo(memployee::class,'user_id');
    }

    public function deviceDoorlock() : BelongsTo
    {
        return $this->belongsTo(doorlockDevices::class,'uid');

    }
    public function deviceAbsence() : BelongsTo
    {
        return $this->belongsTo(attendanceDevice::class,'uid');
    }
}
