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
                        <input type="text" class="form-control datatable-input" id="key"
                               placeholder="{{__('Key')}}">
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
						{{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.gift-code.status'),old('status', isset($data) ? $data->status : null),array('id'=>'status','class'=>'form-control datatable-input',))}}
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
		<table class="table table-bordered table-hover table-checkable " id="kt_datatable">
		</table>
	</div>
</div>
 <!-- set value Modal -->
 <div class="modal fade" id="setValueModal"  role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
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
                            d.key = $('#key').val();
                            d.status = $('#status').val();

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
                        {data: 'key', title: 'KEY'},
                        {
                            data: 'title', title: '{{__('Tiêu đề')}}',
                            render: function (data, type, row) {
                                 return row.title;
                            }
                        },
                        {
                            data: 'link', title: '{{__('Link demo')}}',
                            render: function (data, type, row) {
                                if(row.link == null || row.link.length < 1)
                                {
                                    return "Chưa cập nhật link";
                                }
                                else{
                                    const myArray = row.link.split(",");
                                    console.log(myArray.length);
                                    if(myArray.length > 0){
                                        let html ="";
                                        for(let i=0; i< myArray.length; i++){
                                            html += '<a target="_blank" href="'+myArray[i]+'">'+myArray[i]+'</a><br/>';
                                        }
                                        return html;
                                    }
                                }

                            }
                        },
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
        jQuery(document).ready(function () {
            KTDatatablesDataSourceAjaxServer.init();

            $('.datetimepicker-default').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:00',
                useCurrent: true,
                autoclose: true

            });

            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).attr('rel')
                $('#deleteModal .id').attr('value', id);
            });

            $('body').on('click', '.setvalue_toggle', function(e) {

                e.preventDefault();
                $('#setValueModal .modal-content').empty();
                $('#setValueModal .modal-content').load($(this).attr("href"),function(){
                    $('#setValueModal').modal({show:true});
                });


            })
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
