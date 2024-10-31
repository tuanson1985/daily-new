{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">

        <div class="btn-group">
            <a href="{{route('admin.user-ctv.create')}}" type="button" class="btn btn-success font-weight-bolder">
                <i class="fas fa-plus-circle icon-md"></i>
                {{__('Thêm mới')}}
            </a>
        </div>
    </div>
@endsection

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

            <form class="mb-10" action="{{route('admin.user-ctv-export.index')}}" method="post">
                {{ csrf_field() }}
                <div class="row">
                    {{--ID--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" name="id" class="form-control datatable-input" id="id" placeholder="{{__('ID')}}">
                        </div>
                    </div>
                    {{--username--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" name="username" class="form-control datatable-input" id="username"
                                   placeholder="{{__('Tên tài khoản')}}">
                        </div>
                    </div>
                    {{--email--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" name="email" class="form-control datatable-input" id="email"
                                   placeholder="{{__('Email')}}">
                        </div>
                    </div>

                    {{--role_ids--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>

                            <select id="roles_id" name="roles_id"
                                    class="form-control datatable-input datatable-input-select selectpicker"
                                    multiple="multiple" data-actions-box="true" title="-- {{__('Nhóm vai trò')}} --">
                                <option value="-1">Không có</option>
                                @if( !empty(old('roles_id')) )
                                    {!!\App\Library\Helpers::buildMenuDropdownList($roles,old('roles_id')) !!}
                                @else
                                    <?php $itSelect = [] ?>
                                    @if(isset($data))
                                        <?php array_push($itSelect, $data->parent_id)?>
                                    @endif
                                    {!!\App\Library\Helpers::buildMenuDropdownList($roles,$itSelect) !!}
                                @endif
                            </select>

                        </div>
                    </div>
                    {{-- CTV --}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('type_information_ctv',[''=>'-- Tất cả loại ctv --']+config('module.user-qtv.type_information_ctv'),old('type_information_ctv', isset($data) ? $data->type_information_ctv : null),array('id'=>'type_information_ctv','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>

                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.user.status'),old('status', isset($data) ? $data->status : null),array('id'=>'status','class'=>'form-control datatable-input',))}}
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
                    {{--incorrect_txns--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('incorrect_txns',[''=>'-- Tất cả biến động số dư --','1'=>'Lệch','0'=>'Chuẩn',],old('incorrect_txns', isset($data) ? $data->incorrect_txns : null),array('id'=>'incorrect_txns','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" name="balance_time" id="balance_time" autocomplete="off"
                                   class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian balance')}}" data-toggle="datetimepicker">

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
                        </button>
                        <button class="btn btn-danger btn-secondary--icon" value="1" name="export_excel" type="submit">
                                <span>
                                    <i class="flaticon-folder-2"></i>
                                    <span>Xuất Excel</span>
                                </span>
                        </button>
                        @if (auth()->user()->hasRole('admin'))
                        <button class="btn btn-success btn-secondary--icon btn_product"  type="button">
                                <span>
                                    <i class="flaticon-folder-2"></i>
                                    <span>Phân quyền nhanh</span>
                                </span>
                        </button>
                        @endif
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
                {{Form::open(array('route'=>array('admin.user-ctv.destroy',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'DELETE'))}}
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
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-custom"
                            data-form="form-delete">{{__('Xóa')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- lock item Modal -->
    <div class="modal fade" id="lockModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.user-qtv.lock',0),'class'=>'form-horizontal','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn khóa tài khoản?')}}

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom">Khóa</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- unlock item Modal -->
    <div class="modal fade" id="unlockModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.user-qtv.unlock',0),'class'=>'form-horizontal','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn mở khóa tài khoản?')}}

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom">Mở khóa</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    {{-- Modal new service--}}
    <div class="modal fade" id="serviceModal">
        <div class="modal-dialog  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="text-center modal-title label-service-selected" > {{__("Chọn dịch vụ áp dụng")}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{--ID--}}
                        <div class="form-group col-12 col-sm-6 col-lg-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                </div>
                                <input type="text" class="form-control datatable-input-service" id="id_service" placeholder="{{__('ID')}}">
                            </div>
                        </div>
                        {{--title--}}
                        <div class="form-group col-12 col-sm-6 col-lg-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                </div>
                                <input type="text" class="form-control datatable-input-service" id="title_service"
                                       placeholder="{{__('Tiêu đề')}}">
                            </div>
                        </div>

                        {{--group_id--}}

                        <div class="form-group col-12 col-sm-6 col-lg-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                </div>

                                <select id="id_group_service"
                                        class="form-control datatable-input datatable-input-select-service selectpicker" data-live-search="true"
                                        title="-- {{__('Tất cả danh mục')}} --">
                                    <option value="">--- Chọn danh mục ---</option>
                                    @if( !empty(old('parent_id')) )
                                        {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,old('parent_id')) !!}
                                    @else
                                        <?php $itSelect = [] ?>
                                        {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,$itSelect) !!}
                                    @endif
                                </select>

                            </div>
                        </div>


                        {{--status--}}
                        <div class="form-group col-12 col-sm-6 col-lg-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                </div>
                                {{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.language-nation.status'),old('status', isset($data) ? $data->status : null),array('id'=>'status_service','class'=>'form-control datatable-input-service',))}}
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-primary btn-primary--icon" id="kt_search_service">
                            <span>
                                <i class="la la-search"></i>
                                <span>Tìm kiếm</span>
                            </span>
                            </button>&#160;&#160;
                            <button class="btn btn-secondary btn-secondary--icon" id="kt_reset_service">
                            <span>
                                <i class="la la-close"></i>
                                <span>Reset</span>
                            </span>
                            </button>
                        </div>
                    </div>

                    <div style="padding-top: 20px;padding-bottom: 20px" class="row">
                        <div class="col-auto">
                            <label class="checkbox mb-1">
                                <input value="1" type="checkbox" class="display_info_role">
                                <span></span>&nbsp Xem trước thông tin
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="checkbox mb-1">
                                <input value="1" type="checkbox" class="view_role">
                                <span></span>&nbsp Xem
                            </label>
                        </div>
                        <div class="col-auto">
                            <label class="checkbox mb-1">
                                <input value="1" type="checkbox" class="accept_role">
                                <span></span>&nbsp  Nhận
                            </label>
                        </div>
                    </div>
                    <div style="padding-top: 20px;padding-bottom: 20px" class="row">
                        <div class="col-3">
                            <label class="control-label">
                                Phần trăm nhận trước:
                            </label>
                            <input value="" type="text" class="ratio_all form-control">
                        </div>
                    </div>
                    <!--begin: Datatable-->
                    <table class="table table-bordered table-hover table-checkable " id="kt_datatable_service_modal">

                    </table>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="service_ids" class="user_ids" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                    <button type="button" class="btn btn-primary  submit_service">{{__('Xác nhận')}}</button>
                </div>
            </div>
        </div>
    </div>




@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="/assets/backend/assets/css/user-qtv.css?v={{time()}}" rel="stylesheet" type="text/css"/>
@endsection
{{-- Scripts Section --}}
@section('scripts')

    <script>
        "use strict";
        var datatable;
        var datatable3;
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
                    lengthMenu: [20, 50, 100, 200, 500, 1000],
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
                            d.username = $('#username').val();
                            d.roles_id = $('#roles_id').val();
                            d.email = $('#email').val();
                            d.status = $('#status').val();
                            d.type_information_ctv = $('#type_information_ctv').val();
                            d.started_at = $('#started_at').val();
                            d.incorrect_txns = $('#incorrect_txns').val();
                            d.ended_at = $('#ended_at').val();
                            d.balance_time = $('#balance_time').val();
                        }
                    },

                    buttons: [

                        {
                            text: '<i class="m-nav__link-icon la la-trash"></i> Xóa đã chọn ',
                            action: function (e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatable.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if (total <= 0) {
                                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                                    return;
                                }
                                datatable.$('.ckb_item input[type="checkbox"]').each(function (index, elem) {
                                    if ($(elem).is(':checked')) {
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                        }
                                    }
                                })
                                $('#deleteModal').modal('toggle');
                                $('#deleteModal .id').attr('value', allSelected);

                            }
                        },
                        {
                            "extend": 'excelHtml5',
                            "text": ' <i class="far fa-file-excel icon-md"></i> {{__('Xuất excel')}} ',
                            "action": newexportaction,
                        },
                    ],
                    columns: [
                        {
                            data: null,
                            title: '<label class="checkbox checkbox-lg checkbox-outline"><input type="checkbox" id="btnCheckAll">&nbsp<span></span></label>',
                            orderable: false,
                            searchable: false,
                            width: "20px",
                            class: "ckb_item",
                            render: function (data, type, row) {
                                return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input type="checkbox" rel="' + row.id + '" id="">&nbsp<span></span></label>';

                            }
                        },
                        {data: 'id', title: 'ID'},
                        {
                            data: 'username', title: '{{__('Tên tài khoản')}}',
                            render: function (data, type, row) {
                                if( row.username+"" !="null"){
                                    return row.username;
                                }
                                else{
                                    return "";
                                }

                            }
                        },
                        {
                            data: 'email', title: '{{__('Email')}}',
                            render: function (data, type, row) {
                                if( row.email+"" !="null"){
                                    return row.email;
                                }
                                else{
                                    return "";
                                }

                            }
                        },
                        {
                            data: "account_type", title: '{{__('Loại tài khoản')}}', orderable: false,
                            render: function (data, type, row) {
                                if (row.account_type == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-primary \">" + "{{config('module.user-qtv.account_type.1')}}" + "</span>";
                                }
                                else if(row.account_type==2) {
                                    return "<span class=\"label label-pill label-inline label-center  mr-2 label-success \">" + "{{config('module.user-qtv.account_type.2')}}" + "</span>";
                                }
                                else{
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-success \">" + "{{config('module.user-qtv.account_type.3')}}" + "</span>";
                                }
                            }
                        },
                        {
                            data: "type_information_ctv", title: '{{__('Loại tài khoản CTV')}}', orderable: false,
                            render: function (data, type, row) {
                                if (row.type_information_ctv == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-primary \">" + "{{config('module.user-qtv.type_information_ctv.1')}}" + "</span>";
                                }
                                else if(row.type_information_ctv==2) {
                                    return "<span class=\"label label-pill label-inline label-center  mr-2 label-success \">" + "{{config('module.user-qtv.type_information_ctv.2')}}" + "</span>";
                                }
                                else{
                                    return "";
                                }
                            }
                        },
                        {
                            data: "roles", title: '{{__('Nhóm vai trò')}}', orderable: false,
                            render: function (data, type, row) {
                                var temp = "";
                                $.each(row.roles, function (index, value) {
                                    if (value.name == 'admin') {
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-primary \">" + value.title + "</span><br />";
                                    } else {
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + value.title + "</span><br />";
                                    }
                                });
                                return temp;
                            }
                        },
                        {
                            data: 'balance', title: '{{__('Số dư')}}', render: function (data, type, row) {
                                return number_format(row.balance,'.');

                            }
                        },
                        {
                            data: 'balance_time', title: '{{__('Số dư theo thời gian')}}', render: function (data, type, row) {
                                return number_format(row.balance_time,'.');

                            }
                        },
                        {
                            data: null, title: '{{__('Biến động số dư')}}', orderable: false, searchable: false,
                            render: function (data, type, row) {
                                console.log();
                                var temp = "";
                                temp += "<span class='text-success'>" + "+ " + number_format(data.balance_in,".") + "</span><br/>";
                                temp += "<span class='text-danger'>" + "- " + number_format(data.balance_out-data.balance_in_refund,".") + "</span><br/>";

                                var not_equal = data.balance_in - data.balance_out + data.balance_in_refund - row.balance
                                if (not_equal != 0) {
                                    temp += "<div class='text-danger' style='border:1px solid #f64e60;padding:5px;margin-top:5px;'>" + '{{__('Lệch')}}' + ' ' + number_format(not_equal,".") + "</div><br/>";
                                } else {

                                    temp += "<div class='text-success' style='border:1px solid #1bc5bd;padding:5px;margin-top:5px;' >{{__('Chuẩn')}}" + "</div><br/>";
                                }

                                return temp;
                            }
                        },
                        {
                            data: 'google2fa_enable', title: '{{__('Bảo mật 2FA')}}',
                            render: function (data, type, row) {
                                return row.google2fa_enable
                            }
                        },
                        {
                            data: 'type_information', title: '{{__('Phân loại')}}',
                            render: function (data, type, row) {
                                if (row.type_information == 0) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.user-qtv.type_information.0')}}" + "</span>";
                                } else if (row.type_information == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.user-qtv.type_information.1')}}" + "</span>";
                                } else {
                                    return "Chưa có phân loại";
                                }

                            }
                        },
                        {
                            data: 'status', title: '{{__('Trạng thái')}}',
                            render: function (data, type, row) {
                                if (row.status == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.user.status.1')}}" + "</span>";
                                } else if (row.status == 2) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.user.status.2')}}" + "</span>";
                                } else {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.user.status.0')}}" + "</span>";
                                }

                            }
                        },
                        {data: 'lastlogin_at', title: '{{__('TG đăng nhập gần nhất')}}'},
                        {data: 'action', title: 'Thao tác', className:"textSmall", orderable: false, searchable: false}
                    ],
                    "drawCallback": function (settings) {
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
                    $(".datatable-input-select").val('default').selectpicker("refresh");
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
                        // datatable.rows(currTr).select();
                    } else {
                        var currTr = $(this).closest("tr");
                        // datatable.rows(currTr).deselect();
                    }
                });

                //function update field
                datatable.on("change", ".update_field", function (e) {


                    e.preventDefault();
                    var action = $(this).data('action');
                    var field = $(this).data('field');
                    var id = $(this).data('id');
                    var value = $(this).data('value');
                    if (field == 'status') {

                        if (value == 1) {
                            value = 0;
                            $(this).data('value', 1);
                        } else {
                            value = 1;
                            $(this).data('value', 0);
                        }
                    }


                    $.ajax({
                        type: "POST",
                        url: action,
                        data: {
                            '_token': '{{csrf_token()}}',
                            'field': field,
                            'id': id,
                            'value': value
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

            };
            return {

                //main function to initiate the module
                init: function () {
                    initTable1();
                },

            };
        }();

        // dịch vụ
        var KTDatatablesDataSourceAjaxServerService = function () {
            var initTable3 = function () {
                // begin first table
                datatable3 = $('#kt_datatable_service_modal').DataTable({
                    responsive: true,
                    destroy: true,
                    dom: `
                            <'row'<'col-sm-12'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,

                    lengthMenu: [20, 50, 100, 200,500,1000],
                    pageLength: 100,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "order": [[1, "desc"]],
                    ajax: {
                        url: '{{url()->current()}}' + '?pemission_service_speed=1&ajax=1',
                        type: 'GET',
                        data: function (d) {
                            d.id = $('#id_service').val();
                            d.title = $('#title_service').val();
                            d.status = $('#status_service').val();
                            d.id_group = $('#id_group_service').val();
                            d.position = $('#position_service').val();
                            d.started_at = $('#started_at_service').val();
                            d.ended_at = $('#ended_at_service').val();
                        }
                    },
                    buttons: [
                    ],
                    columns: [
                        {
                            data: null,
                            title: '<label class="checkbox checkbox-lg checkbox-outline"><input type="checkbox" id="btnCheckService">&nbsp<span></span></label>',
                            orderable: false,
                            searchable: false,
                            width: "20px",
                            class: "ckb_item",
                            render: function (data, type, row) {
                                return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item label_checkbox_service"><input class="input_checkbox_service" data-id="' + row.id + '" type="checkbox" rel="' + row.id + '" id="">&nbsp<span></span></label>';

                            }
                        },

                        {data: 'id', title: 'ID'},
                        {
                            data: 'title', title: '{{__('Tiêu đề')}}',
                            render: function (data, type, row) {
                                var domainCurrent='{{session()->get('shop_name')}}'+"";

                                if(domainCurrent!=""){
                                    var temp = "<a href=\"https://"+ domainCurrent + '/dich-vu/' + row.slug + "\" title=\""+row.title+"\"  target='_blank'    >" + row.title + "</a>";
                                }
                                else{
                                    var temp = "<a href=\"" + '/dich-vu/' + row.slug + "\" title=\""+row.title+"\"  target='_blank'    >" + row.title + "</a>";
                                }
                                return temp;
                            }
                        },
                        {
                            data: 'daily', title: '{{__('Cổng dịch vụ')}}',
                            render: function (data, type, row) {
                                if (row.daily){
                                    return row.daily;
                                }
                                return '';
                            }
                        },
                        {
                            data: "groups", title: '{{__('Danh mục')}}', orderable: false,
                            render: function (data, type, row) {
                                var temp = "";
                                $.each(row.groups, function (index, value) {
                                    if (value.name == 'admin') {
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-primary \">" + value.title + "</span><br />";
                                    } else {
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + value.title + "</span><br />";
                                    }

                                    // console.log( value.title);
                                });
                                return temp;
                            }
                        },
                        {
                            data: 'status', title: '{{__('Trạng thái')}}',
                            render: function (data, type, row) {

                                if (row.status == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.service.status.1')}}" + "</span>";
                                } else if (row.status == 2) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.service.status.2')}}" + "</span>";
                                } else {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service.status.0')}}" + "</span>";
                                }

                            }
                        },
                        {
                            data: 'ratio', title: '{{__('Phần trăm tiền nhận khi hoàn tất')}}',
                            render: function (data, type, row) {
                                if (row.ratio){
                                    return row.ratio;
                                }
                                return '';
                            }
                        },
                        {data: 'created_at', title: '{{__('Thời gian')}}'},
                    ],
                    "drawCallback": function (settings) {
                        $(".input-price").inputmask({
                            groupSeparator: ".",
                            radixPoint: ",",
                            alias: "numeric",
                            placeholder: "0",
                            autoGroup: true,
                        });
                        //$('#kt_body').removeClass('modal-open');
                        // $('.modal-backdrop').remove();

                        $('#id_category_service').select2();
                    }

                });
                var filter = function () {
                    var val = $.fn.datatable3.util.escapeRegex($(this).val());
                    datatable3.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                };
                $('#kt_search_service').on('click', function (e) {
                    e.preventDefault();
                    var params = {};
                    $('.datatable-input-service').each(function () {
                        var i = $(this).attr('col-index');
                        if (params[i]) {
                            params[i] += '|' + $(this).val();
                        } else {
                            params[i] = $(this).val();
                        }
                    });

                    $.each(params, function (i, val) {
                        // apply search params to datatable
                        datatable3.column(i).search(val ? val : '', false, false);
                    });
                    datatable3.table().draw();
                });
                $('#kt_reset_service').on('click', function (e) {
                    e.preventDefault();
                    $('.datatable-input-service').each(function () {
                        $(this).val('');
                        datatable3.column($(this).data('col-index')).search('', false, false);
                    });
                    datatable3.table().draw();
                });
                datatable3.on("click", "#btnCheckService", function () {
                    $(".ckb_item .label_checkbox_service input[type='checkbox']").prop('checked', this.checked).change();
                })
                datatable3.on("change", ".ckb_item .label_checkbox_product input[type='checkbox']", function () {
                    if (this.checked) {
                        var currTr = $(this).closest("tr");
                        datatable3.rows(currTr).select();
                    } else {
                        var currTr = $(this).closest("tr");
                        datatable3.rows(currTr).deselect();
                    }
                });
                //function update field
                datatable3.on("change", ".update_field", function (e) {
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

            };
            return {

                //main function to initiate the module
                init: function () {
                    initTable3();
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

            $('#deleteModal').on('show.bs.modal', function (e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#deleteModal .id').attr('value', id);
            });

            //LOCK button
            //triggered when modal is about to be shown
            $('#lockModal').on('show.bs.modal', function (e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#lockModal .id').attr('value', id);
            });

            $('#unlockModal').on('show.bs.modal', function (e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#unlockModal .id').attr('value', id);
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

            function showService(){
                KTDatatablesDataSourceAjaxServerService.init();
            }

            $('body').on('click','.btn_product',function(e){
                e.preventDefault();

                var allSelected = '';
                var total = datatable.$('.checkbox-item input[type="checkbox"]:checked').length;
                if (total <= 0) {
                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                    return;
                }
                datatable.$('.ckb_item input[type="checkbox"]').each(function (index, elem) {
                    if ($(elem).is(':checked')) {
                        let id_service = $(elem).attr('rel');
                        if (allSelected == ''){
                            allSelected = $(elem).attr('rel');
                        }else {
                            allSelected = allSelected + ',' + $(elem).attr('rel');
                        }
                    }
                })
                $('#serviceModal .user_ids').val(allSelected);

                $('#serviceModal').modal('show');
                showService();

            });
        });

        $('body').on('click', '.submit_service', function(e) {
            e.preventDefault();
            let ids = '';
            let ratios = '';

            let total = $("#kt_datatable_service_modal .label_checkbox_service input[type=checkbox]:checked").length;
            if(total>0){
                $("#kt_datatable_service_modal .label_checkbox_service input[type=checkbox]").each(function (index, elem) {
                    if ($(elem).is(':checked')) {
                        let id = $(this).attr('rel');
                        let ratio = $(this).parent().parent().parent().find('.ratio_input').val();
                        if (!ratio){
                            ratio = 'null';
                        }

                        if (ratios == ''){
                            ratios = ratio;
                        }else {
                            ratios = ratios + ',' + ratio;
                        }

                        if (ids == ''){
                            ids = id;
                        }else {
                            ids = ids + ',' + id;
                        }
                    }
                });

            }
            else{
                toast("{{__('Vui lòng chọn dữ liệu cần thêm')}}", 'error');
                return false;
            }

            let user_ids = $('#serviceModal .user_ids').val();

            if (!user_ids){
                toast("{{__('Vui lòng chọn ctv')}}", 'error');
                return false;
            }

            let display_info_role = 0;
            let view_role = 0;
            let accept_role = 0;

            // Lấy tất cả checkbox
            const displayInfoRole = document.querySelector('.display_info_role');
            const viewRole = document.querySelector('.view_role');
            const acceptRole = document.querySelector('.accept_role');

// Kiểm tra trạng thái và lấy value của từng checkbox
            if (displayInfoRole.checked) {
                display_info_role = 1;
            }

            if (viewRole.checked) {
                view_role = 1;
            }

            if (acceptRole.checked) {
                accept_role = 1;
            }

            let ratio_all = $('.ratio_all').val();

            $.ajax({
                type: "POST",
                url: "{{route('admin.user-ctv.post_set_permission_speed')}}",
                data: {
                    '_token':'{{csrf_token()}}',
                    ids: ids,
                    ratios: ratios,
                    user_ids: user_ids,
                    display_info_role: display_info_role,
                    view_role: view_role,
                    accept_role: accept_role,
                    ratio_all: ratio_all,
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if( data.status == 1){
                        // toast(data.message);
                        //
                        // window.location.reload();
                    }
                    else{
                        toast(data.message, 'error');
                    }
                },
                error: function (data) {
                    toast('{{__('Có lỗi phát sinh vui lòng thử lại')}}', 'error');
                },
                complete: function (data) {

                }
            });

        })

    </script>



@endsection
