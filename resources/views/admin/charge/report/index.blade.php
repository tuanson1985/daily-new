{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')

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
            <form class="mb-10" action="{{route('admin.charge-export.index')}}" method="post">
                {{ csrf_field() }}
                <div class="row">
                    {{--ID--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="id" value="{{request('id')}}" placeholder="{{__('ID')}}">
                        </div>
                    </div>
                    {{--username--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="username" value="{{request('username')}}"
                                   placeholder="{{__('Tài khoản')}}">
                        </div>
                    </div>

                    {{--find--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="find" {{request('find')}} value="{{request('find')}}"
                            placeholder="{{__('Mã thẻ,Serial...')}}">
                        </div>
                    </div>

                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.charge.status'),old('status',request('status')),array('id'=>'status','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>

                    {{--group_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('group_id',[''=>'-- Tất cả loại thẻ --']+$telecom,old('key',request('key')),array('id'=>'key','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>

                    {{--amount--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('amount',[''=>'-- Tất cả mệnh giá --']+array('10000'=>'10,000','20000'=>'20,000','30000'=>'30,000','50000'=>'50,000','100000'=>'100,000','200000'=>'200,000','300000'=>'300,000','500000'=>'500,000','1000000'=>'2,000,000','2000000'=>'1,000,000','5000000'=>'5,000,000'),old('amount',request('amount')),array('id'=>'amount','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>


                    {{--started_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="started_at" id="started_at" autocomplete="off" value="{{request('started_at')}}"
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
                            <input type="text" name="ended_at" id="ended_at" autocomplete="off" value="{{request('ended_at')}}"
                                   class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian kết thúc')}}" data-toggle="datetimepicker">

                        </div>
                    </div>
                    {{--gate_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('gate_id',[''=>'-- Tất cả cộng gạch thẻ --']+config('module.telecom.gate_id'),old('gate_id',request('gate_id')),array('id'=>'gate_id','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>
                    {{--process_started_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="process_started_at" id="process_started_at" autocomplete="off" value="{{request('process_started_at')}}"
                                    class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                                    placeholder="{{__('Thời gian hoàn tất')}}" data-toggle="datetimepicker">
                        </div>
                    </div>
                    {{--process_ended_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Đến</span>
                            </div>
                            <input type="text" name="process_ended_at" id="process_ended_at" autocomplete="off" value="{{request('process_ended_at')}}"
                                    class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                    placeholder="{{__('Thời gian hoàn tất')}}" data-toggle="datetimepicker">
                        </div>
                    </div>
                    {{--shop_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3" id="group__shop">
                        <div class="input-group">
                            <div class="row" style="width: 100%;margin: 0 auto;">
                                <div class="col-auto pl-0 pr-0" style="width: 45px">
                                    <div class="input-group-prepend" style="height: 100%">
                                        <span class="input-group-text" style="border-bottom-right-radius: 0;border-top-right-radius: 0"><i
                                                class="la la-calendar-check-o glyphicon-th"></i></span>
                                    </div>
                                </div>
                                <div class="col-auto pr-0 pl-0" style="width: calc(100% - 45px)">
                                    <select name="shop_id[]" class="form-control select2 datatable-input shop_id" id="kt_select2_2" multiple data-placeholder="-- {{__('Tất cả shop')}} --"   style="width: 100%" >
                                        {!!\App\Library\Helpers::buildShopDropdownList($shop,null) !!}
                                    </select>
                                </div>
                            </div>
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
                        @if ( auth()->user()->can('charge-report-export'))
                            <button class="btn btn-danger btn-secondary--icon" type="submit">
                                <span>
                                    <i class="flaticon-folder-2"></i>
                                    <span>Xuất Excel</span>
                                </span>
                            </button>
                        @endif
                        @if ( auth()->user()->can('charge-google-analytics-export'))
                            <button value="1" name="submit" class="btn btn-danger btn-secondary--icon" type="submit">
                                <span>
                                    <i class="flaticon-folder-2"></i>
                                    <span>Xuất Excel Google Analytics</span>
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

            </form>




            <!--begin: Search Form-->
            <!--begin: Datatable-->
            <table class="table table-bordered table-hover table-checkable " id="kt_datatable">

            </table>
            <!--end: Datatable-->
        </div>
    </div>


    {{---------------all modal controll-------}}

@if (Auth::user()->can(['charge-report-recharge']))
<div class="modal fade" id="rechargeModal">
   <div class="modal-dialog">
       <div class="modal-content">
           {{Form::open(array('route'=>array('admin.charge-recharge.index'),'class'=>'form-horizontal','id'=>'form-recharge','method'=>'POST'))}}
           <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <i aria-hidden="true" class="ki ki-close"></i>
               </button>
           </div>
           <div class="modal-body">
               {{__('Bạn thực sự muốn gọi lại thẻ này?')}}
           </div>
           <div class="modal-footer">
               <input type="hidden" name="id" id="id-recharge" class="id" value=""/>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
               <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-recharge" data-form="form-delete">{{__('Xác nhận')}}</button>
           </div>
           {{ Form::close() }}
       </div>
   </div>
</div>
@endif
@endsection

{{-- Styles Section --}}
@section('styles')
    <style>

        #group__shop .select2-selection--multiple{
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

    </style>
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
                    pageLength: 100,
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
                            d.find = $('#find').val();
                            d.username = $('#username').val();
                            d.key = $('#key').val();
                            d.amount = $('#amount').val();
                            d.status = $('#status').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.process_started_at = $('#process_started_at').val();
                            d.process_ended_at = $('#process_ended_at').val();
                            d.shop_id = $('.shop_id').val();
                        }
                    },

                    "drawCallback": function (settings) {
                        $(function ()
                        {
                            $('[data-toggle="tooltip"]').tooltip()
                        });
                        var api = this.api();
                        var apiJson = api.ajax.json();
                        var rows = api.rows({page: 'current'}).nodes();

                        $(rows).eq(0).before(
                            '<tr class="group total-allpage">' +
                            '<td colspan="6"><b>Tổng cộng các trang</b></td>' +
                            '<td colspan=""><b>'+number_format(apiJson.recordsFiltered,'.')+' thẻ</b></td>' +
                            '<td colspan=""><b>'+number_format(apiJson.totalSumary.total_declare_amount,'.')+'</b></td>' +
                            '<td colspan=""><b>Đúng: '+number_format(apiJson.totalSumary.total_success,'.')+
                                            '<br>'+
                                            'SMG: '+number_format(apiJson.totalSumary.total_wrong_amount,'.')+'</b></td>' +
                            '<td colspan=""><b>'+number_format(apiJson.totalSumary.total_received_amount,'.')+'</b></td>' +
                            '<td colspan=""><b>'+number_format(apiJson.totalSumary.total_money_received,'.')+'</b></td>' +
                            '<td colspan=""></b></td>' +
                            '</tr>',
                        );
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

                        {data: 'id', title: 'ID'},
                        {data: 'shop_id', title: 'Shop'},
                        {
                            data: 'created_at', title: '{{__('Thời gian')}}',render: function (data, type, row) {

                                var temp="Gửi:</br>"+row.created_at+"<br>";
                                temp+="OK:</br>"+row.process_at;
                                return temp;

                            }
                        },
                        {data: 'username', title: '{{__('Tài khoản')}}'},
                        {data: 'gate_id', title: '{{__('Cổng nạp')}}', render: function (data, type, row) {

                                var arrConfig= {!! json_encode(config('module.telecom.gate_id')) !!};
                                return arrConfig[row.gate_id]??""


                            }
                        },
                        {
                            data: 'pin', title: '{{__('Mã thẻ / Serial')}}' , orderable: false, searchable: false,responsivePriority: 2,
                            render: function (data, type, row) {

                                var temp="<p style=\"white-space: nowrap\"><b>"+row.telecom_key+"</b></p>";
                                temp+="<p style=\"white-space: nowrap\"><b>MT: </b>"+row.pin+"</p>";
                                temp+="<p style=\"white-space: nowrap\"><b>SR: </b>"+row.serial+"</p>";
                                return temp;

                            }
                        },


                        {data: 'declare_amount', title: '{{__('Mệnh giá')}}', render: function (data, type, row) {
                                var temp="<p>"+row.declare_amount+"</p>";
                                return temp;
                            }
                        },
                        {
                            data: 'status', title: '{{__('Trạng thái')}}',responsivePriority: 3,
                            render: function (data, type, row) {
                                var arrConfig= {!! json_encode(config('module.charge.status')) !!};
                                var temp="<div  class=\"\" data-toggle=\"tooltip\" title=\"" + row.response_code +" - "+ row.response_mess+"\">";
                                if (row.status == 1) {
                                    temp+="<p><b class='text-success'>"+number_format(row.amount,',')+"</b></p>";
                                } else if (row.status == 3) {
                                    temp+="<p><b class='text-danger'>"+ "{{config('module.charge.status.3')}}" +"</b></p>";
                                    temp+="<p><b class='text-danger'>MGT:" +number_format(row.amount,',') +"</b></p>";
                                } else {
                                    temp+="<p><b class='text-danger'>"+ (arrConfig[row.status]??"")+"</b></p>";
                                }

                                temp+="</div>";
                                return temp;
                            }
                        },

                        {
                            data: 'real_received_amount', title: '{{__('Thực nhận')}}' , orderable: false, searchable: false,
                            render: function (data, type, row) {

                                var temp="<span class=\"c-font-bold text-primary\">+"+number_format(row.real_received_amount,',')+"</span>"+"<br /> <span class=\"c-font-bold text-primary\"> ("+row.ratio+")</span>";

                                return temp;

                            }
                        },

                        {
                            data: 'money_received', title: '{{__('Tiền C.K Cổng gạch thẻ')}}' , orderable: false, searchable: false,
                            render: function (data, type, row) {

                                var temp="<span class=\"c-font-bold text-primary\">+"+number_format(row.money_received,',')+"</span>"+"<br /> <span class=\"c-font-bold text-primary\"> ("+row.ratio_received+")</span>";

                                return temp;

                            }
                        },


                        {data: 'google_analytics', title: '{{__('Google analytics')}}', render: function (data, type, row) {
                                if (row.google_analytics && row.google_analytics.gclid){
                                    return row.google_analytics.gclid;
                                }
                                return 'NAV';
                            }
                        },

                        { data: 'action',title:'Thao tác', orderable: false, searchable: false }


                    ],


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

                $('#started_at').val(startedAt);
                $('#ended_at').val(endeddAt);
                datatable.draw();
            });
            $('body').on('click','.btn-recharge',function(){
                var id = $(this).data('id');
                $('#rechargeModal #id-recharge').val(id);
                $('#rechargeModal').modal('show');
            })
            $('#form-recharge').submit(function(e){
                e.preventDefault();
                var formSubmit = $(this);
                var url = formSubmit.attr('action');
                var btnSubmit = formSubmit.find(':submit');
                btnSubmit.text('Đang xử lý...');
                btnSubmit.prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: url,
                    cache:false,
                    data: formSubmit.serialize(), // serializes the form's elements.
                    beforeSend: function (xhr) {

                    },
                    success: function (data) {
                        if(data.status == 1){
                            toast(data.message);
                        }
                        else{
                            toast(data.message, 'error');
                        }
                        $('#kt_datatable').DataTable().ajax.reload();
                        $('#rechargeModal #id-recharge').val('');
                        $('#rechargeModal').modal('hide');
                        btnSubmit.text('Xác nhận');
                        btnSubmit.prop('disabled', false);
                    },
                    error: function (data) {
                        toast('{{__('Có lỗi phát sinh vui lòng thử lại')}}', 'error');
                        $('#rechargeModal #id-recharge').val('');
                        $('#rechargeModal').modal('hide');
                        btnSubmit.text('Xác nhận');
                        btnSubmit.prop('disabled', false);
                    },
                    complete: function (data) {

                    }
                })
            });
        });





    </script>



@endsection
