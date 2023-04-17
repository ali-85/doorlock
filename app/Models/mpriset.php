<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class mpriset extends Model
{
    use HasFactory;

    protected $table = 'mprisets';

    protected $fillable = [
        'name',
        'createdBy',
        'updatedBy',
    ];
    public function Doorlock() : BelongsToMany
    {
        return $this->belongsToMany(
            doorlockDevices::class,
            'doorlock_has_priset',
            'priset_id',
            'doorlock_id',
        );
    }
    public static function rules()
    {
        return [
            'name' => 'required'
        ];
    }

    public static function attributes()
    {
        return [
            'name' => 'Remark'
        ];
    }
}
