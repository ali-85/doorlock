<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\collectAttendance;
use App\Models\memployee;
use App\Models\OutMonitoring;
use App\Models\doorlockDevices;
use App\Models\attendanceDevice;

class DashboardController extends Controller
{
    public function groupBy()
    {
        $year = Carbon::now()->year;
        $d = collectAttendance::whereYear('created_at', '=', $year)
            ->orderBy('created_at', 'asc')
            ->get(['keterangan', 'created_at'])
            ->groupBy('keterangan');

        $y = $d->map(function ($data) {
            return $data->groupBy(function ($data) {
                return Carbon::parse($data->created_at)->format('M');
            });
        });

        return $y;
    }
    public function chartAttendancesMonth()
    {
        $year = Carbon::now()->year;
        $data = collectAttendance::whereYear('created_at', '=', $year)
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function ($data) {
                return Carbon::parse($data->created_at)->format('M');
            });

        $months = [];
        foreach ($data as $month => $value) {
            $months[] = $month;
        }
        return $months;
    }
    public function hCount()
    {
        $y = $this->groupBy();

        $h = empty($y['hadir']) ? collect([]) : $y['hadir'];
        $hCount = $h->map(function ($item, $key) {
            return collect($item)->count();
        });

        return $hCount;
    }
    public function tCount()
    {
        $y = $this->groupBy();
        $t = empty($y['terlambat']) ? collect([]) : $y['terlambat'];
        $tCount = $t->map(function ($item, $key) {
            return collect($item)->count();
        });

        return $tCount;
    }
    public function thCount()
    {
        $y = $this->groupBy();
        $th = empty($y['tidak masuk']) ? collect([]) : $y['tidak masuk'];
        $thCount = $th->map(function ($item, $key) {
            return collect($item)->count();
        });
        return $thCount;
    }
    public function fetchDataTodayAtd()
    {

        $data = collectAttendance::whereDate('created_at','=',Carbon::now())->orderBy('created_at','asc')->get()->groupBy(function($data){
            return $data->keterangan;
        });
        $totalKaryawan = count(memployee::all());

        $keterangan = [];
        $keteranganCount = [];
        $keteranganMap = [];
        $kehadiranPersentase = 0;
        $terlambatPersentase = 0;
        $tidakhadirPersentase = 0;
        foreach ($data as $ket => $value) {
            $keterangan[] = $ket;
            $keteranganCount[] = count($value);
            $keteranganMap[] = $value;
        }

        $ke = collect($keteranganMap)->collapse()->groupBy('keterangan');
        $groupCount = $ke->map(function ($item, $key) {
            return collect($item)->count();
        });
        $kehadiranPersentase = empty($groupCount['hadir']) ? 0 : round($groupCount['hadir']/$totalKaryawan * 100,2);
        $terlambatPersentase = empty($groupCount['terlambat']) ? 0 : round($groupCount['terlambat']/$totalKaryawan * 100,2);
        $tidakhadirPersentase = empty($groupCount['tidak masuk']) ? 0 : round($groupCount['tidak masuk']/$totalKaryawan * 100,2);
        return [
            'kehadiran' => $kehadiranPersentase,
            'terlambat' => $terlambatPersentase,
            'tidakhadir' => $tidakhadirPersentase,
            'dataTodayChart' => $keteranganCount != null ? [empty($groupCount['tidak masuk']) ? 0 : $groupCount['tidak masuk'],empty($groupCount['terlambat']) ? 0 : $groupCount['terlambat'],empty($groupCount['hadir']) ? 0 : $groupCount['hadir']] : [0,0,0,$totalKaryawan]
        ];
    }
    public function fetchOutTimeChart()
    {
        $topdurasi = OutMonitoring::select('nama', DB::raw('SUM(TIMESTAMPDIFF(MINUTE, tmstart, tmend)) as minutes'))->join('memployees','memployees.id','=','out_monitoring.memploye_id')->groupBy('memploye_id')->orderBy('minutes', 'desc')->take(10)->get();
        $topfrekuen = OutMonitoring::select('nama', DB::raw('COUNT(memploye_id) as frekuensi'))->join('memployees','memployees.id','=','out_monitoring.memploye_id')->groupBy('memploye_id')->orderBy('frekuensi', 'desc')->take(10)->get();
        $names = [];
        $namesFrekuen = [];
        $minutes = [];
        $frekuen = [];
        foreach ($topdurasi as $row) {
            $names[] = $row->nama;
            $minutes[] = $row->minutes;
        }
        foreach ($topfrekuen as $row) {
            $namesFrekuen[] = $row->nama;
            $frekuen[] = $row->frekuensi;
        }
        return [
            'name' => $names,
            'nameFrekuen' => $namesFrekuen,
            'minutesout' => $minutes,
            'frekuensi' => $frekuen
        ];
    }
    public function getIndex()
    {
        //count All Employees
        $employees = memployee::count();

        //Count All Devices
        $devices = doorlockDevices::count() + attendanceDevice::count();

        //Count Presents
        $presents = collectAttendance::whereDate(
            'jam_masuk',
            date('Y-m-d')
        )->count();

        //Count Absences
        $absence = collectAttendance::whereDate(
            'jam_masuk',
            date('Y-m-d')
        )->pluck('user_id');
        $absences = memployee::whereNotIn('id', $absence)->count();

        $data = OutMonitoring::select('nama', DB::raw('SUM(TIMESTAMPDIFF(MINUTE, tmstart, tmend)) as minutes'))->join('memployees','memployees.id','=','out_monitoring.memploye_id')->groupBy('memploye_id')->orderBy('minutes', 'desc')->take(10)->get();
        $topfrekuen = OutMonitoring::select('nama', DB::raw('COUNT(memploye_id) as frekuensi'))->join('memployees','memployees.id','=','out_monitoring.memploye_id')->groupBy('memploye_id')->orderBy('frekuensi', 'desc')->take(10)->get();
        //returning View
        return view('pages.dashboard', [
            'total_employee' => $employees,
            'total_doorlock' => $devices,
            'employee_presents' => $presents,
            'employee_absences' => $absences,
            'month' => $this->chartAttendancesMonth(),
            'lineH' => $this->hCount(),
            'lineT' => $this->tCount(),
            'lineTH' => $this->thCount(),
            'tidakHadirPer' => $this->fetchDataTodayAtd()['kehadiran'],
            'terlamabatPer' => $this->fetchDataTodayAtd()['terlambat'],
            'hadiranPer' => $this->fetchDataTodayAtd()['tidakhadir'],
            'dataTodayChart' => $this->fetchDataTodayAtd()['dataTodayChart'],
            'name' => $this->fetchOutTimeChart()['name'],
            'nameFrekuen' => $this->fetchOutTimeChart()['nameFrekuen'],
            'minutesout' => $this->fetchOutTimeChart()['minutesout'],
            'frekuensi' => $this->fetchOutTimeChart()['frekuensi'],
            'topdurasi' => $data,
            'topfrekuensi' => $topfrekuen
        ]);
    }
}
