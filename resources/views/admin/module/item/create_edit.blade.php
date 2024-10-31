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

                    {{-----group_id------}}
                    <div class="form-group row">
                        <label  class="col-12">{{ __('Danh mục cha') }}</label>
                        <div class="col-6">

                            <select name="group_id[]" class="form-control select2 col-md-5" id="kt_select2_2" multiple data-placeholder="-- {{__('Không chọn')}} --"   style="width: 100%" >

                                @if( !empty(old('group_id')) )
                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,old('group_id')) !!}
                                @else
                                    <?php $itSelect = [] ?>
                                    @if(isset($data))
                                        @foreach($data->groups as $gr)
                                            <?php array_push($itSelect, $gr->id)?>
                                        @endforeach
                                    @endif
                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,$itSelect) !!}
                                @endif
                            </select>

                            @if($errors->has('group_id'))
                                <div class="form-control-feedback">{{ $errors->first('group_id') }}</div>
                            @endif
                        </div>
                    </div>


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
{{--                            @dd(session()->get('shop_id'))--}}
                            <span class="">
                               @if(session()->get('shop_name') != null)
                                   @if(isset($setting_zip))
                                    <a  id="permalink" class="permalink" target="_blank" href="https://{{ session()->get('shop_name') }}/blog/{{ old('slug', isset($data) ? $data->slug : null) }}">

                                    <span class="default-slug">https://{{ session()->get('shop_name') }}/blog/<span id="label-slug" data-override-edit="0">{{ old('slug', isset($data) ? $data->slug : null) }}</span></span>
                                    @else
                                    <a  id="permalink" class="permalink" target="_blank" href="https://{{ session()->get('shop_name') }}/tin-tuc/{{ old('slug', isset($data) ? $data->slug : null) }}">

                                    <span class="default-slug">https://{{ session()->get('shop_name') }}/tin-tuc/<span id="label-slug" data-override-edit="0">{{ old('slug', isset($data) ? $data->slug : null) }}</span></span>
                                    @endif
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
                            <button class="btn btn-success btn-spin-content">Spin nội dung</button>
                            <textarea id="content" name="content" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ old('content', isset($data) ? $data->content : null) }}</textarea>
                            @if ($errors->has('content'))
                                <span class="form-text text-danger">{{ $errors->first('content') }}</span>
                            @endif
                        </div>
                    </div>

{{--                    @if(config('module.'.$module.'.key') == 'article' )--}}
{{--                        <div class="form-group row">--}}
{{--                            <div class="col-12 col-md-12">--}}
{{--                                <label for="position">{{ __('Google html') }}</label>--}}
{{--                                <textarea id="position" name="position" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ old('position', isset($data) ? $data->position : null) }}</textarea>--}}
{{--                                @if ($errors->has('position'))--}}
{{--                                    <span class="form-text text-danger">{{ $errors->first('position') }}</span>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endif--}}

                    <div class="form-group row">
                        {{-- target --}}
                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Kiểu mở link:') }}</label>
                            {{Form::select('target',[''=>'-- Không chọn --',1=>"Mở tab mới",2=>"Mở popup"],old('target', isset($data) ? $data->target : null),array('class'=>'form-control'))}}
                            @if($errors->has('target'))
                                <div class="form-control-feedback">{{ $errors->first('target') }}</div>
                            @endif
                        </div>


                        {{-- Url Link --}}
                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Url Link:') }}</label>
                            <input type="text"  name="url" value="{{ old('url', isset($data) ? $data->url : null) }}"
                                   placeholder="{{ __('Url Link') }}"
                                   class="form-control {{ $errors->has('url') ? ' is-invalid' : '' }}">
                            @if($errors->has('url'))
                                <div class="form-control-feedback">{{ $errors->first('url') }}</div>
                            @endif
                        </div>

                        {{-- position --}}
                        @if(!empty(config('module.'.$module.'.position')))
                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Vị trí hiển thị') }}</label>
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

                        <div class="col-md-8">
                            <label for="locale">{{ __('Ảnh mở rộng') }}:</label>
                            <div class="card" >
                                <div class="card-body p-3 ck-parent" style="min-height: 148px">
                                    <input class="image_input_text" type="hidden"  name="image_extension" value="{{ old('image_extension', isset($item) ? $item->image_extension : null) }}" type="text">

                                    <div class="sortable grid">


                                        @if (old('image_extension',isset($data)?$data->image_extension : null) != "")

                                            @foreach(explode('|', old('image_extension',isset($data)?$data->image_extension : null)) as $img)

                                                <div class="image-preview-box">
                                                    <img src="{{\App\Library\MediaHelpers::media($img)}}" alt="">
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
                                            max-width: 100%;
                                            max-height: 100%;
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


                    <div class="form-group row">
                        {{-----banner------}}
                        <div class="col-md-4">
                            <label for="locale">{{ __('Ảnh Banner') }}:</label>
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

                        {{-----icon------}}
                        <div class="col-md-4">
                            <label for="locale">{{ __('Ảnh icon') }}:</label>
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
                            <label for="locale">{{ __('Ảnh Logo') }}:</label>
                            <div class="">
                                <div class="fileinput ck-parent" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                        @if(old('image_logo', isset($data) ? $data->image_logo : null)!="")
                                            <img class="ck-thumb" src="{{ old('image_logo', isset($data) ? \App\Library\MediaHelpers::media($data->image_logo) : null) }}">
                                        @else
                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                        @endif
                                        <input class="ck-input" type="hidden" name="image_logo" value="{{ old('image_logo', isset($data) ? $data->image_logo : null) }}">

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
                            @if($errors->has('expired_at'))
                                <div class="form-control-feedback">{{ $errors->first('expired_at') }}</div>
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
            @if(config('module.'.$module.'.key') == 'article' )
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
                                            <a href="/admin/article/{{ $data->id }}/revision/{{ $log->id }}">{{ $log->created_at }}</a>
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
            @endif
        </div>
    </div>

    {{--Cấu hình giá bán--}}
    @if(config('module.'.$module.'.key') != 'article' )
    <div class="row">
        <div class="col-lg-9">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__('Cấu hình giá bán')}} <i class="mr-2"></i>
                            <span class="d-block text-muted pt-2 font-size-sm">{{__("Thiết lập giá bán và % giảm giá")}}</span>
                        </h3>
                    </div>

                </div>

                <div class="card-body">

                    <div class="form-group row">
                        {{-- price_old --}}
                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Giá bán (đ)') }}</label>
                            <input type="text" id="price_old"  name="price_old" value="{{ old('price_old', isset($data) ? $data->price_old : null) }}"
                                   placeholder="{{ __('Giá bán (đ)') }}"
                                   class="form-control input-price {{ $errors->has('url') ? ' is-invalid' : '' }}">
                            @if($errors->has('price_old'))
                                <div class="form-control-feedback">{{ $errors->first('price_old') }}</div>
                            @endif
                        </div>


                        {{-- percent_sale --}}
                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Giảm giá (%)') }}</label>
                            <input type="text" id="percent_sale"  name="percent_sale" value="{{ old('percent_sale', isset($data) ? $data->percent_sale : null) }}"
                                   placeholder="{{ __('Giảm giá (%)') }}" maxlength="3"
                                   class="form-control {{ $errors->has('percent_sale') ? ' is-invalid' : '' }}">
                            @if($errors->has('percent_sale'))
                                <div class="form-control-feedback">{{ $errors->first('percent_sale') }}</div>
                            @endif
                        </div>

                        {{-- price --}}
                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Giá khuyến mãi (giá bán còn lại) (đ)') }}</label>
                            <input type="text" id="price"  name="price" value="{{ old('price', isset($data) ? $data->price : null) }}"
                                   placeholder="{{ __('Giá khuyến mãi (giá bán còn lại) (đ)') }}"
                                   class="form-control input-price {{ $errors->has('price') ? ' is-invalid' : '' }}">
                            @if($errors->has('price'))
                                <div class="form-control-feedback">{{ $errors->first('price') }}</div>
                            @endif
                        </div>
                    </div>


                </div>
            </div>
        </div>

    </div>
    @else

    @endif
    {{--Cấu hình màu sắc--}}
    @if(config('module.'.$module.'.key') != 'article' )
        <div class="row">
            <div class="col-lg-9">
                <div class="card card-custom gutter-b">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">
                                {{__('Cấu hình màu sắc')}} <i class="mr-2"></i>
                                <span class="d-block text-muted pt-2 font-size-sm">{{__("Cấu hình 2 màu theo dạng mix 2 màu")}}</span>
                            </h3>
                        </div>

                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            {{-- Màu 1 --}}
                            <div class="col-12 col-md-4">
                                <label  class="form-control-label">{{ __('Màu 1') }}</label>
                                <div class="row">
                                    <div class="col-auto pl-0">
                                        <input class="form-control" type="text" name="params[color1]" value="{{ old('params[color1]', isset($data->params->color1) ? $data->params->color1 : null) }}"  pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor">
                                    </div>
                                    <div class="col-auto pr-0">
                                        <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ old('params[color1]', isset($data->params->color1) ? $data->params->color1 : null) }}">
                                    </div>
                                </div>
                                @if ($errors->has('params[color1]'))
                                    <span class="form-text text-danger">{{ $errors->first('params[color1]') }}</span>
                                @endif
                            </div>

                            {{-- Màu 2 --}}
                            <div class="col-12 col-md-4">
                                <label  class="form-control-label">{{ __('Màu 2') }}</label>
                                <div class="row">
                                    <div class="col-auto pl-0">
                                        <input class="form-control" type="text" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor2" name="params[color2]" value="{{ old('params[color1]', isset($data->params->color2) ? $data->params->color2 : null) }}">
                                    </div>
                                    <div class="col-auto pr-0">
                                        <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker2" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ old('params[color2]', isset($data->params->color2) ? $data->params->color2 : null) }}">
                                    </div>
                                </div>
                                @if ($errors->has('params[color2]'))
                                    <span class="form-text text-danger">{{ $errors->first('params[color2]') }}</span>
                                @endif
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>
    @else

    @endif
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
                    @if(config('module.'.$module.'.key') == 'article' )
                        @if(isset($data) && isset($data->params_plus))
                            @php
                                $question = json_decode($data->params_plus);
                                $first_question = null;
                                $first_answer = null;
                                $second_question = null;
                                $second_answer = null;
                                $three_question = null;
                                $three_answer = null;
                                $foor_question = null;
                                $foor_answer = null;

                                if (isset($question)){
                                    if (isset($question->first)){
                                        $first = json_decode($question->first);
                                        $first_question = $first->first_question;
                                        $first_answer = $first->first_answer;

                                    }
                                    if (isset($question->second)){
                                        $second = json_decode($question->second);
                                        $second_question = $second->second_question;
                                        $second_answer = $second->second_answer;

                                    }
                                    if (isset($question->three)){
                                        $three = json_decode($question->three);
                                        $three_question = $three->three_question;
                                        $three_answer = $three->three_answer;

                                    }

                                    if (isset($question->foor)){
                                        $foor = json_decode($question->foor);
                                        $foor_question = $foor->foor_question;
                                        $foor_answer = $foor->foor_answer;

                                    }
                                }


                            @endphp
                            <div class="row">
                                {{--ID--}}
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu hỏi thứ nhất') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="first_question" value="{{ isset($first_question) ? $first_question : '' }}"  placeholder="{{__('Câu hỏi thứ nhất')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu trả lời thứ nhất') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="first_answer" value="{{ isset($first_answer) ? $first_answer : '' }}"  placeholder="{{__('Câu trả lời thứ nhất')}}">
                                    </div>
                                </div>
                                {{--title--}}
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu hỏi thứ hai') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="second_question" value="{{ isset($second_question) ? $second_question : '' }}"  placeholder="{{__('Câu hỏi thứ hai')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu trả lời thứ hai') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="second_answer" value="{{ isset($second_answer) ? $second_answer : '' }}" placeholder="{{__('Câu trả lời thứ hai')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu hỏi thứ ba') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="three_question" value="{{ isset($three_question) ? $three_question : '' }}"  placeholder="{{__('Câu hỏi thứ ba')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu trả lời thứ ba') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="three_answer" value="{{ isset($three_answer) ? $three_answer : '' }}"  placeholder="{{__('Câu trả lời thứ ba')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu hỏi thứ tư') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="foor_question" value="{{ isset($foor_question) ? $foor_question : '' }}"  placeholder="{{__('Câu hỏi thứ tư')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu trả lời thứ tư') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="foor_answer" value="{{ isset($foor_answer) ? $foor_answer : '' }}"  placeholder="{{__('Câu trả lời thứ tư')}}">
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                {{--ID--}}
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu hỏi thứ nhất') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="first_question"  placeholder="{{__('Câu hỏi thứ nhất')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu trả lời thứ nhất') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="first_answer"  placeholder="{{__('Câu trả lời thứ nhất')}}">
                                    </div>
                                </div>
                                {{--title--}}
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu hỏi thứ hai') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="second_question"  placeholder="{{__('Câu hỏi thứ hai')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu trả lời thứ hai') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="second_answer"  placeholder="{{__('Câu trả lời thứ hai')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu hỏi thứ ba') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="three_question"  placeholder="{{__('Câu hỏi thứ ba')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu trả lời thứ ba') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="three_answer"  placeholder="{{__('Câu trả lời thứ ba')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu hỏi thứ tư') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="foor_question"  placeholder="{{__('Câu hỏi thứ tư')}}">
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-lg-6">
                                    <label  class="form-control-label">{{ __('Câu trả lời thứ tư') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datatable-input" name="foor_answer"  placeholder="{{__('Câu trả lời thứ tư')}}">
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    {{-- Lấy data từ params --}}
                    @if(config('module.'.$module.'.key') != 'article' )
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
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

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
                                    @if(isset($setting_zip))
                                    <span class="prefix_url">https://{{ session()->get('shop_name') }}/blog/</span><span id="google_slug" class="google_slug">{{ old('slug', isset($data) ? $data->slug : null) }}</span>
                                    @else
                                    <span class="prefix_url">https://{{ session()->get('shop_name') }}/tin-tuc/</span><span id="google_slug" class="google_slug">{{ old('slug', isset($data) ? $data->slug : null) }}</span>
                                    @endif
                                @else
                                    <span class="prefix_url">{{Request::getSchemeAndHttpHost()}}/</span><span id="google_slug" class="google_slug">{{ old('slug', isset($data) ? $data->slug : null) }}</span>
                                @endif
                            </div>
                            <div id="google_description" class="google_description" style="color: #545454;font-size: small;font-family: arial,sans-serif;">{{ old('description', isset($data) ? $data->description : null) !=""?old('description', isset($data) ? strip_tags(html_entity_decode($data->description)) : null):"Mô tả seo website không vượt quá 160 kí tự. Là những đoạn mô tả ngắn gọn về website, bài viết..." }}</div>
                        </div>
                    </fieldset>
                    @if(config('module.'.$module.'.key') == 'article' )
                    <div class="form-group row">
                        {{-----URL REDIRECT 301------}}
                        <div class="col-md-12">
                            <label  class="form-control-label">{{ __('Link url redirect 301') }}</label>
                            <input type="text" id="url_redirect_301"  name="url_redirect_301" value="{{ old('url_redirect_301', isset($data) ? $data->url_redirect_301 : null) }}"
                                   placeholder="{{ __('link url redirect 301') }}"
                                   class="form-control {{ $errors->has('url_redirect_301') ? ' is-invalid' : '' }}">
                            @if($errors->has('url_redirect_301'))
                                <div class="form-control-feedback">{{ $errors->first('url_redirect_301') }}</div>
                            @endif
                        </div>
                    </div>
                    @endif

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

    <input type="hidden" class="check__data__autosave" value="{{ $data->id??'' }}">
    @if(isset($data) && $data->module == "article")
        <input type="hidden" class="check__module__autosave" value="article">
    @else
        <input type="hidden" class="check__module__autosave" value="">
    @endif
@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')


    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>
    @if($module == "advertise")
        @if(auth()->user()->hasRole('admin') || auth()->user()->can('advertise-folder-image-show'))
            <script>
                $('#colorpicker').on('input', function() {
                    $('#hexcolor').val(this.value);
                });
                $('#hexcolor').on('input', function() {
                    $('#colorpicker').val(this.value);
                });

                $('#colorpicker2').on('input', function() {
                    $('#hexcolor2').val(this.value);
                });
                $('#hexcolor2').on('input', function() {
                    $('#colorpicker2').val(this.value);
                });

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
                        toolbarGroups: [ { name: 'basicstyles' } ],
                        on: {
                            instanceReady: function(ev) {

                                // Autosave but no more frequent than 5 sec.
                                var buffer = CKEDITOR.tools.eventsBuffer( 5000, function() {
                                    var editor = ev.editor.getData();
                                    var check__data__autosave = $('.check__data__autosave').val();
                                    var check__module__autosave = $('.check__module__autosave').val();

                                    if (check__data__autosave && check__module__autosave && editor){

                                        $.ajax({
                                            url: "{{ route('admin.article.autosave-content') }}",
                                            type:'POST',
                                            data: {
                                                _token:$('meta[name="csrf-token"]').attr('content'),
                                                id: {{ $data->id??0 }},
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

                                        console.log( 'Autosave!' );
                                    }


                                } );

                                this.on( 'change', buffer.input );
                            }
                        }
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
        @else
            <script>
                $('#colorpicker').on('input', function() {
                    $('#hexcolor').val(this.value);
                });
                $('#hexcolor').on('input', function() {
                    $('#colorpicker').val(this.value);
                });

                $('#colorpicker2').on('input', function() {
                    $('#hexcolor2').val(this.value);
                });
                $('#hexcolor2').on('input', function() {
                    $('#colorpicker2').val(this.value);
                });

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
                            filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_advertise', [$folder_image,$data->id??0]) }}",
                            filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_advertise', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                            filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_advertise', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                            filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_advertise', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                            filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_advertise', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                            filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_advertise', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
                            height:height,
                            startupMode:startupMode,
                        } );
                    });
                    $('.ckeditor-basic').each(function () {
                        var elem_id=$(this).prop('id');
                        var height=$(this).data('height');
                        height=height!=""?height:150;
                        CKEDITOR.replace(elem_id, {
                            filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_advertise', [$folder_image,$data->id??0]) }}",
                            filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_advertise', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                            filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_advertise', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                            filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_advertise', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                            filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_advertise', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                            filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_advertise', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
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
                            connectorPath: '{{route('admin.ckfinder_connector_advertise', [$folder_image,$data->id??0])}}',
                            resourceType: 'Images',
                            chooseFiles: true,
                            width: 900,
                            height: 600,
                            onInit: function (finder) {
                                finder.on('files:choose', function (evt) {
                                    var file = evt.data.files.first();
                                    var url = file.getUrl();
                                    elemThumb.attr("src",url);
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
                            connectorPath: '{{route('admin.ckfinder_connector_advertise', [$folder_image,$data->id??0])}}',
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
                                                <img src="${MEDIA_URL + file.get('url')}" alt="" data-input="${file.get( 'url' )}">
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
                            connectorPath: '{{route('admin.ckfinder_connector_advertise', [$folder_image,$data->id??0])}}',
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
        @endif
    @elseif($module == "article")
        @if(auth()->user()->hasRole('admin') || auth()->user()->can('article-folder-image-show'))
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
                                            <img src="${MEDIA_URL +file.get('url')}" alt="" data-input="${file.get( 'url' )}">
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
                        toolbarGroups: [ { name: 'basicstyles' } ],
                        on: {
                            instanceReady: function(ev) {

                                // Autosave but no more frequent than 5 sec.
                                var buffer = CKEDITOR.tools.eventsBuffer( 5000, function() {
                                    var editor = ev.editor.getData();
                                    var check__data__autosave = $('.check__data__autosave').val();
                                    var check__module__autosave = $('.check__module__autosave').val();

                                    if (check__data__autosave && check__module__autosave && editor){

                                        $.ajax({
                                            url: "{{ route('admin.article.autosave-content') }}",
                                            type:'POST',
                                            data: {
                                                _token:$('meta[name="csrf-token"]').attr('content'),
                                                id: {{ $data->id??'' }},
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

                                        console.log( 'Autosave!' );
                                    }


                                } );

                                this.on( 'change', buffer.input );
                            }
                        }
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
        @else
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
                            filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_article', [$folder_image,$data->id??0]) }}",
                            filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_article', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                            filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_article', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                            filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_article', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                            filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_article', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                            filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_article', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
                            height:height,
                            startupMode:startupMode,
                        } );
                    });
                    $('.ckeditor-basic').each(function () {
                        var elem_id=$(this).prop('id');
                        var height=$(this).data('height');
                        height=height!=""?height:150;
                        CKEDITOR.replace(elem_id, {
                            filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_article', [$folder_image,$data->id??0]) }}",
                            filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_article', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                            filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_article', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                            filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_article', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                            filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_article', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                            filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_article', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
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
                            connectorPath: '{{route('admin.ckfinder_connector_article', [$folder_image,$data->id??0])}}',
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
                            connectorPath: '{{route('admin.ckfinder_connector_article', [$folder_image,$data->id??0])}}',
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
                                                <img src="${MEDIA_URL + file.get('url')}" alt="" data-input="${file.get( 'url' )}">
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
                            connectorPath: '{{route('admin.ckfinder_connector_article', [$folder_image,$data->id??0])}}',
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
        @endif
    @else
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
                                            <img src="${MEDIA_URL + file.get('url')}" alt="" data-input="${file.get( 'url' )}">
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
                    toolbarGroups: [ { name: 'basicstyles' } ],
                    on: {
                        instanceReady: function(ev) {

                            // Autosave but no more frequent than 5 sec.
                            var buffer = CKEDITOR.tools.eventsBuffer( 5000, function() {
                                var editor = ev.editor.getData();
                                var check__data__autosave = $('.check__data__autosave').val();
                                var check__module__autosave = $('.check__module__autosave').val();

                                if (check__data__autosave && check__module__autosave && editor){

                                    $.ajax({
                                        url: "{{ route('admin.article.autosave-content') }}",
                                        type:'POST',
                                        data: {
                                            _token:$('meta[name="csrf-token"]').attr('content'),
                                            id: {{ $data->id??'' }},
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

                                    console.log( 'Autosave!' );
                                }


                            } );

                            this.on( 'change', buffer.input );
                        }
                    }
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
    @endif
@endsection


