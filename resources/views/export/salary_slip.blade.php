<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Salary - {{ $pekan }}</title>
    <style>
        table {
            border-collapse: collapse;
            margin: auto;
            width: 100%;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        #outer {
            border: 1px solid black;
        }

        .my {
            margin: 5px 0 5px 0;
        }
    </style>
</head>

<body>
    @foreach ($data as $item)
    <table id="outer">
        <tr>
            <th align="center">
                CAHAYA SUKSES PLASTINDO
            </th>
        </tr>
        <div class="my"></div>
        <tr>
            <td align="center">Bukti Kas Keluar</td>
        </tr>
        <div class="my"></div>
        <table>
            <tr>
                <td>Dibayarkan Kpd :</td>
                <td>{{ $item->nama }}</td>
                <td>Periode :</td>
                <td>{{ date('d.m.y', strtotime($pekan)) }}</td>
            </tr>
        </table>
        <div class="my"></div>
        <table border="1" style="border-bottom: none">
            <tr>
                <th>Uraian</th>
                <th>Perkiraan</th>
                <th>Jumlah</th>
                <th>Ket</th>
            </tr>
            <tr>
                <td><span style="float: left">Hari Kerja</span> <span style="float: right">{{ $item->hari_kerja }}</span></td>
                <td align="center">Rp {{ number_format($item->basic_salary,0,',','.') }}</td>
                <td align="center">Rp {{ number_format($item->salary,0,',','.') }}</td>
                <td></td>
            </tr>
            @if ($item->lembur > 0)
                <tr>
                    <td><span style="float: left">Lembur 1</span> <span style="float: right">{{ $item->jumlah_lembur }}</span></td>
                    <td align="center">Rp 5.000</td>
                    <td align="center">Rp {{ number_format($item->lembur,0,',','.') }}</td>
                    <td></td>
                </tr>
            @endif
            @if ($item->lembur2 > 0)
                <tr>
                    <td><span style="float: left">Lembur 2</span> <span style="float: right">{{ $item->lembur2 }}</span></td>
                    <td align="center">Rp 20.000</td>
                    <td align="center">Rp {{ number_format((20000*$item->lembur2),0,',','.') }}</td>
                    <td></td>
                </tr>
            @endif
            @if ($item->lembur3 > 0)
                <tr>
                    <td><span style="float: left">Lembur 3</span> <span style="float: right">{{ $item->lembur3 }}</span></td>
                    <td align="center">Rp 20.000</td>
                    <td align="center">Rp {{ number_format((20000*$item->lembur3),0,',','.') }}</td>
                    <td></td>
                </tr>
            @endif
            @php
                $insentif = 0;
                $ins = json_decode($item->insentif, true);
            @endphp
            @foreach ($ins as $key => $val)
                @if ($val['category'] != null)
                {{ ($val['category'] == 'payroll deductions')?$insentif -= $val['value']:$insentif += $val['value'] }}
                <tr>
                    <td>{{ $val['remark']; }}</td>
                    <td align="center">{{ ($val['category'] == 'payroll deductions')?'-':'' }}Rp {{ number_format($val['value'],0,',','.'); }}</td>
                    <td align="center">{{ ($val['category'] == 'payroll deductions')?'-':'' }}Rp {{ number_format($val['value'],0,',','.'); }}</td>
                    <td></td>
                </tr>
                @endif
            @endforeach
            <tr>
                <td style="border: none">Jumlah</td>
                <td style="border: none"></td>
                <td align="center" style="border: none">Rp {{ number_format(($item->salary+$item->lembur+(20000*$item->lembur2)+(20000*$item->lembur3)+$insentif),0,',','.') }}</td>
                <td align="center" style="border: none">{{ $item->pembayaran == 1?'ATM':'CASH' }}</td>
            </tr>
        </table>
        <div class="my"></div>
        <table>
            <tr>
                <td width="350"></td>
                <td>Tanggal :</td>
                <td>{{ date('d-M-y') }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="border: 1px solid black">Direksi</td>
                <td width="280"></td>
                <td>Yang Menerima,</td>
            </tr>
            <tr>
                <td style="border-right: 1px solid black;" height="50"></td>
                <td></td>
            </tr>
        </table>
    </table>
    @endforeach
</body>

</html>
