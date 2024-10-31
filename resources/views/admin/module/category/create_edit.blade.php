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
                @if($module == 'menu-category' || $module == 'menu-profile' || $module == 'menu-transaction' || $module == 'article-category')
                    @if(session('shop_id'))
                        @if(isset($data))
                            {{__('Cập nhật')}}
                        @else
                            {{__('Thêm mới')}}
                        @endif
                    @else
                        @if(isset($data))
                            {{__('Cập nhật mặc định')}}
                        @else
                            {{__('Thêm mới mặc định')}}
                        @endif
                    @endif
                @else
                    @if(isset($data))
                        {{__('Cập nhật')}}
                    @else
                        {{__('Thêm mới')}}
                    @endif
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

                    {{-----parent_id------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-6">
                            <label>{{ __('Danh mục cha') }}</label>
                            <select name="parent_id" class="form-control select2 col-md-5" id="kt_select2_2" style="width: 100%">
                                <option value="0">-- {{__('Không chọn')}} --</option>

                                @if( !empty(old('parent_id')) )
                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,old('parent_id')) !!}
                                @else
                                    <?php $itSelect = [] ?>
                                    @if(isset($data))
                                        <?php array_push($itSelect, $data->parent_id)?>
                                    @endif
                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,$itSelect) !!}
                                @endif
                            </select>
                            @if($errors->has('parent_id'))
                                <div class="form-control-feedback">{{ $errors->first('parent_id') }}</div>
                            @endif
                        </div>

                    </div>


                    {{-----title------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tiêu đề') }}</label>
                            <input type="text" id="title_gen_slug" name="title" value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus
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
                                @if(session()->get('shop_name') != null)
                                    <a  id="permalink" class="permalink" target="_blank" href="https://{{ session()->get('shop_name') }}/{{ old('slug', isset($data) ? $data->slug : null) }}">

                                <span class="default-slug">https://{{ session()->get('shop_name') }}/<span id="label-slug" data-override-edit="0">{{ old('slug', isset($data) ? $data->slug : null) }}</span></span>

                                </a>
                                @else
                                    <a  id="permalink" class="permalink" target="_blank" href="{{Request::getSchemeAndHttpHost()}}/{{ old('slug', isset($data) ? $data->slug : null) }}">

                                <span class="default-slug">{{Request::getSchemeAndHttpHost()}}/<span id="label-slug" data-override-edit="0">{{ old('slug', isset($data) ? $data->slug : null) }}</span></span>

                                </a>
                                @endif
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
                            <textarea id="description" name="description" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" >{{ old('description', isset($data) ? $data->description : null) }}</textarea>
                            @if ($errors->has('description'))
                                <span class="form-text text-danger">{{ $errors->first('description') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-----content------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="locale">{{ __('Nội dung') }}</label>
                            <textarea id="content" name="content" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ old('content', isset($data) ? $data->content : null) }}</textarea>
                            @if ($errors->has('content'))
                                <span class="form-text text-danger">{{ $errors->first('content') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- target --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-4">
                            <label for="target" class="form-control-label">{{ __('Kiểu mở link:') }}</label>
                            {{Form::select('target',[''=>'-- Không chọn --',1=>"Mở tab mới",2=>"Mở popup",3=>"Check auth"],old('target', isset($data) ? $data->target : null),array('class'=>'form-control'))}}
                            @if($errors->has('target'))
                                <div class="form-control-feedback">{{ $errors->first('target') }}</div>
                            @endif
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="target" class="form-control-label">{{ __('Url Link:') }}</label>
                            <input type="text"  name="url" value="{{ old('url', isset($data) ? $data->url : null) }}"
                                   placeholder="{{ __('Url Link') }}"
                                   class="form-control {{ $errors->has('url') ? ' is-invalid' : '' }}">
                            @if($errors->has('url'))
                                <div class="form-control-feedback">{{ $errors->first('url') }}</div>
                            @endif
                        </div>

                        @if(!empty(config('module.'.$module.'.position')))
                            <div class="col-12 col-md-4">
                                <label for="target" class="form-control-label">{{ __('Vị trí hiển thị') }}</label>
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
                            <label for="locale">{{ __('Hình đại diện') }}:</label>
                            <div class="">
                                <div class="fileinput ck-parent" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                        @if(old('image', isset($data) ? $data->image : null)!="")
                                            <img class="ck-thumb" src="{{ old('image', isset($data) ? \App\Library\MediaHelpers::media($data->image) : null) }}">
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


                        <div class="col-md-4">
                            <label for="locale">{{ __('Hình Banner') }}:</label>
                            <div class="">
                                <div class="fileinput ck-parent" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                        @if(old('image_banner', isset($data) ? $data->image_banner : null)!="")
                                            <img class="ck-thumb" src="{{ old('image_banner', isset($data) ? \App\Library\MediaHelpers::media($data->image_banner) : null) }}">
                                        @else
                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                        @endif
                                        <input class="ck-input" type="hidden" name="image_banner" value="{{ old('image_banner', isset($data) ? $data->image_banner : null) }}">

                                    </div>
                                    <div>
                                        <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                        <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                    </div>
                                </div>
                                @if ($errors->has('image_banner'))
                                    <span class="form-text text-danger">{{ $errors->first('image_banner') }}</span>
                                @endif
                            </div>
                        </div>


                        <div class="col-md-4">
                            <label for="locale">{{ __('Hình icon') }}:</label>
                            <div class="">
                                <div class="fileinput ck-parent" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                        @if(old('image_icon', isset($data) ? $data->image_icon : null)!="")
                                            <img class="ck-thumb" src="{{ old('image_icon', isset($data) ? \App\Library\MediaHelpers::media($data->image_icon) : null) }}">
                                        @else
                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                        @endif
                                        <input class="ck-input" type="hidden" name="image_icon" value="{{ old('image_icon', isset($data) ? $data->image_icon : null) }}">

                                    </div>
                                    <div>
                                        <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                        <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                    </div>
                                </div>
                                @if ($errors->has('image_icon'))
                                    <span class="form-text text-danger">{{ $errors->first('image_icon') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="locale">{{ __('Màu sắc') }}:</label>
                            <div class="row marginauto">
                                <div class="col-auto pl-0">
                                    <input style="width: 120px" class="form-control " type="text" name="params[color]" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor_menu" value="{{ old('params_color', isset($data->params->color) ? $data->params->color : null) }}">
                                </div>
                                <div class="col-auto pr-0">
                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker_menu" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ old('params_color', isset($data->params->color) ? $data->params->color : null) }}">
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


                    {{-- ended_at --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Ngày hết hạn') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                       name="ended_at"
                                       @if( isset($data->ended_at) && $data->ended_at!="0000-00-00 00:00:00" )

                                       value="{{ old('expired_at', isset($data->ended_at) ? date('d/m/Y H:i:s', strtotime($data->ended_at)) : "") }}"
                                       @else
                                       value="{{ old('expired_at', "") }}"
                                       @endif
                                       placeholder="{{ __('Ngày hết hạn') }}" autocomplete="off"
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


    {{--Tối ưu SEO--}}
    <div class="row">
        <div class="col-lg-9">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__('Tối ưu SEO')}} <i class="mr-2"></i>
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
                                @if(session()->get('shop_name') != null)
                                    <span class="prefix_url">https://{{ session()->get('shop_name') }}/</span><span id="google_slug" class="google_slug">{{ old('slug', isset($data) ? $data->slug : null) }}</span>
                                @else
                                    <span class="prefix_url">{{Request::getSchemeAndHttpHost()}}/</span><span id="google_slug" class="google_slug">{{ old('slug', isset($data) ? $data->slug : null) }}</span>
                                @endif                            </div>
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
    <script>
        "use strict";
        $(document).ready(function () {
            $('#hexcolor_menu').on('input', function() {
                $('#colorpicker_menu').val(this.value);
            });
            $('#colorpicker_menu').on('input', function() {
                $('#hexcolor_menu').val(this.value);
            });
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
                toolbarGroups: [ { name: 'basicstyles' } ],
                on: {
                    instanceReady: function() {
                        // Autosave but no more frequent than 5 sec.
                        var buffer = CKEDITOR.tools.eventsBuffer( 5000, function() {
                            console.log( 'Autosave!' );
                        } );

                        this.on( 'change', buffer.input );
                    }
                }
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


