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
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="title"
                                   placeholder="{{__('Tên acc')}}">
                        </div>
                    </div>

                    {{--idkey--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i
                                    class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('idkey',[''=>'-- '.__('Tất cả loại game').' --']+config('module.minigame.game_type'),old('status', isset($data) ? $data->idkey : null),array('id'=>'idkey','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>

                <!--  {{--parent_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select id="parent_id"
                                    class="form-control datatable-input datatable-input-select selectpicker" data-live-search="true"
                                    title="-- {{__('Tất cả phần thưởng')}} --">
                                @if( !empty(old('parent_id')) )
                        {!!\App\Library\Helpers::buildMenuDropdownList($dataItem,old('parent_id')) !!}
                    @else
                        <?php $itSelect = [] ?>
                        {!!\App\Library\Helpers::buildMenuDropdownList($dataItem,$itSelect) !!}
                    @endif
                    </select>
                </div>
            </div> -->


                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.language-nation.status'),old('status', isset($data) ? $data->status : null),array('id'=>'status','class'=>'form-control datatable-input',))}}
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
            <div class="row m--margin-bottom-20">
                <!--  <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                     Tổng số nick thưởng: <b id="total">0</b>
                 </div>
                 <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                    Nick đang hoạt động: <b id="total_active">0</b>
                 </div>
                 <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                     Nick đã khóa: <b id="total_inactive">0</b>
                 </div> -->
                <table  class="table table-bordered table-hover table-checkable " style="width: 30%">
                    <tr>
                        <td>Tổng số nick thưởng</td>
                        <td>&nbsp;&nbsp;&nbsp;<b id="total">0</b></td>
                    </tr>
                    <tr>
                        <td>Nick đang hoạt động</td>
                        <td>&nbsp;&nbsp;&nbsp;<b id="total_active">0</b></td>
                    </tr>
                    <tr>
                        <td>Nick đã khóa</td>
                        <td>&nbsp;&nbsp;&nbsp;<b id="total_inactive">0</b></td>
                    </tr>
                </table>
            </div>
            <br>
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
                    footerCallback: function (row, data, start, end, display) {
                        var json = datatable.ajax.json();
                        $('#total').text(json.total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        $('#total_active').text(json.total_active.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        $('#total_inactive').text((parseInt(json.total) - parseInt(json.total_active)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    },
                    ajax: {
                        url: '{{url()->current()}}' + '?ajax=1',
                        type: 'GET',
                        data: function (d) {
                            d.id = $('#id').val();
                            d.title = $('#title').val();
                            d.status = $('#status').val();
                            d.position = $('#position').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.parent_id = $('#parent_id').val();
                            d.idkey = $('#idkey').val();
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
                        {
                            data: 'author_id', title: '{{__('QTV')}}',
                            render: function (data, type, row) {
                                return row.author_id;
                            }
                        },
                        {
                            data: 'title', title: '{{__('Acc name')}}',
                            render: function (data, type, row) {
                                return row.title;
                            }
                        },
                        {
                            data: 'position', title: '{{__('Acc pass')}}',
                            render: function (data, type, row) {
                                return row.position;
                            }
                        },
                        {
                            data: 'idkey', title: '{{__('Loại game')}}',
                            render: function (data, type, row) {
                                if(row.idkey == 1){
                                    return "{{config('module.minigame.game_type.1')}}";
                                } else if(row.idkey == 2){
                                    return "{{config('module.minigame.game_type.2')}}";
                                }else if(row.idkey == 3){
                                    return "{{config('module.minigame.game_type.3')}}";
                                }else if(row.idkey == 4){
                                    return "{{config('module.minigame.game_type.4')}}";
                                }else if(row.idkey == 5){
                                    return "{{config('module.minigame.game_type.5')}}";
                                }else if(row.idkey == 6){
                                    return "{{config('module.minigame.game_type.6')}}";
                                }else if(row.idkey == 7){
                                    return "{{config('module.minigame.game_type.7')}}";
                                }else if(row.idkey == 8){
                                    return "{{config('module.minigame.game_type.8')}}";
                                }else if(row.idkey == 9){
                                    return "{{config('module.minigame.game_type.9')}}";
                                }else if(row.idkey == 10){
                                    return "{{config('module.minigame.game_type.10')}}";
                                }else if(row.idkey == 11){
                                    return "{{config('module.minigame.game_type.11')}}";
                                }else if(row.idkey == 12){
                                    return "{{config('module.minigame.game_type.12')}}";
                                }else if(row.idkey == 13){
                                    return "{{config('module.minigame.game_type.13')}}";
                                }else if(row.idkey == 14){
                                    return "{{config('module.minigame.game_type.14')}}";
                                }
                            }
                        },
                        {data: 'locale', title: '{{__('Ngôn ngữ')}}'},
                        {data: 'created_at', title: '{{__('Thời gian')}}'},
                        {
                            data: 'status', title: '{{__('Trạng thái')}}',
                            render: function (data, type, row) {
                                if (row.status == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.minigame.status.1')}}" + "</span>";
                                } else {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.minigame.status.0')}}" + "</span>";
                                }
                            }
                        },
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
        });
    </script>



@endsection