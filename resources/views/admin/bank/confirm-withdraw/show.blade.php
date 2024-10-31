<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">
        Thông tin lệnh rút #{{$datatable->id}}
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <i aria-hidden="true" class="ki ki-close"></i>
    </button>
</div>
<div class="modal-body">


    {{--//ref_id table--}}
    <table class="table table-striped">
        <tbody>
        <tr>
            <th colspan="2"><b>Thông tin lệnh rút #{{$datatable->id}}</b></th>
        </tr>

        <tr>
            <td width="40%">Người rút:</td>

            <td>
                <a href="#" class="load-modal" rel="/admin/view-profile?username={{$datatable->user->username??""}}">{{$datatable->user->username??""}}</a>
            </td>
        </tr>
        <!--                <tr>
                        <td>Nhà cung cấp:</td>
                        <td>TichHop</td>
                    </tr>
                    <tr>
                        <td>Cổng:</td>
                        <td>15</td>
                    </tr>-->
        <tr>
            <td>Thời gian gửi yêu cầu:</td>
            <td>{{\App\Library\Helpers::FormatDateTime('d/m/Y H:i:s',$datatable->created_at)}}</td>
        </tr>
        @if($datatable->bank_type==0)
            <tr>
                <td>Ngân hàng:</td>
                <td>{{$datatable->bank_title}}</td>
            </tr>
            <tr>
                <td class="">Tên chủ tài khoản:</td>
                <td class="text-primary">{{$datatable->holder_name}}</td>
            </tr>
            <tr>
                <td>Số tài khoản:</td>
                <td>{{$datatable->account_number}}</td>
            </tr>
        @else
            <tr>
                <td>Ví điện tử:</td>
                <td>{{$datatable->bank_title}}</td>
            </tr>

            <tr>
                <td>Tài khoản ví:</td>
                <td>{{$datatable->account_vi}}</td>
            </tr>
        @endif

        <tr>
            <td class="text-danger">Số tiền rút:</td>
            <td class="text-danger">{{number_format($datatable->amount)}}</td>
        </tr>

        <tr>
            <td>Nội dung:</td>
            <td>{{$datatable->description}}</td>
        </tr>
        <tr>
            <td>Trạng thái :</td>
            <td>{{config('module.withdraw.status.'.$datatable->status)}}</td>
        </tr>
        <tr>
            <td>Người xử lý :</td>
            <td>{{$datatable->processor->username??""}}</td>
        </tr>


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
                var curModal = $('#LoadModal');
                curModal.find('.modal-content').html("<div class=\"loader\" style=\"text-align: center\"><img src=\"/assets/frontend/images/loader.gif\" style=\"width: 50px;height: 50px;\"></div>");
                curModal.modal('show').find('.modal-content').load($(elem).attr('rel'));
            });
        });
    });
</script>
