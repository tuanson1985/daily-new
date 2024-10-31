@php
    $charge = $data['data'];
@endphp
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Shop</th>
            <th>Tài khoản</th>
            <th>Loại thẻ</th>
            <th>Serial</th>
            <th>Mệnh giá yêu cầu</th>
            <th>Mệnh giá thực</th>
            <th>Phí nạp thẻ</th>
            <th>Thực nhận</th>
            <th>Phí cổng nạp</th>
            <th>Thực nhận từ cổng nạp</th>
            <th>Lợi nhuận</th>
            <th>Cổng nạp</th>
            <th>Mã nhà cung cấp</th>
            <th>Thời gian tạo</th>
            <th>Thời gian cập nhật</th>
            <th>Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($charge) && count($charge) > 0)
            @foreach($charge as $item)
                <tr>
                    <td>{{ $item->id??null }}</td>
                    <td>{{ $item->shop->domain??null }}</td>
                    <td>{{ $item->user->username??null }}</td>
                    <td>{{ $item->telecom_key??null }}</td>
                    <td>{{ $item->serial.' '??null }}</td>
                    <td>{{ $item->declare_amount??null }}</td>
                    <td>{{ $item->amount??null }}</td>
                    <td>{{ $item->ratio??null }}</td>
                    <td>{{ $item->real_received_amount??null }}</td>
                    <td>{{ '' }}</td>
                    <td>{{ $item->money_received??null }}</td>
                    <td>{{ $item->money_received - $item->real_received_amount??null }}</td>
                    <td>{{ config('module.telecom.gate_id.'.$item->gate_id)??null }}</td>
                    <td>{{ $item->tranid }}</td>
                    <td>{{ isset($item->created_at)?\Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i:s'):null }}</td>
                    <td>{{ isset($item->process_at)?\Carbon\Carbon::parse($item->process_at)->format('d-m-Y H:i:s'):null }}</td>
                    <td>{{ config('module.charge.status.'.$item->status) }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
