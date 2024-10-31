@php
    $dataExcel = $data['data'];

@endphp
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Cổng Auto</th>
        <th>Dịch vụ</th>
        <th>Loại tài khoản</th>
        <th>Số lượng</th>
        <th>Server</th>
        <th>Tên công việc</th>
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

            <tr>
                <td>{{ $item->id??null }}</td>
                <td>{{ $item->idkey }}</td>
                <td>{{ $item->title}}</td>
                <td>
                    @if ($item->author && $item->author->type_information)
                        @if ($item->author->type_information == 0)
                            Việt Nam
                        @else
                            Global
                        @endif
                    @else
                        Việt Nam
                    @endif
                </td>
                <td>{{ '"'.$item->price_base}}</td>
                <td>{{ $item->params->server??""}}</td>
                <td>
                    @php
                        $uname = null;
                        if (isset($item->roblox_order) && $item->roblox_order->phone){
                            if ($item->idkey == 'roblox_gem_pet'){
                                $valueWithB = $item->roblox_order->phone;
                                // Loại bỏ ký tự "B" và chuyển đổi thành số
                                $valueInBillion = (float) str_replace('B', '', $valueWithB);
                                $uname = '"'.($valueInBillion * 1000000000);

                            }else{
                                $uname = $item->roblox_order->phone??'';
                            }

                        }
                    @endphp
                    @if(isset($uname))
                        {{ $uname }}
                    @endif
                </td>
                <td>{{ $item->price}}</td>
                <td>{{ $item->price_input}}</td>
                <td>{{ $item->price - $item->price_input}}</td>
                <td>{{config('module.service-purchase-auto.status.'.$item->status)}}</td>
                <td>{{date('d/m/Y H:i:s', strtotime($item->created_at))}}</td>
                <td>{{date('d/m/Y H:i:s', strtotime($item->updated_at))}}</td>
                <td>{{$item->author->username??""}}</td>
                <td>{{$item->processor->username??""}}</td>
                <td>{{'"'.$item->request_id_customer??''}}</td>

            </tr>
        @endforeach

    </tbody>
</table>
