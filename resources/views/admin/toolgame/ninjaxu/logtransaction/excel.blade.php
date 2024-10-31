<table>

    <thead>
    <tr>
        <th>ID</th>
        <th>ID order</th>
        <th>Requet id</th>
        <th>Người order</th>
        <th>Số tiền</th>
        <th>Ver</th>
        <th>Server</th>
        <th>Tên bot xử lý</th>
        <th>Tên nhân vật</th>
        <th>Trước G.D</th>
        <th>Số vàng</th>
        <th>Sau G.D</th>
        <th>Trạng thái đơn hàng</th>
        <th>Trạng thái tool</th>
        <th>Thời gian tạo</th>
        <th>Thời gian cập nhật</th>
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
                @if(isset($item->order))
                    {{ $item->order->id }}
                @endif
            </td>
            <td>
                @if(isset($item->order))
                    {{ '"'.$item->order->request_id_customer }}
                @endif
            </td>
            <td>
                @if(isset($item->order))
                    {{ $item->order->author->username }}
                @endif
            </td>
            <td>
                @if ($item->order)
                    @if ($item->order->price)
                        {{ $item->order->price }}
                    @endif
                @endif
            </td>
            <td>{{$item->ver}}</td>
            <td>{{$item->server}}</td>
            <td>{{$item->bot_handle}}</td>
            <td>{{$item->uname}}</td>
            <td>
                @if($item->c_truoc)
                    {{ $item->c_truoc }}
                @endif
            </td>
            <td>
                @if($item->coin)
                    {{ $item->coin }}
                @endif
            </td>
            <td>
                @if($item->c_sau)
                    {{ $item->c_sau }}
                @endif
            </td>
            <td>
                @if($item->order)
                    @php
                        $status = $item->order->status;
                        if ($status == 2 || $status == 7 || $status == 9 || $status == 89){
                            $status = 1;
                        }

                    @endphp
                    {{config('module.service-purchase-auto.status.'.$status)}}
                @else
                    N/A
                @endif
            </td>
            <td>
                @if($item->status)
                    @if($item->status=='danhan')
                        Đã nhận
                    @elseif($item->status=='danap')
                        Đã nạp
                    @elseif($item->status=='chuanhan')
                        Chưa nhận
                    @elseif($item->status=='dahuybo')
                        Đã hủy bỏ
                    @endif
                @else
                    N/A
                @endif
            </td>
            <td>{{\App\Library\Helpers::FormatDateTime('H:i:s d/m/Y',$item->created_at)}}</td>
            <td>{{\App\Library\Helpers::FormatDateTime('H:i:s d/m/Y',$item->updated_at)}}</td>
        </tr>
    @endforeach
    </tbody>
</table>




