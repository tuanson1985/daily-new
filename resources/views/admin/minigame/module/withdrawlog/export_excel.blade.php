@php
    $minigame_logs = $data['data'];

@endphp

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên shop</th>
            <th>Loại rút vật phẩm</th>
            <th>Tên user</th>
            <th>ID game</th>
            <th>Phone</th>
            <th>Loại vật phẩm</th>
            <th>Số vật phẩm</th>
            <th>Giá vốn</th>
            <th>Tranid</th>
            <th>Thời gian tạo</th>
            <th>Thời gian hoàn tất</th>
            <th>Trạng thái</th>
            <th>Chuyển trạng thái</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($minigame_logs) && count($minigame_logs) > 0)
            @foreach($minigame_logs as $item)
                <tr>
                    <td>{{ $item->id??null }}</td>{{--   id                 --}}
                    <td>{{ $item->shop->domain??null }}</td>{{--   shop                 --}}
                    <td>
                        @if($item->module == 'withdraw-item')
                            Rút vật phẩm
                        @elseif($item->module == 'withdraw-service-item')
                            Rút tự động
                        @elseif($item->module == 'withdraw-itemrefund')
                            Hoàn VP
                        @endif
                    </td>{{--   Loại rút vật phẩm                 --}}
                    <td>{{ $item->author->username??null }}</td>{{--   tên user                 --}}
                    <td>
                        {{ $item->idkey }}
                    </td>{{--   Id game                 --}}
                    <td>
                        {{ $item->title??'' }}{{--   Phone                --}}
                    </td>
                    <td>
                        {{config('module.minigame.game_type.'.$item->payment_type)}}{{--   Loại vật phẩm                 --}}
                    </td>
                    <td>
                        {{ $item->price??'' }}
                    </td>{{--   Số vật phẩm               --}}
                    <td>
                        {{ $item->price_input??'' }}
                    </td>{{--   Giá vốn                --}}
                    <td>{{ $item->request_id??'' }}</td>{{--   Tranid                --}}
                    <td>{{ isset($item->created_at)?\Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i:s'):null }}</td>{{--   Thời gian  tao               --}}
                    <td>{{ isset($item->updated_at)?\Carbon\Carbon::parse($item->updated_at)->format('d-m-Y H:i:s'):null }}</td>{{--   Thời gian  hoàn tất               --}}
                    <td>
                        @if($item->payment_type == 11 || $item->payment_type == 12 || $item->payment_type == 13 || $item->payment_type == 14)
                            {{config('module.service-purchase-auto.status.'.$item->status)}}
                        @else
                            {{config('module.minigame.withdraw_status.'.$item->status)}}
                        @endif
                    </td>{{--   Trạng thái               --}}
                    <td>
                        @if($item->payment_type == 11 || $item->payment_type == 12 || $item->payment_type == 13 || $item->payment_type == 14)
                            @if($item->status == 4)
                                Hoàn tất
                            @elseif($item->status == 0)
                                Đã hủy
                            @elseif($item->status == 1)
                                Đang chờ
                            @elseif($item->status == 2)
                                Đang thực hiện
                            @elseif($item->status == 3)
                                Từ chối
                            @elseif($item->status == 5)
                                Thất bại
                            @elseif($item->status == 6)
                                Mất item
                            @elseif($item->status == 7)
                                Kết nối NCC thất bại.
                            @elseif($item->status == 9)
                                Xử lý thủ công
                            @elseif($item->status == 77)
                                Mất item không hoàn tiền
                            @elseif($item->status == 88)
                                Mất item có hoàn tiền
                            @endif
                        @else
                            @if($item->status == 1)
                                Đã hoàn thành giao dịch
                            @elseif($item->status == 0)
                                Hủy giao dịch
                            @elseif($item->status == 0)
                                Đã hủy giao dịch
                            @endif
                        @endif

                    </td>{{--   Chuyển trạng thái                 --}}

                </tr>
            @endforeach
        @endif
    </tbody>
</table>
