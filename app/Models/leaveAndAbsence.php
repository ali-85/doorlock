<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class leaveAndAbsence extends Model
{
    use HasFactory;

    protected $table = 'leave_and_absences';

    protected $fillable = [
        'category',
        'remark',
        'value_1A',
        'value_1B',
        'value_1C',
        'value_1D',
        'value_1E',
        'value_1F',
        'value_2A',
        'value_2B',
        'value_2C',
        'value_2D',
        'value_2E',
        'value_2F',
        'value_3A',
        'value_3B',
        'value_3C',
        'value_3D',
        'value_3E',
        'value_3F',
        'value_4A',
        'value_4B',
        'value_4C',
        'value_4D',
        'value_4E',
        'value_4F',
        'createdBy',
        'updatedBy',
    ];
}
