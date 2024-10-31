@if (isset($data) && count($data) > 0)
<ul class="navi navi-hover py-4">
    @foreach ($data as $item)
        <li class="navi-item">
            <a href="{{route('admin.language-nation.switch',$item->locale)}}" class="navi-link">
                <span class="symbol symbol-20 mr-3">
                    <img src="{{ \App\Library\MediaHelpers::media($item->image) }}" alt=""/>
                </span>
                <span class="navi-text">{{$item->title}}</span>
            </a>
        </li>
    @endforeach
</ul>
@endif
