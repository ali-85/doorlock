<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\attendanceDevice;
use App\Models\collectAttendance;
use App\Models\HistoryDeviceLog;
use App\Models\memployee;
use App\Models\OutMonitoring;
use App\Models\workingTime;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AbsenceController extends BaseController
{
    protected $keyAbsence;
    protected $Base;
    public function __construct(BaseController $base)
    {
        $this->keyAbsence = env('TAZAKA_KEY_ABSENCE', 'RFIDCAM2021');
        $this->Base = $base;
    }

    public function getmode(Request $request)
    {
        Log::channel('Apilog')->info($request->key);
        if (isset($request->key) && isset($request->iddev)) {
            if ($this->keyAbsence == $request->key) {
                $cek_device = attendanceDevice::where(
                    'uid',
                    $request->iddev
                )->first();
                if (is_null($cek_device)) {
                    return $this->sendErrorAbsence('Device tidak terdaftar');
                }
                return 'x*' . $cek_device->mode . '*x';
            }
            return $this->sendErrorAbsence('salah-secret-key');
        }
        return $this->sendErrorAbsence('salah-param');
    }

    public function addcardrfidcam(Request $request)
    {
        if (
            isset($request->key) &&
            isset($request->iddev) &&
            isset($request->rfid)
        ) {
            if ($this->keyAbsence == $request->key) {
                $cek_device = attendanceDevice::where(
                    'uid',
                    $request->iddev
                )->first();
                $cekRfid = memployee::where(
                    'rfid_number',
                    $request->rfid
                )->first();
                if (is_null($cek_device)) {
                    return $this->sendErrorAbsence('Device tidak terdaftar');
                }
                if (is_null($cekRfid)) {
                    $kar = $this->addKaryawanByRfid($request->rfid);
                    $this->SendHistory(
                        $kar->id,
                        'Menambahkan Rfid',
                        $cek_device->uid,
                        true
                    );
                    return $this->sendErrorAbsence('Berhasil Menambahkan Rfid');
                }
                return $this->sendErrorAbsence('rfid sudah terdaftar');
            }
            return $this->sendErrorAbsence('salah-secret-key');
        }
        return $this->sendErrorAbsence('salah-param');
    }

    public function absensi(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tujuan_upload = '';
        $imgname = '';
        if (
            isset($request->key) &&
            isset($request->iddev) &&
            isset($request->rfid)
        ) {
            if ($this->keyAbsence == $request->key) {
                $cek_device = attendanceDevice::where(
                    'uid',
                    $request->iddev
                )->first();
                if (is_null($cek_device)) {
                    return $this->sendErrorAbsence('Device tidak terdaftar');
                }

                $cekRfid = memployee::where(
                    'rfid_number',
                    $request->rfid
                )->first();
                if (is_null($cekRfid)) {
                    return $this->sendErrorAbsence('rfid tidak ditemukan');
                }

                $foto = $request->file('foto');
                $statFoto = '';
                $isClose = collectAttendance::where('user_id', $cekRfid->id)
                    ->orderBy('id', 'desc')
                    ->first();
                // return response()->json(['data' => $isClose->jam_Keluar]);
                if ($isClose->jam_Keluar) {
                    // return response()->json(['data' => $isClose->jam_Keluar]);
                    if (
                        date('Y-m-d H:i:s') >
                        date(
                            'Y-m-d H:i:s',
                            strtotime($isClose->jam_Keluar . ' +1 hour')
                        )
                    ) {
                        // Foto Absen
                        if (isset($foto)) {
                            $imgname =
                                $cekRfid->nama .
                                '_masuk_' .
                                date('dmY_H-i-s_', time()) .
                                uniqid(rand(0, 5)) .
                                '.' .
                                $foto->getClientOriginalExtension();
                            if (
                                in_array($foto->getClientOriginalExtension(), [
                                    'jpg',
                                    'jpeg',
                                    'gif',
                                    'png',
                                ])
                            ) {
                                $tujuan_upload =
                                    'files/Absensi/' .
                                    Carbon::now()->format('Y') .
                                    '/' .
                                    Carbon::now()->format('m');
                                $foto->move($tujuan_upload, $imgname);
                                $statFoto = 'capture foto sukses';
                            } else {
                                $statFoto = 'capture foto gagal';
                            }
                        }
                        $namafoto = $tujuan_upload . '/' . $imgname;
                        $this->SendHistory(
                            $cekRfid->id,
                            'attendance recorded',
                            $cek_device->uid,
                            true
                        );
                        $this->sendAttendance(
                            $cek_device->uid,
                            $cekRfid->id,
                            '-',
                            'hadir',
                            'masuk tepat waktu',
                            'Tazaka Room : ' . $cek_device->uid,
                            $namafoto
                        );
                        return $this->sendMessageAbsence(
                            'hadir',
                            $cekRfid,
                            $statFoto,
                            Carbon::now()->format('H:i:s')
                        );
                    } else {
                        $this->SendHistory(
                            $cekRfid->id,
                            'attendance recorded',
                            $cek_device->uid,
                            true
                        );
                        return $this->sendErrorAbsence(
                            'sudah absen*' . $cekRfid->nama
                        );
                    }
                } else {
                    if (
                        date('Y-m-d H:i:s') >
                        date(
                            'Y-m-d H:i:s',
                            strtotime($isClose->jam_masuk . ' +12 hour')
                        )
                    ) {
                        if (isset($foto)) {
                            $imgname =
                                $cekRfid->nama .
                                '_masuk_' .
                                date('dmY_H-i-s_', time()) .
                                uniqid(rand(0, 5)) .
                                '.' .
                                $foto->getClientOriginalExtension();
                            if (
                                in_array($foto->getClientOriginalExtension(), [
                                    'jpg',
                                    'jpeg',
                                    'gif',
                                    'png',
                                ])
                            ) {
                                $tujuan_upload =
                                    'files/Absensi/' .
                                    Carbon::now()->format('Y') .
                                    '/' .
                                    Carbon::now()->format('m');
                                $foto->move($tujuan_upload, $imgname);
                                $statFoto = 'capture foto sukses';
                            } else {
                                $statFoto = 'capture foto gagal';
                            }
                        }
                        $namafoto = $tujuan_upload . '/' . $imgname;
                        $this->SendHistory(
                            $cekRfid->id,
                            'attendance recorded',
                            $cek_device->uid,
                            true
                        );
                        $this->sendAttendance(
                            $cek_device->uid,
                            $cekRfid->id,
                            '-',
                            'hadir',
                            'masuk tepat waktu',
                            'Tazaka Room : ' . $cek_device->uid,
                            $namafoto
                        );
                        return $this->sendMessageAbsence(
                            'hadir',
                            $cekRfid,
                            $statFoto,
                            Carbon::now()->format('H:i:s')
                        );
                    } else {
                        if (
                            date('Y-m-d H:i:s') >
                            date(
                                'Y-m-d H:i:s',
                                strtotime($isClose->jam_masuk . ' +1 hour')
                            )
                        ) {
                            if (isset($foto)) {
                                $imgname =
                                    $cekRfid->nama .
                                    '_pulang_' .
                                    date('dmY_H-i-s_', time()) .
                                    uniqid(rand(0, 5)) .
                                    '.' .
                                    $foto->getClientOriginalExtension();
                                if (
                                    in_array(
                                        $foto->getClientOriginalExtension(),
                                        ['jpg', 'jpeg', 'gif', 'png']
                                    )
                                ) {
                                    $tujuan_upload =
                                        'files/Absensi/' .
                                        Carbon::now()->format('Y') .
                                        '/' .
                                        Carbon::now()->format('m');
                                    $foto->move($tujuan_upload, $imgname);
                                    $statFoto = 'capture foto sukses';
                                } else {
                                    $statFoto = 'capture foto gagal';
                                }
                            }
                            $namafoto = $tujuan_upload . '/' . $imgname;
                            $this->SendHistory(
                                $cekRfid->id,
                                'attendance recorded',
                                $cek_device->uid,
                                true
                            );
                            $this->updateAttendance(
                                $isClose->id,
                                $cek_device->uid,
                                $namafoto
                            );
                            $monitoringdata = OutMonitoring::where(
                                'memploye_id',
                                $cekRfid->id
                            )
                                ->whereNull('tmend')
                                ->orderBy('id', 'desc')
                                ->first();
                            if ($monitoringdata) {
                                $in = [
                                    'tmend' => date('Y-m-d H:i:s'),
                                ];
                                $monitoringdata->update($in);
                            }
                            return $this->sendMessageAbsence(
                                'success',
                                $cekRfid,
                                $statFoto,
                                Carbon::now()->format('H:i:s')
                            );
                        } else {
                            $this->SendHistory(
                                $cekRfid->id,
                                'attendance recorded',
                                $cek_device->uid,
                                true
                            );
                            return $this->sendErrorAbsence(
                                'sudah absen*' . $cekRfid->nama
                            );
                        }
                    }
                }
                // END : Jam Masuk
                // Jam Keluar
            }
            return $this->sendErrorAbsence('salah secret key');
        }
        return $this->sendErrorAbsence('salah param');
    }
    public function timestamp()
    {
        return time();
    }

    public function datetime()
    {
        return date('Y,m,d,H,i,s', time());
    }
}
