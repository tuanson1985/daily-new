

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
                <a href="#" class="load-modal" rel="/admin-1102/view-profile?username={{$datatable->user->username??""}}&shop_id={{$datatable->user->shop_id}}">{{$datatable->user->fullname??$datatable->user->username}}</a>
            </td>
        </tr>
        <tr>
            <td>Địa chỉ IP:</td>
            <td>{{$datatable->ip}}</td>
        </tr>
        <tr>
            <td>Loại giao dịch:</td>
            <td>{{config('module.txnsvp.trade_type.'.$datatable->trade_type)}}</td>
        </tr>
        <tr>
            <td>Trạng thái giao dịch:</td>
            <td>{{config('module.txnsvp.status.'.$datatable->status)}}</td>
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
