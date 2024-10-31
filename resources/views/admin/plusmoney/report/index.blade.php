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

                    {{--is_add--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>

                            {{Form::select('is_add',[''=>'-- Loại giao dịch --']+config('module.plus_money.is_add'),old('is_add',request('is_add')),array('id'=>'is_add','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>



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
                    {{--source_type--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select name="source_type" id="source_type" class="form-control {{ $errors->has('amount') ? ' is-invalid' : '' }}" required>

                                <option value="">-- Nguồn tiền cộng --</option>
                                <option value="1" {{old('source_type')=="1"?"selected":""}}>
                                    ATM
                                </option>
                                <option value="2" {{old('source_type')=="2"?"selected":""}}>Ví
                                    điện tử
                                </option>
                                <option value="4" {{old('source_type')=="4"?"selected":""}}>
                                    MOMO
                                </option>
                                <option value="5" {{old('source_type')=="5"?"selected":""}}>
                                    Tiền PR
                                </option>
                                <option value="6" {{old('source_type')=="6"?"selected":""}}>
                                    Tiền test
                                </option>
                                <option value="7" {{old('source_type')=="7"?"selected":""}}>
                                    Tiền thẻ lỗi
                                </option>
                                <option value="3" {{old('source_type')=="3"?"selected":""}}>
                                    Khác
                                </option>
                            </select>
                        </div>
                    </div>
                    {{--source_bank--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select name="source_bank" id="source_bank"
                                    class="form-control m-input m-input--air {{ $errors->has('amount') ? ' is-invalid' : '' }}"
                                    required>
                                <option value="">-- Ngân hàng/ví --</option>
                                <option class="c1" value="VIETCOMBANK" {{old('source_bank')=="1"?"selected":""}}>
                                    Vietcombank
                                </option>
                                <option class="c1" value="VIETTINBANK" {{old('source_bank')=="2"?"selected":""}}>
                                    Viettinbank
                                </option>
                                <option class="c1" value="AGRIBANK" {{old('source_bank')=="4"?"selected":""}}>
                                    Agribank
                                </option>
                                <option class="c1" value="TECHCOMBANK" {{old('source_bank')=="5"?"selected":""}}>
                                    Techcombank
                                </option>
                                <option class="c1" value="MBBANK" {{old('source_bank')=="6"?"selected":""}}>
                                    Mbbank
                                </option>
                                <option class="c1" value="BIDV" {{old('source_bank')=="7"?"selected":""}}>
                                    BIDV
                                </option>

                                {{-------}}
                                <option class="c2" value="TCSR" {{old('source_bank')=="TCSR"?"selected":""}}>
                                    TCSR (Thecaosieure.com)
                                </option>
                                <option class="c2" value="TSR" {{old('source_bank')=="TSR"?"selected":""}}>
                                    Tsr(thesieure.com)
                                </option>
                                <option class="c2" value="TKCR" {{old('source_bank')=="TKCR"?"selected":""}}>
                                    Tkcr(tkcr.vn)
                                </option>
                                <option class="c2" value="AZPRO" {{old('source_bank')=="AZPRO"?"selected":""}}>
                                    AZPRO
                                </option>
                                {{----MOMO---}}
                                <option class="c4" value="MOMO2869" {{old('source_bank')=="MOMO2869"?"selected":""}}>
                                    MOMO2869
                                </option>
                                <option class="c4" value="MOMO2442" {{old('source_bank')=="MOMO2442"?"selected":""}}>
                                    MOMO2442
                                </option>

                                <option class="c4" value="MOMO3323" {{old('source_bank')=="MOMO3323"?"selected":""}}>
                                    MOMO3323
                                </option>

                                <option class="c4" value="MOMO2928" {{old('source_bank')=="MOMO2928"?"selected":""}}>
                                    MOMO2928
                                </option>

                                <option class="c4" value="MOMO4666" {{old('source_bank')=="MOMO4666"?"selected":""}}>
                                    MOMO4666
                                </option>

                                <option class="c4" value="MOMO0556" {{old('source_bank')=="MOMO0556"?"selected":""}}>
                                    MOMO0556
                                </option>

                                <option class="c4" value="MOMO9872" {{old('source_bank')=="MOMO9872"?"selected":""}}>
                                    MOMO9872
                                </option>

                                <option class="c4" value="MOMO4555" {{old('source_bank')=="MOMO4555"?"selected":""}}>
                                    MOMO4555
                                </option>
                            </select>
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

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="description" value="{{request('description')}}"
                                   placeholder="{{__('Nội dung')}}">
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
                            d.is_add = $('#is_add').val();
                            d.status = $('#status').val();
                            d.description = $('#description').val();
                            d.source_type = $('#source_type').val();
                            d.source_bank = $('#source_bank').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
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

                        //total first rows
                        $(rows).eq(0).before(
                            '<tr class="group total-allpage">' +
                            '<td colspan="3"><b>Tổng cộng các trang</b></td>' +
                            '<td><b>' +number_format(apiJson.totalSumary.total_add,'.')+" lượt cộng  | "+number_format(apiJson.totalSumary.total_minus,'.')  + " lượt trừ</b></td>"+
                            '<td colspan=""></td>' +
                            '<td colspan=""></td>' +

                            '<td><b>+' +number_format(apiJson.totalSumary.total_add_amount,'.')+" | -"+number_format(apiJson.totalSumary.total_minus_amount,'.')  + "</b></td>"+
                            '<td colspan=""></td>' +
                            '<td colspan=""></td>' +
                            '<td colspan=""></td>' +
                            '<td colspan=""></td>' +
                            '</tr>',
                        );



                        var last=null;
                        var countColumn=datatable.columns(':visible').count();

                        api.column(2, {page:'current'} ).data().each( function ( group, i ) {

                           var currDate= moment(group,"DD/MM/YYYY").format("DD/MM/YYYY");

                            var row=datatable.rows( i ).data();



                            if ( last !== currDate ) {

                                var total_add=0;
                                var total_add_amount=0;
                                var total_minus=0;
                                var total_minus_amount=0;

                                var prevChild =currDate;
                                //duyệt tiếp các phần tử trong group để sum
                                api.column(2, {page:'current'} ).data().each( function ( groupChild, j ) {

                                    var rowChild=datatable.rows( j ).data();

                                    if(prevChild == moment(groupChild,"DD/MM/YYYY").format("DD/MM/YYYY")){
                                        if(rowChild[0].is_add==1){
                                            total_add+=1;
                                            total_add_amount+=parseInt(rowChild[0].amount);

                                        }
                                        else{
                                            total_minus+=1;
                                            total_minus_amount+=parseInt(rowChild[0].amount);

                                        }
                                    }
                                    else{
                                        return false;
                                    }
                                })


                                $(rows).eq( i ).before(
                                    "<tr class='group'><td colspan='"+3+"'><b>Ngày "+currDate+"</b></td>" +
                                    "<td><b>" +total_add+" lượt cộng | "+total_minus +" lượt trừ" + "</b></td>"+
                                    "<td></td>"+
                                    "<td></td>"+
                                    "<td><b>+" +number_format(total_add_amount,'.')+" | -"+number_format(total_minus_amount,'.')  + "</b></td>"+
                                    "<td></td>"+
                                    "<td></td>"+
                                    "<td></td>"+
                                    "<td></td>"+
                                    "</tr>"
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
                            data: 'created_at', title: '{{__('Thời gian')}}',render: function (data, type, row) {

                                var temp= moment(row.created_at,"DD/MM/YYYY hh:mm:ss").format("hh:mm:ss");
                                return temp;

                            }
                        },
                        {
                            data: 'domain', title: '{{__('Shop')}}',render: function (data, type, row) {
                                return row.domain;
                            }
                        },

                        {data: 'is_add', title: '{{__('Loại giao dịch')}}', orderable: false, searchable: false , render: function (data, type, row) {

                                var arrConfig = {!! json_encode(config('module.plus_money.is_add')) !!};
                                var temp="";
                                if (row.is_add == 1) {
                                    temp+="<span class='text-info'><b>"+ (arrConfig[row.is_add] ?? "") +"</b></span>";
                                } else {
                                    temp+="<span class='text-danger'><b>"+ (arrConfig[row.is_add] ?? "") +"</b></span>";
                                }
                                return temp;


                            }
                        },
                        {data: 'processor.username', title: '{{__('Người thao tác')}}', orderable: false, searchable: false,
                            render: function (data, type, row) {
                                var temp=data;
                                return temp;
                            }
                        },
                        {data: 'user.username', title: '{{__('Người nhận')}}', orderable: false, searchable: false,
                            render: function (data, type, row) {
                                var temp=row.user.username??"";
                                return temp;
                            }
                        },
                        {
                            data: 'amount', title: '{{__('Số tiền')}}', orderable: false, searchable: false,
                            render: function (data, type, row) {

                                var temp="";
                                if (row.is_add == 1) {
                                    temp+="<span class='text-info'>+"+number_format(row.amount,'.')+"đ</span>";
                                } else {
                                    temp+="<span class='text-danger'>-"+number_format(row.amount,'.')+"đ</span>";
                                }
                                return temp;
                            }
                        },

                        {
                            data: 'source_type', title: '{{__('Nguồn tiền')}}', orderable: false, searchable: false,
                            render: function (data, type, row) {

                                var source_type="";
                                if (row.source_type == 1){
                                    source_type = "ATM";
                                }
                                else if(row.source_type == 2){
                                    source_type = "Ví điện tử";
                                }
                                else if(row.source_type == 3){
                                    source_type = "Khác";
                                }
                                else if(row.source_type == 4){
                                    source_type = "MOMO";
                                }
                                else if(row.source_type == 5){
                                    source_type = "Tiền PR";
                                }
                                else if(row.source_type == 6){
                                    source_type = "Tiền test";
                                }
                                else if(row.source_type == 7){
                                    source_type = "Tiền thẻ lỗi";
                                }
                                return source_type;

                            }
                        },
                        {
                            data: 'source_bank', title: '{{__('Ngân hàng/ví')}}', orderable: false, searchable: false,
                            render: function (data, type, row) {
                                return row.source_bank;
                            }
                        },

                        {
                            data: 'description', title: '{{__('Chi tiết')}}', orderable: false, searchable: false,
                            render: function (data, type, row) {
                                return row.description;
                            }
                        },
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




        });





    </script>



@endsection
