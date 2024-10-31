<table>

    <thead>
    <tr>

        <th>ID</th>
        <th>ID order</th>
        <th>Requet id</th>
        <th>Loại dịch vụ</th>
        <th>Tài khoản order</th>
        <th>Số tiền</th>
        <th>Ver</th>
        <th>Bot xử lý</th>
        <th>Server</th>
        <th>Tài khoản/Tên nhân vật</th>
        <th>Số vật phẩm</th>
        <th>Trạng thái đơn hàng</th>
        <th>Trạng thái tool</th>
        <th>Thời gian tạo</th>
        <th>Thời gian hoàn thành</th>
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
                    @if($item->order && $item->order->idkey)
                        @if ($item->order->idkey == 'roblox_gem_pet')
                            BÁN GEM ROBUX
                        @elseif ($item->order->idkey == 'huge_psx_auto')
                            BÁN HUGE PSX
                        @elseif ($item->order->idkey == 'roblox_buyserver')
                            BÁN ROBUX DẠNG MUA SERVER
                        @elseif ($item->order->idkey == 'pet_99_auto')
                            BÁN GEM PET 99
                        @elseif ($item->order->idkey == 'huge_99_auto')
                            HUGE 99
                        @elseif ($item->order->idkey == 'gem_unist_auto')
                            BÁN GEM UNIST TOILET TOWER DEFENSE
                        @else
                            BÁN ROBUX DẠNG MUA GAMEPASS
                        @endif
                    @endif
                </td>
                <td>
                    @if(isset($item->order))
                    {{ $item->order->author->username }}
                    @endif
                </td>

                <td>{{isset($item->order->price)?$item->order->price:""}}</td>
                <td>{{$item->ver}}</td>
                <td>{{$item->bot_handle}}</td>
                <td>{{$item->server}}</td>
                <td>{{$item->uname}}</td>
                <td>
                    @if($item->status=="danap")
                        {{ '"'.$item->money}}
                    @else
                        {{ '"'.$item->money}}
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
                    @endif
                </td>
                @if($item->status=="danhan")
                    <td>Đã nhận</td>
                @elseif($item->status=="danap")
                    <td>Đã nạp</td>
                @elseif($item->status=="chuanhan")
                    <td>Chưa nhận</td>
                @elseif($item->status=="dahuybo")
                    <td>Đã hủy bỏ</td>
                @else
                    <td>{{ $item->status }}</td>
                @endif
                <td>{{\App\Library\Helpers::FormatDateTime('H:i:s d/m/Y',$item->created_at)}}</td>
                <td>
                    @php
                        $time = $item->updated_at;
                        if ($item->order && $item->order->updated_at){
                            $time = $item->order->updated_at;
                        }
                    @endphp
                    {{\App\Library\Helpers::FormatDateTime('H:i:s d/m/Y',$time)}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>




