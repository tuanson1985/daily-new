{{-- Extends layout --}}
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
                    </div>

                </div>

                <div class="card-body">


                    {{-----title------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tiêu đề') }}</label>
                            <input type="text" id="title_gen_slug" name="title" value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus="true"
                                   placeholder="{{ __('Tiêu đề') }}" maxlength="120"
                                   class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}">
                            @if ($errors->has('title'))
                                <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                            @endif
                        </div>

                    </div>

                    {{-----slug------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Permalink') }}:</label>

                            <span class="">
                                <a  id="permalink" class="permalink" target="_blank" href="{{Request::getSchemeAndHttpHost()}}/{{ old('slug', isset($data) ? $data->slug : null) }}">

                                <span class="default-slug">{{Request::getSchemeAndHttpHost()}}/<span id="label-slug" data-override-edit="0">{{ old('slug', isset($data) ? $data->slug : null) }}</span></span>

                                </a>
                                <input type="text" value=""  class="form-control" id="input-slug-edit" style="width: auto !important;display: none"/>
                                <a  class="btn btn-light-primary font-weight-bolder mr-2" id="btn-slug-edit">Chỉnh sửa</a>
                                <a  class="btn btn-light-primary font-weight-bolder mr-2" id="btn-slug-renew">Tạo mới</a>
                                <a  class="btn btn-light-primary font-weight-bolder mr-2" id="btn-slug-ok" style="display: none">OK</a>
                                <a  class="btn btn-secondary  button-link mr-2" id="btn-slug-cancel" style="display: none">Cancel</a>

                                <input type="hidden" id="current-slug" name="slug" value="{{ old('slug', isset($data) ? $data->slug : null) }}">
                                <input type="hidden" id="is_slug_override" name="is_slug_override" value="{{ old('is_slug_override', isset($data) ? $data->is_slug_override : null) }}" >
                            </span>
                        </div>

                    </div>



                    {{-----description------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="locale">{{ __('Mô tả') }}:</label>
                            <textarea id="description" name="description" class="form-control ckeditor-basic" >{{ old('description', isset($data) ? $data->description : null) }}</textarea>
                            @if ($errors->has('description'))
                                <span class="form-text text-danger">{{ $errors->first('description') }}</span>
                            @endif
                        </div>
                    </div>

{{--                    --}}{{-----content------}}
{{--                    <div class="form-group row">--}}
{{--                        <div class="col-12 col-md-12">--}}
{{--                            <label for="locale">{{ __('Nội dung') }}</label>--}}
{{--                            <textarea id="content" name="content" class="form-control ckeditor-source" data-height="400px" >{{ old('content', isset($data) ? $data->content : null) }}</textarea>--}}
{{--                            @if ($errors->has('content'))--}}
{{--                                <span class="form-text text-danger">{{ $errors->first('content') }}</span>--}}
{{--                            @endif--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    {{-- target --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-4">
                            <label for="target" class="form-control-label">{{ __('Kiểu mở link:') }}</label>
                            {{Form::select('target',[''=>'-- Không chọn --',1=>"Mở tab mới",2=>"Mở popup"],old('target', isset($data) ? $data->target : null),array('class'=>'form-control'))}}
                            @if($errors->has('target'))
                                <div class="form-control-feedback">{{ $errors->first('target') }}</div>
                            @endif
                        </div>

                        @if(!empty(config('module.'.$module.'.position')))
                        <div class="col-12 col-md-4">
                            <label for="target" class="form-control-label">{{ __('Vị trí') }}</label>
                            {{Form::select('position',[''=>'-- Không chọn --']+(config('module.'.$module.'.position')??[]),old('position', isset($data) ? $data->position : null),array('class'=>'form-control'))}}
                            @if($errors->has('position'))
                                <div class="form-control-feedback">{{ $errors->first('position') }}</div>
                            @endif
                        </div>
                        @endif
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

                        <div class="col-md-8">
                            <label for="locale">{{ __('Ảnh mở rộng') }}:</label>
                            <div class="card" >
                                <div class="card-body p-3 ck-parent" style="min-height: 148px">
                                    <input class="image_input_text" type="hidden"  name="image_extension" value="{{ old('image_extension', isset($item) ? $item->image_extension : null) }}" type="text">

                                    <div class="sortable grid">


                                        @if (old('image_extension',isset($data)?$data->image_extension : null) != "")

                                            @foreach(explode('|', old('image_extension',isset($data)?$data->image_extension : null)) as $img)

                                                <div class="image-preview-box">
                                                    <img src="{{asset($img)}}" alt="">
                                                    <a rel="8" class="btn btn-xs  btn-icon btn-danger btn_delete_image" data-toggle="modal" data-target="#deleteModal"><i class="la la-close"></i></a>
                                                </div>
                                            @endforeach
                                        @endif


                                    </div>
                                    <style>
                                        .sortable div {
                                            border: 1px solid #CCC;
                                            font-family: "Tahoma";
                                            margin: 5px 7px;
                                            padding: 5px;
                                        }

                                        div.sortable-placeholder {
                                            border: 1px dashed #CCC;
                                            background: none;
                                        }

                                        .sortable .image-preview-box{
                                            position: relative;

                                        }
                                        .sortable .image-preview-box .btn_delete_image{
                                            position: absolute;
                                            top:-5px;
                                            right:-5px;
                                            display: none;
                                        }
                                        .sortable .image-preview-box:hover .btn_delete_image{
                                            display: block;
                                        }
                                        .sortable.grid div {
                                            float: left;
                                            width: 84px;
                                            height: 84px;
                                        }

                                        .sortable.grid div img {
                                            float: left;
                                            width: 100%;
                                            height: 100%;
                                        }
                                        .sortable.grid {
                                            overflow: hidden;
                                        }
                                    </style>
                                    <a class="btn btn-success ck-popup-multiply" style="margin-top: 15px;">
                                        <i class="la la-cloud-upload-alt"></i> Chọn hình
                                    </a>

                                </div>
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
                            {{Form::select('status',(config('module.'.$module.'.status')??[]) ,old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
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

            {{--author--}}
            {{--<div class="card card-custom gutter-b">--}}
            {{--    <div class="card-header">--}}
            {{--        <div class="card-title">--}}
            {{--            <h3 class="card-label">--}}
            {{--                Dữ liệu tạo bởi <i class="mr-2"></i>--}}
            {{--            </h3>--}}
            {{--        </div>--}}
            {{--    </div>--}}

            {{--    <div class="card-body">--}}

            {{--        <p>Tên: Tân</p>--}}
            {{--        <p>Email: tannm.2611@gmail.com</p>--}}

            {{--    </div>--}}
            {{--</div>--}}

        </div>
    </div>

    {{--List item in group--}}
    <div class="row">
        <div class="col-lg-9">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__('Cấu hình danh sách')}} <i class="mr-2"></i>
                        </h3>
                    </div>

                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <!--begin: Datatable-->
                            <table class="table table-bordered table-hover table-checkable " id="kt_datatable">
                            </table>
                            <!--end: Datatable-->


                        </div>
                        <div class="col-6">2</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{--Tối ưu SEO--}}
    <div class="row">
        <div class="col-lg-9">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__('Tối ưu seo')}} <i class="mr-2"></i>
                            <span class="d-block text-muted pt-2 font-size-sm">{{__("Thiết lập các thẻ mô tả tối ưu nội dung tìm kiếm trên Google.")}}</span>
                        </h3>
                    </div>

                </div>

                <div class="card-body">
                    {{-----seo_title------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tiêu đề Trang (<title>)') }}</label>
                            <input type="text" id="seo_title" name="seo_title" value="{{ old('seo_title', isset($data) ? $data->seo_title : null) }}"
                                   placeholder=""
                                   class="form-control {{ $errors->has('seo_title') ? ' is-invalid' : '' }}">
                            @if ($errors->has('seo_title'))
                                <span class="form-text text-danger">{{ $errors->first('seo_title') }}</span>
                            @endif
                        </div>

                    </div>

                    {{-----seo_description------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">

                            <label>{{ __('Mô Tả Trang ( <meta Description> )') }}</label>
                            <input type="text" id="seo_description" name="seo_description" value="{{ old('seo_description', isset($data) ? $data->seo_description : null) }}"
                                   placeholder=""
                                   class="form-control {{ $errors->has('seo_description') ? ' is-invalid' : '' }}">
                            @if ($errors->has('seo_description'))
                                <span class="form-text text-danger">{{ $errors->first('seo_description') }}</span>
                            @endif
                        </div>

                    </div>

                    <fieldset class="content-group">
                        <legend class="text-bold" style="border-bottom: 1px solid #e5e5e5;font-size: 15px;padding-bottom: 10px;margin-bottom: 10px">Khi lên top, page này sẽ hiển thị như sau:</legend>
                        <div class="form-group">
                            <h3 id="google_title" class="title_google" style="color:#1a0dab;font-size: 18px;font-family: arial,sans-serif;padding:0;margin: 0;">{{ old('title', isset($data) ? $data->title : null) }}</h3>
                            <div style="color:#006621;font-size: 14px;font-family: arial,sans-serif;">
                                <span class="prefix_url">{{Request::getSchemeAndHttpHost()}}/</span><span id="google_slug" class="google_slug">{{ old('slug', isset($data) ? $data->slug : null) }}</span>
                            </div>
                            <div id="google_description" class="google_description" style="color: #545454;font-size: small;font-family: arial,sans-serif;">{{ old('description', isset($data) ? $data->description : null) !=""??"Mô tả seo website không vượt quá 160 kí tự. Là những đoạn mô tả ngắn gọn về website, bài viết..." }}</div>
                        </div>
                    </fieldset>


                    {{-----seo_robots------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Index, Follow') }}</label>
                            <span class="switch switch-outline switch-icon switch-success">
                                <label><input type="checkbox" checked /><span></span></label>
                            </span>
                        </div>

                    </div>

                    <div class="test-fuck">
                      {!!  clean( old('content', isset($data) ? $data->content : null)) !!}
                    </div>


                </div>
            </div>
        </div>

    </div>

    {{ Form::close() }}

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
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
                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                    height:height
                    // startupMode:'source',
                } );


            });

            $('.ckeditor-basic').each(function () {
                var elem_id=$(this).prop('id');
                var height=$(this).data('height');
                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                    removeButtons: 'Source',
                    height:height,
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

                    dom: `<'row'<'col-sm-12 col-md-5'><'col-sm-12 col-md-7 dataTables_pager'f>>`,

                    // dom: "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
                    lengthMenu: [20, 50, 100, 200,500,1000],
                    pageLength: 5,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "order": [[1, "desc"]],
                    ajax: {
                        url: '/admin-1102/article' + '?ajax=1',
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


