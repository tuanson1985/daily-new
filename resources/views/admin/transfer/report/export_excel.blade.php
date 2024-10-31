@php
    $tranfer = $data['data'];
@endphp
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Shop</th>
            <th>Tài khoản</th>
            <th>Số tiền</th>
            <th>Phí/ chiết khấu</th>
            <th>Thực nhận</th>
            <th>Phí/ chiết khấu của NCC</th>
            <th>Số tiền NCC phải trả</th>
            <th>Lợi nhuận từ phí nạp ATM</th>
            <th>Trạng thái</th>
            <th>Tranid</th>
            <th>Thời gian nhận</th>
            <th>Thời gian nạp</th>
            <th>Mã FT</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($tranfer) && count($tranfer) > 0)
            @foreach($tranfer as $item)
                <tr>
                    <td>{{ $item->id??null }}</td>
                    <td>{{ $item->shop->domain??null }}</td>
                    <td>{{ $item->author->username }}</td>
                    <td>{{ $item->price }}</td>
                    <td>{{ $item->ratio }}</td>
                    <td>{{ $item->real_received_price }}</td>
                    <td>{{ 100 }}</td>
                    <td>{{ 0 }}</td>
                    <td>{{ 0 }}</td>
                    <td>{{ config('module.transfer.status.'.$item->status) }}</td>
                    <td>{{ $item->tranid }}</td>
                    <td>{{ isset($item->created_at)?\Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i:s'):null }}</td>
                    <td>{{ '' }}</td>
                    <td>{{ $item->content }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
