<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mbank extends Model
{
    use HasFactory;

    protected $table = 'mbanks';

    protected $fillable = [
        'nama_bank',
        'kode_bank',
    ];
}
