<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestPayment extends Model
{
    use HasFactory;
    protected $table = 'mrequest_payment';
    protected $fillable = [
        'transaction_id', 'payment_mode', 'start_date', 'end_date', 'approvedBy', 'rejectedBy'
    ];

    public static function rules(){
        return [
            'payment_mode' => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ];
    }
    public static function attributes()
    {
        return [
            'payment_mode' => 'Mode Pembayaran',
            'start_date' => 'Tanggal Awal',
            'end_date' => 'Tanggal Akhir'
        ];
    }
}
