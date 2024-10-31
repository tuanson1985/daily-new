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
        <th>Số ngọc</th>
        <th>Sau G.D</th>
        <th>Thông tin Item</th>
{{--        <th>Item</th>--}}
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
                    @if($item->gem)
                        {{ $item->gem }}
                    @endif
                </td>
                <td>
                    @if($item->c_sau)
                        {{ $item->c_sau }}
                    @endif
                </td>
                <td>
                    @if($item->info_item)
                        {{ str_replace("\n","</br>",base64_decode($item->info_item)) }}
                    @endif
                </td>
{{--                <td>{{$item->item}}</td>--}}
                <td>
                    @if($item->status)
                        @if ($item->status == "danap" || $item->status == "danhanngoc" || $item->status == "loichuyenngoc")
                            Hoàn tất
                        @elseif($item->status == "muanhamitem")
                            @if ($item->order)
                                @if ($item->order->status == 4)
                                    Hoàn tất
                                @elseif ($item->order->status == 9)
                                    Xử lý thủ công
                                @else
                                    Thất bại
                                @endif
                            @endif
                        @elseif($item->status =="taikhoansai" || $item->status =="koosieuthi" || $item->status =="matitem" || $item->status =="kconhanvat" || $item->status =="thieungoc" ||
                           $item->status == "caimk2" || $item->status =="hanhtrangday" || $item->status =="khongcoitemkigui" || $item->status =="kodusucmanh" || $item->status =="tamhetngoc" || $item->status =="dahuybo")
                            Thất bại
                        @else
                            Đang chờ
                        @endif
                    @endif
                </td>
                <td>
                    @if($item->status)
                        @if ($item->status == "danap")
                            Đã nạp
                        @elseif($item->status == "danhanngoc")
                            Đã nhận ngọc
                        @elseif($item->status == "muanhamitem")
                            Mua nhầm item
                        @elseif($item->status == "tamhetngoc")
                            Tạm hết ngọc
                        @elseif($item->status == "thieungoc")
                            Thiếu ngọc
                        @elseif($item->status == "taikhoansai")
                            Tài khoản sai
                        @elseif($item->status == "kodusucmanh")
                            Không sức mạnh
                        @elseif($item->status == "koosieuthi")
                            Không siêu thị
                        @elseif($item->status == "kconhanvat")
                            Không có nhân vật
                        @elseif($item->status == "caimk2")
                            Cài mật khẩu cấp 2
                        @elseif($item->status == "matitem")
                            Mất item
                        @elseif($item->status == "dahuybo")
                            Thất bại
                        @endif
                    @else
                        Đang chờ
                    @endif
                </td>
                <td>{{\App\Library\Helpers::FormatDateTime('H:i:s d/m/Y',$item->created_at)}}</td>
                <td>{{\App\Library\Helpers::FormatDateTime('H:i:s d/m/Y',$item->updated_at)}}</td>
            </tr>
        @endforeach
    </tbody>
</table>




