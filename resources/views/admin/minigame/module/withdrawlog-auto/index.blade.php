{{-- Extends layout --}}
@extends('admin._layouts.master')

{{-- Content --}}
@section('content')


    <div class="card card-custom" id="kt_page_sticky_card">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">
                    {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                </h3>
            </div>
            <div class="card-toolbar"></div>

        </div>

        <div class="card-body">
            <!--begin: Search Form-->
            <form class="mb-10" action="{{route('admin.withdrawlog-export-auto.index')}}" method="post">
                {{ csrf_field() }}
                <div class="row">
                    {{--title--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="id"
                                   placeholder="{{__('id/ trand id')}}">
                        </div>
                    </div>

                    {{--title--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="title"
                                   placeholder="{{__('id game/ phone')}}">
                        </div>
                    </div>

                    {{--Loai giao dich--}}
{{--                    <div class="form-group col-12 col-sm-6 col-lg-3">--}}
{{--                        <div class="input-group">--}}
{{--                            <div class="input-group-prepend">--}}
{{--                                <span class="input-group-text"><i--}}
{{--                                        class="la la-calendar-check-o glyphicon-th"></i></span>--}}
{{--                            </div>--}}

{{--                            <select id="module"  name="module"--}}
{{--                                    class="form-control datatable-input datatable-input-select selectpicker select2" data-live-search="true"--}}
{{--                                    title="-- {{__('Tất cả danh mục')}} --">--}}
{{--                                <option value="" selected="selected">-- Tất cả danh mục --</option>--}}
{{--                                <option value="1" >Giao dịch rút vật phẩm</option>--}}
{{--                                <option value="2" >Giao dịch rút tự động</option>--}}
{{--                                <option value="3" >Giao dịch hoàn tiền</option>--}}
{{--                            </select>--}}

{{--                        </div>--}}
{{--                    </div>--}}

                    {{--group_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>

                            {{Form::select('payment_type',([''=>'-- Tất cả loại vật phẩm --']+$listgametype??[]) ,old('payment_type', isset($data) ? $data->payment_type : null),array('id'=>'payment_type','class'=>'form-control datatable-input'))}}

                        </div>
                    </div>

                    {{--started_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="started_at" id="started_at" autocomplete="off"
                                   class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian bắt đầu')}}" data-toggle="datetimepicker">

                        </div>
                    </div>

                    {{--ended_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Đến</span>
                            </div>
                            <input type="text" name="ended_at" id="ended_at" autocomplete="off"
                                   class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian kết thúc')}}" data-toggle="datetimepicker">

                        </div>
                    </div>

                    {{--started_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="started_updated_at" id="started_updated_at" autocomplete="off"
                                   class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian hoàn thành bắt đầu')}}" data-toggle="datetimepicker">

                        </div>
                    </div>

                    {{--ended_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Đến</span>
                            </div>
                            <input type="text" name="ended_updated_at" id="ended_updated_at" autocomplete="off"
                                   class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian hoàn thành kết thúc')}}" data-toggle="datetimepicker">

                        </div>
                    </div>

                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.service-purchase-auto.status'),old('status', request('status')),array('id'=>'status','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <button class="btn btn-primary btn-primary--icon" id="kt_search">
                            <span>
                                <i class="la la-search"></i>
                                <span>Tìm kiếm</span>
                            </span>
                        </button>&#160;&#160;
                        <button class="btn btn-secondary btn-secondary--icon" id="kt_reset">
                            <span>
                                <i class="la la-close"></i>
                                <span>Reset</span>
                            </span>
                        </button>&#160;&#160;
                        @if ( auth()->user()->can('withdrawlog-export'))
                            <button class="btn btn-danger btn-secondary--icon" type="submit">
                                <span>
                                    <i class="flaticon-folder-2"></i>
                                    <span>Xuất Excel</span>
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-6">
                        <div class="btn-group m-btn-group" role="group" aria-label="...">
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date" >Hôm nay</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::yesterday()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::yesterday()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Hôm qua</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng này</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->startOfMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->endOfMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng trước</a>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Số giao dịch: <b id="total_record">0</b>
                    </div>
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Thành công: <b id="total_record_complete">0</b> - Thất bại: <b id="total_record_delete">0</b> - Chờ xử lý: <b id="total_record_wanning">0</b>
                    </div>
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Đang thực hiện: <b id="total_record_pendding2">0</b> - Mất item: <b id="total_record_pendding6">0</b> - Kết nối NCC TB: <b id="total_record_pendding7">0</b>
                    </div>
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Xử lý thủ công: <b id="total_record_pendding9">0</b> - Mất item không hoàn tiền: <b id="total_record_pendding77">0</b> - Mất item có hoàn tiền: <b id="total_record_pendding88">0</b>
                    </div>
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Tổng vật phẩm rút: <b id="total_withdraw_item">0</b>
                    </div>
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Tổng số vật phẩm hoàn thành: <b id="total_withdraw_item_complete">0</b>
                    </div>
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Tổng số số vật phẩm hủy: <b id="total_withdraw_item_delete">0</b>
                    </div>
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Tổng số số vật phẩm đang chờ xử lý: <b id="total_withdraw_item_pendding">0</b>
                    </div>
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Tổng số số vật phẩm còn trên user: <b id="total_item">0</b>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-12">

                        <div class="checkbox-inline">
                            <label for="username" class="checkbox toggle-vis" data-column="5">
                                <input id="username" type="checkbox" name="checkboxes2">
                                <span></span>Username
                            </label>
                            <label for="parameters_1" class="checkbox toggle-vis" data-column="6">
                                <input id="parameters_1" type="checkbox" name="checkboxes3">
                                <span></span>Tham số thứ nhất</label>
                            <label for="parameters_2" class="checkbox toggle-vis" data-column="7">
                                <input id="parameters_2" type="checkbox" name="checkboxes4" >
                                <span></span>Tham số thứ hai
                            </label>
                            <label for="parameters_3" class="checkbox toggle-vis" data-column="8">
                                <input id="parameters_3" type="checkbox" name="checkboxes5">
                                <span></span>Tham số thứ ba</label>
                            <label for="parameters_4" class="checkbox toggle-vis" data-column="9">
                                <input id="parameters_4" type="checkbox" name="checkboxes6">
                                <span></span>Tham số thứ tư</label>
                            <label for="parameters_5" class="checkbox toggle-vis" data-column="10">
                                <input id="parameters_5" type="checkbox" name="checkboxes7">
                                <span></span>Tham số thứ năm</label>
                        </div>

                    </div>
                </div>
            </form>
            <!--begin: Search Form-->

            <!--begin: Datatable-->
            <table class="table table-bordered table-hover table-checkable " id="kt_datatable">
            </table>
            <!--end: Datatable-->
        </div>
    </div>


    {{---------------all modal controll-------}}

    <!-- delete item Modal -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.'.$module.'.destroy',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'DELETE'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn xóa?')}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-custom" data-form="form-delete">{{__('Xóa')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteStatus">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Vui lòng nhập lý do hủy đơn(nội dung sẽ được gửi cho khách hàng):')}}
                    <input style="margin-top: 16px"  type="text" name="w_content" value="" class="w_content form-control">
                </div>
                <div class="modal-footer">
                    <input type="hidden" class="w_id" >
                    <input type="hidden" class="w_user_id">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom deleteStatus">{{__('Xóa')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="urlModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Danh sách link đã chạy.')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover table-checkable dataTable no-footer dtr-inline">
                        <thead>
                        <tr role="row">
                            <th class="sorting_desc" style="width: 13px;">STT</th>
                            <th class="sorting" colspan="1" style="width: 46px;">URL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="odd">
                            <td>1</td>
                            <td class="sorting_1">2</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                </div>
            </div>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')

    <script>
        $('body').delegate('.d-change-status', 'click', function(){
            var id = $(this).attr("data-id");
            $('.w_id').val(id);
            var user_id = $(this).attr("data-userid");
            $('.w_user_id').val(user_id);
            $('#deleteStatus').modal('show');
        });
        $('body').delegate('.deleteStatus', 'click', function(){
            var status = 2;
            var id = $('.w_id').val();
            var w_content = $('.w_content').val();
            var w_user_id = $('.w_user_id').val();
            $.ajax({
                url: './withdraw-item/changestatus',
                datatype:'json',
                data:{
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                    status: status,
                    w_content: w_content,
                    userid:w_user_id,
                },
                type: 'post',
                success: function (data) {
                    if(data.status=='ERROR'){
                        alert(data.msg);
                        return;
                    }else{
                        $('#deleteStatus').modal('hide');
                        $('.success'+data.msg).remove();
                        $('.danger'+data.msg).removeClass('fa-times');
                        if(data.rubystatus == '1'){
                            $('.danger'+data.msg).addClass('fa-check-square');
                            $('.not-send-text'+data.msg).text('Hoàn thành');
                            $('.not-send-text'+data.msg).addClass('label-success');
                            $('.not-send-text'+data.msg).removeClass('label-warning');
                        }else{
                            $('.danger'+data.msg).addClass('fa-exclamation-triangle');
                            $('.not-send-text'+data.msg).text('Đã hủy');
                            $('.not-send-text'+data.msg).addClass('label-danger');
                            $('.not-send-text'+data.msg).removeClass('label-warning');
                        }
                    }
                },
                error: function(){
                    alert("error");
                }
            })
        });
        $('body').delegate('.change-status', 'click', function(){
            var status = 1;
            var result = false;
            if($(this).attr('data-action') == 'cancel'){
                result = confirm("Bạn có chắc chắn hủy giao dịch?");
                status = 2;
            }else{
                result = confirm("Bạn có chắc chắn hoàn thành giao dịch?");
                status = 1;
            }
            if (!result) {
                return;
            }
            $.ajax({
                url: './withdraw-item/changestatus',
                datatype:'json',
                data:{
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: $(this).attr("data-id"),
                    status: status,
                    userid: $(this).attr("data-userid")
                },
                type: 'post',
                success: function (data) {
                    if(data.status=='ERROR'){
                        alert(data.msg);
                        return;
                    }else{
                        $('.success'+data.msg).remove();
                        $('.danger'+data.msg).removeClass('fa-times');
                        if(data.rubystatus == '1'){
                            $('.danger'+data.msg).addClass('fa-check-square');
                            $('.not-send-text'+data.msg).text('Hoàn thành');
                            $('.not-send-text'+data.msg).addClass('label-success');
                            $('.not-send-text'+data.msg).removeClass('label-warning');
                        }else{
                            $('.danger'+data.msg).addClass('fa-exclamation-triangle');
                            $('.not-send-text'+data.msg).text('Đã hủy');
                            $('.not-send-text'+data.msg).addClass('label-danger');
                            $('.not-send-text'+data.msg).removeClass('label-warning');
                        }
                        window.location.reload();
                    }
                },
                error: function(){
                    alert("error");
                }
            })
        });
        "use strict";
        var datatable;
        var KTDatatablesDataSourceAjaxServer = function () {
            var initTable1 = function () {
                // begin first table
                datatable = $('#kt_datatable').DataTable({
                    responsive: true,
                    // Pagination settings
                    //full dom i và lp
                    // dom: `
                    //         <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>
                    //         <'row'<'col-sm-12'tr>>
                    //         <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    // read more: https://datatables.net/examples/basic_init/dom.html
                    dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                            <'row'<'col-sm-12'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    // dom: "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
                    lengthMenu: [20, 50, 100, 200,500,1000],
                    pageLength: 20,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "order": [[1, "desc"]],
                    ajax: {
                        url: '{{url()->current()}}' + '?ajax=1',
                        type: 'GET',
                        data: function (d) {
                            d.id = $('#id').val();
                            d.title = $('#title').val();
                            d.module = $('#module').val();
                            d.status = $('#status').val();
                            d.position = $('#position').val();
                            d.started_updated_at = $('#started_updated_at').val();
                            d.ended_updated_at = $('#ended_updated_at').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.payment_type = $('#payment_type').val();
                        }
                    },
                    buttons: [
                        {{--{--}}
                        {{--    "extend": 'excelHtml5',--}}
                        {{--    "text": ' <i class="far fa-file-excel icon-md"></i> {{__('Xuất excel')}} ',--}}
                        {{--    "action": newexportaction,--}}
                        {{--},--}}
                    ],
                    columns: [
                        {data: 'id', title: 'ID'},
                        {data: 'request_id', title: 'Tranid'},
                        {
                            data: 'shop', title: '{{__('Điểm bán')}}',
                            render: function (data, type, row) {
                                if (row.shop){
                                    return row.shop.domain;
                                }else{
                                    return '';
                                }
                            }
                        },
                        {
                            data: 'idkey', title: '{{__('Cổng giao dịch')}}',
                            render: function (data, type, row) {
                                var html = '';

                                if (row.idkey == 'roblox_buyserver' || row.idkey == 'nrogem' || row.idkey == 'ninjaxu' || row.idkey == 'nrocoin'){
                                    html = 'Đại lý';
                                }else if (row.idkey == 'roblox_buyserver_internal' || row.idkey == 'nrocoin_internal' || row.idkey == 'nrogem_internal' || row.idkey == 'ninjaxu_internal'){
                                    html = 'QLTT';
                                }

                                return html;
                            }
                        },
                        {
                            data: 'payment_type', title: '{{__('Tên loại vật phẩm')}}',
                            render: function (data, type, row) {
                                if(row.payment_type == 1){
                                    return "{{config('module.minigame.game_type.1')}}";
                                } else if(row.payment_type == 2){
                                    return "{{config('module.minigame.game_type.2')}}";
                                }else if(row.payment_type == 3){
                                    return "{{config('module.minigame.game_type.3')}}";
                                }else if(row.payment_type == 4){
                                    return "{{config('module.minigame.game_type.4')}}";
                                }else if(row.payment_type == 5){
                                    return "{{config('module.minigame.game_type.5')}}";
                                }else if(row.payment_type == 6){
                                    return "{{config('module.minigame.game_type.6')}}";
                                }else if(row.payment_type == 7){
                                    return "{{config('module.minigame.game_type.7')}}";
                                }else if(row.payment_type == 8){
                                    return "{{config('module.minigame.game_type.8')}}";
                                }else if(row.payment_type == 9){
                                    return "{{config('module.minigame.game_type.9')}}";
                                }else if(row.payment_type == 10){
                                    return "{{config('module.minigame.game_type.10')}}";
                                }else if(row.payment_type == 11){
                                    return "{{config('module.minigame.game_type.11')}}";
                                }else if(row.payment_type == 12){
                                    return "{{config('module.minigame.game_type.12')}}";
                                }else if(row.payment_type == 13){
                                    return "{{config('module.minigame.game_type.13')}}";
                                }else if(row.payment_type == 14){
                                    return "{{config('module.minigame.game_type.14')}}";
                                }
                            }
                        },
                        {
                            data: 'author_id', title: '{{__('ID user')}}',
                            render: function (data, type, row) {
                                return row.author_id;
                            }
                        },
                        {
                            data: 'module', title: '{{__('Loại rút VP')}}',
                            render: function (data, type, row) {
                                var html = '';
                                if (row.module == "withdraw-item"){
                                    html += '<span class="badge badge-success" ">';
                                    html += 'Rút vật phẩm';
                                    html +='</span>';
                                }else if (row.module == "withdraw-service-item"){
                                    html += '<span class="badge badge-warning" ">';
                                    html += 'Rút tự động';
                                    html +='</span>';
                                }else if (row.module == 'withdraw-itemrefund'){
                                    html += '<span class="badge badge-warning" ">';
                                    html += 'Hoàn VP';
                                    html +='</span>';
                                }
                                return html;
                            }
                        },
                        {
                            data: 'author', title: '{{__('User name')}}',visible: false,
                            render: function (data, type, row) {
                                if (row.author){
                                    return row.author.username;
                                }else {
                                    return '';
                                }

                            }
                        },
                        {
                            data: 'idkey', title: '{{__('Tham số 1')}}',visible: false,
                            render: function (data, type, row) {
                                return row.idkey;
                            }
                        },
                        {
                            data: 'title', title: '{{__('Tham số 2')}}',visible: false,
                            render: function (data, type, row) {
                                return row.title;
                            }
                        },
                        {
                            data: 'params', title: '{{__('Tham số 3')}}',visible: false,
                            render: function (data, type, row) {

                                var parameters_3 = "";
                                if (row.payment_type != 11 && row.payment_type != 12 && row.payment_type != 13 && row.payment_type != 14){
                                    if (row.params){

                                        var params = JSON.parse(row.params);
                                        if (params){
                                            if (params.parameters_3){
                                                parameters_3 = params.parameters_3;
                                            }
                                        }

                                    }
                                }

                                return parameters_3;
                            }
                        },
                        {
                            data: 'params', title: '{{__('Tham số 4')}}',visible: false,
                            render: function (data, type, row) {

                                var parameters_4 = "";
                                if (row.payment_type != 11 && row.payment_type != 12 && row.payment_type != 13 && row.payment_type != 14){
                                    if (row.params){

                                        var params = JSON.parse(row.params);
                                        if (params){
                                            if (params.parameters_4){
                                                parameters_4 = params.parameters_4;
                                            }
                                        }

                                    }
                                }

                                return parameters_4;
                            }
                        },
                        {
                            data: 'params', title: '{{__('Tham số 5')}}',visible: false,
                            render: function (data, type, row) {

                                var parameters_5 = "";
                                if (row.payment_type != 11 && row.payment_type != 12 && row.payment_type != 13 && row.payment_type != 14){
                                    if (row.params){

                                        var params = JSON.parse(row.params);
                                        if (params){
                                            if (params.parameters_5){
                                                parameters_5 = params.parameters_5;
                                            }
                                        }

                                    }
                                }

                                return parameters_5;
                            }
                        },
                        {
                            data: 'price_base', title: '{{__('Đơn giá')}}',
                            render: function (data, type, row) {
                                var price_base = "";

                                if (row.price_input){
                                    price_base = row.price_input;
                                }
                                return price_base;
                            }
                        },
                        {
                            data: 'price', title: '{{__('Số vật phẩm')}}',
                            render: function (data, type, row) {
                                var price = "";
                                if (row.price){
                                    price = row.price;
                                    price = price.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
                                    price = price.split('').reverse().join('').replace(/^[\.]/,'');
                                }
                                return price;
                            }
                        },
                        // {data: 'locale', title: '{{__('Ngôn ngữ')}}'},
                        {data: 'created_at', title: '{{__('Thời gian')}}'},
                        {data: 'updated_at', title: '{{__('Thời gian hoàn thành')}}'},
                        {
                            data: 'status', title: '{{__('Trạng thái')}}',
                            render: function (data, type, row) {
                                if (row.payment_type == 13 || row.payment_type == 11 || row.payment_type == 12 || row.payment_type == 14){
                                    if (row.status == 0 || row.status == 3 || row.status == 5) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2  label-danger not-send-text not-send-text"+row.id+"\">" + "Giao dịch thất bại" + "</span>";
                                    }
                                    else if(row.status == 3) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2  label-danger not-send-text not-send-text"+row.id+"\">" + "Từ chối" + "</span>";
                                    }
                                    else if(row.status == 5) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2  label-danger not-send-text not-send-text"+row.id+"\">" + "Thất bại" + "</span>";
                                    }
                                    else if(row.status == 2) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-info not-send-text not-send-text"+row.id+"\">" + "Đang thực hiện" + "</span>";
                                    }
                                    else if(row.status == 6) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-info not-send-text not-send-text"+row.id+"\">" + "Mất Item" + "</span>";
                                    }
                                    else if(row.status == 9) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-info not-send-text not-send-text"+row.id+"\">" + "Xử lý thủ công" + "</span>";
                                    }
                                    else if(row.status == 77) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-info not-send-text not-send-text"+row.id+"\">" + "Mất item không hoàn tiền" + "</span>";
                                    }
                                    else if(row.status == 88) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-info not-send-text not-send-text"+row.id+"\">" + "Mất item có hoàn tiền" + "</span>";
                                    }
                                    else if(row.status == 999) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-dark not-send-text not-send-text"+row.id+"\">" + "Lỗi logic xử ly" + "</span>";
                                    }
                                    else if(row.status == 1) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-warning not-send-text not-send-text"+row.id+"\">" + "Đang chờ xử lý" + "</span>";
                                    } else if(row.status == 4) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-success not-send-text not-send-text"+row.id+"\">" + "Hoàn thành" + "</span>";
                                    }
                                }else {
                                    if (row.status == 0) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2  label-warning not-send-text not-send-text"+row.id+"\">" + "{{config('module.minigame.withdraw_status.0')}}" + "</span>";
                                    } else if(row.status == 1) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-success not-send-text not-send-text"+row.id+"\">" + "{{config('module.minigame.withdraw_status.1')}}" + "</span>";
                                    } else if(row.status == 2) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-danger not-send-text not-send-text"+row.id+"\">" + "{{config('module.minigame.withdraw_status.2')}}" + "</span>";
                                    }else {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-danger not-send-text not-send-text"+row.id+"\">" + "{{config('module.minigame.withdraw_status.3')}}" + "</span>";
                                    }
                                }
                                return "";
                            }
                        },
                        { data: 'status',title:'Chuyển trạng thái',
                            render: function ( data, type, row ) {
                                if (row.payment_type == 13 || row.payment_type == 11 || row.payment_type == 12 || row.payment_type == 14){

                                }else{
                                    if(row.status==0){
                                        return  "<i title='Hủy giao dịch' style='text-align:center; ;padding-left: 30px;cursor:pointer' class=\"fa fa-times d-change-status danger"+row.id+"\" aria-hidden=\"true\" data-action='cancel' data-id="+row.id+" data-userid="+row.author_id+"></i><i title='Hoàn thành giao dịch' style='text-align:center;;padding-left: 30px;cursor:pointer' class=\"fa fa-check change-status success"+row.id+"\" aria-hidden=\"true\" data-action='done' data-id="+row.id+" data-userid="+row.author_id+"></i>";
                                    }
                                    if(row.status==1){
                                        return "<i title='Đã hoàn thành giao dịch' style='display:block;padding-left: 30px' class=\"fa fa-check-square\" aria-hidden=\"true\"></i>";
                                    }
                                    if(row.status==2){
                                        return "<i title='Đã hủy giao dịch' style='display:block;padding-left: 30px' class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>";
                                    }else{
                                        return "";
                                    }
                                }
                                return "";
                            }
                        },
                        { data: 'action',title:'Thao tác', orderable: false, searchable: false}
                    ],
                    "drawCallback": function (settings) {
                        var api = this.api();
                        var apiJson = api.ajax.json();
                        var rows = api.rows({page: 'current'}).nodes();

                        $('#total_record').text(number_format(apiJson.recordsFiltered,'.'));
                        $('#total_record_complete').text(number_format(apiJson.totalSumary.total_record_complete,'.'));
                        $('#total_record_wanning').text(number_format(apiJson.totalSumary.total_record_wanning,'.'));
                        $('#total_record_delete').text(number_format(apiJson.totalSumary.total_record_delete,'.'));
                        $('#total_record_pendding2').text(number_format(apiJson.totalSumary.total_record_pendding2,'.'));
                        $('#total_record_pendding6').text(number_format(apiJson.totalSumary.total_record_pendding6,'.'));
                        $('#total_record_pendding7').text(number_format(apiJson.totalSumary.total_record_pendding7,'.'));
                        $('#total_record_pendding9').text(number_format(apiJson.totalSumary.total_record_pendding9,'.'));
                        $('#total_record_pendding77').text(number_format(apiJson.totalSumary.total_record_pendding77,'.'));
                        $('#total_record_pendding88').text(number_format(apiJson.totalSumary.total_record_pendding88,'.'));
                        $('#total_withdraw_item').text(number_format(apiJson.totalSumary.total_withdraw_item,'.'));
                        $('#total_withdraw_item_complete').text(number_format(apiJson.totalSumary.total_withdraw_item_complete,'.'));
                        $('#total_withdraw_item_delete').text(number_format(apiJson.totalSumary.total_withdraw_item_delete,'.'));
                        $('#total_withdraw_item_pendding').text(number_format(apiJson.totalSumary.total_withdraw_item_pendding,'.'));
                        $('#total_item').text(number_format(apiJson.totalSumary.total_item,'.'));
                    }
                });
                var filter = function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    datatable.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                };
                $('#kt_search').on('click', function (e) {
                    e.preventDefault();
                    var params = {};
                    $('.datatable-input').each(function () {
                        var i = $(this).data('col-index');
                        if (params[i]) {
                            params[i] += '|' + $(this).val();
                        } else {
                            params[i] = $(this).val();
                        }
                    });
                    $.each(params, function (i, val) {
                        // apply search params to datatable
                        datatable.column(i).search(val ? val : '', false, false);
                    });
                    datatable.table().draw();
                });
                $('#kt_reset').on('click', function (e) {
                    e.preventDefault();
                    $('.datatable-input').each(function () {
                        $(this).val('');
                        datatable.column($(this).data('col-index')).search('', false, false);
                    });
                    datatable.table().draw();
                });
                datatable.on("click", "#btnCheckAll", function () {
                    $(".ckb_item input[type='checkbox']").prop('checked', this.checked).change();
                })
                datatable.on("change", ".ckb_item input[type='checkbox']", function () {
                    if (this.checked) {
                        var currTr = $(this).closest("tr");
                        datatable.rows(currTr).select();
                    } else {
                        var currTr = $(this).closest("tr");
                        datatable.rows(currTr).deselect();
                    }
                });
                //function update field
                datatable.on("change", ".update_field", function (e) {
                    e.preventDefault();
                    var action=$(this).data('action');
                    var field=$(this).data('field');
                    var id=$(this).data('id');
                    var value=$(this).data('value');
                    if(field=='status'){
                        if(value==1){
                            value=0;
                            $(this).data('value',1);
                        }
                        else{
                            value=1;
                            $(this).data('value',0);
                        }
                    }
                    $.ajax({
                        type: "POST",
                        url: action,
                        data: {
                            '_token':'{{csrf_token()}}',
                            'field':field,
                            'id':id,
                            'value':value
                        },
                        beforeSend: function (xhr) {
                        },
                        success: function (data) {
                            if (data.success) {
                                if (data.redirect + "" != "") {
                                    location.href = data.redirect;
                                }
                                toast('{{__('Cập nhật thành công')}}');
                            } else {
                                toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                            }
                        },
                        error: function (data) {
                            toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                        },
                        complete: function (data) {
                        }
                    });
                });

                $('label.toggle-vis').on('click', function (e) {
                    e.preventDefault();

                    var input = $(this).find('input');
                    if (input.is(":checked")){
                        input.prop('checked', false);
                    }else{
                        input.prop('checked', true);

                    }
                    // Get the column API object
                    var column = datatable.column($(this).attr('data-column'));

                    // Toggle the visibility
                    column.visible(!column.visible());
                });
            };
            return {
                //main function to initiate the module
                init: function () {
                    initTable1();
                },
            };
        }();
        function newexportaction(e, dt, button, config) {
            $(button).text("Đang tải...");
            $(button).prop('disabled', true);
            var self = this;
            var oldStart = dt.settings()[0]._iDisplayStart;
            dt.one('preXhr', function (e, s, data) {
                // Just this once, load all data from the server...
                data.start = 0;
                data.length = 2147483647;
                dt.one('preDraw', function (e, settings) {
                    // Call the original action function
                    if (button[0].className.indexOf('buttons-copy') >= 0) {
                        $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                    } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                        $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                            $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                    } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                        $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                            $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                    } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                        $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                            $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                    } else if (button[0].className.indexOf('buttons-print') >= 0) {
                        $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                    }
                    // dt.one('preXhr', function (e, s, data) {
                    //     // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    //     // Set the property to what it was before exporting.
                    //     settings._iDisplayStart = oldStart;
                    //     data.start = oldStart;
                    // });
                    // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                    // setTimeout(dt.ajax.reload, 0);
                    // Prevent rendering of the full data to the DOM
                    $(button).text("Xuất excel");
                    $(button).prop('disabled', false);
                    return false;
                });
            });
            // Requery the server with the new one-time export settings
            dt.ajax.reload();
        };
        //Funtion web ready state
        jQuery(document).ready(function () {
            KTDatatablesDataSourceAjaxServer.init();
            $('.datetimepicker-default').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:00',
                useCurrent: true,
                autoclose: true
            });
            $('#deleteModal').on('show.bs.modal', function(e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#deleteModal .id').attr('value', id);
            });
            $('.btn-submit-custom').click(function (e) {
                e.preventDefault();
                $(".btn-submit-custom").each(function (index, value) {
                    KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                });
                var btn = this;
                //gắn thêm hành động close khi submit
                $('#submit-close').val($(btn).data('submit-close'));
                var formSubmit = $('#' + $(btn).data('form'));
                formSubmit.submit();
            });
            $('.btn-filter-date').click(function (e) {
                e.preventDefault();
                var startedAt=$(this).data('started-at');
                var endeddAt=$(this).data('ended-at');
                $('#started_updated_at').val(startedAt);
                $('#ended_updated_at').val(endeddAt);
                datatable.draw();
            });
        });
    </script>



@endsection
