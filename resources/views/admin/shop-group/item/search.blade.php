
@forelse ($datatable as $item)
<div class="rs-item item-shop-{{$item->id}}">
    <a href="#" class="d-flex d-block align-items-center btnAppend" data-id="{{$item->id}}">
        <div class="thumb" style="width: 60px;height: 60px;text-align: center"> <img src="{{$item->image}}" alt="" style="max-width:60px;max-height: 60px"></div>
        <div class="info">
            <p>{{$item->title}}</p>
            <p style="color:#000000"></p>
            <p style="color:#000000">
                <span><b>ID:</b> {{$item->id}}</span>
            </p>
            <p style="color:#000000">
                <span>
                    <b>Trạng thái:</b>
                    @if ($item->status == 1)
                        <span class="label label-pill label-inline label-center mr-2  label-success">{{config('module.shop.status.1')}}</span>
                    @else
                        <span class="label label-pill label-inline label-center mr-2 label-danger">{{config('module.shop.status.0')}}</span>
                    @endif
                    
                </span>
            </p>
            @if (isset($item->group))
                <p style="color:#000000">
                    <span>
                        <b>Nhóm:</b>
                        <span class="label label-pill label-inline label-center mr-2  label-info">{{$item->group->title}}</span>
                    </span>
                </p>
            @endif

        </div>

    </a>
</div>
<hr>
@empty
<div class="rs-item ">
    Không có kết quả
</div>
@endforelse





