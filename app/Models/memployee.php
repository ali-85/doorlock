<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class memployee extends Model
{
    use HasFactory;

    protected $table = 'memployees';

    protected $fillable = [
        'nip',
        'rfid_number',
        'fingerprint',
        'attendance_type',
        'nama',
        'job_title',
        'profile_photo_path',
        'alamat',
        'noHandphone',
        'email',
        'payment_mode',
        'departement_id',
        'subdepartement_id',
        'golongan_id',
        'shiftcode_id',
        'basic_salary',
        'transfer_type',
        'bank_name',
        'bank_account',
        'credited_accont',
        'pph_type',
        'pph_pemerintahan',
        'pph_perusahaan',
        'intmonitoring',
        'createdBy',
        'updatedBy',
    ];

    public function bank() : BelongsTo
    {
        return $this->belongsTo(mbank::class,'bank_account');
    }
    public function departement() : BelongsTo
    {
        return $this->belongsTo(mdepartement::class, 'departement_id');
    }
    public function historyDevice() : BelongsTo
    {
        return $this->belongsTo(HistoryDeviceLog::class, 'user_id');
    }
    public function subDepartement() : BelongsTo
    {
        return $this->belongsTo(msubdepartement::class, 'subdepartement_id');
    }
    public function Golongan() : BelongsTo
    {
        return $this->belongsTo(Golongan::class, 'golongan_id');
    }
    public function Doorlock() : BelongsToMany
    {
        return $this->belongsToMany(
            doorlockDevices::class,
            'doorlock_has_employees',
            'memployes_id',
            'doorlock_id',
        );
    }
    public function Attendance() : HasMany
    {
        return $this->hasMany(collectAttendance::class,'user_id');
    }
    public function shiftcode() : BelongsTo
    {
        return $this->belongsTo(workingTime::class,'shiftcode_id');
    }
    public function schadule() : BelongsToMany
    {
        return $this->belongsToMany(
            Schadule::class,
            'schadules_meemployes',
            'memployes_id',
            'schadules_id',
        );
    }
    public static function rules($id = false)
    {
        if ($id) {
            return [
                'nama' => 'required',
                'nip' => 'required|max:16|unique:memployees,nip,' . $id,
                'rfid_number' => 'required|max:124|unique:memployees,rfid_number,' . $id,
                'email' => 'required|email|max:124|unique:memployees,email,' . $id,
                'departement_id' => 'required',
                'subdepartement_id' => 'required',
                'basic_salary' => 'required',
                'credited_accont' => 'nullable|max:124|unique:memployees,credited_accont,' . $id,
            ];
        } else {
            return [
                'nama' => 'required',
                'nip' => 'required|max:16|unique:memployees,nip',
                'rfid_number' => 'required|max:16|unique:memployees,rfid_number',
                'email' => 'required|email|max:124|unique:memployees,email',
                'departement_id' => 'required',
                'subdepartement_id' => 'required',
                'credited_accont' => 'nullable|max:124|unique:memployees,credited_accont',
                'basic_salary' => 'required'
            ];
        }
    }
    public static function messages()
    {
        return [
            'required'  => ':attribute wajib diisi.',
            'unique'    => ':attribute sudah digunakan',
            'email'    => 'Format Email tidak valid',
        ];
    }
    public static function attributes()
    {
        return [
            'nama' => 'Nama',
            'nip' => 'NIP',
            'rfid_number' => 'RFID',
            'email' => 'Email',
            'departement_id' => 'Departement',
            'subdepartement_id' => 'Sub Departement',
            'credited_accont' => 'No Rekening'
        ];
    }
}
