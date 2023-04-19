<table>
    <tr>
        <th>NO</th>
        <th>NAMA</th>
        @for ($i = 0; $i < count($dates); $i++)
            <td>{{ date('d-m-Y', strtotime($dates[$i])) }}</td>
        @endfor
        <th>OVERTIME</th>
        <th>ATTENDANCE</th>
    </tr>
    @foreach ($data as $item)
        <tr>
            <td align="center">{{ $loop->iteration }}</td>
            <td>{{ $item->nama }}</td>
            @php
                $index = 0;
                $jam = json_decode($item->tanggal, true);
                for ($i = 0; $i < count($dates); $i++) {
                    if (isset($jam[$index]['tanggal'])) {
                        if (date('Y-m-d', strtotime($dates[$i])) == date('Y-m-d', strtotime($jam[$index]['tanggal']))) {
                            echo '<td>' . date('H:i', strtotime($jam[$index]['masuk'])) . ' - ' . date('H:i', strtotime($jam[$index]['keluar'])) . '</td>';
                            $index++;
                        } else {
                            echo '<td>-</td>';
                        }
                    } else {
                        echo '<td>-</td>';
                    }
                }
            @endphp
            <td>{{ $item->lembur }}</td>
            <td>{{ count($jam) }}</td>
        </tr>
    @endforeach
</table>
