{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
        <div class="btn-group">
            <a href="{{route('admin.'.$module.'.create')}}" type="button"  class="btn btn-success font-weight-bolder">
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
            <form class="mb-10">
                <div class="row">
                    {{--ID--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="id" placeholder="{{__('ID')}}">
                        </div>
                    </div>
                    {{--title--}}
                    @if(isset($client))
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="row" style="width: 100%;margin: 0 auto;">
                                <div class="col-auto pl-0 pr-0" style="width: 45px">
                                    <div class="input-group-prepend" style="height: 100%">
                                    <span class="input-group-text" style="border-bottom-right-radius: 0;border-top-right-radius: 0"><i
                                            class="la la-calendar-check-o glyphicon-th"></i></span>
                                    </div>
                                </div>
                                <div class="col-auto pr-0 pl-0" style="width: calc(100% - 45px)">
                                    <select  name="shop_access[]" multiple="multiple" title="Chọn shop cần tìm" class="form-control select2 col-md-5 datatable-input"  data-placeholder="{{__('Chọn shop cần tìm')}}" id="kt_select2_1" style="width: 100%;;border-bottom-left-radius: 0;border-top-left-radius: 0;">
                                        <option value="">Chọn shop cần tìm</option>
                                        @foreach($client as $key => $item)
                                            <option value="{{ $item->domain }}">{{ $item->domain }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($group_shops))
                        <div class="form-group col-12 col-sm-6 col-lg-3">
                            <div class="input-group">
                                <div class="row" style="width: 100%;margin: 0 auto;">
                                    <div class="col-auto pl-0 pr-0" style="width: 45px">
                                        <div class="input-group-prepend" style="height: 100%">
                                    <span class="input-group-text" style="border-bottom-right-radius: 0;border-top-right-radius: 0"><i
                                            class="la la-calendar-check-o glyphicon-th"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-auto pr-0 pl-0" style="width: calc(100% - 45px)">
                                        <select  name="group_shops[]" multiple="multiple" title="Chọn nhóm shop cần tìm" class="form-control select2 col-md-5 datatable-input"  data-placeholder="{{__('Chọn nhóm shop cần tìm')}}" id="kt_select2_3" style="width: 100%;border-bottom-left-radius: 0;border-top-left-radius: 0;">
                                            <option value="">Chọn shop cần tìm</option>
                                            @foreach($group_shops as $key => $group)
                                                <option value="{{ $group->id }}">{{ $group->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.shop.status'),old('status', isset($data) ? $data->status : null),array('id'=>'status','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>


                    @if(!empty(config('module.'.$module.'.position')))

                        {{--position--}}
                        <div class="form-group col-12 col-sm-6 col-lg-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                </div>
                                {{Form::select('position',[''=>'-- '.__('Tất cả vị trí').' --']+config('module.'.$module.'.position'),old('status', isset($data) ? $data->position : null),array('id'=>'position','class'=>'form-control datatable-input',))}}
                            </div>
                        </div>
                    @endif

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

                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select class="form-control datatable-input" id="kitio">
                                <option value="">-- Tất cả trạng thái --</option>
                                <option value="0"> Không hiển thị </option>
                                <option value="1"> Hiển thị </option>
                            </select>
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







@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')

    <script>
        "use strict";
        var datatable;
        function updateServer(id){
            $.ajax({
                type: "POST",
                url: "{{route('admin.shop.update-server')}}",
                data: {
                    '_token':'{{csrf_token()}}',
                    'id':id
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {

                    if (data.status == 1) {

                        if(data.new_ip != "0.0.0.0") {
                            toast('{{__('Cập nhật thành công')}}');
                            $("#dataserver_" + id).html(data.new_ip);
                            $("#dataserver_" + id).attr("href", "/admin/server/" + data.idServer + "/edit");
                            $("#dataserver_" + id).attr("title", "Chỉnh sửa server");
                        }
                        else{
                            toast('{{__('Không thể lấy ip web từ API. Kiểm tra lại API web')}}','error');
                        }
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
        }
        var KTDatatablesDataSourceAjaxServer = function () {
            var initTable1 = function () {


                // begin first table
                datatable = $('#kt_datatable').DataTable({
                    responsive: true,

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
                            d.domain = $('#kt_select2_1').val();
                            d.group = $('#kt_select2_3').val();
                            d.status = $('#status').val();
                            d.position = $('#position').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.kitio = $('#kitio').val();
                        }
                    },

                    buttons: [

                        {
                            text: '<i class="m-nav__link-icon la la-trash"></i> Xóa đã chọn ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatable.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                                    return;
                                }

                                datatable.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {
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
                        {data: 'created_at', title: '{{__('Thời gian')}}'},
                        {
                            data: 'domain', title: '{{__('Tên shop')}}',
                            render: function (data, type, row) {
                                return "<a href=\"https://"+ row.domain + "\" target='_blank'    >" + row.domain + "</a>";
                            }
                        },
                        {
                            data: 'type_information', title: '{{__('Phân loại')}}',
                            render: function (data, type, row) {
                                if (row.type_information == 0) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.shop.type_information.0')}}" + "</span>";
                                } else if (row.type_information == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.shop.type_information.1')}}" + "</span>";
                                } else {
                                    return "Chưa có phân loại";
                                }

                            }
                        },
                        { data: 'server',title:'Server', orderable: false, searchable: false},
                        { data: 'param_tracking',title:'Trackking', orderable: false, searchable: false},
                        {
                            data: 'group', title: '{{__('Nhóm')}}',
                            render: function (data, type, row) {
                                return row.group;
                            }
                        },
                        // { data: 'kitio',title:'Kitio', orderable: false, searchable: false},
                        {data: 'expired_time', title: '{{__('Ngày hết hạn')}}'},
                        { data: 'status',title:'Trạng thái', orderable: false, searchable: false},
                        { data: 'action',title:'Thao tác', orderable: false, searchable: false}

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



            function UpdateStatusClient(id){
                $.ajax({
                        type: "POST",
                        url: "{{route('admin.shop.update-stt')}}",
                        data: {
                            '_token':'{{csrf_token()}}',
                            'id':id,
                        },
                        beforeSend: function (xhr) {

                        },
                        success: function (data) {

                            if (data.status == 1) {
                                toast(data.message);
                            }
                            else {
                                toast(data.message, 'error');
                                $("#kt_datatable").DataTable().ajax.reload();
                            }
                        },
                        error: function (data) {
                            if(data.status === 429) {
                                toast('{{__('Bạn đã thao tác quá nhiều lần, không thể cập nhật')}}', 'error');
                            }
                            else {
                                toast('{{__('Lỗi hệ thống, vui lòng liên hệ QTV để xử lý')}}', 'error');
                            }
                            $("#kt_datatable").DataTable().ajax.reload();

                        },
                        complete: function (data) {

                        }
                    });
            }

            $('body').on('change','.btn-update-stt',function(e){
                e.preventDefault();
                var id = $(this).data('id');
                UpdateStatusClient(id);
            });
        });

    </script>

@endsection
