


<form action="" accept-charset="UTF-8" class="form-horizontal"><input name="_method" type="hidden">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{__('Thông tin tài khoản')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i aria-hidden="true" class="ki ki-close"></i>
        </button>
    </div>
    <div class="modal-body">
        <p style="text-align: center">
            <img width="100" height="100" src="{{$user->image==""?"/assets/backend/themes/images/default-avatar.png":$user->image}}" alt="">
        </p>
        <div style="text-align: center">
            <h3 class="bold">{{$user->username}}</h3>
        </div>
        @if(Auth::user()->can(['view-profile']))
            @if($user->email!="")
                <div style="text-align: center">
                    <h3 class="bold">{{$user->email}}</h3>
                </div>
            @endif

            @if($user->phone!="")
                <div style="text-align: center">
                    <h3 class="bold">{{$user->phone}}</h3>
                </div>
            @endif
        @endif


        <div style="text-align: center">
            <h3 class="bold">

                @foreach($user->roles??[] as $aRole)
                    {{$aRole->title}} @if(!$loop->last) -  @endif
                @endforeach
            </h3>
        </div>
        <div style="text-align: center">
            @if(Auth::user()->can(['view-profile']))
                <h2 class="c-font-22 c-font-red " style="color: red">{{currency_format($user->balance)}} VNĐ</h2>
            @endif
        </div>


    </div>
    <div class="modal-footer">
        <style>.btn-dk a,.btn-dk button{margin-bottom: 15px;}</style>
        <div class="btn-dk">
            @if(Auth::user()->can(['txns-report-list']) || Auth::user()->hasRole('admin'))
                <a href="/admin/txns-report?username={{$user->username}}&shop_id={{$user->shop_id}}" class="btn btn-info m-btn m-btn--custom" target="_blank">Xem giao dịch</a>
            @endif
            @if(Auth::user()->can(['access-user']) || Auth::user()->hasRole('admin'))
                @if ($user->account_type == 2)
                    <a href="#" class="btn btn-info m-btn m-btn--custom btn-access-user" rel={{$user->id}} data-id="{{$user->id}}">Truy cập</a>
                @else
                    <a href="#" class="btn btn-info m-btn m-btn--custom">Truy cập</a>
                @endif
            @endif
            @if(Auth::user()->can(['plus-minus-money']) || Auth::user()->hasRole('admin'))
                <a href="/admin/money?mode=1&field=username&value={{$user->username}}&id={{$user->id}}" class="btn btn-success m-btn m-btn--custom">Cộng tiền</a>
                <a href="/admin/money?mode=0&field=username&value={{$user->username}}&id={{$user->id}}" class="btn btn-danger m-btn m-btn--custom">Trừ tiền</a>
            @endif

        </div>
        <div class="btn-dk">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
        </div>
    </div>
</form>
