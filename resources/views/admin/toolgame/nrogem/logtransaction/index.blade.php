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
            <form class="mb-10" action="{{route('admin.nrogem-export.index')}}" method="post">
                {{ csrf_field() }}
                <div class="row">
                    {{--ID--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" name="id" class="form-control datatable-input" id="id" value="{{request('id')}}" placeholder="{{__('ID')}}">
                        </div>
                    </div>
                    {{--username--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" name="username" class="form-control datatable-input" id="username" value="{{request('username')}}"
                                   placeholder="{{__('Tài khoản')}}">
                        </div>
                    </div>

                    {{--ver--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" name="ver" class="form-control datatable-input" id="ver" value="{{request('ver')}}"
                                   placeholder="{{__('Verbot')}}">
                        </div>
                    </div>


                    {{--server--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" name="server" class="form-control datatable-input" id="server" value="{{request('server')}}"
                                   placeholder="{{__('Server')}}">
                        </div>
                    </div>

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('status',[''=>'-- Tất cả trạng thái (Order) --']+config('module.service-purchase.status'),old('status',request('status')),array('id'=>'status','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>

                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select id="status_nrogem" name="status_nrogem" class="form-control">
                                <option value="">-- Tất cả trạng thái (tool) --</option>
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
                        Số ngọc bán: <b id="total_gem">0</b> -  Số ngọc nạp: <b id="total_gem_nap">0</b>
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
                    dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
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
                        url: '{{url()->current()}}' + '?ajax=1',
                        type: 'GET',
                        data: function (d) {

                            d.id = $('#id').val();
                            d.find = $('#find').val();
                            d.username = $('#username').val();
                            d.group_id = $('#group_id').val();
                            d.amount = $('#amount').val();
                            d.server = $('#server').val();
                            d.status = $('#status').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.shop_id = $('.shop_id').val();
                            d.status_nrogem = $('#status_nrogem').val();
                            d.finished_started_at = $('#finished_started_at').val();
                            d.finished_ended_at = $('#finished_ended_at').val();
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


                        $('#total_record').text(number_format(apiJson.totalSumary.total_record,'.'));
                        $('#total_gem').text(number_format(apiJson.totalSumary.total_gem,'.'));
                        $('#total_gem_nap').text(number_format(apiJson.totalSumary.total_gem_nap,'.'));
                        // $('#total_price').text(number_format(apiJson.totalSumary.total_price,'.'));
                        // $(rows).eq(0).before(
                        //     '<tr class="group total-allpage">' +
                        //     '<td colspan="6"><b>Tổng cộng các trang</b></td>' +
                        //     '<td colspan=""><b>'+number_format(apiJson.recordsFiltered,'.')+' thẻ</b></td>' +
                        //     '<td colspan=""><b>'+number_format(apiJson.totalSumary.total_declare_amount,'.')+'</b></td>' +
                        //     '<td colspan=""><b>Đúng: '+number_format(apiJson.totalSumary.total_success,'.')+
                        //     '<br>'+
                        //     'SMG: '+number_format(apiJson.totalSumary.total_wrong_amount,'.')+'</b></td>' +
                        //     '<td colspan=""><b>'+number_format(apiJson.totalSumary.total_received_amount,'.')+'</b></td>' +
                        //     '<td colspan=""></td>' +
                        //     '</tr>',
                        // );
                    },


                    buttons: [
                        {{--{--}}
                        {{--    "extend": 'excelHtml5',--}}
                        {{--    "text": ' <i class="far fa-file-excel icon-md"></i> {{__('Xuất excel')}} ',--}}
                        {{--    "action": newexportaction,--}}
                        {{--},--}}
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
                            data: 'id', title: '{{__('ID')}}'
                            ,render: function (data, type, row) {

                                if(row.order){
                                    return row.id +'<br/> Đơn hàng: '+row.order.id+'<br/> Requet id: '+row.order.request_id_customer;
                                }else{
                                    return row.id;
                                }

                            }
                        },
                        {data: 'username', title: 'Người order'},
                        {
                            data: 'order', title: '{{__('Số tiền')}}',render: function (data, type, row) {
                                let html = '';
                                if (row.order){
                                    if (row.order.price){
                                        html += number_format(row.order.price,',');
                                    }

                                }
                                return html;
                            }
                        },

                        {data: 'ver', title: 'Ver'},
                        {data: 'server', title: 'Server'},
                        {data: 'bot_handle', title: 'Tên bot xử lý'},
                        {data: 'uname', title: 'Tên nhân vật'},
                        {
                            data: 'c_truoc', title: '{{__('Trước G.D')}}',render: function (data, type, row) {
                                return number_format(row.c_truoc,',');
                            }
                        },
                        {
                            data: 'gem', title: '{{__('Số ngọc')}}',render: function (data, type, row) {
                                var temp="";
                                if(row.status=='danap'){
                                    temp="<span class=\"c-font-bold text-success\">+"+number_format(row.gem,',')+"</span>";
                                }
                                else{
                                    temp="<span class=\"c-font-bold text-danger\">-"+number_format(row.gem,',')+"</span>";
                                }
                                return temp;
                            }
                        },
                        {
                            data: 'c_sau', title: '{{__('Sau G.D')}}',render: function (data, type, row) {
                                return number_format(row.c_sau,',');
                            }
                        },

                        {
                            data: 'info_item', title: '{{__('Thông tin Item')}}',render: function (data, type, row) {
                                return row.info_item
                            }
                        },
                        {
                            data: 'status', title: '{{__('Trạng thái đơn hàng')}}',render: function (data, type, row) {
                                var temp="";
                                if(row.status){
                                    if (row.status == "danap" || row.status == "danhanngoc" || row.status == "loichuyenngoc"){
                                        temp= "<span class='badge badge-success'>Hoàn tất</span>";
                                    }else if (row.status == "muanhamitem"){
                                        if (row.order){
                                            if (row.order.status == 4){
                                                temp= "<span class='badge badge-success'>Hoàn tất</span>";
                                            }else if (row.order.status == 9){
                                                temp= "<span class='badge badge-warning'>Xử lý thủ công</span>";
                                            }else{
                                                temp= "<span class='badge badge-danger'>Thất bại</span>";
                                            }
                                        }

                                    }else if (row.status =="taikhoansai" || row.status =="koosieuthi" || row.status =="matitem" || row.status =="kconhanvat" || row.status =="thieungoc" ||
                                        row.status == "caimk2" || row.status =="hanhtrangday" || row.status =="khongcoitemkigui" || row.status =="kodusucmanh" || row.status =="tamhetngoc" || row.status =="dahuybo"){
                                        temp= "<span class='badge badge-danger'>Thất bại</span>";
                                    }else {
                                        temp= "<span class='badge badge-warning'>Đang chờ</span>";
                                    }
                                }

                                return temp;
                            }
                        },
                        {
                            data: 'status', title: '{{__('Trạng thái tool')}}',render: function (data, type, row) {
                                var temp="";
                                if(row.status){
                                    if (row.status == "danap"){
                                        temp= "<b class='text-info'>Đã nạp</b>";
                                    }else if (row.status == "danhanngoc"){
                                        temp= "<b class='text-info'>Đã nhận ngọc</b>";
                                    }else if (row.status == "muanhamitem"){
                                        temp= "<b class='text-danger'>Mua nhầm item</b>";
                                    }else if (row.status == "tamhetngoc"){
                                        temp= "<b class='text-danger'>Tạm hết ngọc</b>";
                                    }else if (row.status == "thieungoc"){
                                        temp= "<b class='text-danger'>Thiếu ngọc</b>";
                                    }
                                    else if (row.status == "taikhoansai"){
                                        temp= "<b class='text-danger'>Tài khoản sai</b>";
                                    }else if (row.status == "kodusucmanh"){
                                        temp= "<b class='text-danger'>Không sức mạnh</b>";
                                    }else if (row.status == "koosieuthi"){
                                        temp= "<b class='text-danger'>Không siêu thị</b>";
                                    }else if (row.status == "kconhanvat"){
                                        temp= "<b class='text-danger'>Không có nhân vật</b>";
                                    }else if (row.status == "caimk2"){
                                        temp= "<b class='text-danger'>Cài mật khẩu cấp 2</b>";
                                    }else if (row.status == "matitem"){
                                        temp= "<b class='text-danger'>Mất item</b>";
                                    }else if (row.status == "dahuybo"){
                                        temp= "<b class='text-danger'>Thất bại</b>";
                                    }

                                }else {
                                    temp= "<b class='text-warning'>Đang chờ</b>";
                                }

                                return temp;
                            }
                        },
                        {
                            data: 'created_at', title: '{{__('Thời gian')}}',render: function (data, type, row) {
                                return row.created_at;
                            }
                        },
                        {
                            data: 'updated_at', title: '{{__('Thời gian cập nhật')}}',render: function (data, type, row) {
                                return row.updated_at;
                            }
                        },

                        // { data: 'action',title:'Thao tác', orderable: false, searchable: false }


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

                $('#finished_started_at').val(startedAt);
                $('#finished_ended_at').val(endeddAt);
                datatable.draw();
            });
        });





    </script>



@endsection
