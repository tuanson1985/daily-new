@if (auth('frontend')->user()->account_type == 1)
    <div class="dropdown" style="margin-left: 15px">
        <div class="topbar-item">
            @if (\Session::has('shop_name'))
                <a target="_blank" href="https://{{\Session::get('shop_name')}}" class="btn btn-light btn-text-success btn-hover-text-success font-weight-bold">{{__('Truy cập')}}</a>
            @else
                <span class="btn btn-light btn-text-success btn-hover-text-success font-weight-bold">{{__('Truy cập')}}</span>
            @endif
        </div>
    </div>
@endif
