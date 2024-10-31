@php
    $dataExcel = $data['data'];

@endphp
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Requet id</th>
        <th>Dịch vụ</th>
        <th>Server</th>
        <th>Trị giá</th>
        <th>Chiết khấu dành cho CTV</th>
        <th>Số tiền CTV Nhận</th>
        <th>Lợi nhuận</th>
        <th>Trạng thái</th>
        <th>Ngày tạo</th>
        <th>Ngày hoàn tất</th>
        <th>Người order</th>
        <th>Người nhận</th>
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
                <td>{{'"'.$item->request_id_customer??''}}</td>
                <td>{{ $item->title}}</td>
                <td>{{ $item->params->server??""}}</td>
                <td>{{ $item->price}}</td>
                <td>
                    @if($item->status!=4)
                        @php

                            $ratio = 80;
                            $author_id=$item->processor_id??auth('frontend')->user()->id;
                            $service_access1 = App\Models\ServiceAccess::where('user_id', $author_id)->first();
                            $param1 = json_decode(isset($service_access1->params) ? $service_access1->params : "");
                            $ratio = isset($param1->{'ratio_' . ($item->itemconfig_ref->items->id??null)}) ? $param1->{'ratio_' . ($item->itemconfig_ref->items->id??null)??null} : $ratio;
                        @endphp
                    @else
                        @php
                            $ratio = $item->ratio_ctv;
                        @endphp
                    @endif
                    @php
                        $ratio= floor($ratio *10)/10;
                    @endphp
                        {{$ratio}}
                </td>
                <td>{{ $item->real_received_price_ctv}}</td>
                <td>
                    @if($item->status==4)
                        {{intval($item->price)-intval($item->real_received_price_ctv)}}
                    @else
                        0
                    @endif
                </td>
                <td>{{config('module.service-purchase.status.'.$item->status)}}</td>
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


            </tr>
        @endforeach

    </tbody>
</table>
