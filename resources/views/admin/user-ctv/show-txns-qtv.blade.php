

{{-- mode --}}
<div class="form-group row">
    <div class="col-12 col-md-12">
        <label for="mode" class="form-control-label text-success">{{ __('Thông tim tài khoản người nhận') }} <span style="color: red">(*)</span></label>

    </div>
    {{-- <div class="col-12 col-md-12" style="font-size: 14px"><b>{{__('Shop')}}:</b> {{$user->shop->domain}}</div> --}}
    <div class="col-12 col-md-12" style="font-size: 14px"><b>{{__('ID')}}:</b> {{$user->id}}</div>
    <div class="col-12 col-md-12" style="font-size: 14px"><b>{{__('Tài khoản')}}:</b> {{$user->username}}</div>
    <div class="col-12 col-md-12" style="font-size: 14px" ><b>{{__('Email')}}:</b> {{$user->email}}</div>
    <div class="col-12 col-md-12" style="font-size: 14px" ><b>{{__('Số dư')}}:</b> <span class="text-success">{{currency_format($user->balance)}}</span></div>

</div>



<div class="dataTables_scroll">
    <div class=""
         style="position: relative; overflow: auto; width: 100%;">
        <table class="table table-striped table-bordered table-hover table-checkable dataTable no-footer dtr-inline" >
            <tr>
                <td>Thời gian</td>
                <td>Mã giao dịch</td>
                <td>Loại giao dịch</td>
                <td>Người thao tác</td>
                <td>Người nhận</td>
                <td>Số tiền</td>
                <td>Nguồn tiền</td>
                <td>Ngân hàng/ví</td>
            </tr>
            @foreach($data as $item)
                <tr>
                    <td>{{\App\Library\Helpers::FormatDateTime('d/m/Y H:i:s',$item->created_at)}}</td>
                    <td>#{{$item->txns->id??"Không khớp dữ liệu"}}</td>

                    <td> @if($item->is_add==1)
                            <span class=" text-primary">{{config('module.plus_money.is_add.'.$item->is_add)}}</span>
                        @elseif($item->is_add==0)
                            <span class=" text-danger">{{config('module.plus_money.is_add.'.$item->is_add)}}</span>
                        @endif
                    </td>




                    <td><a href="#" class="load-modal text-link" rel="{{route('admin.view-profile',['username'=>$item->processor->username??""])}}">{{$item->processor->username??""}}</a></td>
                    <td><a href="#" class="load-modal text-link" rel="{{route('admin.view-profile',['username'=>$item->user->username??""])}}">{{$item->user->username??""}}</a></td>
                    <td>
                        @if($item->is_add==1)
                            <span class="c-font-bold text-info">+{{currency_format($item->amount)}}</span>
                        @elseif($item->is_add==0)
                            <span class="c-font-bold text-danger">-{{currency_format($item->amount)}}</span>
                        @endif


                    </td>
                    <td>

                        @if($item->source_type==1)
                            ATM
                        @elseif($item->source_type==2)
                            Ví điện tử
                        @elseif($item->source_type==3)
                            Khác
                        @elseif($item->source_type==4)
                            MOMO
                        @elseif($item->source_type==5)
                            Tiền PR
                        @elseif($item->source_type==6)
                            Tiền test
                        @elseif($item->source_type==7)
                            Tiền thẻ lỗi
                        @endif

                    </td>
                    <td>{{$item->source_bank}}</td>
                </tr>
            @endforeach


        </table>
    </div>
</div>
