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

    <input type="text" style="display: none" value="{{$module}}" id="module_group">


    {{---------------all modal controll-------}}

    <!-- list item Modal -->
    <div class="modal fade" id="listItemModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                @include('admin.module.group.additem')
            </div>
        </div>
    </div>

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
                            d.position = $('#position').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
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
                            data: 'title', title: '{{__('Tiêu đề')}}',
                            render: function (data, type, row) {
                                if( row.slug+"" !="null" &&  row.slug!=""){
                                    var temp = "<a href=\"" +ROOT_DOMAIN+"/"+ row.slug + "\" title=\""+row.title+"\"  target='_blank'    >" + row.title + "</a>";
                                    return temp;
                                }
                                else{
                                    var temp = "<a href=\"" +ROOT_DOMAIN + "\" title=\""+row.title+"\"  target='_blank'     >" + row.title + "</a>";
                                    return temp;
                                }

                            }
                        },
                        @if(!empty(config('module.'.$module.'.position')))

                        {data: 'position',title:'{{__('Vị trí')}}', orderable: false, searchable: false,

                            render: function ( data, type, row ) {
                                 var arrConfig= {!! json_encode(config('module.'.$module.'.position')) !!}

                                 return arrConfig[row.position]??"";

                            }
                        },
                        @endif
                        {data: 'locale', title: '{{__('Ngôn ngữ')}}'},

                        {data: 'image',title:'{{__('Hình ảnh')}}', orderable: false, searchable: false,
                            render: function ( data, type, row ) {
                                if(row.image=="" || row.image==null){

                                    return  "<img class=\"image-item\" src=\"/assets/backend/themes/images/empty-photo.jpg\" style=\"max-width: 40px;max-height: 40px\">";
                                }
                                else{
                                    return  "<img class=\"image-item\" src=\""+row.image+"\" style=\"max-width: 40px;max-height: 40px\">";
                                }
                            }
                        },
                        {data: 'order', title: '{{__('Thứ tự')}}'},
                        {
                            data: 'status', title: '{{__('Trạng thái')}}',
                            render: function (data, type, row) {

                                if (row.status == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.'.$module.'.status.1')}}" + "</span>";
                                } else if (row.status == 2) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.'.$module.'.status.2')}}" + "</span>";
                                } else {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.'.$module.'.status.0')}}" + "</span>";
                                }

                            }
                        },

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
                    $(button).text("Xuất excel");
                    $(button).prop('disabled', false);
                    return false;
                });
            });
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


<script>
    jQuery(document).ready(function () {
        const host = window.location.hostname;
        $('#txtSearch').donetyping(function() {
            var find=$(this).val()
            var module = $('input#module_group').val();
            var url ='/admin-1102/'+module+'/search';
            if(find == null || find == "" || find == undefined){
                $('.nav-search-in-value').css('display','none');
                return false
            }

            $.ajax({
                type: "GET",
                url: url,
                data: {
                    find:find
                }, // serializes the form's elements.
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    $('.nav-search-in-value').css('display','block');
                    $('#result-search').html(data);
                },
                error: function (data) {
                    alert("Không kết nối được với máy chủ");
                },
                complete: function (data) {
                }
            });
        }, 400);


        $(document).on('click','.btn-show-item',function(e){
            e.preventDefault();
            var module = $('input#module_group').val();
            var url = '/admin-1102/'+module+'/show-item';
            $('#result-search .rs-item').remove();
            $('#txtSearch').val('');
            var id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    id:id
                },
                beforeSend: function (xhr) {

                },

                success: function (data) {
                    if(data.status == 1){
                        console.log(data.id)
                        $('#listItemModal .modal-body #id-group').val(data.id);
                        let html = '';
                        let parent_id = 0;
                        if(data.data.length > 0){
                            html += '<ol class="dd-list">';
                            $.each(data.data,function(key,value){
                                if (value.item != null ){
                                    if (value.item.status == 1){
                                        html += '<li class="dd-item nested-list-item remove__group' + value.id + '" data-order="' + value.order + '" data-id="' + value.id + '">';
                                        html += '<div class="dd-handle nested-list-handle">';
                                        html += "<span class='la la-arrows-alt'></span>";
                                        html += '</div>';
                                        html += '<div class="nested-list-content">';
                                        html += '<div class="m-checkbox">';
                                        html += '<label class="checkbox checkbox-outline">';
                                        html += '<input  type="checkbox" rel="' + value.id + '" class="children_of_' + value.id + '">';
                                        html += '<span></span>';
                                        html += value.item.title;
                                        html += '</label>';
                                        html += '</div>';

                                        html += '<div class="btnControll">';
                                        html += '<a href="#" class="btn btn-sm btn-danger delete_toggle btn-delete-item" data-id="'+value.id+'">';
                                        html += 'Xóa';
                                        html += '</a>';
                                        html += '</div>';
                                        html += '</div>';
                                        html += "</li>";
                                    }
                                }
                            })
                            html += '</ol>';
                        }
                        else{
                            html += '<tr>';
                            html += 'Không có dữ liệu';
                            html += '</tr>';
                        }
                        $('#listItemModal #nestable').html(html);
                        $('#listItemModal').modal('show');
                    }
                },
                error: function (data) {
                    alert("Không kết nối được với máy chủ");
                },
                complete: function (data) {
                }
            });
        })

//nestable
        $(function () {
            $('.dd').nestable({
                dropCallback: function (details) {

                    var order = new Array();
                    $("li[data-id='" + details.destId + "']").find('ol:first').children().each(function (index, elem) {
                        order[index] = $(elem).attr('data-id');
                    });

                    if (order.length === 0) {
                        var rootOrder = new Array();
                        $("#nestable > ol > li").each(function (index, elem) {
                            rootOrder[index] = $(elem).attr('data-id');
                        });
                    }

                    $.post('{{route('admin.'.$module.'.order')}}',
                        {
                            _token:'{{ csrf_token() }}',
                            source: details.sourceId,
                            destination: details.destId,
                            order: JSON.stringify(order),
                            rootOrder: JSON.stringify(rootOrder)
                        },
                        function (data) {
                            // console.log('data '+data);
                        })
                        .done(function () {

                            $(".success-indicator").fadeIn(100).delay(1000).fadeOut();
                        })
                        .fail(function () {
                        })
                        .always(function () {
                        });
                }
            });


        });

        $(document).on("click",".btnAppend",function(e) {
            e.preventDefault();
            var module = $('input#module_group').val();
            var url = '/admin-1102/'+module+'/update-item';
            var group_id =  $('#listItemModal .modal-body #id-group').val();
            var id= $(this).data('id');
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    group_id:group_id,
                    id:id
                },
                beforeSend: function (xhr) {

                },

                success: function (data) {
                   if(data.status == 1){
                       // console.log(data.data.item.title)
                        let html = "";
                       html += '<li class="dd-item nested-list-item remove__group' + data.data.id + '" data-order="' + data.data.order + '" data-id="' + data.data.id + '">';
                       html += '<div class="dd-handle nested-list-handle">';
                       html += "<span class='la la-arrows-alt'></span>";
                       html += '</div>';
                       html += '<div class="nested-list-content">';
                       html += '<div class="m-checkbox">';
                       html += '<label class="checkbox checkbox-outline">';
                       html += '<input  type="checkbox" rel="' + data.data.id + '" class="children_of_' + data.data.id + '">';
                       html += '<span></span>';
                       html += data.data.item.title;
                       html += '</label>';
                       html += '</div>';

                       html += '<div class="btnControll">';
                       html += '<a href="#" class="btn btn-sm btn-danger  delete_toggle btn-delete-item" data-id="' + data.data.id + '">';
                       html += 'Xóa';
                       html += '</a>';
                       html += '</div>';
                       html += '</div>';
                       html += "</li>";
                        $('#listItemModal #nestable .dd-list').append(html);
                   }
                   else{
                    alert(data.message);
                   }
                },
                error: function (data) {
                    alert("Không kết nối được với máy chủ");
                },
                complete: function (data) {
                }
            });
        });
        $(document).on('click','.btn-delete-item',function(e){
            e.preventDefault();
            var module = $('input#module_group').val();
            var url = '/admin-1102/'+module+'/delete-item';
            var id = $(this).data('id');
            // var group_id = $(this).data('group');
            var confirm = confirmFunction();
            if(confirm == false){
                return false;
            }
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _token: '{{ csrf_token() }}',
                    // group_id:group_id,
                    id:id
                },
                beforeSend: function (xhr) {

                },

                success: function (data) {
                   if(data.status == 1){
                       $('#nestable .remove__group'+id).remove();
                   }
                   else{
                       alert(data.message);
                   }
                },
                error: function (data) {
                    alert("Không kết nối được với máy chủ");
                },
                complete: function (data) {
                }
            });
        })
        function confirmFunction(){
            var txt;
            var r = confirm("Bạn có muốn xóa ?");
            if (r == true) {
                return true;
            } else {
                return false;
            }
        }
    });
</script>
@endsection
