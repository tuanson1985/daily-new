<table>

    <thead>
    <tr>
        <th>ID</th>
        <th>Tên tài khoản</th>
        <th>Số dư</th>
        <th>Số dư theo thời gian</th>
        <th>Biến động số dư</th>
        <th>Trạng thái</th>
        <th>Thời gian</th>
    </tr>
    </thead>
    <tbody>

        @php
            $data = $data['data'];
        @endphp

        @foreach($data as $index=> $item)

            <tr>
                <td>
                    {{ $item->id }}
                </td>
                <td>
                    @if(isset($item->username))
                    {{ $item->username }}
                    @endif
                </td>
                <td>
                    @if(isset($item->balance))
                        {{ $item->balance }}
                    @endif
                </td>
                <td>
                    @if(isset($item->last_balance))
                        {{ $item->last_balance }}
                    @endif
                </td>
                <td>
                    @php
                        $balance_in = intval($item->balance_in);
                        $balance_out = intval($item->balance_out);
                        $balance_in_refund = intval($item->balance_in_refund);
                        $resuft_in_out = $balance_out - $balance_in_refund;
                        $balance = $item->balance;
                        $not_equal = $balance_in - $balance_out + $balance_in_refund - $balance
                    @endphp
                    <span class='text-success'>+ {{ $balance_in }}</span><br/>
                    <span class='text-danger'>- {{ $resuft_in_out }}</span><br/>
                    @if($not_equal != 0) {
                        <div class='text-danger' style='border:1px solid #f64e60;padding:5px;margin-top:5px;'>Lệch {{ $not_equal }}</div><br/>
                    @else
                        <div class='text-success' style='border:1px solid #1bc5bd;padding:5px;margin-top:5px;' >Chuẩn +</div><br/>
                    @endif
                </td>
                <td>
                    {{config('module.user.status.'.$item->status)}}
                </td>
                <td>{{\App\Library\Helpers::FormatDateTime('H:i:s d/m/Y',$item->created_at)}}</td>
            </tr>
        @endforeach
    </tbody>
</table>




