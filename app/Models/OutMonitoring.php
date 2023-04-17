<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutMonitoring extends Model
{
    use HasFactory;
    protected $table = 'out_monitoring';
    protected $fillable = ['uid_room', 'memploye_id', 'tmstart', 'tmend'];
    public $timestamps = false;
}
