@php
    $minigame_logs = $data['data'];

@endphp

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Shop</th>
            <th>Tên user</th>
            <th>Tên danh mục</th>
            <th>Tên phần thưởng</th>
            <th>ID phần thưởng</th>
            <th>Loại vật phẩm</th>
            <th>Giá tiền/lượt</th>
            <th>Số lượt</th>
            <th>Tổng tiền</th>
            <th>Giá trị giải thưởng</th>
            <th>Bonus</th>
            <th>Thực nhận</th>
            <th>Thời gian</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($minigame_logs) && count($minigame_logs) > 0)
            @foreach($minigame_logs as $item)
                <tr>
                    <td>{{ $item->id??null }}</td>{{--   id                 --}}
                    <td>
                        @if($item->shop)
                            {{ $item->shop->domain??"" }}
                        @else
                        @endif
                    </td>{{--   id                 --}}
                    <td>{{ $item->author->username??null }}</td>{{--   tên user                 --}}
                    <td>
                        {{ $item->group->title??'' }}
                    </td>{{--   tên danh mục                 --}}
                    <td>
                        {{ $item->item_ref->title??'' }}{{--   Tên phần thưởng                 --}}
                    </td>
                    <td>
                        {{ $item->ref_id }}{{--   ID phần thưởng                 --}}
                    </td>
                    <td>
                        @if(isset($item->group) && isset($item->group->params))
                        {{config('module.minigame.game_type.'.$item->group->params->game_type)}}
                        @endif
                    </td>{{--   Loại vật phẩm                --}}
                    <td>
                        {{ $item->price??'' }}
                    </td>{{--   Giá tiền/lượt                 --}}
                    <td>1 Lượt quay</td>{{--   Số lượt                 --}}
                    <td>{{ $item->price }}</td>{{--   Tổng tiền                 --}}
                    <td>{{ $item->real_received_price }}</td>{{--   Giá trị giải thưởng               --}}
                    <td>
                        @php
                            $bonus = 0;
                            if ($item->value_gif_bonus){
                                $bonus = $item->value_gif_bonus;
                            }
                        @endphp
                        {{ $bonus }}
                    </td>{{--   Giá trị bonus               --}}
                    @php
                        $total = (int)$item->real_received_price + (int)$bonus;
                    @endphp
                    <td>{{ $total }}</td>{{--   Thực nhận              --}}
                    <td>{{ isset($item->created_at)?\Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i:s'):null }}</td>{{--   Thời gian                 --}}

                </tr>
            @endforeach
        @endif
    </tbody>
</table>
