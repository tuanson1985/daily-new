{{-- Extends layout --}}
@extends('frontend._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">

        {{--<div class="btn-group">--}}
        {{--    <a href="{{route('admin.'.$module.'.create')}}" type="button"  class="btn btn-success font-weight-bolder">--}}
        {{--        <i class="fas fa-plus-circle icon-md"></i>--}}
        {{--        {{__('Thêm mới')}}--}}
        {{--    </a>--}}
        {{--</div>--}}
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
            <form class="mb-10">
                <div class="row">
                    {{--ID--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="id" name="id" value="{{request('id')}}"   placeholder="{{__('ID hoặc Request ID')}}">
                        </div>
                    </div>
                    {{--title--}}
                    {{--<div class="form-group col-12 col-sm-6 col-lg-3">--}}
                    {{--    <div class="input-group">--}}
                    {{--        <div class="input-group-prepend">--}}
                    {{--            <span class="input-group-text"><i--}}
                    {{--                    class="la la-calendar-check-o glyphicon-th"></i></span>--}}
                    {{--        </div>--}}
                    {{--        <input type="text" class="form-control datatable-input" id="title"--}}
                    {{--               placeholder="{{__('Tiêu đề')}}">--}}
                    {{--    </div>--}}
                    {{--</div>--}}

                    {{--author--}}
{{--                    <div class="form-group col-12 col-sm-6 col-lg-3">--}}
{{--                        <div class="input-group">--}}
{{--                            <div class="input-group-prepend">--}}
{{--                                <span class="input-group-text"><i--}}
{{--                                        class="la la-calendar-check-o glyphicon-th"></i></span>--}}
{{--                            </div>--}}
{{--                            <input type="text" class="form-control datatable-input" id="author" name="author" value="{{request('author')}}"--}}
{{--                                   placeholder="{{__('Người order')}}">--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    {{--processor--}}
{{--                    <div class="form-group col-12 col-sm-6 col-lg-3">--}}
{{--                        <div class="input-group">--}}
{{--                            <div class="input-group-prepend">--}}
{{--                                <span class="input-group-text"><i--}}
{{--                                        class="la la-calendar-check-o glyphicon-th"></i></span>--}}
{{--                            </div>--}}
{{--                            <input type="text" class="form-control datatable-input"  id="processor" name="processor" value="{{request('processor')}}"--}}
{{--                                   placeholder="{{__('Người thực hiện')}}">--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    {{--group_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>

                            <select  id="group_id" name="group_id" value="{{request('group_id')}}"
                                    class="form-control datatable-input datatable-input-select selectpicker" data-live-search="true"
                                    title="-- {{__('Tất cả danh mục')}} --">
                                <option value="">-- Tất cả danh mục -- </option>
                                @if( !empty(old('group_id')) )
                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,old('group_id',request('group_id'))) !!}
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
                            {{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.service-purchase-auto.status'),old('status', request('status')),array('id'=>'status','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <select id="status_nrogem" name="status_nrogem" class="form-control">
                                <option value="">-- Tất cả trạng thái tool --</option>
                                <option value="danhanngoc">Đã nhận ngọc</option>
                                <option value="thieungoc">Thiếu ngọc</option>
                                <option value="koosieuthi">Không siêu thị</option>
                                <option value="matitem">Mất item</option>
                                <option value="kodusucmanh">Không sức mạnh</option>
                                <option value="kconhanvat">Bán Ngọc</option>
                                <option value="dahuybo">Đã hủy bỏ</option>
                                <option value="hanhtrangday">Hành trang đầy</option>
                                <option value="muanhamitem">Mua nhầm item</option>
                                <option value="taikhoansai">Tài khoản sai</option>
                                <option value="thieungoc">Thiếu ngọc</option>
                            </select>

                        </div>
                    </div>

                    {{--status--}}
{{--                    <div class="form-group col-12 col-sm-6 col-lg-3">--}}
{{--                        <div class="input-group">--}}
{{--                            <div class="input-group-prepend">--}}
{{--                                <span class="input-group-text"><i--}}
{{--                                        class="la la-calendar-check-o glyphicon-th"></i></span>--}}
{{--                            </div>--}}
{{--                            {{Form::select('payment_type',[''=>'-- Tất cả đơn rút và mua --']+['0'=>'Rút tự động','1'=>'Mua dịch vụ'],old('status', request('payment_type')),array('id'=>'payment_type','class'=>'form-control datatable-input',))}}--}}

{{--                        </div>--}}
{{--                    </div>--}}




                    {{--started_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="started_at" id="started_at" value="{{request('started_at')}}" autocomplete="off"
                                   class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian tạo từ')}}" data-toggle="datetimepicker">

                        </div>
                    </div>

                    {{--ended_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Đến</span>
                            </div>
                            <input type="text" name="ended_at" id="ended_at" value="{{request('ended_at')}}"  autocomplete="off"
                                   class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian tạo đến')}}" data-toggle="datetimepicker">

                        </div>
                    </div>



                    {{--finished_started_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="finished_started_at" id="finished_started_at" value="{{request('finished_started_at')}}" autocomplete="off"
                                   class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian hoàn tất từ')}}" data-toggle="datetimepicker">

                        </div>
                    </div>

                    {{--finished_ended_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Đến</span>
                            </div>
                            <input type="text" name="finished_ended_at" id="finished_ended_at" value="{{request('finished_ended_at')}}"  autocomplete="off"
                                   class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian hoàn tất đến')}}" data-toggle="datetimepicker">

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


{{--                        @if ( auth('frontend')->user()->can('store-card-export'))--}}
                            <input type="hidden" name="export_excel" value="">
                            <button class="btn btn-danger btn-secondary--icon" value="1" name="export_excel" type="submit">
                                <span>
                                    <i class="flaticon-folder-2"></i>
                                    <span>Xuất Excel</span>
                                </span>
                            </button>
{{--                        @endif--}}

                        <button class="btn btn-secondary btn-secondary--icon" id="kt_reset">
                            <span>
                                <i class="la la-close"></i>
                                <span>Reset</span>
                            </span>
                        </button>

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
                        Số giao dịch: <b id="total_record">0</b> - Tổng tiền: <b id="total_price">0</b>
                    </div>
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Số lượng ( Tiền Ingame): <b id="total_price_base">0</b>
                    </div>
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Tổng phải trả NCC: <b id="total_price_input">0</b>
                    </div>
                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                        Tổng lợi nhuận: <b id="total_profit">0</b>
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

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')

    <script>
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
                            d.status = $('#status').val();
                            d.group_id = $('#group_id').val();
                            d.author = $('#author').val();
                            d.processor = $('#processor').val();
                            d.payment_type = $('#payment_type').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.finished_started_at = $('#finished_started_at').val();
                            d.finished_ended_at = $('#finished_ended_at').val();
                            d.status_nrogem = $('#status_nrogem').val();
                        }
                    },
                    buttons: [

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

                        {
                            data: 'id', title: 'ID',
                            render: function (data, type, row) {

                                return  row.id+ "<br/> Request ID: "+row.request_id_customer;
                            }
                        },
                        {
                            data: 'id', title: 'Dịch vụ',
                            render: function (data, type, row) {
                               var temp="";
                               if (row.item_ref){

                                   if (row.item_ref.title!==null){
                                       temp+=row.item_ref.title;
                                   }
                                   temp+="<br />";
                                   if( row.idkey !=null){

                                       temp+="<i style='font-size:12px'>"+"Cổng Auto: "+ row.idkey +"</i>";
                                   }

                                   return temp;
                               }

                                return temp;
                            }
                        },
                        {
                            data: 'price_base', title: 'Số vật phẩm/Tên huge',
                            render: function (data, type, row) {
                                return row.price_base;
                            }
                        },
                        {
                            data: 'server', title: 'Server',
                            render: function (data, type, row) {

                                return row.server;
                            }
                        },
                        {
                            data: 'price', title: 'Trị giá',
                            render: function (data, type, row) {
                                return number_format(row.price,'.');

                            }
                        },
                        {
                            data: 'status', title: 'Trạng thái',
                            render: function (data, type, row) {

                                if (row.status == 0) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-danger \">" + "{{config('module.service-purchase-auto.status.0')}}" + "</span>";
                                }
                                else if (row.status == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-warning \">" + "{{config('module.service-purchase-auto.status.1')}}" + "</span>";

                                } else if (row.status == 2) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-info \">" + "{{config('module.service-purchase-auto.status.2')}}" + "</span>";
                                }
                                else if (row.status == 3) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service-purchase-auto.status.3')}}" + "</span>";
                                }
                                else if (row.status == 4) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-success \">" + "{{config('module.service-purchase-auto.status.4')}}" + "</span>";
                                }
                                else if (row.status == 5) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service-purchase-auto.status.5')}}" + "</span>";
                                }

                                else if (row.status == 6) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.service-purchase-auto.status.6')}}" + "</span>";
                                }

                                else if (row.status == 7) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.service-purchase-auto.status.7')}}" + "</span>";
                                }

                                else if (row.status == 77) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service-purchase-auto.status.77')}}" + "</span>";
                                }
                                else if (row.status == 88) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service-purchase-auto.status.88')}}" + "</span>";
                                }
                                else if (row.status == 89) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-info \">" + "{{config('module.service-purchase-auto.status.89')}}" + "</span>";
                                }

                                else if (row.status == 9) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.service-purchase-auto.status.9')}}" + "</span>";
                                }

                                else {
                                    return "";
                                }

                            }
                        },

                        {
                            data: 'created_at', title: 'Ngày tạo',
                            render: function (data, type, row) {
                                return row.created_at;
                            }
                        },
                        {
                            data: 'process_at', title: 'Ngày hoàn tất',
                            render: function (data, type, row) {
                                if(row.status == 4){
                                    return row.process_at;
                                }else{
                                    return "";
                                }
                            }
                        },

                        {
                            data: 'author', title: 'Người Order',
                            render: function (data, type, row) {
                                return row.author.replace('tt_', '');
                            }
                        },
                        {data: 'action', title: 'Thao tác', orderable: false, searchable: false}

                    ],
                    "drawCallback": function (settings) {

                        var api = this.api();
                        var apiJson = api.ajax.json();
                        var rows = api.rows({page: 'current'}).nodes();

                        $('#total_record').text(number_format(apiJson.recordsFiltered,'.'));
                        $('#total_price').text(number_format(apiJson.totalSumary.total_price,'.'));
                        $('#total_price_base').text(number_format(apiJson.totalSumary.total_price_base,'.'));
                        $('#total_price_input').text(number_format(apiJson.totalSumary.total_price_input,'.'));
                        $('#total_profit').text(number_format(apiJson.totalSumary.total_profit,'.'));

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

                $('#group_id').on('change', function (e) {
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
                    $('.datatable-input').each(function (e) {
                        if($(this).hasClass('select2')){
                            $(this).val('').change();
                        }
                        $(this).val('')
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

                $('.btn-filter-date').click(function (e) {
                    e.preventDefault();
                    var startedAt=$(this).data('started-at');
                    var endeddAt=$(this).data('ended-at');


                    $('#finished_started_at').val(startedAt);
                    $('#finished_ended_at').val(endeddAt);
                    datatable.draw();
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


            // $('#rechargeModal').on('show.bs.modal', function (e) {
            //     //get data-id attribute of the clicked element

            // });

            $('#refundModal').on('show.bs.modal', function(e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#refundModal .refund_id').attr('value', id);
            });

            $(document).on('click', '.recharge_toggle', function(e){

                var id= $(this).attr('rel');
                $('#rechargeModal .id').attr('value', id);
            });



            $("#rechargeModal form").submit(function(e) {

                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var actionUrl = form.attr('action');



                $("#rechargeModal button:submit").each(function (index, value) {
                    KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                });

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (data) {
                        if (data.status==1) {
                            toast(data.message);
                            $('#rechargeModal').modal('toggle');
                            datatable.ajax.reload();
                        } else {
                            $('#rechargeModal').modal('toggle');
                            toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                        }
                    },
                    error: function (data) {
                        $('#rechargeModal').modal('toggle');
                        toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function(data) {
                        $("#rechargeModal button:submit").each(function (index, value) {
                            KTUtil.btnRelease(this);
                        });

                    }

                });

            });


            $("#psxModal form").submit(function(e) {

                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var actionUrl = form.attr('action');



                $("#psxModal button:submit").each(function (index, value) {
                    KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                });

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (data) {
                        if (data.status==1) {
                            toast(data.message);
                            $('#psxModal').modal('toggle');
                            datatable.ajax.reload();
                        } else {
                            $('#psxModal').modal('toggle');
                            toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                        }
                    },
                    error: function (data) {
                        $('#psxModal').modal('toggle');
                        toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function(data) {
                        $("#psxModal button:submit").each(function (index, value) {
                            KTUtil.btnRelease(this);
                        });

                    }

                });

            });

            $("#userModal form").submit(function(e) {

                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var actionUrl = form.attr('action');



                $("#userModal button:submit").each(function (index, value) {
                    KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                });

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (data) {
                        if (data.status==1) {
                            toast(data.message);
                            $('#userModal').modal('toggle');
                            datatable.ajax.reload();
                        } else {
                            $('#userModal').modal('toggle');
                            toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                        }
                    },
                    error: function (data) {
                        $('#userModal').modal('toggle');
                        toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function(data) {
                        $("#userModal button:submit").each(function (index, value) {
                            KTUtil.btnRelease(this);
                        });

                    }

                });

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



        });





    </script>



@endsection
