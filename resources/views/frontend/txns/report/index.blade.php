{{-- Extends layout --}}
@extends('frontend._layouts.master')
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
            <form class="mb-10">
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
                    {{--trade_type--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                     <span class="input-group-text"><i
                             class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('trade_type',[''=>'-- Tất cả trạng thái --']+config('module.txns.trade_type'),old('trade_type',request('trade_type')),array('id'=>'trade_type','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>
                    {{--is_add--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                     <span class="input-group-text"><i
                             class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('is_add',[''=>'-- Loại giao dịch --']+config('module.txns.is_add'),old('is_add',request('is_add')),array('id'=>'is_add','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>
                    {{--is_add--}}

                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                     <span class="input-group-text"><i
                             class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.txns.status'),old('status',request('status')),array('id'=>'status','class'=>'form-control datatable-input',))}}
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
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-6">
                        <div class="btn-group m-btn-group" role="group" aria-label="...">
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date" >Hôm nay</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::yesterday()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::yesterday()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Hôm qua</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng này</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfMonth()->subMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng trước</a>
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
                            d.email = $('#email').val();
                            d.trade_type = $('#trade_type').val();
                            d.is_add = $('#is_add').val();
                            d.status = $('#status').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.account_type = $('#account_type').val();

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

                        var last=null;
                        var countColumn=datatable.columns(':visible').count();
                        api.column(2, {page:'current'} ).data().each( function ( group, i ) {
                            var currDate= moment(group,"DD/MM/YYYY").format("DD/MM/YYYY");
                            if ( last !== currDate ) {
                                $(rows).eq( i ).before(
                                    "<tr class='group'><td colspan='"+countColumn+"'><b>Ngày "+currDate+"</b></td></tr>"
                                );
                                last = currDate;
                            }
                        } );
                    },


                    buttons: [
                        {
                            "extend": 'excelHtml5',
                            "text": ' <i class="far fa-file-excel icon-md"></i> {{__('Xuất excel')}} ',
                            "action": newexportaction,
                            "exportOptions": {
                                "columns": [1,3,4,5,6,8,10,11,12]
                            }
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

                        {data: 'id', title: 'ID'},//1
                        {
                            data: 'created_at', title: '{{__('Thời gian')}}',render: function (data, type, row) {

                                var temp= moment(row.created_at,"DD/MM/YYYY hh:mm:ss").format("HH:mm:ss");
                                return temp;

                            }
                        },//2
                        {data: 'username', title: '{{__('Tài khoản')}}'},//4
                        {data: 'account_type', title: '{{__('Loại tài khoản')}}', render: function (data, type, row) {
                                var temp="";
                                if (row.account_type == 1) {
                                    temp += "<span class='text-info'>" + "{{config('module.user-qtv.account_type.1')}}" + "</span>";
                                }
                                else if (row.account_type == 2) {
                                    temp+="<span class='text-info'>"+"{{config('module.user.account_type.2')}}"+"</span>";
                                }
                                else if (row.account_type == 3) {
                                    temp+="<span class='text-info'>"+"{{config('module.user-qtv.account_type.3')}}"+"</span>";
                                }
                                return temp;
                            }//5
                        },

                        {data: 'trade_type', title: '{{__('Giao dịch')}}', render: function (data, type, row) {

                                var arrConfig = {!! json_encode(config('module.txns.trade_type')) !!};
                                var temp = "";
                                temp += "<p><b>" + (arrConfig[row.trade_type] ?? "") + "</b></p>";
                                if(row.description+""!="null"){
                                    temp += "<p>" +row.description + "</p>";
                                }
                                return temp;

                            }//6
                        },
                        {
                            data: 'amount', title: '{{__('Số tiền')}}',
                            render: function (data, type, row) {

                                var temp="";
                                if (row.is_add == 1) {
                                    temp+="<span class='text-info'>+"+number_format(row.amount,'.')+"đ</span>";
                                } else {
                                    temp+="<span class='text-danger'>-"+number_format(row.amount,'.')+"đ</span>";
                                }
                                return temp;
                            }
                        },//7
                        {
                            data: 'amount', title: '{{__('Số tiền')}}',visible: false,
                            render: function (data, type, row) {

                                var temp="";
                                if (row.is_add == 1) {
                                    temp+="<span class='text-info'>+ " + row.amount + "</span>";
                                } else {
                                    temp+="<span class='text-danger'>- " + row.amount + "</span>";
                                }
                                return temp;
                            }
                        },//8
                        {
                            data: 'last_balance', title: '{{__('Số dư cuối')}}',
                            render: function (data, type, row) {
                                var temp=number_format(row.last_balance,'.')+"đ";
                                return temp;
                            }
                        },//9
                        {
                            data: 'last_balance', title: '{{__('Số dư cuối')}}',visible: false,
                            render: function (data, type, row) {
                                var temp= row.last_balance;
                                return temp;
                            }
                        },//10
                        {
                            data: 'status', title: '{{__('Trạng thái')}}',responsivePriority: 3,
                            render: function (data, type, row) {

                                if (row.status == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.txns.status.1')}}" + "</span>";
                                } else if (row.status == 2) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.txns.status.2')}}" + "</span>";
                                } else {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.txns.status.0')}}" + "</span>";
                                }
                            }
                        },//11
                        {data: 'created_at', title: '{{__('Thời gian')}}',visible: false},//12
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
        });
    </script>
@endsection