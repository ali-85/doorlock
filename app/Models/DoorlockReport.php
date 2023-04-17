<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoorlockReport extends Model
{
    use HasFactory;

    protected $table = 'doorlock_reports';

    protected $fillable = [
        'uid',
        'user_id',
        'keterangan',
        'doorlock_photo_path',
        'remark_log',
        'count_access',
        'createdBy',
        'updatedBy',
    ];

    public function karyawan() : BelongsTo
    {
        return $this->belongsTo(memployee::class,'user_id');
    }
    public function device() : BelongsTo
    {
       return $this->belongsTo(doorlockDevices::class,'uid');
    }
}
