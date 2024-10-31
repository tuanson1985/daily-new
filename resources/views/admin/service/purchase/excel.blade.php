@php
    $dataExcel = $data['data'];

@endphp
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Requet id</th>
        <th>Dịch vụ</th>
        <th>Loại tài khoản</th>
        <th>Server</th>
        <th>Tên công việc</th>
        @if(Auth::user()->account_type!=3)
            <th>Trị giá</th>
        @endif
{{--        <th>Chiết khấu dành cho CTV</th>--}}
        <th>Số tiền CTV Nhận</th>
        @if(Auth::user()->can('service-purchase-view-profit'))
        <th>Lợi nhuận</th>
        @endif
{{--        <th>Lý do từ chối</th>--}}
        <th>Trạng thái</th>
        <th>Ngày tạo</th>
        <th>Ngày hoàn tất</th>
        <th>Người order</th>
        <th>Người nhận</th>

    </tr>
    </thead>
    <tbody>

        @foreach($dataExcel??[] as $item)
            <tr>
                <td>{{ $item->id??null }}</td>
                <td>{{'"'.$item->request_id_customer??''}}</td>
                <td>{{ $item->title}}</td>
                <td>
                    @if ($item->author &&  $item->author->type_information)
                        @if ($item->author->type_information == 0)
                            Việt Nam
                        @else
                            Global
                        @endif
                    @else
                        Việt Nam
                    @endif
                </td>
                <td>{{ $item->params->server??""}}</td>
                <td>
                    {{ $item->description??'' }}
                </td>
                @if(Auth::user()->account_type!=3)
                    <td>{{ $item->price}}</td>
                @endif
                <td>{{ $item->real_received_price_ctv}}</td>
                @if(Auth::user()->can('service-purchase-view-profit'))
                <td>
                    @if($item->status==4)
                        {{intval($item->price)-intval($item->real_received_price_ctv)}}
                    @else
                        0
                    @endif
                </td>
                @endif
                <td>{{config('module.service-purchase.status.'.$item->status)}}</td>
                <td>{{date('d/m/Y H:i:s', strtotime($item->created_at))}}</td>
                <td>
                    {{date('d/m/Y H:i:s', strtotime($item->updated_at))}}
                </td>
                <td>{{$item->author->username??""}}</td>
                <td>{{$item->processor->username??""}}</td>
            </tr>
        @endforeach

    </tbody>
</table>
