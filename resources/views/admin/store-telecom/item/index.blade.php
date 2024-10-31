{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
        @if (auth()->user()->can('store-telecom-replication') && session('shop_id'))
            <div class="btn-group" style="margin-right:15px">
                <a href="#" type="button" id="btn-replication" class="btn btn-danger font-weight-bolder" data-toggle="modal" data-target="#replicationModal">
                    <i class="flaticon-notes icon-md"></i>
                    {{__('Nhân bản')}}
                </a>
            </div>
        @endif
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
						<input type="text" class="form-control datatable-input" id="title"
							   placeholder="{{__('Tiêu đề')}}">
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


				{{--status--}}
				<div class="form-group col-12 col-sm-6 col-lg-3">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i
									class="la la-calendar-check-o glyphicon-th"></i></span>
						</div>
						{{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.store-telecom.status'),old('status', isset($data) ? $data->status : null),array('id'=>'status','class'=>'form-control datatable-input',))}}
					</div>
				</div>

				    {{--gate_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('gate_id',[''=>'-- '.__('Tất cả cổng').' --']+config('module.'.$module.'.gate_id'),old('status', isset($data) ? $data->gate_id : null),array('id'=>'gate_id','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>
                    {{--shop_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <select name="shop_id[]" class="form-control select2 datatable-input shop_id" id="kt_select2_2" multiple data-placeholder="-- {{__('Tất cả shop')}} --"   style="width: 100%" >
                                {!!\App\Library\Helpers::buildShopDropdownList($shop,null) !!}
                            </select>
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

<div class="card card-custom mt-3">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">
                Thông báo ở mục mua thẻ<i class="mr-2"></i>
            </h3>
        </div>
        <div class="card-toolbar"></div>
    </div>

    <div class="card-body">
        {{Form::open(array('route'=>array('admin.store-telecom.setting'),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data" , 'files' => true))}}

        <div class="tab-content" style="margin-top: 25px;">
            @if(!empty(config('setting_fields', [])) )

                @foreach(config('setting_fields') as $section => $fields)

                    <div class="tab-pane {{Arr::get($fields, 'class')}}" id="{{$section}}" role="tabpanel">
                        @foreach($fields['elements'] as $field)
                            @if($field['name'] == 'sys_store_card_seo' || $field['name'] == 'sys_store_card_title' || $field['name'] == 'sys_store_card_content')
                            @includeIf('admin.setting.fields.' . $field['type'] )
                            @endif
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>

            <div class="m-portlet__foot m-portlet__foot--fit">
                <div class="m-form__actions text-right">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary btn-primary-muathe btn-brand  m-btn m-btn--icon m-btn--wide m-btn--sm formMain__button">
                            <span>
                                <i class="la la-check"></i>
                                <span>Lưu</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

        {{ Form::close() }}
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


<div class="modal fade" id="replicationModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {{Form::open(array('route'=>array('admin.'.$module.'.replication'),'class'=>'form-horizontal','method'=>'POST','id' => 'form-replication'))}}
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> {{__('Nhân bản cấu hình này sang các shop khác')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="kt_tree_3" class="tree-demo"></div>
                 <input type="hidden" id="shop_id" name="shop_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                <button type="submit" class="btn btn-danger m-btn m-btn--custom">{{__('Chấp nhận')}}</button>
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
                            d.status = $('#status').val();
                            d.position = $('#position').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.shop_id = $('.shop_id').val();
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
                        {data: 'shop_id', title: 'Shop'},
                        {
                            data: 'title', title: '{{__('Nhà mạng')}}',
                            render: function (data, type, row) {
                                 return row.title;
                            }
                        },
                        {data: 'key', title: 'Key'},


                        {data: 'image',title:'{{__('Hình ảnh')}}', orderable: false, searchable: false,
                            render: function ( data, type, row ) {
                                if(row.image=="" || row.image==null){

                                    return  "<img class=\"image-item\" src=\"/assets/backend/themes/images/empty-photo.jpg\" style=\"max-width: 40px;max-height: 40px\">";
                                }
                                else{
                                    return  "<img class=\"image-item\" src=\""+MEDIA_URL+row.image+"\" style=\"max-width: 40px;max-height: 40px\">";
                                }
                            }
                        },
                        {data: 'gate_id',title:'{{__('Cổng gạch thẻ')}}', orderable: false, searchable: false,

                            render: function ( data, type, row ) {
                                var arrConfig= {!! json_encode(config('module.'.$module.'.gate_id')) !!}

                                    return arrConfig[row.gate_id]??"";

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
            var jsondata={!! $shop_replication !!};
            $('#kt_tree_3').jstree({
                "plugins": ["wholerow", "checkbox", "types","search"],
                "core": {
                    "dblclick_toggle" : false,
                    "themes": {
                        "responsive": false,
                        "icons":false,
                        "dots": true,
                    },
                    "data": jsondata
                },
                "types": {
                    "default": {
                        "icon": "fa fa-folder text-warning"
                    },
                    "file": {
                        "icon": "fa fa-file  text-warning"
                    }
                },
            })
            .on('changed.jstree', function (e, data) {
                var i, j, r = [];
                for(i = 0, j = data.selected.length; i < j; i++) {
                    r.push(data.instance.get_node(data.selected[i]).id);
                }
                $('#shop_id').val(r.join(','));
            });
            $('form#form-replication').submit(function() {
                var c = confirm("Bạn có chắc chắn muốn nhân bản cấu hình này?");
                return c;
            });

        });

    </script>

    <script>

        $('.ckeditor-source').each(function () {
            var elem_id=$(this).prop('id');
            var height=$(this).data('height');
            height=height!=""?height:150;
            var startupMode= $(this).data('startup-mode');
            if(startupMode=="source"){
                startupMode="source";
            }
            else{
                startupMode="wysiwyg";
            }

            CKEDITOR.replace(elem_id, {
                filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                height:height,
                startupMode:startupMode,
            } );
            CKEDITOR.on('instanceReady', function(ev) {
                var editor = ev.editor;
                editor.dataProcessor.htmlFilter.addRules({
                    elements : {
                        a : function( element ) {
                            if ( !element.attributes.rel ){
                                //gets content's a href values
                                var url = element.attributes.href;

                                //extract host names from URLs (IE safe)
                                var parser = document.createElement('a');
                                parser.href = url;

                                var hostname = parser.hostname;
                                if ( hostname !== window.location.host) {
                                    element.attributes.rel = 'nofollow';
                                    element.attributes.target = '_blank';
                                }
                            }
                        }
                    }
                });
            })
        });

    </script>

@endsection
