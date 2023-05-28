<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\collectAttendance;
use App\Models\doorlockDevices;
use App\Models\DoorlockReport;
use App\Models\Golongan;
use App\Models\HistoryDeviceLog;
use App\Models\mdepartement;
use App\Models\memployee;
use App\Models\msubdepartement;
use App\Models\User;
use App\Models\workingTime;
use App\Models\OutMonitoring;
use App\Notifications\SystemNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

class BaseController extends Controller
{
    public function sendError($error, $code = 200)
    {
        $response = [
            'status' => 'failed',
            'ket' => $error,
        ];

        return response()->json($response, $code);
    }
    public function withremarks($remarks, $link)
    {
        $response = [
            'data' => $remarks->makeHidden([
                'createdBy',
                'updatedBy',
                'created_at',
                'updated_at',
                'pivot',
            ]),
            'links' => [
                'self' => URL::temporarySignedRoute(
                    'withremarks',
                    now()->addMinutes(5),
                    ['id' => $link->id]
                ),
            ],
        ];
        return $response;
    }
    public function withLinks($link, $expired = 5)
    {
        $response = [
            'self' => URL::temporarySignedRoute(
                'withcounter',
                now()->addMinutes($expired),
                ['id' => $link->id]
            ),
        ];
        return $response;
    }

    public function sendMessage(
        $status,
        $keterangan,
        $departement,
        $lock,
        $device,
        $user,
        $remarks = [],
        $links = [],
        $included = [],
        $code = 200
    ) {
        $response = [
            'status' => $status,
            'ket' => $keterangan,
            'nama' => $user->nama,
            'department' => $departement,
            'lock' => $lock,
            'nama_room' => $device->name,
            'remark' => $device->access_mode,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nip' => $user->nip,
                    'rfid_number' => $user->rfid_number,
                    'fingerprint' => $user->fingerprint,
                    'doorTime' => $user->user_DoorTime,
                ],
                'remarks' => $remarks,
            ],
            'links' => $links,
        ];

        return response()->json($response, $code);
    }
    public function sendRoom(
        $keterangan,
        $room_name,
        $departement,
        $type = 'public',
        $code = 200
    ) {
        $response = [
            'status' => 'success',
            'ket' => $keterangan,
            'nama_room' => $room_name,
            'department' => $departement,
            'type' => $type,
        ];
        return response()->json($response, $code);
    }

    public function SendHistory(
        $id_karyawan,
        $keterangan,
        $id_room,
        $absence = false
    ) {
        $id_history = HistoryDeviceLog::create([
            'uid' => $id_room,
            'user_id' => $id_karyawan,
            'keterangan' => $keterangan,
            'is_attendance' => $absence,
            'createdBy' => 'Tazaka Room : ' . $id_room,
            'updatedBy' => 'Tazaka Room : ' . $id_room,
        ]);

        return $id_history;
    }

    public function sendResponseRemark(
        $status,
        $keterangan,
        $departement,
        $lock,
        $device,
        $user,
        $code = 200
    ) {
        $response = [
            'status' => $status,
            'ket' => $keterangan,
            'nama' => $user->nama,
            'department' => $departement,
            'lock' => $lock,
            'nama_room' => $device->name,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nip' => $user->nip,
                    'rfid_number' => $user->rfid_number,
                    'fingerprint' => $user->fingerprint,
                    'doorTime' => $user->user_DoorTime,
                ],
            ],
        ];

        return response()->json($response, $code);
    }

    public function SendDoorlockReport(
        $id_karyawan,
        $keterangan,
        $id_room,
        $count = 0,
        $remarks_log = '-'
    ) {
        $this->sendOutMonitoring($id_room, $id_karyawan);
        $id_doorlock = DoorlockReport::create([
            'uid' => $id_room,
            'user_id' => $id_karyawan,
            'keterangan' => $keterangan,
            'remark_log' => $remarks_log,
            'count_access' => $count,
            'createdBy' => 'Tazaka Room : ' . $id_room,
            'updatedBy' => 'Tazaka Room : ' . $id_room,
        ]);
        return $id_doorlock;
    }
    public function sendOutMonitoring($id_room, $user_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $user = memployee::findOrfail($user_id);
        if ($user->intmonitoring == 1) {
            $device = doorlockDevices::where('uid', $id_room)->first();
            if ($device->intactivemonitoring == 1) {
                if ($device->access_type == 'out') {
                    $out = [
                        'uid_room' => $id_room,
                        'memploye_id' => $user_id,
                        'tmstart' => date('Y-m-d H:i:s'),
                    ];
                    OutMonitoring::create($out);
                } else {
                    $monitoringdata = OutMonitoring::where('memploye_id', $user_id)
                        ->whereNull('tmend')
                        ->orderBy('id', 'desc')
                        ->first();
                    if ($monitoringdata) {
                        $in = [
                            'tmend' => date('Y-m-d H:i:s'),
                        ];
                        $monitoringdata->update($in);
                    }
                }
            }
        }
    }
    public function editRemark($id_doorlock, $remarks)
    {
        $data = DoorlockReport::find($id_doorlock);
        $data->remark_log = $remarks;
        $data->save();
    }
    public function editCount($id_doorlock, $counter)
    {
        $data = DoorlockReport::find($id_doorlock);
        $data->count_access = $counter;
        $data->save();

        return $data;
    }
    public function editCapture($id_doorlock, $foto)
    {
        $data = DoorlockReport::find($id_doorlock);
        $data->doorlock_photo_path = $foto;
        $data->save();

        return $data;
    }

    public function responseCapture1($status, $id_log, $deviceId, $expired = 1)
    {
        $response = [
            'status' => $status,
            'id_log' => $id_log,
            'id_device' => $deviceId,
            'links' => [
                'self' => URL::temporarySignedRoute(
                    'withcapture',
                    now()->addMinutes($expired),
                    ['id' => $id_log]
                ),
            ],
        ];
        return $response;
    }

    // absence
    public function sendErrorAbsence($errorMessage)
    {
        $response = 'x*' . $errorMessage . '*x';

        return $response;
    }
    public function sendMessageAbsence($response, $nama, $statFoto, $wkt)
    {
        // return $response;
        return response()->json([
            'status' => 'success',
            'keterangan' => $response,
            'nama' => $nama->nama,
            'nip' => $nama->nip,
            'waktu' => $wkt
        ], 200);
    }

    public function sendAttendance(
        $uid,
        $user_id,
        $jamKeluar,
        $keterangan,
        $record,
        $cb,
        $photojamMasuk
    ) {
        collectAttendance::create([
            'uid' => $uid,
            'user_id' => $user_id,
            'jam_masuk' => Carbon::now(),
            'jam_masuk_photo_path' => $photojamMasuk,
            'jam_keluar' => $jamKeluar,
            'keterangan' => $keterangan,
            'keterangan_detail' => $record,
            'createdBy' => $cb,
            'updatedBy' => 'Tazaka Room : ' . $uid,
        ]);
    }
    public function updateAttendance($id, $uid, $photojamKeluar)
    {
        $data = collectAttendance::find($id);
        $data->uid = $uid;
        $data->jam_keluar = Carbon::now();
        $data->jam_keluar_photo_path = $photojamKeluar;
        $data->updatedBy = 'Tazaka Room : ' . $uid;

        $data->save();
    }

    public function addKaryawanByRfid($rfid)
    {
        $dep_id = mdepartement::all()->first();
        $sub_id = msubdepartement::where(
            'departement_id',
            $dep_id->id
        )->first();
        $gol_id = Golongan::all()->first();
        $shif_id = workingTime::all()->first();

        $data = new memployee();
        $data->nip = uniqid();
        $data->attendance_type = rand(1, 2);
        $data->rfid_number = $rfid;
        $data->nama = '-';
        $data->job_title = '-';
        $data->payment_mode = 'weekly';
        $data->basic_salary = '0';
        $data->transfer_type = '2';
        $data->departement_id = $dep_id->id;
        $data->subdepartement_id = $sub_id->id;
        $data->golongan_id = $gol_id->id;
        $data->shiftcode_id = $shif_id->id;

        $data->save();

        return $data;
    }

    // notification
    public function notifyDB($userData, $data, $system = false)
    {
        $users = User::all();

        foreach ($users as $key => $user) {
            Notification::send(
                $user,
                new SystemNotification($userData, $data, $system)
            );
        }
    }
}
