{{-- Extends layout --}}
@extends('admin._layouts.master')

@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.shop.access', $id)}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>

        <div class="btn-group">
            <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
                <i class="ki ki-check icon-sm"></i>
                {{__('Cập nhật')}}

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
                                {{__('Cập nhật & tiếp tục')}}
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

    {{Form::open(array('method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data" , 'files' => true))}}
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
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label>{{ __('Hệ số tài khoản fake.') }}</label>
                                <input type="text" name="account_fake" value="{{ old('account_fake', isset($data->account_fake) ? $data->account_fake : (isset($cattegory->account_fake) ? $cattegory->account_fake : null) ) }}" autofocus
                                       placeholder="{{ __('Hệ số tài khoản fake') }}" maxlength="120"
                                       class="form-control {{ $errors->has('account_fake') ? ' is-invalid' : '' }}">
                                @if ($errors->has('account_fake'))
                                    <span class="form-text text-danger">{{ $errors->first('account_fake') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-----title------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tiêu đề') }}</label>
                            <input type="text" id="title_gen_slug" name="title" value="{{ old('title', isset($data->title) ? $data->title : (isset($cattegory->title) ? $cattegory->title : null) ) }}" autofocus
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
                                    <a  id="permalink" class="permalink" target="_blank" href="https://{{ $shop->name }}/danh-muc/{{ old('slug', isset($data->slug) ? $data->slug : (isset($cattegory->slug) ? $cattegory->slug : null) ) }}">

                                <span class="default-slug">https://{{ $shop->name }}/danh-muc/<span id="label-slug" data-override-edit="0">{{ old('slug', isset($data->slug) ? $data->slug : (isset($cattegory->slug) ? $cattegory->slug : null) ) }}</span></span>

                                </a>
                                @else
                                    <a  id="permalink" class="permalink" target="_blank" href="{{Request::getSchemeAndHttpHost()}}/{{ old('slug', isset($data->slug) ? $data->slug : (isset($cattegory->slug) ? $cattegory->slug : null)) }}">

                                <span class="default-slug">{{Request::getSchemeAndHttpHost()}}/<span id="label-slug" data-override-edit="0">{{ old('slug', isset($data->slug) ? $data->slug : (isset($cattegory->slug) ? $cattegory->slug : null)) }}</span></span>

                                </a>
                                @endif

                                <input type="text" value=""  class="form-control" id="input-slug-edit" style="width: auto !important;display: none"/>
                                <a  class="btn btn-light-primary font-weight-bolder mr-2" id="btn-slug-edit">Chỉnh sửa</a>
                                <a  class="btn btn-light-primary font-weight-bolder mr-2" id="btn-slug-renew">Tạo mới</a>
                                <a  class="btn btn-light-primary font-weight-bolder mr-2" id="btn-slug-ok" style="display: none">OK</a>
                                <a  class="btn btn-secondary  button-link mr-2" id="btn-slug-cancel" style="display: none">Cancel</a>

                                <input type="hidden" id="current-slug" name="slug" value="{{ old('slug', isset($data->slug) ? $data->slug : (isset($cattegory->slug) ? $cattegory->slug : null)) }}">
                            </span>
                        </div>

                    </div>



                    {{-----description------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="locale">{{ __('Mô tả') }}:</label>
                            <textarea id="description" name="description" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" >{{ old('description', isset($data->description) ? $data->description : (isset($cattegory->description) ? $cattegory->description : null) ) }}</textarea>
                            @if ($errors->has('description'))
                                <span class="form-text text-danger">{{ $errors->first('description') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-----content------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="locale">{{ __('Nội dung') }}</label>
                            <textarea id="content" name="content" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ old('content', isset($data->content) ? $data->content : (isset($cattegory->content) ? $cattegory->content : null) ) }}</textarea>
                            @if ($errors->has('content'))
                                <span class="form-text text-danger">{{ $errors->first('content') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-----meta------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="meta_popup">Nội dung popup:</label>
                            <textarea id="meta_popup" name="meta[popup]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ old('meta.popup', $data->meta['popup']??null) }}</textarea>
                        </div>
                    </div>

                    {{-----gallery block------}}
                    <div class="form-group row">
                        {{-----image------}}
                        <div class="col-md-4">
                            <label for="locale">{{ __('Hình đại diện') }}:</label>
                            <div class="">
                                <div class="fileinput ck-parent" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                        @if(old('image', isset($data->image) ? $data->image : (isset($cattegory->image) ? $cattegory->image : null) )!="")
                                            <img class="ck-thumb" src="{{ old('image', \App\Library\MediaHelpers::media(isset($data->image) ? $data->image : (isset($cattegory->image) ? $cattegory->image : null))) }}">
                                        @else
                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                        @endif
                                        <input class="ck-input" type="hidden" name="image" value="{{ old('image', isset($data->image) ? $data->image : (isset($cattegory->image) ? $cattegory->image : null)) }}">

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
                            <label for="locale">Hình chi tiết:</label>
                            <div class="">
                                <div class="fileinput ck-parent" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                        @if(old('meta.image_detail', isset($data->meta['image_detail']) ? $data->meta['image_detail'] : (isset($cattegory->meta['image_detail']) ? $cattegory->meta['image_detail'] : null) )!="")
                                            <img class="ck-thumb" src="{{ old('meta.image_detail', \App\Library\MediaHelpers::media(isset($data->meta['image_detail']) ? $data->meta['image_detail'] : (isset($cattegory->meta['image_detail']) ? $cattegory->meta['image_detail'] : null))) }}">
                                        @else
                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                        @endif
                                        <input class="ck-input" type="hidden" name="meta[image_detail]" value="{{ old('meta.image_detail', isset($data->meta['image_detail']) ? $data->meta['image_detail'] : (isset($cattegory->meta['image_detail']) ? $cattegory->meta['image_detail'] : null)) }}">

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
                            <label for="locale">{{ __('Hình Banner') }}:</label>
                            <div class="">
                                <div class="fileinput ck-parent" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                        @if(old('image_banner', isset($data->image_banner) ? $data->image_banner : (isset($cattegory->image_banner) ? $cattegory->image_banner : null))!="")
                                            <img class="ck-thumb" src="{{ old('image_banner', \App\Library\MediaHelpers::media(isset($data->image_banner) ? $data->image_banner : (isset($cattegory->image_banner) ? $cattegory->image_banner : null))) }}">
                                        @else
                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                        @endif
                                        <input class="ck-input" type="hidden" name="image_banner" value="{{ old('image_banner', isset($data->image_banner) ? $data->image_banner : (isset($cattegory->image_banner) ? $cattegory->image_banner : null)) }}">

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

                                        @if(old('image_icon', isset($data->image_icon) ? $data->image_icon : (isset($cattegory->image_icon) ? $cattegory->image_icon : null))!="")
                                            <img class="ck-thumb" src="{{ old('image_icon', \App\Library\MediaHelpers::media($data->image_icon??null)) }}">
                                        @else
                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                        @endif
                                        <input class="ck-input" type="hidden" name="image_icon" value="{{ old('image_icon', isset($data->image_icon) ? $data->image_icon : (isset($cattegory->image_icon) ? $cattegory->image_icon : null)) }}">

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
                            <label for="locale">{{ __('Hình nút Mua') }}:</label>
                            <div class="">
                                <div class="fileinput ck-parent" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                        @if(old('meta.image_btn', isset($data->meta['image_btn']) ? $data->meta['image_btn'] : (isset($cattegory->meta['image_btn']) ? $cattegory->meta['image_btn'] : null) )!="")
                                            <img class="ck-thumb" src="{{ old('meta.image_btn', \App\Library\MediaHelpers::media(isset($data->meta['image_btn']) ? $data->meta['image_btn'] : (isset($cattegory->meta['image_btn']) ? $cattegory->meta['image_btn'] : null))) }}">
                                        @else
                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                        @endif
                                        <input class="ck-input" type="hidden" name="meta[image_btn]" value="{{ old('meta.image_btn', isset($data->meta['image_btn']) ? $data->meta['image_btn'] : (isset($cattegory->meta['image_btn']) ? $cattegory->meta['image_btn'] : null)) }}">

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
                            @php
                                $status_list = config('etc.acc_property.status');
                                if (!Auth::user()->can('client-access-list')) {
                                    unset($status_list[0]);
                                }
                            @endphp
                            <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                            {{Form::select('status', $status_list , old('status', isset($data->status) ? $data->status : (isset($cattegory->status) ? $cattegory->status : null)),array('class'=>'form-control'))}}
                            @if($errors->has('status'))
                                <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                            @endif
                        </div>

                    </div>
                    {{-- order --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">

                            <label for="order">{{ __('Thứ tự') }}</label>
                            <input type="text" name="order" value="{{ old('order', isset($data->order) ? $data->order : (isset($cattegory->order) ? $cattegory->order : null)) }}"
                                   placeholder="{{ __('Thứ tự') }}"
                                   class="form-control {{ $errors->has('order') ? ' is-invalid' : '' }}">
                            @if ($errors->has('order'))
                                <span class="form-text text-danger">{{ $errors->first('order') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {{-- price_old --}}
                        <div class="">
                            <label  class="form-control-label">{{ __('Giá hiển thị mặc định (đ)') }}</label>
                            <input type="text" name="meta[price_old]" value="{{ old('meta.price_old', $data->meta['price_old']??null) }}" placeholder="Giá hiển thị" class="form-control m-input input-price {{ $errors->has('meta.price_old') ? ' is-invalid' : '' }}">
                            @if($errors->has('meta.price_old'))
                                <div class="form-control-feedback">{{ $errors->first('meta.price_old') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {{-- price --}}
                        <div class="">
                            <label  class="form-control-label">{{ __('Giá bán mặc định (đ)') }}</label>
                            <input type="text" name="meta[price]" value="{{ old('meta.price', $data->meta['price']??null) }}" placeholder="Giá cuối" class="form-control m-input input-price {{ $errors->has('meta.price') ? ' is-invalid' : '' }}">
                            @if($errors->has('meta.price'))
                                <div class="form-control-feedback">{{ $errors->first('meta.price') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($log_edit))
                <div class="card card-custom gutter-b">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">
                                Log edit <i class="mr-2"></i>
                            </h3>
                        </div>
                    </div>

                    <div class="card-body" style="padding-top: 24px">
                        {{-- status --}}
                        <div class="form-group row">
                            @foreach($log_edit as $key_log => $log)
                                <div class="col-12 col-md-12" style="padding-top: 16px">
                                    <ul style="float: left;padding-left: 0;margin-bottom: 0">
                                        <li style="list-style: none;float: left;">
                                            <span style="font-weight: bold">{{ $key_log + 1 }}.</span>
                                        </li>
                                        <li style="list-style: none;float: left;margin-left: 8px">
                                            <span style="background: #A7ABC3;padding: 6px 8px;border-radius: 4px"><i class="menu-icon fas fa-user" style="color: #ffffff;font-size: 12px"></i></span>
                                        </li>
                                        <li style="list-style: none;float: left;margin-left: 4px">{{ $log->author->username }}</li>
                                        <li style="list-style: none;float: left;margin-left: 8px">
                                            <a href="/admin/acc/{{ $data->id }}/revision/{{ $log->id }}">{{ $log->created_at }}</a>
                                            {{--                                            <a href="javascript:void(0)">{{ $log->time_now }}</a>--}}

                                        </li>
                                        @if(isset($log->type))
                                            <li style="list-style: none;float: left;margin-left: 8px">
                                                [ {{ config('module.article.log_edit.'.$log->type) }} ]
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
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
                            <input type="text" id="seo_title" name="seo_title" value="{{ old('seo_title', isset($data->seo_title) ? $data->seo_title : (isset($cattegory->seo_title) ? $cattegory->seo_title : null)) }}"
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
                            <input type="text" id="seo_description" name="seo_description" value="{{ old('seo_description', isset($data->seo_description) ? $data->seo_description : (isset($cattegory->seo_description) ? $cattegory->seo_description : null)) }}"
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
                            <h3 id="google_title" class="title_google" style="color:#1a0dab;font-size: 18px;font-family: arial,sans-serif;padding:0;margin: 0;">{{ old('title', isset($data->title) ? $data->title : (isset($cattegory->title) ? $cattegory->title : null)) }}</h3>
                            <div style="color:#006621;font-size: 14px;font-family: arial,sans-serif;">
                                @if(session()->get('shop_name') != null)
                                    <span class="prefix_url">https://{{ $shop->name }}/dich-vu/</span><span id="google_slug" class="google_slug">{{ old('slug', isset($data->slug) ? $data->slug : (isset($cattegory->slug) ? $cattegory->slug : null)) }}</span>
                                @else
                                    <span class="prefix_url">{{Request::getSchemeAndHttpHost()}}/</span><span id="google_slug" class="google_slug">{{ old('slug', isset($data->slug) ? $data->slug : (isset($cattegory->slug) ? $cattegory->slug : null)) }}</span>
                                @endif                            </div>
                            <div id="google_description" class="google_description" style="color: #545454;font-size: small;font-family: arial,sans-serif;">{{ old('description', isset($data->description) ? $data->description : (isset($cattegory->description) ? $cattegory->description : null)) !=""??"Mô tả seo website không vượt quá 160 kí tự. Là những đoạn mô tả ngắn gọn về website, bài viết..." }}</div>
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
                </div>
            </div>
        </div>

    </div>

    {{ Form::close() }}

    <input type="hidden" class="check__shop__autosave" value="{{ $id??'' }}">
    <input type="hidden" class="check__nick__autosave" value="{{ $data->id??'' }}">
@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')
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
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_folder_id', [$folder_image,$data->id??0]) }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_folder_id', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_folder_id', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_folder_id', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_folder_id', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_folder_id', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
                    height:height,
                    startupMode:startupMode,
                    toolbarGroups: [ { name: 'basicstyles' } ],
                    on: {
                        instanceReady: function(ev) {

                            // Autosave but no more frequent than 5 sec.
                            var buffer = CKEDITOR.tools.eventsBuffer( 5000, function() {
                                var editor = ev.editor.getData();
                                var id__shop__autosave = $('.check__shop__autosave').val();
                                var id__nick__autosave = $('.check__nick__autosave').val();

                                if (id__shop__autosave && id__nick__autosave && editor){

                                    $.ajax({
                                        url: "{{ route('admin.shop.autosave-content') }}",
                                        type:'POST',
                                        data: {
                                            _token:$('meta[name="csrf-token"]').attr('content'),
                                            shop_id: {{ $id??'' }},
                                            nick_id: {{ $cat_id??'' }},
                                            content:editor,
                                        },
                                        success:function (res) {
                                            if(res.status == 1){
                                                console.log(res.message);
                                            }else  {
                                                console.log(res.message);
                                            }
                                        }
                                    })
                                    console.log(editor)
                                    console.log( 'Autosave!' );
                                }


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
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_folder_id', [$folder_image,$data->id??0]) }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_folder_id', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_folder_id', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_folder_id', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_folder_id', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_folder_id', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
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
                    connectorPath: '{{route('admin.ckfinder_connector_folder_id', [$folder_image,$data->id??0])}}',
                    resourceType: 'Images',
                    chooseFiles: true,
                    // connectorInfo: '', /*params*/
                    width: 900,
                    height: 600,
                    onInit: function (finder) {
                        finder.on('files:choose', function (evt) {
                            var file = evt.data.files.first();
                            var url = file.getUrl();
                            elemThumb.attr("src",MEDIA_URL+url);
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
                    connectorPath: '{{route('admin.ckfinder_connector_folder_id', [$folder_image,$data->id??0])}}',
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

            //ckfinder for upload file
            $(".ck-popup-file").click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.ck-parent');


                var elemInput = parent.find('.ck-input');
                var elemBtnRemove = parent.find('.ck-btn-remove');
                CKFinder.modal({
                    connectorPath: '{{route('admin.ckfinder_connector_folder_id', [$folder_image,$data->id??0])}}',
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


