<table>
    <tr>
        <th>No</th>
        <th>Transaction ID</th>
        <th>Transfer Type</th>
        <th>Beneficiary ID</th>
        <th>Credited Account</th>
        <th>Receiver Name</th>
        <th>Amount</th>
        <th>NIP</th>
        <th>Remark</th>
        <th>Beneficiary Email Address</th>
        <th>Receiver Swift Code</th>
        <th>Receiver Cust type</th>
        <th>Receiver Cust Residence</th>
    </tr>
    @foreach ($data as $item)
        @php
            $insentif = 0;
            $ins = json_decode($item->insentif, true);
            foreach ($ins as $key => $val) {
                if ($val['category'] != null) {
                    ($val['category'] == 'payroll deductions')?$insentif -= $val['value']:$insentif += $val['value'];
                }
            }
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->transaction_id . '' . $loop->iteration }}</td>
            <td align="center">{{ $item->transfer_type != 'CASH'?explode(' ', $item->transfer_type)[1]:$item->transfer_type }}</td>
            <td></td>
            <td align="center">{{ $item->credited_accont }}</td>
            <td>{{ $item->bank_name }}</td>
            <td align="center">{{ number_format($item->salary + $item->lembur + $item->lembur2 + $item->lembur3 + $insentif, 2, ',', '.') }}</td>
            <td align="center">{{ $item->nip }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
</table>
