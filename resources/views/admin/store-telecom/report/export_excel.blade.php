@php
    $store_card = $data['data'];

@endphp
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Shop</th>
            <th>Tài khoản</th>
            <th>Nhà mạng</th>
            <th>Mệnh giá</th>
            <th>Số lượng</th>
            <th>Tổng mệnh giá</th>
            <th>Chiết khấu</th>
            <th>Tổng thanh toán</th>
            <th>Chiết khấu NCC</th>
            <th>Phải trả NCC</th>
            <th>Lợi nhuận</th>
            <th>Nhà cung cấp</th>
            <th>Mã nhà cung cấp</th>
            <th>Ngày tạo</th>
            <th>Ngày thành công</th>
            <th>Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($store_card) && count($store_card) > 0)
            @foreach($store_card as $item)
            @php
                $params = json_decode($item->params);
                $amount = $params->amount;
                $quantity = $params->quantity;
                $telecom = $params->telecom;
            @endphp
                <tr>
                    <td>{{ $item->id??null }}</td>
                    <td>{{ $item->shop->domain??null }}</td>
                    <td>{{ $item->author->username }}</td>
                    <td>{{ $telecom }}</td>
                    <td>{{ $amount }}</td>
                    <td>{{ $quantity }}</td>
                    <td>{{ $amount * $quantity }}</td>
                    <td>{{ $item->ratio }}</td>
                    <td>{{ $item->real_received_price }}</td>
                    <td>{{ '' }}</td>
                    <td>{{ $item->price_input??null }}</td>
                    <td>{{ isset($item->price_input)?$item->real_received_price - $item->price_input : '' }}</td>
                    <td>{{ config('module.store-card.gate_id.'.$item->gate_id) }}</td>
                    <td>'{{ $item->request_id }}</td>
                    <td>{{ isset($item->created_at)?\Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i:s'):null }}</td>
                    <td>{{ isset($item->process_at)?\Carbon\Carbon::parse($item->process_at)->format('d-m-Y H:i:s'):null }}</td>
                    <td>{{ config('module.store-card.status.'.$item->status) }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
