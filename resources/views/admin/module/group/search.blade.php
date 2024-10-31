
@forelse ($datatable as $item)
    <div class="rs-item ">
        <a href="#" class="d-flex d-block align-items-center btnAppend" data-id="{{$item->id}}">
            <div class="thumb" style="width: 60px;height: 60px;text-align: center"> <img src="{{$item->image}}" alt="" style="max-width:60px;max-height: 60px"></div>
            <div class="info">
                <p>{{$item->title}}</p>
                <p style="color:#000000">


                </p>
                <p style="color:#000000">
                    <span><b>ID:</b> {{$item->id}}</span> - <span><b>Danh mục:</b>
                    @foreach($item->groups??[] as  $category)
                            {{$category->title??""}}
                            @if(!$loop->last)
                                -
                            @endif
                    @endforeach
                    </span>
                </p>
                <p style="color:#000000">
                    <span><b>Giá tiền:</b> {{currency_format($item->price)}}</span>
                </p>

            </div>

        </a>
    </div>
@empty
    <div class="rs-item ">
        Không có kết quả
    </div>
@endforelse





