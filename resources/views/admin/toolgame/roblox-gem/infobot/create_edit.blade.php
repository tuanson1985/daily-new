@extends('admin._layouts.master')
@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.'.$module.'.index')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>



        <div class="btn-group">
            <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
                <i class="ki ki-check icon-sm"></i>
                @if(isset($data))
                    {{__('Cập nhật')}}
                @else
                    {{__('Thêm mới')}}
                @endif

            </button>
            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split btn-submit-dropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <ul class="nav nav-hover flex-column">
                    <li class="nav-item">
                        <button  class="nav-link btn-submit-custom" data-form="formMain">
                            <i class="nav-icon flaticon2-reload"></i>
                            <span class="ml-2">
                                 @if(isset($data))
                                    {{__('Cập nhật & tiếp tục')}}
                                @else
                                    {{__('Thêm mới & tiếp tục')}}
                                @endif
                            </span>
                        </button>
                    </li>

                </ul>
            </div>
        </div>






    </div>
@endsection

{{-- Content --}}
@section('content')

    @if(isset($data))
        {{Form::open(array('route'=>array('admin.'.$module.'.update',$data->id),'method'=>'PUT','id'=>'formMain','enctype'=>"multipart/form-data" , 'files' => true))}}
    @else
        {{Form::open(array('route'=>array('admin.'.$module.'.store'),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
    @endif
    <input type="hidden" name="submit-close" id="submit-close">

    <div class="row">
        <div class="col-lg-9">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                        </h3>
                        @if(isset($data))
                        <div class="btn-group">
                            <a href="/admin/roblox-gem-info-bot/add-units-{{ $data->id }}"  class="btn btn-success font-weight-bolder">
                                <i class="fas fa-plus-circle icon-md"></i>
                                {{__('Thêm units')}}
                            </a>
                        </div>
                        @endif
                    </div>

                </div>

                <div class="card-body">
                    {{-----acc------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Loại bot') }}</label>
                            {{Form::select('type_bot',[''=>'-- Tất cả loại bot --']+config('module.service.type_bot'),old('type_bot', isset($data) ? $data->type_bot : null),array('id'=>'type_bot','class'=>'form-control datatable-input',))}}
                            @if ($errors->has('type_bot'))
                                <span class="form-text text-danger">{{ $errors->first('type_bot') }}</span>
                            @endif
                        </div>
                    </div>



                    {{-----ver------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Máy chủ') }}</label>
                            <input type="text" id="ver_gen_slug" name="ver" value="{{ old('ver', isset($data) ? $data->ver : null) }}"
                                   placeholder="{{ __('Ver') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ver') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ver'))
                                <span class="form-text text-danger">{{ $errors->first('ver') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-----acc------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tên bot') }}</label>
                            <input type="text" id="acc_gen_slug" name="acc" value="{{ old('acc', isset($data) ? $data->acc : null) }}"
                                   placeholder="{{ __('Tên tài khoản') }}" maxlength="120"
                                   class="form-control {{ $errors->has('acc') ? ' is-invalid' : '' }}">
                            @if ($errors->has('acc'))
                                <span class="form-text text-danger">{{ $errors->first('acc') }}</span>
                            @endif
                        </div>
                    </div>

                    @php
                        $units = null;
                        if(isset($data)){
                            if (!empty($data->roblox_bot_item)){
                                $units = $data->roblox_bot_item;
                            }
                        }
                    @endphp
                    @if(isset($units) && count($units))
                        <div class="form-group row">
                            <div class="col-md-12" style="padding-top: 8px;padding-bottom: 8px">
                                <label for="locale" style="font-weight: 700">{{ __('Danh sách units:') }}</label>
                                <div class="row data__redirect">
                                    <div class="col-md-12" style="padding-left: 0;padding-right: 0">
                                        <div class="row" style="width: 100%;margin: 0 auto">
                                            <div class="col-12" style="position: relative;padding-top: 8px">
                                                <table class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Tên vật phẩm</th>
                                                        <th scope="col">Số lượng</th>
                                                        <th scope="col">Loại vật phẩm</th>
                                                        <th scope="col">Trạng thái</th>
                                                        <th scope="col">Hành động</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($units??[] as $index => $item)
                                                        <tr>
                                                            <th scope="row">{{ $index + 1 }}</th>
                                                            <td>{{ $item->title }}</td>
                                                            <td>{{ $item->quantity }}</td>
                                                            <td>
                                                                {{ config('module.service.type_item.'.$item->type_item) }}
                                                            </td>
                                                            <td>
                                                                @if($item->status == 1)
                                                                    <span class="switch switch-outline switch-icon switch-success btn-update-stt" data-id="{{ $item->id }}" style="margin-left: 12px">
                                                                        <label>
                                                                            <input type="checkbox" name="status" checked="checked"><span></span>
                                                                        </label>
                                                                    </span>
                                                                @elseif($item->status == 0)
                                                                    <span class="switch switch-outline switch-icon switch-success btn-update-stt" data-id="{{ $item->id }}" style="margin-left: 12px">
                                                                        <label>
                                                                            <input type="checkbox" name="status" ><span></span>
                                                                        </label>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="/admin/roblox-gem-info-bot/add-units-{{ $data->id }}/{{ $item->id }}"><i class="la la-edit edit_unit" data-id="{{ $item->id }}" style="cursor: pointer;font-size: 24px;color: black;margin-left: 8px"></i></a>
                                                                <i class="la la-plus plus_unit" data-id="{{ $item->id }}" style="cursor: pointer;font-weight: 700;font-size: 24px;color: black;margin-left: 8px"></i>
                                                                <i class="la la-minus minus_unit" data-id="{{ $item->id }}" style="cursor: pointer;font-weight: 700;font-size: 24px;color: black;margin-left: 8px"></i>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            Trạng thái <i class="mr-2"></i>
                        </h3>
                    </div>
                </div>

                <div class="card-body">
                    {{-- status --}}
                    <div class="form-group row">

                        <div class="col-12 col-md-12">
                            <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                            {{Form::select('status',[1=>'Hoạt động',0=>'Ngừng hoạt động'] ,old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
                            @if($errors->has('status'))
                                <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{ Form::close() }}

    <!-- delete item Modal -->
    <div class="modal fade" id="plusUnits">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.toolgame.roblox-gem-info-bot.add-units.update-push-quantity',0),'class'=>'form-horizontal','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Số lượng') }}</label>
                            <input type="number" name="quantity_modal" value=""
                                   placeholder="{{ __('Số lượng') }}" maxlength="120"
                                   class="form-control quantity_modal {{ $errors->has('quantity_modal') ? ' is-invalid' : '' }}">
                            @if ($errors->has('quantity_modal'))
                                <span class="form-text text-danger">{{ $errors->first('quantity_modal') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-success m-btn m-btn--custom">{{__('Xác nhận')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="minusUnits">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.toolgame.roblox-gem-info-bot.add-units.update-minus-quantity',0),'class'=>'form-horizontal','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Số lượng') }}</label>
                            <input type="number" name="quantity_modal" value=""
                                   placeholder="{{ __('Số lượng') }}" maxlength="120"
                                   class="form-control quantity_modal {{ $errors->has('quantity_modal') ? ' is-invalid' : '' }}">
                            @if ($errors->has('quantity_modal'))
                                <span class="form-text text-danger">{{ $errors->first('quantity_modal') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-success m-btn m-btn--custom">{{__('Xác nhận')}}</button>
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


    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>
    <script>
        $(document).ready(function () {

            $('body').on('change','.btn-update-stt',function(e){
                e.preventDefault();
                var id = $(this).data('id');
                UpdateStatusClient(id);
            })

            $('body').on('click', '.btn__redirect', function (e) {
                let check_unit = false;
                $('.units').each(function (i, e) {
                    let value = $(this).val();
                    if (!value){
                        check_unit = true;
                    }
                });

                if(check_unit){
                    toast('{{__('Vui lòng điền đầy đủ thông tin units.')}}', 'error');
                    return false;
                }

                e.preventDefault();

                var html = `
                    <div class="col-md-12" style="padding-left: 0;padding-right: 0">
                        <div class="row" style="width: 100%;margin: 0 auto">
                            <div class="col-6" style="position: relative;padding-top: 8px">
                                <input type="text" class="form-control units" name="units[]" value="">
                                <i class="la la-trash thungrac" style="position: absolute;top: 16px;right: -12px;font-size: 18px;cursor: pointer"></i>
                            </div>
                        </div>
                    </div>
                `;

                $('.data__redirect').append(html);
            })
            $('body').on('click', '.plus_unit', function (e) {
                let id = $(this).data('id');
                $('#plusUnits .id').val(id);
                $('#plusUnits').modal('show');
            })

            $('body').on('click', '.minus_unit', function (e) {
                let id = $(this).data('id');
                $('#minusUnits .id').val(id);
                $('#minusUnits').modal('show');
            })

            function UpdateStatusClient(id){
                $.ajax({
                    type: "POST",
                    url: "{{route('admin.toolgame.roblox-gem-info-bot.add-units.update-status-quantity')}}",
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
                        }
                    },
                    error: function (data) {
                        if(data.status === 429) {
                            toast('{{__('Bạn đã thao tác quá nhiều lần, không thể cập nhật')}}', 'error');
                        }
                        else {
                            toast('{{__('Lỗi hệ thống, vui lòng liên hệ QTV để xử lý')}}', 'error');
                        }

                    },
                    complete: function (data) {
                        $("#kt_datatable_attribute").DataTable().ajax.reload();
                    }
                });
            }
        });

        "use strict";
        $(document).ready(function () {
            //btn submit form
            $('.btn-submit-custom').click(function (e) {
                e.preventDefault();
                var btn = this;

                let check_unit = false;
                $('.units').each(function (i, e) {
                    let value = $(this).val();
                    if (!value){
                        check_unit = true;
                    }
                });

                $(".btn-submit-custom").each(function (index, value) {
                    KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                });
                $('.btn-submit-dropdown').prop('disabled', true);
                //gắn thêm hành động close khi submit
                $('#submit-close').val($(btn).data('submit-close'));
                var formSubmit = $('#' + $(btn).data('form'));
                formSubmit.submit();
            });


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
            });
            $('.ckeditor-basic').each(function () {
                var elem_id=$(this).prop('id');
                var height=$(this).data('height');
                height=height!=""?height:150;
                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                    height:height,
                    removeButtons: 'Source',
                } );
            });

            // Image choose item
            $(".ck-popup").click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.ck-parent');

                var elemThumb = parent.find('.ck-thumb');
                var elemInput = parent.find('.ck-input');
                var elemBtnRemove = parent.find('.ck-btn-remove');
                CKFinder.modal({
                    connectorPath: '{{route('admin.ckfinder_connector')}}',
                    resourceType: 'Images',
                    chooseFiles: true,

                    width: 900,
                    height: 600,
                    onInit: function (finder) {
                        finder.on('files:choose', function (evt) {
                            var file = evt.data.files.first();
                            var url = file.getUrl();
                            elemThumb.attr("src", MEDIA_URL+url);
                            elemInput.val(url);

                        });
                    }
                });
            });
            $(".ck-btn-remove").click(function (e) {
                e.preventDefault();

                var parent = $(this).closest('.ck-parent');

                var elemThumb = parent.find('.ck-thumb');
                var elemInput = parent.find('.ck-input');
                elemThumb.attr("src", "/assets/backend/themes/images/empty-photo.jpg");
                elemInput.val("");

            });

            // Image extenstion choose item
            $(".ck-popup-multiply").click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.ck-parent');
                var elemBoxSort = parent.find('.sortable');
                var elemInput = parent.find('.image_input_text');
                CKFinder.modal({
                    connectorPath: '{{route('admin.ckfinder_connector')}}',
                    resourceType: 'Images',
                    chooseFiles: true,
                    width: 900,

                    height: 600,
                    onInit: function (finder) {
                        finder.on('files:choose', function (evt) {
                            var allFiles = evt.data.files;

                            var chosenFiles = '';
                            var len = allFiles.length;
                            allFiles.forEach( function( file, i ) {
                                chosenFiles += file.get('url');
                                if (i != len - 1) {
                                    chosenFiles += "|";
                                }

                                elemBoxSort.append(`<div class="image-preview-box">
                                            <img src="${MEDIA_URL+file.get('url')}" alt="" data-input="${file.get( 'url' )}">
                                            <a rel="8" class="btn btn-xs  btn-icon btn-danger btn_delete_image" data-toggle="modal" data-target="#deleteModal"><i class="la la-close"></i></a>
                                        </div>`);
                            });
                            var allImageChoose=parent.find(".image-preview-box img");
                            var allPath = "";
                            var len = allImageChoose.length;
                            allImageChoose.each(function (index, obj) {
                                allPath += $(this).attr('data-input');

                                if (index != len - 1) {
                                    allPath += "|";
                                }
                            });
                            elemInput.val(allPath);

                            //set lại event cho các nút xóa đã được thêm
                            //remove image extension each item
                            $('.btn_delete_image').click(function (e) {

                                var parent = $(this).closest('.ck-parent');
                                var elemInput = parent.find('.image_input_text');
                                $(this).closest('.image-preview-box').remove();
                                var allImageChoose=parent.find(".image-preview-box img");

                                var allPath = "";
                                var len = allImageChoose.length;
                                allImageChoose.each(function (index, obj) {
                                    allPath += $(this).attr('src');

                                    if (index != len - 1) {
                                        allPath += "|";
                                    }
                                });
                                elemInput.val(allPath);
                            });
                            //khoi tao lại sortable sau khi append phần tử mới
                            $('.sortable').sortable().bind('sortupdate', function (e, ui) {

                                var parent = $(this).closest('.ck-parent');
                                var allImageChoose=parent.find(".image-preview-box img");
                                var elemInput = parent.find('.image_input_text');
                                var allPath = "";
                                var len = allImageChoose.length;
                                allImageChoose.each(function (index, obj) {
                                    allPath += $(this).attr('src');

                                    if (index != len - 1) {
                                        allPath += "|";
                                    }
                                });
                                elemInput.val(allPath);
                            });

                        });
                    }
                });
            });

            //remove image extension each item
            $('.btn_delete_image').click(function (e) {

                var parent = $(this).closest('.ck-parent');
                var elemInput = parent.find('.image_input_text');
                $(this).closest('.image-preview-box').remove();
                var allImageChoose=parent.find(".image-preview-box img");

                var allPath = "";
                var len = allImageChoose.length;
                allImageChoose.each(function (index, obj) {
                    allPath += $(this).attr('src');

                    if (index != len - 1) {
                        allPath += "|";
                    }
                });
                elemInput.val(allPath);
            });


            //khoi tao sortable
            $('.sortable').sortable().bind('sortupdate', function (e, ui) {

                var parent = $(this).closest('.ck-parent');
                var allImageChoose=parent.find(".image-preview-box img");
                var elemInput = parent.find('.image_input_text');
                var allPath = "";
                var len = allImageChoose.length;
                allImageChoose.each(function (index, obj) {
                    allPath += $(this).attr('src');

                    if (index != len - 1) {
                        allPath += "|";
                    }
                });
                elemInput.val(allPath);
            });


            //ckfinder for upload file
            $(".ck-popup-file").click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.ck-parent');


                var elemInput = parent.find('.ck-input');
                var elemBtnRemove = parent.find('.ck-btn-remove');
                CKFinder.modal({
                    connectorPath: '{{route('admin.ckfinder_connector')}}',
                    resourceType: 'Files',
                    chooseFiles: true,

                    width: 900,
                    height: 600,
                    onInit: function (finder) {
                        finder.on('files:choose', function (evt) {
                            var file = evt.data.files.first();
                            var url = file.getUrl();
                            elemInput.val(url);

                        });
                    }
                });
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


        $('.ckeditor-basic').each(function () {
            var elem_id=$(this).prop('id');
            var height=$(this).data('height');
            height=height!=""?height:150;
            CKEDITOR.replace(elem_id, {
                filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                height:height,
                removeButtons: 'Source',
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


        // Image choose item
        $(".ck-popup").click(function (e) {
            e.preventDefault();
            var parent = $(this).closest('.ck-parent');

            var elemThumb = parent.find('.ck-thumb');
            var elemInput = parent.find('.ck-input');
            var elemBtnRemove = parent.find('.ck-btn-remove');
            CKFinder.modal({
                connectorPath: '{{route('admin.ckfinder_connector')}}',
                resourceType: 'Images',
                chooseFiles: true,

                width: 900,
                height: 600,
                onInit: function (finder) {
                    finder.on('files:choose', function (evt) {
                        var file = evt.data.files.first();
                        var url = file.getUrl();
                        elemThumb.attr("src", url);
                        elemInput.val(url);

                    });
                }
            });
        });
        $(".ck-btn-remove").click(function (e) {
            e.preventDefault();

            var parent = $(this).closest('.ck-parent');

            var elemThumb = parent.find('.ck-thumb');
            var elemInput = parent.find('.ck-input');
            elemThumb.attr("src", "/assets/backend/themes/images/empty-photo.jpg");
            elemInput.val("");
        });


        //ckfinder for upload file
        $(".ck-popup-file").click(function (e) {
            e.preventDefault();
            var parent = $(this).closest('.ck-parent');


            var elemInput = parent.find('.ck-input');
            var elemBtnRemove = parent.find('.ck-btn-remove');
            CKFinder.modal({
                connectorPath: '{{route('admin.ckfinder_connector')}}',
                resourceType: 'Files',
                chooseFiles: true,

                width: 900,
                height: 600,
                onInit: function (finder) {
                    finder.on('files:choose', function (evt) {
                        var file = evt.data.files.first();
                        var url = file.getUrl();
                        elemInput.val(url);

                    });
                }
            });
        });


    </script>
@endsection


