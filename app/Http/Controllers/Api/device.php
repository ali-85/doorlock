<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\captureDoorlockResorce;
use App\Http\Resources\HistoryResource;
use App\Models\collectAttendance;
use App\Models\doorlockDevices;
use App\Models\DoorlockReport;
use App\Models\memployee;
use App\Models\Schadule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class device extends BaseController
{

    protected $key;
    protected $Base;
    public function __construct(BaseController $base)
    {
        $this->key = env('TAZAKA_KEY');
        $this->Base = $base;
    }

    public function index()
    {
        $response = [
            "success" => true,
            "message" => "Welcome To Api Doorlock PT Cahaya Sukses Plastindo",
            "data" => [],
        ];

        return response()->json($response);
    }
    public function registerdev(Request $request)
    {
        if (isset($request->key) && isset($request->rfid) && isset($request->token)) {
            $key = $request->key;

            if ($this->key == $key) {
                $rfid = $request->rfid;

                $cekRfid = memployee::where('rfid_number',$rfid)->first();

                if ($cekRfid) {
                    return $this->sendError('RFID sudah terdaftar',200);
                } else {
                }
            }else{
                return $this->sendError('salah secret key',401);
            }
        }else{
            return $this->sendError('salah parameter',401);
        }
    }
    public function getroom(Request $request)
    {
        if (isset($request->key) && isset($request->iddev)) {
            if ($this->key == $request->key) {

                $cek_device = doorlockDevices::where('uid',$request->iddev)->first();

                if (!is_null($cek_device)) {
                    Log::channel('Apilog')->info('Room : ' . $cek_device->name . ' Berhasil Masuk, Success : Device Terdaftar');
                    return $this->sendRoom('Device terdaftar',$cek_device->name,$cek_device->departement->nama,$cek_device->type);
                }
                Log::channel('Apilog')->info('Room : ' . $request->iddev . ' Mencoba Masuk Tapi Gagal, Error : Device Tidak Terdaftar');
                return $this->sendError('Device tidak ada');
            } else {
                Log::channel('Apilog')->info('Room : ' . $request->iddev . ' Mencoba Masuk Tapi Gagal, Error : Secret Key Salah');
                return $this->sendError('salah secret key');
            }
        }else{
            Log::channel('Apilog')->info('Room : ' . $request->iddev . ' Mencoba Masuk Tapi Gagal, Error : salah parameter');
            return $this->sendError('salah parameter');
        }
    }

    public function access_room(Request $request)
    {
        if (isset($request->key) && isset($request->rfid) && isset($request->iddev)) {
            if ($this->key == $request->key) {

                $cek_device = doorlockDevices::where('uid',$request->iddev)->first();
                if (is_null($cek_device)) {
                    return $this->sendError('Device tidak terdaftar');
                }

                $cekRfid = memployee::where('rfid_number',$request->rfid)->first();
                if (is_null($cekRfid)) {
                    return $this->sendError('RFID tidak terdaftar');
                }

                $lock  = $cek_device->access_mode == 1 ? 'close' : 'open';

                $schadule = $cek_device->schadule()->get();

                if($schadule){
                    foreach ($schadule as  $value) {
                        if (Carbon::now() >= carbon::parse($value->tanggal_awal) && Carbon::now() <= carbon::parse($value->tanggal_akhir)->addHours(23)->addMinutes(59)->addSeconds(59)){
                            $cekuser = $cekRfid->schadule()->get();
                            foreach ($cekuser as $key => $userSchadule) {
                                if ($userSchadule->pivot->schadules_id == $value->id) {
                                    $workingTime = $cekRfid->shiftcode;
                                    if (Carbon::now() >= carbon::parse($workingTime->jam_masuk) && Carbon::now() <= carbon::parse($workingTime->jam_keluar)){
                                        $this->SendHistory($cekRfid->id,'Access Granted from schedule',$cek_device->uid);
                                        $doorlockReport = $this->SendDoorlockReport($cekRfid->id,'Access Granted from schedule',$cek_device->uid);
                                        $withremark = $this->withremarks($cek_device->remarks,$doorlockReport);
                                        $withLinks = $this->withLinks($doorlockReport,$cekRfid->user_DoorTime);
                                        return $this->sendMessage('success','Access Granted from schedule',$cekRfid->departement->nama,$lock,$cek_device, $cekRfid,$withremark,$withLinks);
                                    }
                                }
                            }
                        }
                    }
                }

                if ($cek_device->type == 'restricted') {
                    if ($cek_device->departement_id === $cekRfid->departement_id) {
                        $cekPrivelage = $cekRfid->Doorlock()->get(['uid']);
                        foreach ($cekPrivelage as  $value) {
                            if ($value->uid == $cek_device->uid) {
                                $this->SendHistory($cekRfid->id,'Access Granted',$cek_device->uid);
                                $doorlockReport = $this->SendDoorlockReport($cekRfid->id,'Access Granted',$cek_device->uid);
                                $withremark = $this->withremarks($cek_device->remarks,$doorlockReport);
                                $withLinks = $this->withLinks($doorlockReport,$cekRfid->user_DoorTime);
                                return $this->sendMessage('success','Access Granted',$cekRfid->departement->nama,$lock,$cek_device, $cekRfid,$withremark,$withLinks);
                            }
                        }
                        return $this->sendMessage('failed','No Access',$cekRfid->departement->nama,'close',$cek_device, $cekRfid);
                    }
                }

                // Cek Privelage
                $cekPrivelage = $cekRfid->Doorlock()->get(['uid']);

                foreach ($cekPrivelage as  $value) {
                    if ($value->uid == $cek_device->uid) {
                        $this->SendHistory($cekRfid->id,'Access Granted',$cek_device->uid);
                        $doorlockReport = $this->SendDoorlockReport($cekRfid->id,'Access Granted',$cek_device->uid);
                        $withremark = $this->withremarks($cek_device->remarks,$doorlockReport);
                        $withLinks = $this->withLinks($doorlockReport,$cekRfid->user_DoorTime);
                        return $this->sendMessage('success','Access Granted',$cekRfid->departement->nama,$lock,$cek_device, $cekRfid,$withremark,$withLinks);
                    }
                }
                return $this->sendMessage('failed','No Access',$cekRfid->departement->nama,'close',$cek_device, $cekRfid);

            }else{
                return $this->sendError('salah secret key');
            }
        }else{
            return $this->sendError('salah parameter');
        }
    }

    public function remarks(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            return $this->sendError('Link Expired',401);
        }

        $doorlock = DoorlockReport::find($id);

        if (!$doorlock) {
            return $this->sendError('Data Not Found');
        }

        if (isset($request->key) && isset($request->remark) && isset($request->iddev)) {
            if ($this->key == $request->key) {
                $cekRemark = doorlockDevices::find($doorlock->uid);
                $user = memployee::find($doorlock->user_id);
                if ($request->iddev == $doorlock->uid) {
                    foreach ($cekRemark->remarks as $value) {
                        if ($request->remark == $value->id) {
                            $this->editRemark($id,$value->name);
                            return $this->sendResponseRemark('success','Access Granted',$user->departement->nama,'open',$cekRemark,$user);
                        }
                    }
                    return $this->sendResponseRemark('failed','No Access',$user->departement->nama,'close',$cekRemark, $user);
                }
                return $this->sendError('Doorlock id tidak sama');
            }
            return $this->sendError('salah secret key');
        }
        return $this->sendError('salah parameter');
    }
    public function counter(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            return $this->sendError('Link Expired',401);
        }

        $doorlock = DoorlockReport::find($id);

        if (!$doorlock) {
            return $this->sendError('Data Not Found');
        }
        if (isset($request->key) && isset($request->count) && isset($request->iddev)) {
            if ($this->key == $request->key) {
                $door = doorlockDevices::find($doorlock->uid);
                $user = memployee::find($doorlock->user_id);
                if ($request->iddev == $doorlock->uid) {
                    $data = $this->editCount($id,$request->count);

                    return response()->json(new HistoryResource($data));
                }
            }
            return $this->sendError('salah secret key');
        }
        return $this->sendError('salah parameter');
    }

    public function checkCapture(Request $request)
    {
        if (isset($request->key) && isset($request->iddev)) {
            if ($this->key == $request->key) {
                $doorlock = DoorlockReport::where('uid',$request->iddev)->latest('created_at')->first();

                if ($doorlock) {
                    if ($doorlock->doorlock_photo_path == null) {
                        $second = Carbon::parse($doorlock->created_at)->addSeconds(30);
                        if ($second < Carbon::now()){
                            return $this->responseCapture1(0,$doorlock->id,$doorlock->uid);
                        }
                        return $this->responseCapture1(1,$doorlock->id,$doorlock->uid);
                    }
                    return $this->responseCapture1(0,$doorlock->id,$doorlock->uid);
                }
                return $this->sendError('belum ada data');
            }
            return $this->sendError('Key Tidak Sesuai');
        }
        return $this->sendError('paramater salah');
    }

    public function capture(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            return $this->sendError('Link Expired');
        }

        $doorlock = DoorlockReport::find($id);

        if (!$doorlock) {
            return $this->sendError('Data Not Found');
        }

        if (isset($request->key) && isset($request->foto)) {
            if ($this->key == $request->key) {
                $foto = $request->file('foto');
                $fotoName = "doorlock_access_".$doorlock->id."_".Carbon::now()->format('d-m-Y').".".$foto->getClientOriginalExtension();
                if ($doorlock->doorlock_photo_path == null) {
                    $uploadFoto = $foto->storeAs('public/doorlock', $fotoName);

                    if ($uploadFoto) {
                        $data = $this->editCapture($id,'storage/doorlock/'.$fotoName);
                        return response()->json(new captureDoorlockResorce($data));
                    }
                }
                return $this->sendError('Capture Foto Sudah Ada');
            }
            return $this->sendError('salah secret key');
        }
        return $this->sendError('salah parameter');
    }
}
