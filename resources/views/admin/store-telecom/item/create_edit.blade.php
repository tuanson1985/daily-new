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

    @if (session('shop_id'))
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
                        {{-- gate_id --}}
                        <div class="form-group row">
                            <div class="col-12 col-md-6">
                                <label class="form-control-label">{{ __('Nhà cung cấp') }}</label>
                                {{Form::select('gate_id',config('module.'.$module.'.gate_id'),old('gate_id', isset($data) ? $data->gate_id : null),array('class'=>'form-control'))}}
                                @if($errors->has('gate_id'))
                                    <div class="form-control-feedback">{{ $errors->first('gate_id') }}</div>
                                @endif
                            </div>
                        </div>

                        {{-----title------}}
                        <div class="form-group row">
                            <div class="col-12 col-md-12">
                                <label>{{ __('Tên nhà mạng') }}</label>
                                <input type="text" id="title_gen_slug" name="title" value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus="true"
                                    placeholder="{{ __('Tên nhà mạng') }}" maxlength="120"
                                    class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}">
                                @if ($errors->has('title'))
                                    <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                                @endif
                            </div>

                        </div>

                        {{-----key------}}
                        <div class="form-group row">
                            <div class="col-12 col-md-12">
                                <label>{{ __('Key') }}</label>
                                <input type="text" id="key_gen_slug" name="key" value="{{ old('key', isset($data) ? $data->key : null) }}" autofocus="true"
                                    placeholder="{{ __('key') }}" maxlength="120"
                                    class="form-control {{ $errors->has('key') ? ' is-invalid' : '' }}">
                                @if ($errors->has('key'))
                                    <span class="form-text text-danger">{{ $errors->first('key') }}</span>
                                @endif
                            </div>
                        </div>
                        {{-----gallery block------}}
                        <div class="form-group row">
                            {{-----image------}}
                            <div class="col-md-4">
                                <label for="locale">{{ __('Ảnh đại diện') }}:</label>
                                <div class="">
                                    <div class="fileinput ck-parent" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                            @if(old('image', isset($data) ? $data->image : null)!="")
                                                <img class="ck-thumb" src="{{ old('image', isset($data) ? $data->image : null) }}">
                                            @else
                                                <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                            @endif
                                            <input class="ck-input" type="hidden" name="image" value="{{ old('image', isset($data) ? $data->image : null) }}">

                                        </div>
                                        <div>
                                            <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                            <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                        </div>
                                    </div>
                                    @if ($errors->has('image'))
                                        <span class="form-text text-danger">{{ $errors->first('image') }}</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                        {{-----end gallery block------}}
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
                                {{Form::select('status',config('module.'.$module.'.status'),old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
                                @if($errors->has('status'))
                                    <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                                @endif
                            </div>

                        </div>
                        {{-- created_at --}}
                        <div class="form-group row">
                            <div class="col-12 col-md-12">
                                <label>{{ __('Ngày tạo') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                        name="created_at"
                                        value="{{ old('created_at', isset($data) ? $data->created_at->format('d/m/Y H:i:s') : date('d/m/Y H:i:s')) }}"
                                        placeholder="{{ __('Ngày tạo') }}" autocomplete="off"
                                        data-toggle="datetimepicker">

                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="la la-calendar"></i></span>
                                    </div>
                                </div>
                                @if($errors->has('created_at'))
                                    <div class="form-control-feedback">{{ $errors->first('created_at') }}</div>
                                @endif
                            </div>

                        </div>

                        {{-- order --}}
                        <div class="form-group row">
                            <div class="col-12 col-md-12">
                                <label for="order">{{ __('Thứ tự') }}</label>
                                <input type="text" name="order" value="{{ old('order', isset($data) ? $data->order : null) }}"
                                    placeholder="{{ __('Thứ tự') }}"
                                    class="form-control {{ $errors->has('order') ? ' is-invalid' : '' }}">
                                @if ($errors->has('order'))
                                    <span class="form-text text-danger">{{ $errors->first('order') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--Thông tin khác--}}
        @if(config('module.'.$module.'.params_field') )
            <div class="row">
                <div class="col-lg-9">
                    <div class="card card-custom gutter-b">
                        <div class="card-header">
                            <div class="card-title">
                            <h3 class="card-label">
                                {{__('Thông tin mở rộng')}} <i class="mr-2"></i>
                                <span class="d-block text-muted pt-2 font-size-sm">{{__("Thiết lập giá các thông tin mở rộng")}}</span>
                            </h3>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Lấy data từ params --}}
                            @php
                            $params= isset($data) ? $data->params : null
                            @endphp
                            {{--                    @dd($params)--}}
                            @foreach(config('module.'.$module.'.params_field') as $key => $fields)
                            {{--nếu nó là một phần tử thì set nó hẳn 1 row--}}
                            @if( Arr::isAssoc($fields))
                            {{--set chung cùng 1 biến để blade đọc--}}
                            @php $field= $fields  @endphp
                            <div class="form-group row">
                            <div class="{{Arr::get($field,'div_parent_class')}}">
                                @includeIf('admin.module.__fields.' . Arr::get($field,'type') )
                            </div>
                            </div>
                            {{--nếu nó là một nhóm phần tử thì set nó trong 1 row và điều chỉnh col--}}
                            @else
                            <div class="form-group row ">
                            @foreach($fields as $key => $field)
                            <div class="{{Arr::get($field,'div_parent_class')}}">
                                @includeIf('admin.module.__fields.' . Arr::get($field,'type') )
                            </div>
                            @endforeach
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{ Form::close() }}
    @else  
        <div class="alert alert-custom alert-outline-2x alert-outline-danger fade show mb-5" role="alert">
            <div class="alert-icon">
                <i class="flaticon-warning"></i>
            </div>
            <div class="alert-text"> <b>Để sử dụng tính năng này vui lòng lựa chọn shop cấu hình trên thanh header</b> </div>
        </div>
    @endif


@endsection

@section('scripts')


    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>
    <script>


        "use strict";
        $(document).ready(function () {
            //btn submit form
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
    </script>
@endsection
