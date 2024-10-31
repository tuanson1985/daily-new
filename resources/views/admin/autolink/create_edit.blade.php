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
        <div class="col-lg-12">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                        </h3>
                    </div>

                </div>

                <div class="card-body replication">

                    <ul class="nav nav-tabs" role="tablist">

                        <li class="nav-item nav-item-replication">
                            <a class="nav-link show active nav_thong-tin-hien-thi" data-toggle="tab" href="#system" role="tab" aria-selected="true">
                                <span class="nav-text">Thông tin Auto</span>
                            </a>
                        </li>

                    </ul>

                    <div class="tab-content tab-content-replication">
                        <!-- Thông tin hiển thị -->
                        <div class="tab-pane show active" id="system" role="tabpanel">
                            <div class="row marginauto blook-row">
                                <div class="col-md-12 left-right">
                                    <!-- Block 1 -->
                                    <div class="row marginauto blook-item-row">
                                        <div class="col-md-12 left-right blook-item-title">
                                            <span>Tên và Đường dẫn.</span>
                                        </div>

                                        <div class="col-md-12 left-right blook-item-body">

                                            {{-----title------}}
                                            <div class="form-group row">
                                                <div class="col-12 col-md-4">
                                                    <label class="label-w">{{ __('Tiêu đề') }}<span style="color: red">(*)</span> :</label>
                                                    <input type="text" id="title_gen_slug" data-required="1" name="title" value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus
                                                           placeholder="{{ __('Tiêu đề') }}" maxlength="120"
                                                           class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}">
                                                    @if ($errors->has('title'))
                                                        <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                                                    @endif
                                                </div>

                                                <div class="col-12 col-md-4 blook-item-body_hide_in">
                                                    <label for="target" class="form-control-label label-w">{{ __('Url Link:') }}</label>
                                                    <input type="text"  name="url" value="{{ old('url', isset($data) ? $data->url : null) }}" placeholder="{{ __('Url Link') }}" class="form-control {{ $errors->has('url') ? ' is-invalid' : '' }}">

                                                    @if($errors->has('url'))
                                                        <div class="form-control-feedback">{{ $errors->first('url') }}</div>
                                                    @endif

                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <label for="target" class="form-control-label label-w">{{ __('Kiểu mở link:') }}</label>
                                                    {{Form::select('target',[''=>'-- Không chọn --',1=>"Mở tab mới",2=>"Mở popup"],old('target', isset($data) ? $data->target : null),array('class'=>'form-control'))}}
                                                    @if($errors->has('target'))
                                                        <div class="form-control-feedback">{{ $errors->first('target') }}</div>
                                                    @endif
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                    <div class="row marginauto block-hr"></div>
                                    <!-- Block 2 -->

                                    <div class="row marginauto blook-item-row">
                                        <div class="col-md-12 left-right blook-item-title">
                                            <span>Link type.</span>
                                        </div>

                                        <div class="col-md-12 left-right blook-item-body">

                                            {{-----Link type------}}
                                            <div class="form-group row" style="margin-bottom: 0">

                                                <div class="col-12 col-md-4">
                                                    <div class="row marginauto">
                                                        <div class="col-md-12 left-right">
                                                            <label for="link_type" class="form-control-label label-w">{{ __('Kiểu đi link:') }}</label>
                                                            {{Form::select('link_type',[1=>"Internal link",2=>"External link"],old('link_type', isset($data) ? $data->link_type : null),array('class'=>'form-control link_type'))}}
                                                            @if($errors->has('link_type'))
                                                                <div class="form-control-feedback">{{ $errors->first('link_type') }}</div>
                                                            @endif
                                                        </div>

                                                    </div>
                                                </div>
                                                @if(isset($data))
                                                    <div class="col-12 col-md-4 blook-item-body_hide">
                                                    <div class="row marginauto">
                                                        <div class="col-md-12 left-right data_url_link">
                                                            <label for="target" class="form-control-label label-w">{{ __('Url external Link:') }}</label>

                                                            @if(isset($data->params_access) && $data->link_type == 2)
                                                                @php
                                                                    $params_access = json_decode($data->params_access);
                                                                @endphp
                                                                @foreach($params_access as $p_key => $params_acces)
                                                                    @if($p_key == 0)
                                                                        <input type="text"  name="url_external[]" value="{{ $params_acces }}" placeholder="{{ __('Url external Link') }}" class="form-control input_url_link">
                                                                    @else
                                                                        <input type="text"  name="url_external[]" value="{{ $params_acces }}" placeholder="{{ __('Url external Link') }}" class="form-control input_url_link" style="margin-top: 8px">
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <input type="text"  name="url_external[]" value="" placeholder="{{ __('Url external Link') }}" class="form-control input_url_link">
                                                            @endif
                                                        </div>
                                                        <div class="col-md-12 text-right" style="margin-top: 12px">
                                                            <span class="add_url_link" style="color: #0a90eb;cursor: pointer">Add URL</span>
                                                        </div>
                                                    </div>

                                                </div>
                                                @else
                                                    <div class="col-12 col-md-4 blook-item-body_hide">
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right data_url_link">
                                                                <label for="target" class="form-control-label label-w">{{ __('Url external Link:') }}</label>
                                                                <input type="text"  name="url_external[]" value="" placeholder="{{ __('Url external Link') }}" class="form-control input_url_link">

                                                            </div>
                                                            <div class="col-md-12 text-right" style="margin-top: 12px">
                                                                <span class="add_url_link" style="color: #0a90eb;cursor: pointer">Add URL</span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                @endif
                                            </div>

                                        </div>

                                        <div class="col-md-8 left-right blook-item-body blook-item-body_hide">
                                            <div class="row">

{{--                                                <div class="col-md-2">--}}
{{--                                                    <label class="checkbox" style="line-height: 40px">--}}
{{--                                                        <input type="checkbox" name="link_all" value="1">--}}
{{--                                                                                                                        <input type="checkbox" name="shop_access_all" value="1" {{ isset($data->shop_access) ? ($data->shop_access == 'all'? 'checked': '') : '' }}>--}}
{{--                                                        <span></span><b class="ml-2">All điểm bán</b>--}}
{{--                                                    </label>--}}
{{--                                                </div>--}}
{{--                                                <div class="col-md-8">--}}
{{--                                                    <select name="link_access[]" multiple="multiple" title="" class="form-control select2 col-md-5"  data-placeholder="{{__('Hoặc chọn danh mục')}}" id="kt_select2_1" style="width: 100%">--}}
{{--                                                                                                                        <?php $access_shops = !empty(old('shop_access'))? old('shop_access'): $data->access_shops->pluck('id')->toArray(); ?>--}}
{{--                                                                                                                        @foreach($shops as $key => $item)--}}
{{--                                                                                                                            <option value="{{ $item->id }}" {{ in_array($item->id, $access_shops)? 'selected': '' }}>{{ $item->domain }}</option>--}}
{{--                                                                                                                        @endforeach--}}
{{--                                                        @foreach($shops as $key => $shop)--}}
{{--                                                            <option value="{{ $shop->id }}">{{ $shop->domain }}</option>--}}
{{--                                                        @endforeach--}}
{{--                                                    </select>--}}
{{--                                                    @if($errors->has('link_access'))--}}
{{--                                                        <div class="form-control-feedback">{{ $errors->first('link_access') }}</div>--}}
{{--                                                    @endif--}}
{{--                                                </div>--}}
                                            </div>


                                        </div>
                                    </div>

                                    <div class="row marginauto block-hr"></div>
                                    <!-- Block 3 -->

                                    <div class="row marginauto blook-item-row">
                                        <div class="col-md-8 pl-0 pr-0">
                                            <div class="row marginauto">
                                                <div class="col-md-12 left-right blook-item-title">
                                                    <span>Chọn danh mục và shop phân phối.</span>
                                                </div>
                                                <div class="col-md-12 left-right blook-item-body">
                                                    <div class="form-group" style="margin-bottom: 8px">
                                                        <label class="form-control-label label-w">Chọn danh mục <span style="color: red">(*)</span>:</label>
                                                    </div>
                                                    {{-- position --}}
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label class="checkbox" style="line-height: 40px">
{{--                                                                <input type="checkbox" checked name="group_all" value="1">--}}
{{--                                                                <input type="checkbox" name="shop_access_all" value="1" {{ isset($data->shop_access) ? ($data->shop_access == 'all'? 'checked': '') : '' }}>--}}
                                                                <span></span><b class="ml-2">Chọn danh mục</b>
                                                            </label>
                                                        </div>

                                                        @if(isset($data))
                                                        <div class="col-md-8">
                                                            <select name="group_access" title="" class="form-control select2 col-md-5"  data-placeholder="{{__('Hoặc chọn danh mục')}}" id="kt_select2_3" style="width: 100%">
                                                                @foreach($groups as $key => $group)
                                                                    @if($key == $data->group_id)
                                                                    <option value="{{ $key }}" selected>{{ $group }}</option>
                                                                    @else
                                                                        <option value="{{ $key }}">{{ $group }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                            @if($errors->has('group_access'))
                                                                <div class="form-control-feedback">{{ $errors->first('group_access') }}</div>
                                                            @endif
                                                        </div>
                                                        @else
                                                            <div class="col-md-8">
                                                                <select name="group_access" title="" class="form-control select2 col-md-5"  data-placeholder="{{__('Hoặc chọn danh mục')}}" id="kt_select2_3" style="width: 100%">
                                                                    @foreach($groups as $key => $group)
                                                                        <option value="{{ $key }}">{{ $group }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @if($errors->has('group_access'))
                                                                    <div class="form-control-feedback">{{ $errors->first('group_access') }}</div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>

                                                    @if(!session('shop_id'))
                                                    <div class="form-group" style="margin-bottom: 8px;margin-top: 8px">
                                                        <label class="form-control-label label-w">Chọn điểm bán<span style="color: red">(*)</span>:</label>
                                                    </div>
                                                    {{-- position --}}
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label class="checkbox" style="line-height: 40px">
                                                                <input type="checkbox" name="shop_all" value="1">
                                                                {{--                                                                <input type="checkbox" name="shop_access_all" value="1" {{ isset($data->shop_access) ? ($data->shop_access == 'all'? 'checked': '') : '' }}>--}}
                                                                <span></span><b class="ml-2">All điểm bán</b>
                                                            </label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select name="shop_access[]" multiple="multiple" title="" class="form-control select2 col-md-5"  data-placeholder="{{__('Hoặc chọn điểm bán')}}" id="kt_select2_2" style="width: 100%">
                                                                @foreach($shops as $key => $shop)
                                                                    <option value="{{ $shop->id }}">{{ $shop->domain }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if($errors->has('shop_access'))
                                                                <div class="form-control-feedback">{{ $errors->first('shop_access') }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row marginauto block-hr"></div>
                                    <!-- Block 2 -->
                                    <div class="row marginauto blook-item-row">
                                        <div class="col-md-12 left-right blook-item-title">
                                            <span>Dofollow.</span>
                                        </div>
                                        <div class="col-md-12 left-right blook-item-body">
                                            {{-- target --}}
                                            <div class="form-group row">
                                                <div class="col-4 col-md-2">
                                                    <label class="label-w">{{ __('Dofollow, NoFollow:') }}</label>
                                                    @if(isset($data))
                                                        @if(isset($data->dofollow) && $data->dofollow == 1)
                                                            <span class="switch switch-outline switch-icon switch-success">
                                                                <label><input type="checkbox" name="dofollow" checked /><span></span></label>
                                                            </span>
                                                        @else
                                                            <span class="switch switch-outline switch-icon switch-success">
                                                                <label><input type="checkbox" name="dofollow" /><span></span></label>
                                                            </span>
                                                        @endif
                                                    @else
                                                    <span class="switch switch-outline switch-icon switch-success">
                                                        <label><input type="checkbox" name="dofollow" checked /><span></span></label>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="col-8 col-md-4">
                                                    <label class="label-w">{{ __('Số lượng link:') }}</label>
                                                    <input type="text" id="percent_dofollow" data-required="1" name="percent_dofollow" value="{{ old('percent_dofollow', isset($data) ? $data->percent_dofollow : '') }}" autofocus
                                                           placeholder="{{ __('Số lượng link') }}"
                                                           class="form-control {{ $errors->has('percent_dofollow') ? ' is-invalid' : '' }}">
                                                    @if ($errors->has('percent_dofollow'))
                                                        <span class="form-text text-danger">{{ $errors->first('percent_dofollow') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ Form::close() }}
    @if(isset($data))
    <input type="hidden" name="c_link_type" class="c_link_type" value="{{ $data->link_type }}">
    @endif
@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="/assets/backend/assets/css/replication.css?v={{time()}}" rel="stylesheet" type="text/css"/>
@endsection


{{-- Scripts Section --}}
@section('scripts')


    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>
    <script>
        "use strict";
        $(document).ready(function () {

            var c_link_type = $('.c_link_type').val();
            // if (c_link_type && c_link_type == 2){
            //     $('.blook-item-body_hide').css('display','block');
            //     $('.blook-item-body_hide_in').css('display','none');
            // }

            $(document).on('click', '.add_url_link',function(e){
                var length_url = $('.input_url_link').length;
                console.log(length_url)
                if (length_url > 2){
                    return false;
                }

                var html = '<input type="text"  name="url_external[]" value="" placeholder="Url Link" class="form-control input_url_link" style="margin-top: 8px">';
                $('.data_url_link').append(html);
            })

            // $(document).on('change', '.link_type',function(e){
            //     var link_type = $(this).val();
            //
            //     if (link_type == 2){
            //         $('.blook-item-body_hide').css('display','block');
            //         $('.blook-item-body_hide_in').css('display','none');
            //
            //     }else {
            //         $('.blook-item-body_hide').css('display','none');
            //         $('.blook-item-body_hide_in').css('display','block');
            //     }
            // })

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
                                            <img src="${file.get('url')}" alt="" data-input="${file.get( 'url' )}">
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
    <script>

    </script>
@endsection


