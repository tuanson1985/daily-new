

<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"> Giao dịch #{{$datatable->id}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <i aria-hidden="true" class="ki ki-close"></i>
    </button>
</div>

<div class="modal-body">

    <table class="table table-striped">
        <tbody>
        <tr>
            <th colspan="2"><b>Thông tin giao dịch #{{$datatable->id}}</b></th>
        </tr>
        <tr>
            <td width="40%">Thời gian:</td>
            <td>{{\App\Library\Helpers::FormatDateTime('d/m/Y H:i:s',$datatable->created_at)}}</td>
        </tr>
        <tr>
            <td>Tài khoản:</td>
            <td>
                <a href="#" class="load-modal" rel="/admin/view-profile?username={{$datatable->user->username??""}}&shop_id={{$datatable->user->shop_id}}">{{$datatable->user->username}}</a>
            </td>
        </tr>
        <tr>
            <td>Địa chỉ IP:</td>
            <td>{{$datatable->ip}}</td>
        </tr>
        <tr>
            <td>Loại giao dịch:</td>
            <td>{{config('module.txns.trade_type.'.$datatable->trade_type)}}</td>
        </tr>
        <tr>
            <td>Trạng thái giao dịch:</td>
            <td>{{config('module.txns.status.'.$datatable->status)}}</td>
        </tr>
        <tr>
            <td>Số tiền:</td>
            <td>@if($datatable->is_add==1)
                    <span class="c-font-bold text-info">+{{number_format($datatable->amount)}}đ</span>
                @elseif($datatable->is_add==0)
                    <span class="c-font-bold text-danger">-{{number_format($datatable->amount)}}đ</span>
                @endif

            </td>

        </tr>
        <tr>
            <td>Số dư cuối:</td>
            <td>{{number_format($datatable->last_balance)}}đ</td>

        </tr>
        <tr>
            <td>Ghi chú:</td>
            <td>{{$datatable->description}}</td>

        </tr>
        @if($datatable->processor_username!="")
            <tr>
                <td>Người thực hiện:</td>
                <td>
                    <a href="#" class="load-modal" rel="/admin-1102/view-profile?username={{$datatable->processor_username}}">{{$datatable->processor_username}}</a>
                </td>
            </tr>
        @endif
        </tbody>
    </table>


    @if($datatable->trade_type=="plus_money")
    @elseif($datatable->trade_type=="minus_money")
    @elseif($datatable->trade_type=="charge")
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <th colspan="2"><b>Thông tin nạp thẻ #{{$datatable->txnsable->id??""}}</b></th>
                    </tr>

                    <tr>
                        <td width="40%">Tài khoản:</td>

                        <td>
                            <a href="#" class="load-modal" rel="/admin/view-profile?username={{$datatable->user->username??""}}&shop_id={{$datatable->user->shop_id}}">{{$datatable->user->username}}</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Cổng gạch thẻ:</td>
                        <td> {{config('module.charge.gate_id.'.$datatable->txnsable->gate_id)}}</td>
                    </tr>
                    <tr>
                        <td>Loại thẻ:</td>
                        <td>{{$datatable->txnsable->telecom_key??""}}</td>
                    </tr>
                    <tr>
                        <td>Mã thẻ:</td>
                        <td>
                            {{\App\Library\Helpers::Decrypt($datatable->txnsable->pin??"", config('module.charge.key_encrypt')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Serial:</td>
                        <td>{{$datatable->txnsable->serial??""}}</td>
                    </tr>
                    <tr>
                        <td>Nạp lúc:</td>
                        <td>{{\App\Library\Helpers::FormatDateTime('d/m/Y H:i:s',$datatable->txnsable->created_at??"")}}</td>
                    </tr>

                    <tr>
                        <td>Mệnh giá:</td>
                        <td>{{number_format($datatable->txnsable->amount??"")}} đ</td>
                    </tr>

                    <tr>
                        <td>Địa chỉ IP:</td>
                        <td>{{$datatable->ip}}</td>
                    </tr>
                    <tr>
                        <td>Chiết khấu:</td>
                        <td>{{number_format($datatable->txnsable->ratio??"",2)}}%</td>
                    </tr>
                    <tr>
                        <td>Trạng thái:</td>
                        <td>{{config('module.charge.status.'.$datatable->txnsable->status??"")}}</td>
                    </tr>
                    </tbody>
                </table>
    @endif

    {{--//ref_id table--}}
{{--    @if($datatable->trade_type==1)--}}
{{--        <table class="table table-striped">--}}
{{--            <tbody>--}}
{{--            <tr>--}}
{{--                <th colspan="2"><b>Thông tin nạp thẻ #{{$datatable->charge->id}}</b></th>--}}
{{--            </tr>--}}

{{--            <tr>--}}
{{--                <td width="40%">Tài khoản:</td>--}}

{{--                <td>--}}
{{--                    <a href="#" class="load-modal" rel="/admin/view-profile?username={{$datatable->charge->username}}">{{$datatable->charge->username}}</a>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Cổng gạch thẻ:</td>--}}
{{--                <td> {{config('constants.module.telecom.gate_id.'.$datatable->charge->gate_id)}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Loại thẻ:</td>--}}
{{--                <td>{{$datatable->charge->telecom_key}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Mã thẻ:</td>--}}
{{--                <td>{{$datatable->charge->pin}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Serial:</td>--}}
{{--                <td>{{$datatable->charge->serial}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Nạp lúc:</td>--}}
{{--                <td>{{\App\Library\Helpers::FormatDateTime('d/m/Y H:i:s',$datatable->charge->created_at)}}</td>--}}
{{--            </tr>--}}

{{--            <tr>--}}
{{--                <td>Mệnh giá:</td>--}}
{{--                <td>{{number_format($datatable->charge->amount)}} đ</td>--}}
{{--            </tr>--}}

{{--            <tr>--}}
{{--                <td>Địa chỉ IP:</td>--}}
{{--                <td>{{$datatable->charge->ip}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Chiết khấu:</td>--}}
{{--                <td>{{number_format($datatable->charge->ratio,2)}}%</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Trạng thái:</td>--}}
{{--                <td>{{config('constants.module.charge.status-auto.'.$datatable->charge->status)}}</td>--}}
{{--            </tr>--}}
{{--            </tbody>--}}
{{--        </table>--}}
{{--    @elseif($datatable->trade_type==2)--}}
{{--        <table class="table table-striped">--}}
{{--            <tbody>--}}
{{--            <tr>--}}
{{--                <th colspan="2"><b>Thông tin nạp thẻ #{{$datatable->charge->id}}</b></th>--}}
{{--            </tr>--}}

{{--            <tr>--}}
{{--                <td width="40%">Tài khoản:</td>--}}

{{--                <td>--}}
{{--                    <a href="#" class="load-modal" rel="/admin/view-profile?username={{$datatable->charge->username}}">{{$datatable->charge->username}}</a>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <!--                <tr>--}}
{{--                            <td>Nhà cung cấp:</td>--}}
{{--                            <td>TichHop</td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <td>Cổng:</td>--}}
{{--                            <td>15</td>--}}
{{--                        </tr>-->--}}
{{--            <tr>--}}
{{--                <td>Loại thẻ:</td>--}}
{{--                <td>{{$datatable->charge->telecom_key}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Mã thẻ:</td>--}}
{{--                <td>{{$datatable->charge->pin}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Serial:</td>--}}
{{--                <td>{{$datatable->charge->serial}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Nạp lúc:</td>--}}
{{--                <td>{{\App\Library\Helpers::FormatDateTime('d/m/Y H:i:s',$datatable->charge->created_at)}}</td>--}}
{{--            </tr>--}}
{{--            --}}{{--<tr>--}}
{{--            --}}{{--<td>Phản hồi:</td>--}}
{{--            --}}{{--<td>{{\Carbon\Carbon::c}} giây</td>--}}
{{--            --}}{{--</tr>--}}
{{--            <tr>--}}
{{--                <td>Mệnh giá:</td>--}}
{{--                <td>{{number_format($datatable->charge->amount)}} đ</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Nội dung:</td>--}}
{{--                <td>--}}
{{--                    @if($datatable->charge->status>=10000)--}}

{{--                        @if($datatable->charge->status==$datatable->charge->amount)--}}
{{--                            Đúng mệnh giá {{$datatable->charge->telecom_key}} {{number_format($datatable->charge->status)}}--}}

{{--                        @else--}}
{{--                            Sai mệnh giá {{$datatable->charge->telecom_key}} {{number_format($datatable->charge->status)}}--}}

{{--                        @endif--}}

{{--                    @endif--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Địa chỉ IP:</td>--}}
{{--                <td>{{$datatable->charge->ip}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Chiết khấu:</td>--}}
{{--                <td>{{number_format($datatable->charge->ratio,2)}}%</td>--}}
{{--            </tr>--}}
{{--            </tbody>--}}
{{--        </table>--}}
{{--    @elseif($datatable->trade_type==3)--}}
{{--        <table class="table table-striped">--}}
{{--            <tbody>--}}
{{--            <tr>--}}

{{--                <th colspan="2"><b>Thông tin giao dịch nhận #{{$datatable->txns->id}}</b></th>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td width="40%">Thời gian:</td>--}}
{{--                <td>{{\App\Library\Helpers::FormatDateTime('d/m/Y H:i:s',$datatable->txns->created_at)}}</td>--}}
{{--            </tr>--}}

{{--            <tr>--}}
{{--                <td>Tài khoản:</td>--}}
{{--                <td>--}}
{{--                    <a href="#" class="load-modal" rel="/admin/view-profile?username={{$datatable->txns->username}}">{{$datatable->txns->username}}</a>--}}

{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Loại giao dịch:</td>--}}
{{--                <td>{{config('constants.module.txns.trade_type.'.$datatable->txns->trade_type)}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Trạng thái giao dịch:</td>--}}
{{--                <td>{{config('constants.module.txns.status.'.$datatable->txns->status)}}</td>--}}

{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Số tiền:</td>--}}
{{--                <td>@if($datatable->txns->is_add==1)--}}
{{--                        <span class="c-font-bold text-info">+{{number_format($datatable->txns->real_received_amount)}}đ</span>--}}
{{--                    @elseif($datatable->txns->is_add==0)--}}
{{--                        <span class="c-font-bold text-danger">-{{number_format($datatable->txns->real_received_amount)}}đ</span>--}}
{{--                    @endif--}}

{{--                </td>--}}

{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Số dư cuối:</td>--}}
{{--                <td>{{number_format($datatable->txns->last_balance)}}đ</td>--}}

{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Ghi chú:</td>--}}
{{--                <td>{{$datatable->txns->description}}</td>--}}

{{--            </tr>--}}


{{--            </tbody>--}}
{{--        </table>--}}
{{--    @elseif($datatable->trade_type==4)--}}
{{--        <table class="table table-striped">--}}
{{--            <tbody>--}}
{{--            <tr>--}}

{{--                <th colspan="2"><b>Thông tin giao dịch chuyển #{{$datatable->txns->id}}</b></th>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td width="40%">Thời gian:</td>--}}
{{--                <td>{{\App\Library\Helpers::FormatDateTime('d/m/Y H:i:s',$datatable->txns->created_at)}}</td>--}}
{{--            </tr>--}}

{{--            <tr>--}}
{{--                <td>Tài khoản:</td>--}}
{{--                <td>--}}
{{--                    <a href="#" class="load-modal" rel="/admin/view-profile?username={{$datatable->txns->username}}">{{$datatable->txns->username}}</a>--}}

{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Loại giao dịch:</td>--}}
{{--                <td>{{config('constants.module.txns.trade_type.'.$datatable->txns->trade_type)}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Trạng thái giao dịch:</td>--}}
{{--                <td>{{config('constants.module.txns.status.'.$datatable->txns->status)}}</td>--}}

{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Số tiền:</td>--}}
{{--                <td>@if($datatable->txns->is_add==1)--}}
{{--                        <span class="c-font-bold text-info">+{{number_format($datatable->txns->real_received_amount)}}đ</span>--}}
{{--                    @elseif($datatable->txns->is_add==0)--}}
{{--                        <span class="c-font-bold text-danger">-{{number_format($datatable->txns->real_received_amount)}}đ</span>--}}
{{--                    @endif--}}

{{--                </td>--}}

{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Số dư cuối:</td>--}}
{{--                <td>{{number_format($datatable->txns->last_balance)}}đ</td>--}}

{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Ghi chú:</td>--}}
{{--                <td>{{$datatable->txns->description}}</td>--}}

{{--            </tr>--}}


{{--            </tbody>--}}
{{--        </table>--}}
{{--    @elseif($datatable->trade_type==5)--}}
{{--        <table class="table table-striped">--}}
{{--            <tbody>--}}
{{--            <tr>--}}

{{--                <th colspan="2"><b>Thông tin lệnh rút tiền #{{$datatable->withdraw->id}}</b></th>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td width="40%">Ngân hàng/Ví:</td>--}}
{{--                <td>{{$datatable->withdraw->bank_title}}</td>--}}
{{--            </tr>--}}

{{--            <tr>--}}
{{--                <td>Số tài khoản/Tài khoản ví:</td>--}}
{{--                <td>{{$datatable->withdraw->account_number}}{{$datatable->withdraw->account_vi}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Số tiền:</td>--}}
{{--                <td>{{number_format($datatable->withdraw->amount)}}đ</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Phí:</td>--}}
{{--                <td>{{number_format($datatable->withdraw->fee)}}đ</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Tổng tiền:</td>--}}
{{--                <td class="text-danger">{{number_format($datatable->withdraw->amount_passed)}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Trạng thái:</td>--}}
{{--                <td>{{config('constants.module.withdraw.status.'.$datatable->status)}}</td>--}}

{{--            </tr>--}}


{{--            </tbody>--}}
{{--        </table>--}}
{{--    @elseif($datatable->trade_type==6)--}}
{{--    @elseif($datatable->trade_type==7)--}}
{{--    @elseif($datatable->trade_type==8)--}}
{{--    @elseif($datatable->trade_type==9)--}}
{{--        <table class="table table-striped">--}}
{{--            <tbody>--}}
{{--            <tr>--}}

{{--                <th colspan="2"><b>Thông tin tài khoản #{{$datatable->item->id}}</b></th>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td width="40%">Game:</td>--}}
{{--                <td>{{$datatable->item->groups[0]->title}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Tài khoản:</td>--}}
{{--                <td>{{$datatable->item->acc_name}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Quản trị viên:</td>--}}
{{--                <td>{{$datatable->item->author}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Giá bán:</td>--}}
{{--                <td>{{number_format($datatable->item->price)}}đ</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Người mua:</td>--}}
{{--                <td>--}}
{{--                    <a href="#" class="load-modal" rel="/admin/view-profile?username={{$datatable->item->buyer}}">{{$datatable->item->buyer}}</a>--}}
{{--                </td>--}}
{{--            </tr>--}}


{{--            </tbody>--}}
{{--        </table>--}}
{{--    @elseif($datatable->trade_type==10)--}}
{{--        <table class="table table-striped">--}}
{{--            <tbody>--}}
{{--            <tr>--}}

{{--                <th colspan="2"><b>Thông tin trả góp #{{$datatable->hire_purchase->id}}</b></th>--}}
{{--            </tr>--}}

{{--            <tr>--}}
{{--                <td width="40%">Mã số Acc:</td>--}}
{{--                <td><a href="/acc/{{$datatable->hire_purchase->item_id}}" target="_blank">#{{$datatable->hire_purchase->item_id}}</a></td>--}}
{{--            </tr>--}}

{{--            <tr>--}}
{{--                <td>Tổng giá trị:</td>--}}
{{--                <td>{{number_format($datatable->hire_purchase->total)}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Trả trước:</td>--}}
{{--                <td>{{number_format($datatable->hire_purchase->pay_first)}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Chiết khấu trả trước:</td>--}}
{{--                <td>{{number_format($datatable->hire_purchase->ratio_pay_first,2)}}%</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Ngày hết hạn trả góp:</td>--}}
{{--                <td>{{\App\Library\Helpers::FormatDateTime('d/m/Y H:i:s',$datatable->hire_purchase->expired_at)}}</td>--}}
{{--            </tr>--}}
{{--            @if($datatable->hire_purchase->status==3)--}}
{{--                <tr>--}}
{{--                    <td>Chiết khấu hủy bỏ:</td>--}}
{{--                    <td>{{number_format($datatable->hire_purchase->ratio_refund,2)}}%</td>--}}
{{--                </tr>--}}
{{--            @endif--}}
{{--            @if($datatable->hire_purchase->status==2)--}}
{{--                <tr>--}}
{{--                    <td>Số tiền tất toán:</td>--}}
{{--                    <td>{{number_format($datatable->hire_purchase->pay_complete)}}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td>Tất toán lúc:</td>--}}
{{--                    <td>{{\App\Library\Helpers::FormatDateTime('d/m/Y H:i:s',$datatable->hire_purchase->pay_complete_at)}}</td>--}}
{{--                </tr>--}}
{{--            @endif--}}
{{--            <tr>--}}
{{--                <td>Trạng thái :</td>--}}
{{--                <td>{{config('constants.module.hire_purchase.status.'.$datatable->hire_purchase->status)}}</td>--}}
{{--            </tr>--}}


{{--            </tbody>--}}
{{--        </table>--}}
{{--    @elseif($datatable->trade_type==11)--}}
{{--    @elseif($datatable->trade_type==105)--}}
{{--        <table class="table table-striped">--}}
{{--            <tbody>--}}
{{--            <tr>--}}

{{--                <th colspan="2"><b>Thông tin tài khoản #{{$datatable->item->id}}</b></th>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td width="40%">Game:</td>--}}
{{--                <td>{{$datatable->item->groups[0]->title}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Tài khoản:</td>--}}
{{--                <td>{{$datatable->item->acc_name}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Quản trị viên:</td>--}}
{{--                <td>{{$datatable->item->author}}</td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td>Giá bán:</td>--}}
{{--                <td>{{number_format($datatable->item->price)}}đ</td>--}}
{{--            </tr>--}}


{{--            </tbody>--}}
{{--        </table>--}}
{{--    @endif--}}


</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>

</div>


<script>
    $(document).ready(function () {
        $('.load-modal').each(function (index, elem) {
            $(elem).unbind().click(function (e) {
                e.preventDefault();
                e.preventDefault();
                var curModal = $('#LoadModal');
                curModal.find('.modal-content').html("<div class=\"loader\" style=\"text-align: center\"><img src=\"/assets/frontend/images/loader.gif\" style=\"width: 50px;height: 50px;\"></div>");
                curModal.modal('show').find('.modal-content').load($(elem).attr('rel'));
            });
        });
    });
</script>
