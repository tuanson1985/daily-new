@php
    $txnsvps = $data['data'];

@endphp

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên shop</th>
            <th>Tên user</th>
            <th>Loại tài khoản</th>
            <th>Giao dịch</th>
            <th>Loại vật phẩm</th>
            <th>Số vật phẩm</th>
            <th>Dư cuối</th>
            <th>Thời gian</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($txnsvps) && count($txnsvps) > 0)
            @foreach($txnsvps as $item)
                <tr>
                    <td>{{ $item->id??null }}</td>{{--   id                 --}}
                    <td>{{ $item->shop->domain??null }}</td>{{--   shop                 --}}
                    <td>{{ $item->user->username }}</td>{{--   tên user                 --}}
                    <td>
                        {{config('module.user.account_type.'.$item->user->account_type)}}{{--   Loại tài khoản                 --}}
                    </td>
                    <td>
                        {{ $item->description }}{{--   Giao dịch                 --}}
                    </td>
                    <td>{{config('module.minigame.game_type.'.$item->item_type)}}</td>{{--   Loại vật phẩm                 --}}
                    <td>
                        @if($item->is_add == 1)
                            + {{ $item->amount }}
{{--                            {{config('module.minigame.game_type.'.$item->item_type)}}--}}
                        @else
                            - {{ $item->amount }}
{{--                            {{config('module.minigame.game_type.'.$item->item_type)}}--}}
                        @endif
                    </td>{{--   Số dư vật phẩm                 --}}
                    <td>{{ $item->last_balance }}</td>{{--   Số dư cuối                 --}}
                    <td>{{ isset($item->created_at)?\Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i:s'):null }}</td>{{--   Thời gian                 --}}

                    <td>{{config('module.txns.status.'.$item->status)}}</td>{{--   Trạng thái                --}}
                    <td>
                        Thao tác
                    </td>{{--   Thao tác                --}}

                </tr>
            @endforeach
        @endif
    </tbody>
</table>
