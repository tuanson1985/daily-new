<table>

    <thead>
    <tr>

        <th>ID</th>
        <th>Mã đơn hàng</th>
        <th>Tài khoản order</th>
        <th>Số tiền</th>
        <th>Ver</th>
        <th>Server</th>
        <th>Tên nhân vật</th>
        <th>Trước G.D</th>
        <th>Số vàng</th>
        <th>Sau G.D</th>
        <th>Thời gian tạo</th>
        <th>Thời gian hoàn thành</th>
        <th>Trạng thái</th>



    </tr>
    </thead>
    <tbody>
        @php
            $datatable = $data['data'];
        @endphp

        @foreach($datatable as $index=> $item)

            <tr>

                <td>{{$item->id}}</td>
                <td>{{isset($item->item->id)?"#".$item->item->id:""}}</td>
                <td><a href="#" class="load-modal" rel="/admin/view-profile?username={{isset($item->item->author)?$item->item->author:""}}" style="color: #575962 !important;">{{isset($item->item->author)?$item->item->author:""}}</a></td>
                <td>{{isset($item->item->price)?$item->item->price:""}}</td>
                <td>{{$item->ver}}</td>
                <td>{{$item->server}}</td>
                <td>{{$item->uname}}</td>
                <td>{{$item->c_truoc}}</td>
                <td>
                    @if($item->status=="danap")
                        <span class="c-font-bold text-success">+{{$item->coin}}</span>
                    @else
                        <span class="c-font-bold text-danger">-{{$item->coin}}</span>
                    @endif

                </td>
                <td>{{$item->c_sau}}</td>
                <td>{{\App\Library\Helpers::FormatDateTime('H:i:s d/m/Y',$item->created_at)}}</td>
                <td>{{\App\Library\Helpers::FormatDateTime('H:i:s d/m/Y',$item->updated_at)}}</td>
                @if($item->status=="danhan")
                    <td>Đã nhận</td>
                @elseif($item->status=="danap")
                    <td>Đã nạp</td>

                @elseif($item->status=="chuanhan")
                    <td>Chưa nhận</td>
                @elseif($item->status=="dahuybo")
                    <td>Đã hủy bỏ</td>
                @endif

            </tr>
        @endforeach
    </tbody>
</table>




