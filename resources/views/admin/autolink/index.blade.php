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
                                   placeholder="{{__('Tiêu đề')}}">
                        </div>
                    </div>


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

    <div class="modal fade" id="urlModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Danh sách link đã chạy.')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body scroll-default">
                    <table class="table table-bordered table-hover table-checkable dataTable no-footer dtr-inline">
                        <thead>
                        <tr role="row">
                            <th class="sorting_desc" style="width: 13px;">STT</th>
                            <th class="sorting" colspan="1" style="width: 46px;">URL</th>
                        </tr>
                        </thead>
                        <tbody class="data_url">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                </div>
            </div>
        </div>
    </div>


    <style>

        .scroll-default{
            padding-right: 8px;
            max-height: 540px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .scroll-default:hover::-webkit-scrollbar-thumb{
            background-color: #DCDEE9;
        }

        .scroll-default::-webkit-scrollbar-track
        {
            position: absolute;
            top: 100px;
            left: -60px;
            /*-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);*/
            background-color:  #ffffff;
            border: none;
        }

        .scroll-default::-webkit-scrollbar
        {
            width: 8px;
            border: none;
        }

        .scroll-default::-webkit-scrollbar-thumb
        {
            /*Màu thanh sroll*/
            background: #BCBFD6;
            border-radius: 100px;
            border: none;
            margin-left: 20px;
            height: 20px;
        }

    </style>
@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')

    <script>

        $('body').on('click','.show_url',function(e) {
            e.preventDefault();

            var id = $(this).data('id');
            var shop_id = $(this).data('shop');

            $.ajax({
                url: '/admin/auto-link/show-url',
                datatype:'json',
                data:{
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                    shop_id: shop_id,
                },
                type: 'post',
                success: function (data) {
                    if(data.status == 1){

                        $('#urlModal .data_url').html(data.html);

                        $('#urlModal').modal('show');
                    }else{


                    }
                },
                error: function(){
                    alert("error");
                }
            })


            // var arr_url = JSON.parse($(this).data('url'));
            //
            // console.log(arr_url);



        })

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
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                        }
                    },

                    buttons: [
                            @if($module == 'article' || $module == 'advertise')
                        {
                            text: '<i class="fas fa-plus-circle icon-md"></i> Clone {{ $module }} ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var allcate = '';
                                var total = datatable.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                                    return;
                                }

                                datatable.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {
                                    if ($(elem).is(':checked')) {
                                        allSelected = allSelected + $(elem).attr('rel');
                                        allcate = allcate + $(elem).data('cate');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                            allcate = allcate + ',';
                                        }
                                    }
                                })
                                $('#duplicateModal').modal('toggle');
                                $('#duplicateModal .id').attr('value', allSelected);
                                $('#duplicateModal .cate').attr('value', allcate);
                            }
                        },
                            @endif
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
                            data: 'title', title: '{{__('Tiêu đề')}}',
                            render: function (data, type, row) {

                                var temp = "<span>" + row.title + "</span>";
                                return temp;
                            }
                        },
                        {
                            data: "group_id", title: '{{__('Danh mục')}}', orderable: false,
                            render: function (data, type, row) {
                                var group_id = row.group_id;

                                var temp = "";
                                if (group_id){
                                    if (group_id == 1){
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-success \">Minigame</span>";
                                    }else if (group_id == 0){
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-success \">Dịch vụ</span>";
                                    }else if (group_id == 2){
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-success \">Bài viết</span>";
                                    }else if (group_id == 2){
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-success \">Nick</span>";
                                    }
                                }else{
                                    temp += "<span class=\"label label-pill label-inline label-center mr-2  label-success \">Bài viết</span>";
                                }

                                return temp;
                            }
                        },
                        {
                            data: 'author', title: '{{__('Tác giả')}}',
                            render: function (data, type, row) {

                                if (row.author){
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-primary \">" + row.author.username + "</span>";
                                }else {
                                    return "";
                                }
                            }
                        },
                        {
                            data: 'link_type', title: '{{__('Phân loại')}}',
                            render: function (data, type, row) {

                                if (row.link_type == 1){
                                    var temp = "<span>Internal</span>";
                                    return temp;
                                }else {
                                    var temp = "<span>External</span>";
                                    return temp;
                                }
                            }
                        },
                        {
                            data: 'status', title: '{{__('Trạng thái')}}',
                            render: function (data, type, row) {

                                if (row.status == 1) {
                                    return '<span class="switch switch-outline switch-icon switch-success btn-update-stt" data-id="' + row.id + '">\n' +
                                        '<label><input type="checkbox" name="status" checked /><span></span></label>\n' +
                                        '</span>';
                                } else if (row.status == 0) {
                                    return '<span class="switch switch-outline switch-icon switch-success btn-update-stt" data-id="' + row.id + '">\n' +
                                        '<label><input type="checkbox" name="status" /><span></span></label>\n' +
                                        '</span>';
                                }

                            }
                        },
                        {
                            data: 'shop', title: '{{__('Điểm bán')}}',
                            render: function (data, type, row) {

                                if (row.shop){
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-primary \">" + row.shop.domain + "</span>";
                                }else {
                                    return "";
                                }
                            }
                        },
                        {data: 'percent_dofollow', title: '{{__('Số lượng')}}'},
                        {data: 'created_at', title: '{{__('Thời gian')}}'},
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

            function UpdateStatusAutoLink(id){
                $.ajax({
                    type: "POST",
                    url: "{{route('admin.auto-link.update-stt')}}",
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
                            toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
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

                UpdateStatusAutoLink(id);

            });

        });





    </script>



@endsection

