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
                    {{__($page_breadcrumbs[0]['title'].' - cấu hình giải thưởng')}} <i class="mr-2"></i>
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

                    {{--group_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>

                            <select id="roles_id"
                                    class="form-control datatable-input datatable-input-select selectpicker" data-live-search="true"
                                    title="-- {{__('Tất cả loại giải thưởng')}} --">
                                @if( !empty(old('parent_id')) )
                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,old('parent_id')) !!}
                                @else
                                    <?php $itSelect = [] ?>
                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,$itSelect) !!}
                                @endif
                            </select>

                        </div>
                    </div>
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input id="setted" name="setted" type="checkbox"><span></span>&nbsp;Giải thưởng đã cấu hình</label>
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

    <!-- save item Modal -->
    <div class="modal fade" id="saveModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.'.$module.'.setitem',$id),'class'=>'form-horizontal','id'=>'form-save','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn đã cập nhật đủ thông tin cho giải thưởng?')}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="object" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-custom" data-form="form-save">{{__('Cập nhật')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

<style type="text/css">
    .image-item:hover{
        transform: scale(3);
    }
</style>
<button class="btn btn-primary btn-lg toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="info" data-position-class="toast-bottom-right" style='display:none;'>Info Toast</button>




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
                    //"order": [[1, "desc"]],
                    ajax: {
                        url: '{{url()->current()}}' + '?ajax=1',
                        type: 'GET',
                        data: function (d) {

                            d.id = $('#id').val();
                            d.title = $('#title').val();
                            d.position = $('#position').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.setted = $('#setted').is(":checked")?1:0;
                        }
                    },

                    buttons: [
                        {
                            text: ' <i class="far fa-save icon-md"></i> Lưu giải thưởng đã chọn ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatable.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                                    return;
                                }
                                var check = false;
                                var item = null;
                                $('input[data-required=1]').each(function(){
                                    if($(this).val() == "" && $(this).parent().parent().find('input[type=checkbox]').is(':checked')){
                                        console.log('trong:'+$(this).val());
                                        $(this).focus();
                                        item = $(this);
                                        check = true;
                                        return;
                                    }
                                });
                                if(check){
                                    toastr.options =
                                    {
                                        "closeButton" : true,
                                        "progressBar" : true,
                                        'positionClass' : 'toast-top-center',
                                    }
                                    toastr.error("Chưa nhập mục bắt buộc");
                                    item.focus();
                                    return;
                                }
                                var inputArray = [];
                                datatable.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {
                                    if ($(elem).is(':checked') || (!$(elem).is(':checked') && $(elem).parent().next().val()!="")) {
                                        if($(elem).is(':checked')){
                                            var curentRow = $(this).parent().parent().parent();
                                            var id = $(elem).attr('rel');
                                            var limit = curentRow.find('.limit').val();
                                            var bonus_from = curentRow.find('.bonus_from').val();
                                            var bonus_to = curentRow.find('.bonus_to').val();
                                            var percent = curentRow.find('.percent').val();
                                            var try_percent = curentRow.find('.try_percent').val();
                                            var nohu_percent = curentRow.find('.nohu_percent').val();
                                            var order = curentRow.find('.order').val();
                                            var iditemset = curentRow.find('.iditemset').val();
                                        }else{
                                            var curentRow = $(this).parent().parent().parent();
                                            var id = '';
                                            var limit = '';
                                            var bonus_from = '';
                                            var bonus_to = '';
                                            var percent = '';
                                            var try_percent = '';
                                            var nohu_percent = '';
                                            var order = '';
                                            var iditemset = curentRow.find('.iditemset').val();
                                        }

                                        inputArray.push({
                                            'id':id,
                                            'iditemset':iditemset,
                                            'params':{'limit':limit,
                                            'bonus_from':bonus_from,
                                            'bonus_to':bonus_to,
                                            'percent':percent,
                                            'try_percent':try_percent,
                                            'nohu_percent':nohu_percent},
                                            'order':order
                                        })
                                    }
                                })
                                $('#saveModal').modal('toggle');
                                $('#saveModal .id').attr('value', JSON.stringify(inputArray));

                            }
                        },
                        // {
                        //     "extend": 'excelHtml5',
                        //     "text": ' <i class="far fa-file-excel icon-md"></i> {{__('Xuất excel')}} ',
                        //     "action": newexportaction,
                        // },





                    ],
                    columns: [
                        {
                            data: null,
                            title: 'Chọn',
                            orderable: false,
                            searchable: false,
                            width: "20px",
                            class: "ckb_item",
                            render: function (data, type, row) {
                                return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input '+(row.children.length>0?"checked":"")+' type="checkbox" rel="' + row.id + '" id="">&nbsp<span></span></label><input type="hidden" class="iditemset" name="iditemset" value="'+(row.children.length>0?row.children[0].id:'')+'">';

                            }
                        },

                        {data: 'id', title: 'ID'},
                        {
                            data: 'title', title: '{{__('Tên giải thưởng')}}',
                            render: function (data, type, row) {
                                return row.title;
                            }
                        },

                        {
                            data: "groups", title: '{{__('Loại giải thưởng')}}', orderable: false,
                            render: function (data, type, row) {
                                var temp = "";
                                $.each(row.groups, function (index, value) {
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
                            data: 'children', title: '{{__('Số lượng')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field limit' data-field='limit' data-required='1' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' value='" + (row.children.length>0?(row.children[0].params.limit!=null?row.children[0].params.limit:''):'') + "' style='width:80px'>";
                            }
                        },
                        {
                            data: 'children', title: '{{__('Giá trị bonus từ')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field bonus_from' data-field='bonus_from' data-required='0' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' value='" + (row.children.length>0?(row.children[0].params.bonus_from!=null?row.children[0].params.bonus_from:''):'') + "' style='width:80px'>";
                            }
                        },
                        {
                            data: 'children', title: '{{__('Giá trị bonus đến')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field bonus_to' data-field='bonus_to' data-required='0' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' value='" + (row.children.length>0?(row.children[0].params.bonus_to!=null?row.children[0].params.bonus_to:''):'') + "' style='width:80px'>";
                            }
                        },
                        {
                            data: 'children', title: '{{__('Vị trí')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field order' data-field='order' data-required='1' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' value='" + (row.children.length>0?row.children[0].order:'') + "' style='width:50px'>";
                            }
                        },
                        {
                            data: 'params', title: '{{__('Phần trăm')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field percent' data-field='percent' data-required='1' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' value='" + (row.children.length>0?(row.children[0].params.percent!=null?row.children[0].params.percent:''):'') + "' style='width:55px'>";
                            }
                        },
                        {
                            data: 'params', title: '{{__('% chơi thử')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field try_percent' data-field='try_percent' data-required='0' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' value='" + (row.children.length>0?(row.children[0].params.try_percent!=null?row.children[0].params.try_percent:''):'') + "' style='width:55px'>";
                            }
                        },
                        {
                            data: 'params', title: '{{__('% nổ hũ')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field nohu_percent' data-field='nohu_percent' data-required='0' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' value='" + (row.children.length>0?(row.children[0].params.nohu_percent!=null?row.children[0].params.nohu_percent:''):'') + "' style='width:55px'>";
                            }
                        },

                        {data: 'locale', title: '{{__('Ngôn ngữ')}}'},

                        {data: 'image',title:'{{__('Hình ảnh')}}', orderable: false, searchable: false,
                            render: function ( data, type, row ) {
                                if(row.image=="" || row.image==null){

                                    return  "<img class=\"image-item\" src=\"/assets/backend/themes/images/empty-photo.jpg\" style=\"max-width: 70px\">";
                                }
                                else{
                                    return  "<img class=\"image-item\" src=\""+MEDIA_URL+row.image+"\" style=\"max-width: 70px\">";
                                }
                            }
                        }
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
                    var field=$(this).data('field');
                    var id=$(this).data('id');
                    if(id==''){
                        return;
                    }
                    var required=$(this).data('required');

                    var value=$(this).val();
                    $.ajax({
                        type: "POST",
                        url: '{{route('admin.minigame-category.updatefield')}}',
                        data: {
                            '_token':'{{csrf_token()}}',
                            'field':field,
                            'id':id,
                            'value':value,
                            'required' :required
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

                                toast(data.message, 'error');
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
