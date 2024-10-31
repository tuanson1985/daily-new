@php
    $dataExcel = $data['data'];

@endphp
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Cổng Auto</th>
        <th>Dịch vụ</th>
        <th>Số lượng</th>
        <th>Server</th>
        <th>Trị giá</th>
        <th>Phải trả NCC</th>
        <th>Lợi nhuận</th>
        <th>Trạng thái</th>
        <th>Ngày tạo</th>
        <th>Ngày hoàn tất</th>
        <th>Người order</th>
        <th>Người nhận</th>
        <th>Requet id</th>

    </tr>
    </thead>
    <tbody>

        @foreach($dataExcel??[] as $item)

{{--            @php--}}
{{--                $params = json_decode($item->params);--}}
{{--                $amount = $params->amount;--}}
{{--                $quantity = $params->quantity;--}}
{{--                $telecom = $params->telecom;--}}
{{--            @endphp--}}
            <tr>
                <td>{{ $item->id??null }}</td>
                <td>{{ $item->idkey }}</td>
                <td>{{ $item->title}}</td>
                <td>{{ $item->price_base}}</td>
                <td>{{ $item->params->server??""}}</td>
                <td>{{ $item->price}}</td>
                <td>{{ $item->price_input}}</td>
                <td>{{ $item->price - $item->price_input}}</td>
                <td>{{config('module.service-purchase-auto.status.'.$item->status)}}</td>
                <td>{{date('d/m/Y H:i:s', strtotime($item->created_at))}}</td>
                <td>{{date('d/m/Y H:i:s', strtotime($item->updated_at))}}</td>
                <td>
                    @php
                        $author = str_replace('tt_', '', $item->author->username??"");
                    @endphp
                    {{ $author }}
                </td>
                <td>
                    @php
                        $processor = str_replace('tt_', '', $item->processor->username??"");
                    @endphp
                    {{$processor}}
                </td>
                <td>{{'"'.$item->request_id_customer??''}}</td>

            </tr>
        @endforeach

    </tbody>
</table>
