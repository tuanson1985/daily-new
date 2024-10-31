<div class="form-group row">
    <div class="col-12 col-md-12">
        <label for="mode" class="form-control-label text-success">{{ __('Thông tim tài khoản người nhận') }} <span style="color: red">(*)</span></label>

    </div>

    <div class="col-12 col-md-12" style="font-size: 14px"><b>{{__('ID')}}:</b> {{$user->id}}</div>
    <div class="col-12 col-md-12" style="font-size: 14px"><b>{{__('Tài khoản')}}:</b> {{$user->username}}</div>
    <div class="col-12 col-md-12" style="font-size: 14px" ><b>{{__('Số vật phẩm')}}:</b></div>
    @if (isset($typeVp) && count($typeVp) > 0)
        @foreach ($typeVp as $item)
            @php
                $type_ruby = 'ruby_num'.$item->parent_id;
            @endphp

            @if($item->parent_id == 11 || $item->parent_id == 12 || $item->parent_id == 13 || $item->parent_id == 14)
            @else
            <div class="col-12 col-md-12" style="font-size: 14px" >- {{__($item->image)}}: <span class="text-success">{{currency_format($user->$type_ruby)}}</span></div>
            @endif
        @endforeach
        <div class="col-12 col-md-12" style="font-size: 14px" >- {{__('Số ngọc')}}: <span class="text-success">{{currency_format($user->gem_num)}}</span></div>
        <div class="col-12 col-md-12" style="font-size: 14px" >- {{__('Số coin')}}: <span class="text-success">{{currency_format($user->coin_num)}}</span></div>
        <div class="col-12 col-md-12" style="font-size: 14px" >- {{__('Số xu')}}: <span class="text-success">{{currency_format($user->xu_num)}}</span></div>
            <div class="col-12 col-md-12" style="font-size: 14px" >- {{__('Số Roblox')}}: <span class="text-success">{{currency_format($user->robux_num)}}</span></div>
    @endif
</div>
