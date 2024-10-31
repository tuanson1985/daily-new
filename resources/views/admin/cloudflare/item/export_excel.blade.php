@php
    $cloudflares = $data['data'];

@endphp

<table>
    <thead>
    <tr>
        <th>Domain</th>
{{--        <th>IP</th>--}}
        <th>Trạng thái</th>
        <th>Loại</th>
        <th>Đăng kí</th>
    </tr>
    </thead>
    <tbody>
    @if (isset($cloudflares) && count($cloudflares) > 0)
        @foreach($cloudflares as $item)
            <tr>
                <td>{{$item->name}}</td>{{--   id                 --}}
{{--                <td>--}}
{{--                    @if(isset($item->permissions) && count($item->permissions))--}}
{{--                        @php--}}
{{--                            $permissions = $item->permissions;--}}
{{--                        @endphp--}}
{{--                        @foreach($permissions as $permission)--}}
{{--                            {{ $permission->name }}--}}
{{--                            : {{ $permission->content }}--}}
{{--                            - proxied: {{ isset($permission->proxied) ? 'true' : 'false' }} <br>--}}
{{--                        @endforeach--}}
{{--                    @endif--}}
{{--                </td>--}}{{--   shop                 --}}
                <td>
                    {{$item->status}}
                </td>{{--   Loại rút vật phẩm                 --}}
                <td>{{$item->type}}</td>{{--   tên user                 --}}
                <td>
                    {{$item->original_registrar}}
                </td>{{--   Id game                 --}}
            </tr>
        @endforeach
    @endif
    </tbody>
</table>

