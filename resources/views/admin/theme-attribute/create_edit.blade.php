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
                    </div>
                </div>

                <div class="card-body">
                    {{-----title------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Mã thuộc tính(Dùng để lấy giá trị API cho frontend)') }}</label>
                            <input type="text" id="key" name="key" value="{{ old('key', isset($data) ? $data->key : null) }}" autofocus="true"
                                   placeholder="{{ __('Mã thuộc tính') }}" maxlength="120"
                                   class="form-control {{ $errors->has('key') ? ' is-invalid' : '' }}">
                            @if ($errors->has('key'))
                                <span class="form-text text-danger">{{ $errors->first('key') }}</span>
                            @endif
                        </div>

                    </div>
                    {{-----title------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tiêu đề') }}</label>
                            <input type="text" id="title" name="title" value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus="true"
                                   placeholder="{{ __('Tên theme') }}" maxlength="120"
                                   class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}">
                            @if ($errors->has('title'))
                                <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                            @endif
                        </div>

                    </div>

                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Link Demo - Cách nhau dấu , Nếu có nhiều link') }}</label>
                            <input type="text" id="link" name="link" value="{{ old('link', isset($data) ? $data->link : null) }}" autofocus="true"
                                   placeholder="{{ __('Link demo') }}"
                                   class="form-control {{ $errors->has('link') ? ' is-invalid' : '' }}">
                        </div>
                    </div>


                    <div class="form-group row">
                        <h4>Giá trị thuộc tính</h4>
                    </div>

                    <div id="field_send_container" class="form-group m-form__group">
                        @if(isset($data))
                            @php
                                $send_name =  \App\Library\Helpers::DecodeJson('send_name',$data->param_attribute);
                                $send_key =  \App\Library\Helpers::DecodeJson('send_key',$data->param_attribute);
                            @endphp
                            @if(!empty($send_name))
                                @for ($i = 0; $i < count($send_name); $i++)
                                    @if( $send_name[$i]!=null)
                                        <div class="row cat-item" style="margin-top: 5px;">
                                            <div class="input-group">
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
                                                        <input type="text" class="m-input m-input--air" style="width: 200px" name="send_key[]" placeholder="Mã thuộc tính" value="{{$send_key != null ? $send_key[$i] : ""}}">
                                                        <input type="text" class="form-control m-input m-input--air" name="send_name[]" placeholder="Tên thuộc tính" value="{{$send_name[$i]}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endfor
                            @endif
                        @else
                            <div class="row cat-item" style="margin-top: 5px;">
                                <div class="input-group">
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
                                            <input type="text" class="form-control m-input m-input--air" name="send_key[]" placeholder="Mã thuộc tính">
                                            <input type="text" class="form-control m-input m-input--air" name="send_name[]" placeholder="Tên thuộc tính">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="form-group row">
                            <div class="text-right" style="display: block;width: 100%;">
                                <button id="btnAddSend" type="button" class="btn btn-primary m-btn m-btn--air">
                                    + Thêm thuộc tính
                                </button>
                            </div>
                            <span style="margin-top:5px;display: block;width: 100%;text-align: right;" class="m-form__help">Cho phép cấu hình tối đa 5 thuộc tính động</span>
                    </div>
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
                            <label for="status" class="form-control-label">{{ __('Hình ảnh(button, background...)') }}</label>
                            {{Form::select('is_image',config('module.'.$module.'.is_image'),old('is_image', isset($data) ? $data->is_image : null),array('class'=>'form-control'))}}
                            @if($errors->has('is_image'))
                                <div class="form-control-feedback">{{ $errors->first('is_image') }}</div>
                            @endif
                        </div>

                    </div>
                    {{-- status --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                            {{Form::select('status',config('module.'.$module.'.status'),old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
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

@endsection

@section('scripts')


    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>
    <script>


        "use strict";
        $(document).ready(function () {
            //btn submit form
            $('#btnAddSend').click(function(){
                var fcount = $('#field_send_container>.row').length;
                if (fcount >= 5)
                    return;
                var a = $('#field_send_container>.row').first().clone();
                a.addClass('hide-data');
                $('input[type="text"]', a).val('');
                $('[name="send_type[]"]', a).val(1);
                a.appendTo($('#field_send_container'));
                $('input[type="text"]', a).focus();
                SendEvents(a);
                $('select[name="send_type[]"]', a).change();
            });

            $(".btnRemove").click(function (){
                $(this).closest(".row").remove();
            });


            $('.btn-submit-custom').click(function (e) {
                e.preventDefault();
                var btn = this;
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
                                            <img src="${file.get( 'url' )}" alt="">
                                            <a rel="8" class="btn btn-xs  btn-icon btn-danger btn_delete_image" data-toggle="modal" data-target="#deleteModal"><i class="la la-close"></i></a>
                                        </div>`);
                            });
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
        });
        $(document).ready(function () {

            $(".add-row").click(function () {
                var markup = '<tr><td><input type="text" class="form-control amount-item" name="params_amount[]"  value=""></td><td><input type="text"  class="form-control percent-item" maxlength="3" name="params_percent[]"  value=""></td></tr>';
                $("#type-code-1 tbody").append(markup);
            });
            checkType();
            function checkType(){
                var type = $('.type-gift').val();
                $('.item-type').css('display','none')
                if(type == 1){
                    $('.item-type-1').css('display','block');
                }
                else if(type == 2){
                    $('.item-type-2').css('display','block');
                }
            }
            $('.type-gift').on('change',function(){
                checkType();
            })
        });
    </script>
@endsection
