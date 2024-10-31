@extends('admin._layouts.master')
@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.'.$module.'.index')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>

        @if(Auth::user()->can('service-config-update-config-base'))
            @if(isset($data))
                <a href="#" rel="{{$data->id}}"  data-toggle="modal" data-target="#updateConfigBaseModal"
                   class="btn btn-danger font-weight-bolder mr-2">
                    <i class="flaticon-cogwheel-1"></i>
                    Cập nhật cấu hình theo dịch vụ gốc
                </a>

            @endif
        @endif

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
                    {{--<div class="form-group row">--}}
                    {{--    <div class="col-12 col-md-6">--}}
                    {{--        <label>{{ __('Danh mục cha') }}</label>--}}
                    {{--        <select name="group_id[]" class="form-control select2 col-md-5" id="kt_select2_2" multiple data-placeholder="-- {{__('Không chọn')}} --"   style="width: 100%" >--}}

                    {{--            @if( !empty(old('group_id')) )--}}
                    {{--                {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,old('group_id')) !!}--}}
                    {{--            @else--}}
                    {{--                <?php $itSelect = [] ?>--}}
                    {{--                @if(isset($data))--}}
                    {{--                    @foreach($data->groups as $gr)--}}
                    {{--                        <?php array_push($itSelect, $gr->id)?>--}}
                    {{--                    @endforeach--}}
                    {{--                @endif--}}
                    {{--                {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,$itSelect) !!}--}}
                    {{--            @endif--}}
                    {{--        </select>--}}

                    {{--        @if($errors->has('group_id'))--}}
                    {{--            <div class="form-control-feedback">{{ $errors->first('group_id') }}</div>--}}
                    {{--        @endif--}}
                    {{--    </div>--}}

                    {{--    <div class="col-12 col-md-6">--}}
                    {{--        @if(isset($data))--}}
                    {{--            @php--}}
                    {{--                $account_fake =  \App\Library\Helpers::DecodeJson('account_fake',$data->params);--}}
                    {{--            @endphp--}}
                    {{--        @endif--}}
                    {{--        <label>{{ __('Bộ fake dịch vụ') }}</label>--}}
                    {{--        <input type="text" name="account_fake" value="{{ old('account_fake', isset($account_fake) ? $account_fake : null) }}" autofocus="true"--}}
                    {{--               placeholder="{{ __('Bộ fake dịch vụ') }}" maxlength="120"--}}
                    {{--               class="form-control {{ $errors->has('account_fake') ? ' is-invalid' : '' }}">--}}
                    {{--        @if ($errors->has('account_fake'))--}}
                    {{--            <span class="form-text text-danger">{{ $errors->first('account_fake') }}</span>--}}
                    {{--        @endif--}}
                    {{--    </div>--}}

                    {{--</div>--}}


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

                                @if(session()->get('shop_name') != null)
                                    <a  id="permalink" class="permalink" target="_blank" href="https://{{ session()->get('shop_name') }}/dich-vu/{{ old('slug', isset($data) ? $data->slug : null) }}">

                                <span class="default-slug">https://{{ session()->get('shop_name') }}/dich-vu/<span id="label-slug" data-override-edit="0">{{ old('slug', isset($data) ? $data->slug : null) }}</span></span>

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

                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="note">{{ __('Thông báo') }}</label>
                            <textarea id="note" name="note" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ old('note', isset($data) ? $data->note : null) }}</textarea>
                            @if ($errors->has('note'))
                                <span class="form-text text-danger">{{ $errors->first('note') }}</span>
                            @endif
                        </div>
                    </div>

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
                    {{-----fk data------}}
                    <div class="form-group row">

                        {{-- Số giao dịch --}}
                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Số giao dịch:') }}</label>
                            <input type="text"  name="params_plus[fk_buy]" value="{{ old('params_plus.fk_buy', $data->params_plus->fk_buy??"") }}"
                                   placeholder="{{ __('Số giao dịch') }}"
                                   class="form-control input_number {{ $errors->has('params_plus.fk_buy') ? ' is-invalid' : '' }}">
                            @if($errors->has('params_plus.fk_buy'))
                                <div class="form-control-feedback">{{ $errors->first('params_plus.fk_buy') }}</div>
                            @endif
                        </div>
                        {{-- Số giao dịch --}}
                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Số người online:') }}</label>
                            <input type="text"  name="params_plus[fk_online]" value="{{ old('params_plus.fk_online', $data->params_plus->fk_online??"" ) }}"
                                   placeholder="{{ __('Số người online') }}"
                                   class="form-control input_number {{ $errors->has('params_plus.fk_online') ? ' is-invalid' : '' }}">
                            @if($errors->has('params_plus.fk_online'))
                                <div class="form-control-feedback">{{ $errors->first('params_plus.fk_online') }}</div>
                            @endif
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
                    @if(auth()->user()->can('service-config-update-status'))
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                            {{Form::select('status',(config('module.service.status')??[]) ,old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
                            @if($errors->has('status'))
                                <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                            @endif
                        </div>

                    </div>
                    @endif
                    {{-- created_at --}}
                    {{-- <div class="form-group row">
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

                    </div> --}}


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
                                            <a href="/admin/service/{{ $data->id }}/revision/{{ $log->id }}">{{ $log->created_at }}</a>
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

    {{--Cấu hình giá bán--}}
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

                    {{--<div class="form-group row">--}}
                    {{--    --}}{{-- price_old --}}
                    {{--    <div class="col-12 col-md-4">--}}
                    {{--        <label  class="form-control-label">{{ __('Giá bán (đ)') }}</label>--}}
                    {{--        <input type="text" id="price_old"  name="price_old" value="{{ old('price_old', isset($data) ? $data->price_old : null) }}"--}}
                    {{--               placeholder="{{ __('Giá bán (đ)') }}"--}}
                    {{--               class="form-control input-price {{ $errors->has('url') ? ' is-invalid' : '' }}">--}}
                    {{--        @if($errors->has('price_old'))--}}
                    {{--            <div class="form-control-feedback">{{ $errors->first('price_old') }}</div>--}}
                    {{--        @endif--}}
                    {{--    </div>--}}


                    {{--    --}}{{-- percent_sale --}}
                    {{--    <div class="col-12 col-md-4">--}}
                    {{--        <label  class="form-control-label">{{ __('Giảm giá (%)') }}</label>--}}
                    {{--        <input type="text" id="percent_sale"  name="percent_sale" value="{{ old('percent_sale', isset($data) ? $data->percent_sale : null) }}"--}}
                    {{--               placeholder="{{ __('Giảm giá (%)') }}" maxlength="3"--}}
                    {{--               class="form-control {{ $errors->has('percent_sale') ? ' is-invalid' : '' }}">--}}
                    {{--        @if($errors->has('percent_sale'))--}}
                    {{--            <div class="form-control-feedback">{{ $errors->first('percent_sale') }}</div>--}}
                    {{--        @endif--}}
                    {{--    </div>--}}

                    {{--    --}}{{-- price --}}
                    {{--    <div class="col-12 col-md-4">--}}
                    {{--        <label  class="form-control-label">{{ __('Giá khuyến mãi (giá bán còn lại) (đ)') }}</label>--}}
                    {{--        <input type="text" id="price"  name="price" value="{{ old('price', isset($data) ? $data->price : null) }}"--}}
                    {{--               placeholder="{{ __('Giá khuyến mãi (giá bán còn lại) (đ)') }}"--}}
                    {{--               class="form-control input-price {{ $errors->has('price') ? ' is-invalid' : '' }}">--}}
                    {{--        @if($errors->has('price'))--}}
                    {{--            <div class="form-control-feedback">{{ $errors->first('price') }}</div>--}}
                    {{--        @endif--}}
                    {{--    </div>--}}
                    {{--</div>--}}

                    <div class="form-group row">
                        {{-- gate_id --}}
                        <div class="col-12 col-md-6">
                            <label  class="form-control-label">{{ __('Giao dịch tự động') }}</label>
                            {{Form::select('gate_id',[0=>"Không",1=>"Có"],old('gate_id', isset($data) ? $data->gate_id : null),array('class'=>'form-control'))}}
                            @if($errors->has('gate_id'))
                                <div class="form-control-feedback">{{ $errors->first('gate_id') }}</div>
                            @endif
                        </div>


                        {{-- idkey --}}
                        <div class="col-12 col-md-6">
                            <label  class="form-control-label">{{ __('Cổng Auto SMS Game') }}</label>
                            {{Form::select('idkey',[''=>'-- Không chọn --']+config('module.service.idkey'),old('idkey', isset($data) ? $data->idkey : null),array('class'=>'form-control','disabled'=>auth()->user()->can('service-config-change-gate-sms')?false:true   ))}}
                            @if($errors->has('idkey'))
                                <div class="form-control-feedback">{{ $errors->first('idkey') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Lựa chọn giá tính tiền cho CTV') }}</label>
                            {{Form::select('commission_type',[0=>"Giá gốc",1=>"Giá config"],old('commission_type', isset($data) ? \App\Library\Helpers::DecodeJson('commission_type',$data->params) : null),array('class'=>'form-control'))}}
                            @if($errors->has('commission_type'))
                                <div class="form-control-feedback">{{ $errors->first('commission_type') }}</div>
                            @endif
                        </div>

                        {{-- server_mode --}}
                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Máy chủ') }}</label>
                            {{Form::select('server_mode',[0=>"Không dùng",1=>"Dùng"],old('server_mode', isset($data) ? \App\Library\Helpers::DecodeJson('server_mode',$data->params) : null),array('class'=>'form-control'))}}
                            @if($errors->has('server_mode'))
                                <div class="form-control-feedback">{{ $errors->first('server_mode') }}</div>
                            @endif
                        </div>


                        {{-- server_price --}}

                        <div  style="display:{{old('server_price', isset($data) ? \App\Library\Helpers::DecodeJson('server_mode',$data->params) : null)==1?"block":"none" }}"  class="col-12 col-md-4 server-price">
                            <label  class="form-control-label">{{ __('Tính giá') }}</label>
                            {{Form::select('server_price',[0=>"Giống nhau",1=>"Khác nhau"],old('server_mode', isset($data) ? \App\Library\Helpers::DecodeJson('server_price',$data->params) : null),array('class'=>'form-control'))}}
                            @if($errors->has('server_price'))
                                <div class="form-control-feedback">{{ $errors->first('server_price') }}</div>
                            @endif
                        </div>
                    </div>



                    <div class="form-group row">
                        <div style="display:{{old('server_price', isset($data) ? \App\Library\Helpers::DecodeJson('server_mode',$data->params) : null)==1?"block":"none" }}"   class="col-sm-12 server-container options-container">
                            <label>Danh sách máy chủ (Hỗ trợ tối đa 50 máy chủ)</label>

                            @if(isset($data))
                                @php
                                    $server_id =  \App\Library\Helpers::DecodeJson('server_id',$data->params);
                                    $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                    $server_data_minValue =  \App\Library\Helpers::DecodeJson('server_data_minValue',$data->params);
                                    $server_data_maxValue =  \App\Library\Helpers::DecodeJson('server_data_maxValue',$data->params);

                                @endphp

                                @if(!empty($server_id) && count($server_id)>0)
                                    @for ($i = 0; $i < count($server_id); $i++)
                                        @if($server_data[$i]!="" && $server_data[$i]!='null')

                                            <div class="data-item">
                                                <div class="input-group">
                                                    <input value="{{$server_id[$i]}}" style="display:none;" type="text" name="server_id[]">
                                                    {{--<span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>--}}
                                                    <input value="{{$server_data[$i]}}" type="text" class="send-data form-control m-input m-input--air" name="server_data[]" placeholder="Tên máy chủ">
                                                    <input value="{{$server_data_minValue != null && $server_data_minValue[$i] != null ? $server_data_minValue[$i] : ""}}" type="text" class="send-data form-control m-input m-input--air" name="server_data_minValue[]" placeholder="Giá trị tối thiểu">
                                                    <input value="{{$server_data_maxValue != null && $server_data_maxValue[$i] != null ? $server_data_maxValue[$i] : ""}}" type="text" class="send-data form-control m-input m-input--air" name="server_data_maxValue[]" placeholder="Giá trị tối đa">
                                                    <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                    <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                </div>
                                            </div>
                                        @else
                                            {{--nếu server cấu hình empty thì vẫn ra ô cấu hình trống--}}
                                            <div class="data-item" >
                                                <div class="input-group">
                                                    <input value="0" style="display:none;" type="text" name="server_id[]">
                                                    {{--<span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>--}}
                                                    <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data[]" placeholder="Tên máy chủ">
                                                    <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data_minValue[]" placeholder="Giá trị tối thiểu">
                                                    <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data_maxValue[]" placeholder="Giá trị tối đa">
                                                    <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                    <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                </div>
                                            </div>
                                        @endif
                                    @endfor
                                @else
                                    <div class="data-item" >
                                        <div class="input-group">
                                            <input value="0" style="display:none;" type="text" name="server_id[]">
                                            {{--<span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>--}}
                                            <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data[]" placeholder="Tên máy chủ">
                                            <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data_minValue[]" placeholder="Giá trị tối thiểu">
                                            <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data_maxValue[]" placeholder="Giá trị tối đa">
                                            <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                            <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="data-item" >
                                    <div class="input-group">
                                        <input value="0" style="display:none;" type="text" name="server_id[]">
                                        {{--<span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>--}}
                                        <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data[]" placeholder="Tên máy chủ">
                                        <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data_minValue[]" placeholder="Giá trị tối thiểu">
                                        <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data_maxValue[]" placeholder="Giá trị tối đa">
                                        <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                        <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>




                    <div class="form-group row">

                        <div class="col-12 col-md-2">
                        </div>
                        {{-- filter_name --}}
                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Tên bảng giá') }}</label>

                            <input type="text" id="filter_name" name="filter_name" value="{{old('filter_name', isset($data) ? \App\Library\Helpers::DecodeJson('filter_name',$data->params) : null)}}"
                                   placeholder=""
                                   class="form-control {{ $errors->has('seo_title') ? ' is-invalid' : '' }}">
                            @if ($errors->has('seo_title'))
                                <span class="form-text text-danger">{{ $errors->first('seo_title') }}</span>
                            @endif


                        </div>


                        {{-- target --}}
                        <div class="col-12 col-md-4">
                            <label  class="form-control-label">{{ __('Loại') }}</label>
                            {{Form::select('filter_type',[
                                    3=>"Dạng tiền tệ",
                                    4=>"Dạng chọn một",
                                    5=>"Dạng chọn nhiều",
                                    6=>"Dạng chọn từ A->B",
                                    7=>"Dạng nhập tiền để thanh toán",

                                ],old('filter_type', isset($data) ? \App\Library\Helpers::DecodeJson('filter_type',$data->params) : 3),array('class'=>'form-control'))}}
                            @if($errors->has('filter_type'))
                                <div class="form-control-feedback">{{ $errors->first('filter_type') }}</div>
                            @endif

                        </div>
                    </div>



                    <div id="price_wrapper">

                        <div class="pack-settings">
                            <div class="form-group row ">
                                <div class="col-12 col-md-4">

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Số tiền thấp nhất</span>
                                        </div>
                                        <input value="{{old('input_pack_min', isset($data) ? \App\Library\Helpers::DecodeJson('input_pack_min',$data->params) : null)}}" type="text" class="form-control m-input m-input--air" name="input_pack_min" placeholder="Số tiền thấp nhất">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Số tiền cao nhất</span>
                                        </div>
                                        <input value="{{old('input_pack_max', isset($data) ? \App\Library\Helpers::DecodeJson('input_pack_max',$data->params) : null)}}" type="text" class="form-control m-input m-input--air" name="input_pack_max" placeholder="Số tiền cao nhất">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"> Tỉ lệ quy đổi trên 1k</span>
                                        </div>
                                        <input value="{{old('input_pack_rate', isset($data) ? \App\Library\Helpers::DecodeJson('input_pack_rate',$data->params) : null)}}" type="text" class="form-control m-input m-input--air" name="input_pack_rate" placeholder="Tỉ lệ">
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="form-group row" >
                            <div id="field_filter_container">
                                <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand" width="100%">
                                    <thead>
                                    <tr>
                                        <th style="width: 92px;" class="range muilti pack"></th>
                                        <th class="all">Máy chủ</th>
                                        <th class="range muilti single">Thuộc tính</th>
                                        <th class="pack">Thuộc tính</th>
                                        <th class="need_price_base">Giá gốc</th>
                                        <th class="all">Giá custom</th>
                                        <th class="pack">Hệ số</th>
                                        <th class="all">Tỉ giá (%)</th>
                                        <th class="pack">Hệ số hiển thị sau tỉ giá (%)</th>
                                        <th class="need_price_base">Giá hiển thị (+{{currency_format($ratioOfShop->additional_amount)}}) </th>
                                        <th class="pack">Giá trị thực nhận (Tiền x (Hệ số/Tỉ giá * 100) )</th>
                                        {{--<th class="all">Thời hạn hoàn thành</th>--}}
                                        {{--<th class="all">Phạt quá hạn</th>--}}
                                        {{--<th class="all">Thời hạn thưởng</th>--}}
                                        <th class="all">Tiền thưởng/Số Vật phẩm</th>
                                        <th class="range muilti pack">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($data))

                                        @php
                                            $name =  \App\Library\Helpers::DecodeJson('name',$data->params);
                                        @endphp

                                        @if(!empty($name) && count(array($name))>0)

                                            @for ($i = 0; $i < count($name); $i++)

                                                @if($name[$i]!="" && $name[$i]!=null)
                                                    <tr>
                                                        <th class="range muilti pack">
                                                        <span>
                                                            <a class="btnUp m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                                <i class="la la-arrow-up"></i>
                                                            </a>
                                                            <a class="btnDown m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                                <i class="la la-arrow-down"></i>
                                                            </a>
                                                        </span>
                                                        </th>

                                                        <th class="all"></th>
                                                        <th><input value="7" type="text" class="form-control m-input m-input--air" name="id[]"></th>
                                                        <th class="all"><input value="{{$name[$i]}}" type="text" class="form-control m-input m-input--air" name="name[]" placeholder="Tên thuộc tính"></th>
                                                        <th class="need_price_base">

                                                            @if(\App\Library\Helpers::DecodeJson('server_mode',$data->items->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->items->params)=="1")

                                                                @php
                                                                    $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->items->params);
                                                                @endphp

                                                                @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)

                                                                    @if($server_data[$p]!=null || $server_data[$p]!="[DELETE]"  || $server_data[$p]!="" )
                                                                        @php
                                                                            $price =  \App\Library\Helpers::DecodeJson('price'.$p,$data->items->params);
                                                                        @endphp

                                                                        <input id="sv{{$p}}" value="{{$price[$i]??""}}" type="text" class="form-control m-input m-input--air pack-input pack-price-base lock"  placeholder="Giá gốc {{$server_data[$p]}}">
                                                                    @endif

                                                                @endfor

                                                            @else

                                                                @php
                                                                    $price =  \App\Library\Helpers::DecodeJson('price',$data->items->params);
                                                                @endphp

                                                                <input  value="{{$price[$i]??""}}" type="text" class="form-control m-input m-input--air pack-input pack-price-base lock"  placeholder="Giá gốc">

                                                            @endif


                                                        </th>
                                                        <th class="all">
                                                            @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")

                                                                @php
                                                                    $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                                @endphp

                                                                @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)

                                                                    @if($server_data[$p]!=null || $server_data[$p]!="[DELETE]"  || $server_data[$p]!="" )
                                                                        @php
                                                                            $price =  \App\Library\Helpers::DecodeJson('price'.$p,$data->params);
                                                                        @endphp
                                                                        <input id="sv{{$p}}" value="{{$price[$i]??""}}" type="text" class="form-control m-input m-input--air pack-input pack-price" name="price{{$p}}[]" placeholder="Giá">
                                                                    @endif

                                                                @endfor

                                                            @else

                                                                @php
                                                                    $price =  \App\Library\Helpers::DecodeJson('price',$data->params);
                                                                @endphp
                                                                <input  value="{{$price[$i]??""}}" type="text" class="form-control m-input m-input--air pack-input pack-price" name="price[]" placeholder="Giá">

                                                            @endif


                                                        </th>
                                                        <th class="pack">
                                                            @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")
                                                                @php
                                                                    $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                                @endphp

                                                                @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                                    @if($server_data[$p]!=null || $server_data[$p]!="[DELETE]"  || $server_data[$p]!="" )
                                                                        @php
                                                                            $discount =  \App\Library\Helpers::DecodeJson('discount'.$p,$data->params);
                                                                        @endphp

                                                                        <input id="sv{{$p}}" value="{{$discount[$i]??""}}" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount{{$p}}[]" placeholder="Hệ số">
                                                                    @endif
                                                                @endfor

                                                            @else

                                                                @php
                                                                    $discount =  \App\Library\Helpers::DecodeJson('discount',$data->params);
                                                                @endphp
                                                                <input  value="{{$discount[$i]??""}}" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số">

                                                            @endif
                                                        </th>
                                                        <th class="all"><input type="text" class="form-control m-input m-input--air lock "  value="{{$ratioOfShop->ratio_percent}}" placeholder="Tỉ giá"></th>
                                                        <th class="pack">
                                                            @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")
                                                                @php
                                                                    $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                                @endphp

                                                                @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                                    @if($server_data[$p]!=null || $server_data[$p]!="[DELETE]" )
                                                                        @php
                                                                             $discountFinal= floatval(($discount[$i]??0)/$ratioOfShop->ratio_percent*100);
                                                                             $discountFinal=  floor($discountFinal *10)/10;
                                                                        @endphp


                                                                        <input id="sv{{$p}}" value="{{$discountFinal}}" type="text" class="form-control m-input m-input--air lock  pack-discount-display"  placeholder="Hệ số sau tỉ giá">
                                                                    @endif
                                                                @endfor

                                                            @else

                                                                @php
                                                                    $discountFinal= floatval($discount[$i]/$ratioOfShop->ratio_percent*100);
                                                                    $discountFinal=  floor($discountFinal *10)/10;
                                                                @endphp
                                                                <input  value="{{$discountFinal}}" type="text" class="form-control m-input m-input--air lock  pack-discount-display"  placeholder="Hệ số sau tỉ giá">

                                                            @endif
                                                        </th>
                                                        <th class="need_price_base">
                                                            @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")

                                                                @php
                                                                    $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                                @endphp

                                                                @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                                    @if($server_data[$p]!=null || $server_data[$p]!="[DELETE]" )
                                                                        @php
                                                                            $price =  \App\Library\Helpers::DecodeJson('price'.$p,$data->params);
                                                                        @endphp

                                                                        {{--nếu số tiền ở dịch vụ không được cấu hình--}}
                                                                        @if(isset($price[$i]))
                                                                            <input id="sv{{$p}}" value="{{($price[$i]*$ratioOfShop->ratio_percent/100)+$ratioOfShop->additional_amount}}" type="text" class="form-control m-input m-input--air pack-input  pack-price-display"   placeholder="Tiền hiển thị">
                                                                        @else
                                                                            <input id="sv{{$p}}" value="" type="text" class="form-control m-input m-input--air pack-input lock pack-price-display"   placeholder="Tiền hiển thị">
                                                                        @endif

                                                                    @endif

                                                                @endfor
                                                            @else

                                                                @php
                                                                    $price =  \App\Library\Helpers::DecodeJson('price',$data->params);
                                                                @endphp

                                                                @if(isset($price[$i]))
                                                                    <input  value="{{floor((int)$price[$i]*$ratioOfShop->ratio_percent/100)+$ratioOfShop->additional_amount}}" type="text" class="form-control m-input m-input--air pack-input lock pack-price-display"  placeholder="Tiền hiển thị">
                                                                @else
                                                                    <input id="sv{{$p??""}}" value="" type="text" class="form-control m-input m-input--air pack-input lock pack-price-display"   placeholder="Tiền hiển thị">
                                                                @endif
                                                            @endif



                                                        </th>
                                                        <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total lock" name="total[]" placeholder="Tiền"></th>
                                                        <th class="all">

                                                            @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")
                                                                @php
                                                                    $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                                @endphp

                                                                @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                                    @if($server_data[$p]!=null || $server_data[$p]!="[DELETE]" )
                                                                        @php
                                                                            $praise_price =  \App\Library\Helpers::DecodeJson('praise_price'.$p,$data->params);
                                                                        @endphp

                                                                        <input id="sv{{$p}}" value="{{$praise_price[$i]??""}}" type="text" class="form-control m-input m-input--air " name="praise_price{{$p}}[]" placeholder="Số lượng">
                                                                    @endif
                                                                @endfor
                                                            @else
                                                                @php
                                                                    $praise_price =  \App\Library\Helpers::DecodeJson('praise_price',$data->params);
                                                                @endphp
                                                                <input  value="{{$praise_price[$i]??""}}" type="text" class="form-control m-input m-input--air " name="praise_day[]" placeholder="Số lượng">
                                                            @endif
                                                        </th>
                                                        <th class="range muilti pack">
                                                            <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Xóa">
                                                                <i class="la la-trash-o"></i>
                                                            </a>


                                                        </th>
                                                    </tr>

                                                @else

                                                    {{--nếu cấu hình lỗi thì vẫn hiện 1 ô cấu hình mặc định--}}
                                                    <tr>
                                                        <th class="range muilti pack">
                                                        <span>
                                                            <a class="btnUp m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                                <i class="la la-arrow-up"></i>
                                                            </a>
                                                            <a class="btnDown m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                                <i class="la la-arrow-down"></i>
                                                            </a>
                                                        </span>
                                                        </th>
                                                        <th class="all"></th>
                                                        <th><input type="text" class="form-control m-input m-input--air" name="id[]"></th>
                                                        <th class="all"><input type="text" class="form-control m-input m-input--air" name="name[]" placeholder="Tên thuộc tính"></th>
                                                        <th class="need_price_base"><input type="text" class="form-control m-input m-input--air pack-input pack-price-base lock"  placeholder="Giá gốc"></th>
                                                        <th class="all"><input type="text" class="form-control m-input m-input--air pack-input pack-price" name="price[]" placeholder="Giá"></th>
                                                        <th class="pack"><input value="1" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số"></th>
                                                        <th class="all"><input type="text" class="form-control m-input m-input--air " name="" value="{{$ratioOfShop->ratio_percent}}" placeholder="Tỉ giá"></th>
                                                        <th class="pack"><input value="" type="text" class="form-control m-input m-input--air pack-input lock pack-discount-display"  placeholder="Hệ số sau tỉ giá"></th>
                                                        <th class="need_price_base"><input type="text" class="form-control m-input m-input--air pack-price-display lock" name="" value="" placeholder="Tiền hiển thị"></th>
                                                        <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total lock" name="total[]" placeholder="Tổng tiền"></th>

                                                        {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="day[]" placeholder="Phút"></th>--}}
                                                        {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="punish_price[]" placeholder="Tiền"></th>--}}
                                                        {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_day[]" placeholder="Phút"></th>--}}
                                                        <th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_price[]" placeholder="Tiền"></th>
                                                        <th class="range muilti pack">
                                                            <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Xóa">
                                                                <i class="la la-trash-o"></i>
                                                            </a>
                                                        </th>
                                                    </tr>
                                                @endif
                                            @endfor

                                        @else
                                            <tr>
                                                <th class="range muilti pack">
                                                <span>
                                                    <a class="btnUp m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                        <i class="la la-arrow-up"></i>
                                                    </a>
                                                    <a class="btnDown m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                        <i class="la la-arrow-down"></i>
                                                    </a>
                                                </span>
                                                </th>
                                                <th class="all"></th>
                                                <th><input type="text" class="form-control m-input m-input--air" name="id[]"></th>
                                                <th class="all"><input type="text" class="form-control m-input m-input--air" name="name[]" placeholder="Tên thuộc tính"></th>
                                                <th class=" need_price_base"><input type="text" class="form-control m-input m-input--air pack-input pack-price-base lock"  placeholder="Giá gốc"></th>
                                                <th class="all"><input type="text" class="form-control m-input m-input--air pack-input pack-price" name="price[]" placeholder="Giá"></th>
                                                <th class="pack"><input value="1" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số"></th>
                                                <th class="all"><input type="text" class="form-control m-input m-input--air " name="" value="{{$ratioOfShop->ratio_percent}}" placeholder="Tỉ giá"></th>
                                                <th class="pack"><input value="1" type="text" class="form-control m-input m-input--air pack-input lock pack-discount-display"  placeholder="Hệ số sau tỉ giá"></th>
                                                <th class="need_price_base"><input type="text" class="form-control m-input m-input--air pack-price-display lock" name="" value="" placeholder="Tiền hiển thị"></th>
                                                <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total lock" name="total[]" placeholder="Tổng tiền"></th>
                                                {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="day[]" placeholder="Phút"></th>--}}
                                                {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="punish_price[]" placeholder="Tiền"></th>--}}
                                                {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_day[]" placeholder="Phút"></th>--}}
                                                <th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_price[]" placeholder="Tiền"></th>
                                                <th class="range muilti pack" width="100px">
                                                    <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Xóa">
                                                        <i class="la la-trash-o"></i>
                                                    </a>

                                                </th>
                                            </tr>
                                        @endif

                                    @else

                                        <tr>
                                            <th class="range muilti pack">
                                                <span>
                                                    <a class="btnUp m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                        <i class="la la-arrow-up"></i>
                                                    </a>
                                                    <a class="btnDown m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                        <i class="la la-arrow-down"></i>
                                                    </a>
                                                </span>
                                            </th>
                                            <th class="all"></th>
                                            <th><input type="text" class="form-control m-input m-input--air" name="id[]"></th>
                                            <th class="all"><input type="text" class="form-control m-input m-input--air" name="name[]" placeholder="Tên thuộc tính"></th>
                                            <th class=" need_price_base"><input type="text" class="form-control m-input m-input--air pack-input pack-price-base lock"  placeholder="Giá gốc"></th>
                                            <th class="all"><input type="text" class="form-control m-input m-input--air pack-input pack-price" name="price[]" placeholder="Giá"></th>
                                            <th class="pack"><input value="1" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số"></th>
                                            <th class="all"><input type="text" class="form-control m-input m-input--air " name="" value="{{$ratioOfShop->ratio_percent}}" placeholder="Tỉ giá"></th>
                                            <th class="pack"><input value="1" type="text" class="form-control m-input m-input--air pack-input lock pack-discount-display" name="discount_display[]" placeholder="Hệ số sau tỉ giá"></th>
                                            <th class="need_price_base"><input type="text" class="form-control m-input m-input--air pack-price-display lock" name="" value="" placeholder="Tiền hiển thị"></th>
                                            <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total lock" name="total[]" placeholder="Tổng tiền"></th>
                                            {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="day[]" placeholder="Phút"></th>--}}
                                            {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="punish_price[]" placeholder="Tiền"></th>--}}
                                            {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_day[]" placeholder="Phút"></th>--}}
                                            <th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_price[]" placeholder="Tiền"></th>

                                            <th class="range muilti pack">
                                                <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Xóa">
                                                    <i class="la la-trash-o"></i>
                                                </a>

                                            </th>
                                        </tr>


                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>




                </div>
            </div>
        </div>

    </div>


    {{--Thêm thuộc tính yêu cầu khách nhập--}}
    <div class="row">
        <div class="col-lg-9">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__('Thuộc tính xác nhận (Yêu cầu khách hàng nhập)')}} <i class="mr-2"></i>
                        </h3>
                    </div>

                </div>


                <div class="card-body">

                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <div id="field_send_container" class="form-group m-form__group">
                                @if(isset($data))
                                    @php
                                        $send_name =  \App\Library\Helpers::DecodeJson('send_name',$data->params);
                                        $send_type =  \App\Library\Helpers::DecodeJson('send_type',$data->params);

                                    @endphp
                                    @if(!empty($send_name))
                                        @for ($i = 0; $i < count($send_name); $i++)

                                            @if( $send_name[$i]!=null)
                                                <div class="row cat-item">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            {{--<span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>--}}
                                                            <input value="{{$send_name[$i]}}" type="text" class="form-control m-input m-input--air" name="send_name[]" placeholder="Tên thuộc tính">
                                                            <select class="form-control m-input m-input--air" name="send_type[]">

                                                                <option value="1" {{$send_type[$i]=="1"?"selected":""}}>Dạng chữ</option>
                                                                <option value="2" {{$send_type[$i]=="2"?"selected":""}}>Dạng số</option>
                                                                <option value="3" {{$send_type[$i]=="3"?"selected":""}}>Dạng tiền tệ</option>
                                                                <option value="4" {{$send_type[$i]=="4"?"selected":""}}>Dạng hình ảnh</option>
                                                                <option value="5" {{$send_type[$i]=="5"?"selected":""}}>Dạng mật khẩu</option>
                                                                <option value="6" {{$send_type[$i]=="6"?"selected":""}}>Dạng chọn</option>
                                                                <option value="7" {{$send_type[$i]=="7"?"selected":""}}>Dạng checkbox</option>
                                                                <option value="8" {{$send_type[$i]=="8"?"selected":""}}>Dạng tài khoản</option>
                                                            </select>
                                                            <span class="input-group-addon btnUp"><i class="la la-arrow-up"></i></span>
                                                            <span class="input-group-addon btnDown"><i class="la la-arrow-down"></i></span>
                                                        </div>
                                                    </div>





                                                    @if($send_type[$i]=="6")

                                                        @php
                                                            $send_data =  \App\Library\Helpers::DecodeJson('send_data'.$i,$data->params);
                                                        @endphp
                                                        @if(!empty($send_data))
                                                            <div class="col-sm-12 cat-container options-container" style="">
                                                                <label>Cài đặt lựa chọn</label>
                                                                @for ($sd = 0; $sd < count($send_data); $sd++)
                                                                    @if( $send_data[$sd]!=null)
                                                                        <div class="data-item">
                                                                            <div class="input-group">
                                                                                <input  value="{{$sd}}" style="display:none" type="text" class="send-id" name="send_id{{$i}}[]">
                                                                                {{--<span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>--}}
                                                                                <input type="text" value="{{$send_data[$sd]}}" class="send-data form-control m-input m-input--air" name="send_data{{$i}}[]" placeholder="Tên lựa chọn">
                                                                                <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                                                <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                                            </div>
                                                                        </div>
                                                                    @endif


                                                                @endfor
                                                            </div>

                                                        @else

                                                            <div class="col-sm-12 cat-container options-container" style="">
                                                                <label>Cài đặt lựa chọn</label>
                                                                <div class="data-item">
                                                                    <div class="input-group">
                                                                        <input style="display:none;" type="text" class="send-id" name="send_id{{$i}}[]">
                                                                        {{--<span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>--}}
                                                                        <input type="text" class="send-data form-control m-input m-input--air" name="send_data{{$i}}[]" placeholder="Tên lựa chọn">
                                                                        <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                                        <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    @else

                                                        <div class="col-sm-12 cat-container options-container" style="display:none">
                                                            <label>Cài đặt lựa chọn</label>
                                                            <div class="data-item">
                                                                <div class="input-group">
                                                                    <input style="display:none;" type="text" class="send-id" name="send_id{{$i}}[]">
                                                                    {{--<span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>--}}
                                                                    <input type="text" class="send-data form-control m-input m-input--air" name="send_data{{$i}}[]" placeholder="Tên lựa chọn">
                                                                    <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                                    <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    @endif

                                                </div>
                                            @else
                                                {{--nếu thuộc tính cấu hình trống thì vẫn hiện 1 thuộc tính mặc định để config--}}
                                                <div class="row cat-item">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            {{--<span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>--}}
                                                            <input type="text" class="form-control m-input m-input--air" name="send_name[]" placeholder="Tên thuộc tính">
                                                            <select class="form-control m-input m-input--air" name="send_type[]">
                                                                <option value="1">Dạng chữ</option>
                                                                <option value="2">Dạng số</option>
                                                                <option value="3">Dạng tiền tệ</option>
                                                                <option value="4">Dạng hình ảnh</option>
                                                                <option value="5">Dạng mật khẩu</option>
                                                                <option value="6">Dạng chọn</option>
                                                                <option value="7">Dạng checkbox</option>
                                                                <option value="8">Dạng tài khoản</option>
                                                            </select>
                                                            <span class="input-group-addon btnUp"><i class="la la-arrow-up"></i></span>
                                                            <span class="input-group-addon btnDown"><i class="la la-arrow-down"></i></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 cat-container options-container" style="display: none;">
                                                        <label>Cài đặt lựa chọn</label>
                                                        <div class="data-item">
                                                            <div class="input-group">
                                                                {{--<span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>--}}
                                                                <input type="text" class="send-data form-control m-input m-input--air" name="send_data0[]" placeholder="Tên lựa chọn">
                                                                <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                                <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endfor
                                    @else
                                        <div class="row cat-item">
                                            <div class="col-sm-12">
                                                <div class="input-group">
                                                    {{--<span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>--}}
                                                    <input type="text" class="form-control m-input m-input--air" name="send_name[]" placeholder="Tên thuộc tính">
                                                    <select class="form-control m-input m-input--air" name="send_type[]">
                                                        <option value="1">Dạng chữ</option>
                                                        <option value="2">Dạng số</option>
                                                        <option value="3">Dạng tiền tệ</option>
                                                        <option value="4">Dạng hình ảnh</option>
                                                        <option value="5">Dạng mật khẩu</option>
                                                        <option value="6">Dạng chọn</option>
                                                        <option value="7">Dạng checkbox</option>
                                                        <option value="8">Dạng tài khoản</option>
                                                    </select>
                                                    <span class="input-group-addon btnUp"><i class="la la-arrow-up"></i></span>
                                                    <span class="input-group-addon btnDown"><i class="la la-arrow-down"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 cat-container options-container" style="display: none;">
                                                <label>Cài đặt lựa chọn</label>
                                                <div class="data-item">
                                                    <div class="input-group">
                                                        {{--<span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>--}}
                                                        <input type="text" class="send-data form-control m-input m-input--air" name="send_data0[]" placeholder="Tên lựa chọn">
                                                        <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                        <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="row cat-item">
                                        <div class="col-sm-12">
                                            <div class="input-group">
                                                {{--<span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>--}}
                                                <input type="text" class="form-control m-input m-input--air" name="send_name[]" placeholder="Tên thuộc tính">
                                                <select class="form-control m-input m-input--air" name="send_type[]">
                                                    <option value="1">Dạng chữ</option>
                                                    <option value="2">Dạng số</option>
                                                    <option value="3">Dạng tiền tệ</option>
                                                    <option value="4">Dạng hình ảnh</option>
                                                    <option value="5">Dạng mật khẩu</option>
                                                    <option value="6">Dạng chọn</option>
                                                    <option value="7">Dạng checkbox</option>
                                                    <option value="8">Dạng tài khoản</option>
                                                </select>
                                                <span class="input-group-addon btnUp"><i class="la la-arrow-up"></i></span>
                                                <span class="input-group-addon btnDown"><i class="la la-arrow-down"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 cat-container options-container" style="display: none;">
                                            <label>Cài đặt lựa chọn</label>
                                            <div class="data-item">
                                                <div class="input-group">
                                                    {{--<span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>--}}
                                                    <input type="text" class="send-data form-control m-input m-input--air" name="send_data0[]" placeholder="Tên lựa chọn">
                                                    <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                    <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif


                            </div>
                        </div>

                    </div>

                    {{-----Thêm thuộc tính------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            {{--<div class="text-right">--}}
                            {{--    <button id="btnAddSend" type="button" class="btn btn-primary m-btn m-btn--air">--}}
                            {{--        + Thêm thuộc tính--}}
                            {{--    </button>--}}
                            {{--</div>--}}
                            {{--<div class="text-right mt-5">--}}
                            {{--    <span style="margin-top:15px;" class="m-form__help text-right">Cho phép cấu hình tối đa 5 thuộc tính động</span>--}}
                            {{--</div>--}}
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
                                    <span class="prefix_url">https://{{ session()->get('shop_name') }}/dich-vu/</span><span id="google_slug" class="google_slug">{{ old('slug', isset($data) ? $data->slug : null) }}</span>
                                @else
                                    <span class="prefix_url">{{Request::getSchemeAndHttpHost()}}/</span><span id="google_slug" class="google_slug">{{ old('slug', isset($data) ? $data->slug : null) }}</span>
                                @endif
                            </div>
                            <div id="google_description" class="google_description" style="color: #545454;font-size: small;font-family: arial,sans-serif;">{{ old('description', isset($data) ? $data->description : null) !=""?old('description', isset($data) ? strip_tags(html_entity_decode($data->description)) : null):"Mô tả seo website không vượt quá 160 kí tự. Là những đoạn mô tả ngắn gọn về website, bài viết..." }}</div>
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



    <!-- updateConfigBaseModal  Modal -->
    <div class="modal fade" id="updateConfigBaseModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.'.$module.'.update-config-base',0),'class'=>'form-horizontal','id'=>'form-update-config-base','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn cập nhật cấu hình từ dịch vụ gốc?')}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-success m-btn m-btn--custom btn-submit-custom" data-form="form-update-config-base">{{__('Cập nhật')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>


@endsection

{{-- Styles Section --}}
@section('styles')

    <style>


        .m-table.m-table--head-bg-brand thead th {
            background: #716aca;
            color: #fff;
            border-bottom: 0;
            border-top: 0;
        }

        .data-item+.data-item {
            margin-top: 5px;
        }


        .data-item .btnRemoveOpt{
            margin-top: 10px;
            margin-right: 5px;
            cursor: pointer;

        }

        .cat-item+.cat-item{
            margin-top: 10px;
        }
        .cat-item.hide-data .cat-price{
            display: block !important;
        }
        .cat-item.hide-data #field_filter{
            display: none;
        }
        .data-item+.data-item{
            margin-top: 5px;
        }
        .lock{
            pointer-events: none;
            background-color: #eae9e9;
        }

        .options-container{
            border: 1px solid #f3f2f2;
            border-radius: 3px;
            padding: 10px 20px 20px 20px;
            margin: 10px;
            width: calc(100% - 20px);
            background-color: #fbfbfb;
            float: left;
            display: block;
        }

        th{
            display: none;
        }
        th input+input{
            margin-top: 5px;
        }

        .pack-settings{
            display: none;
        }
        .pack-mode .pack-settings{
            display: block;
        }
        .multi-mode th.muilti{
            display: table-cell;
        }
        .single-mode th.single{
            display: table-cell;
        }

        .multi-mode .need_price_base,.range-mode .need_price_base,.single-mode .need_price_base{
            display: table-cell;
        }
        th.all{
            display: table-cell;
        }
        .range-mode th.range{
            display: table-cell;
        }
        .pack-mode th.pack{
            display: table-cell;
        }

        .multi-mode .range{
            display: none;
        }



        .range-mode tbody tr:nth-child(1){
            pointer-events: none;
        }
        .range-mode tbody tr:nth-child(1) input{
            background-color: #eae9e9;
        }
        .range-mode tbody tr:nth-child(1) th:nth-child(1),.range-mode tbody tr:nth-child(1) th:nth-child(4),.range-mode tbody tr:nth-child(1) th:nth-child(12){
            pointer-events: initial;
        }
        .range-mode tbody tr:nth-child(1) th:nth-child(4) input{
            background-color: white;
        }
        .single-mode .single{
            display: none;
        }
        .single-mode tbody tr:nth-child(n+2){
            display: none;
        }

        tbody th:nth-child(2),thead th:nth-child(2){
            display: none;
        }

        tbody th:nth-child(2), tbody th:nth-child(8) input{
            pointer-events: none;
        }

        tbody th:nth-child(2) input, tbody th:nth-child(8) input{
            background-color: #eae9e9;
        }

        .server-all tbody th:nth-child(2),.server-all thead th:nth-child(2){
            display: table-cell;
        }

        .btnUp,.btnDown{
            padding: 4px !important;
        }
        #field_filter_container{width: 100%}
    </style>
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
                height=height!=""?height:150;
                var startupMode= $(this).data('startup-mode');
                if(startupMode=="source"){
                    startupMode="source";
                }
                else{
                    startupMode="wysiwyg";
                }

                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_service_config', [$folder_image,$data->id??0]) }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_service_config', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_service_config', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_service_config', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_service_config', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_service_config', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
                    height:height,
                    startupMode:startupMode,
                } );
            });
            $('.ckeditor-basic').each(function () {
                var elem_id=$(this).prop('id');
                var height=$(this).data('height');
                height=height!=""?height:150;
                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_service_config', [$folder_image,$data->id??0]) }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_service_config', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_service_config', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_service_config', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_service_config', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_service_config', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
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
                    connectorPath: '{{route('admin.ckfinder_connector_service_config', [$folder_image,$data->id??0])}}',
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
                    connectorPath: '{{route('admin.ckfinder_connector_service_config', [$folder_image,$data->id??0])}}',
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
                    connectorPath: '{{route('admin.ckfinder_connector_service_config', [$folder_image,$data->id??0])}}',
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
{{--    <script>--}}



{{--        $('.ckeditor-source').each(function () {--}}
{{--            var elem_id=$(this).prop('id');--}}
{{--            var height=$(this).data('height');--}}
{{--            height=height!=""?height:150;--}}
{{--            var startupMode= $(this).data('startup-mode');--}}
{{--            if(startupMode=="source"){--}}
{{--                startupMode="source";--}}
{{--            }--}}
{{--            else{--}}
{{--                startupMode="wysiwyg";--}}
{{--            }--}}

{{--            CKEDITOR.replace(elem_id, {--}}
{{--                filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",--}}
{{--                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",--}}
{{--                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",--}}
{{--                filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",--}}
{{--                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",--}}
{{--                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",--}}
{{--                height:height,--}}
{{--                startupMode:startupMode,--}}
{{--            } );--}}
{{--            CKEDITOR.on('instanceReady', function(ev) {--}}
{{--                var editor = ev.editor;--}}
{{--                editor.dataProcessor.htmlFilter.addRules({--}}
{{--                    elements : {--}}
{{--                        a : function( element ) {--}}
{{--                            if ( !element.attributes.rel ){--}}
{{--                                //gets content's a href values--}}
{{--                                var url = element.attributes.href;--}}

{{--                                //extract host names from URLs (IE safe)--}}
{{--                                var parser = document.createElement('a');--}}
{{--                                parser.href = url;--}}

{{--                                var hostname = parser.hostname;--}}
{{--                                if ( hostname !== window.location.host) {--}}
{{--                                    element.attributes.rel = 'nofollow';--}}
{{--                                    element.attributes.target = '_blank';--}}
{{--                                }--}}
{{--                            }--}}
{{--                        }--}}
{{--                    }--}}
{{--                });--}}
{{--            })--}}
{{--        });--}}


{{--        $('.ckeditor-basic').each(function () {--}}
{{--            var elem_id=$(this).prop('id');--}}
{{--            var height=$(this).data('height');--}}
{{--            height=height!=""?height:150;--}}
{{--            CKEDITOR.replace(elem_id, {--}}
{{--                filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",--}}
{{--                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",--}}
{{--                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",--}}
{{--                filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",--}}
{{--                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",--}}
{{--                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",--}}
{{--                height:height,--}}
{{--                removeButtons: 'Source',--}}
{{--            } );--}}

{{--            CKEDITOR.on('instanceReady', function(ev) {--}}
{{--                var editor = ev.editor;--}}
{{--                editor.dataProcessor.htmlFilter.addRules({--}}
{{--                    elements : {--}}
{{--                        a : function( element ) {--}}
{{--                            if ( !element.attributes.rel ){--}}
{{--                                //gets content's a href values--}}
{{--                                var url = element.attributes.href;--}}

{{--                                //extract host names from URLs (IE safe)--}}
{{--                                var parser = document.createElement('a');--}}
{{--                                parser.href = url;--}}

{{--                                var hostname = parser.hostname;--}}
{{--                                if ( hostname !== window.location.host) {--}}
{{--                                    element.attributes.rel = 'nofollow';--}}
{{--                                    element.attributes.target = '_blank';--}}
{{--                                }--}}
{{--                            }--}}
{{--                        }--}}
{{--                    }--}}
{{--                });--}}
{{--            })--}}
{{--        });--}}


{{--        // Image choose item--}}
{{--        $(".ck-popup").click(function (e) {--}}
{{--            e.preventDefault();--}}
{{--            var parent = $(this).closest('.ck-parent');--}}

{{--            var elemThumb = parent.find('.ck-thumb');--}}
{{--            var elemInput = parent.find('.ck-input');--}}
{{--            var elemBtnRemove = parent.find('.ck-btn-remove');--}}
{{--            CKFinder.modal({--}}
{{--                connectorPath: '{{route('admin.ckfinder_connector_service_config', [$folder_image,$data->id??0])}}',--}}
{{--                resourceType: 'Images',--}}
{{--                chooseFiles: true,--}}

{{--                width: 900,--}}
{{--                height: 600,--}}
{{--                onInit: function (finder) {--}}
{{--                    finder.on('files:choose', function (evt) {--}}
{{--                        var file = evt.data.files.first();--}}
{{--                        var url = file.getUrl();--}}
{{--                        elemThumb.attr("src", url);--}}
{{--                        elemInput.val(url);--}}

{{--                    });--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}
{{--        $(".ck-btn-remove").click(function (e) {--}}
{{--            e.preventDefault();--}}

{{--            var parent = $(this).closest('.ck-parent');--}}

{{--            var elemThumb = parent.find('.ck-thumb');--}}
{{--            var elemInput = parent.find('.ck-input');--}}
{{--            elemThumb.attr("src", "/assets/backend/themes/images/empty-photo.jpg");--}}
{{--            elemInput.val("");--}}
{{--        });--}}


{{--        //ckfinder for upload file--}}
{{--        $(".ck-popup-file").click(function (e) {--}}
{{--            e.preventDefault();--}}
{{--            var parent = $(this).closest('.ck-parent');--}}


{{--            var elemInput = parent.find('.ck-input');--}}
{{--            var elemBtnRemove = parent.find('.ck-btn-remove');--}}
{{--            CKFinder.modal({--}}
{{--                connectorPath: '{{route('admin.ckfinder_connector')}}',--}}
{{--                resourceType: 'Files',--}}
{{--                chooseFiles: true,--}}

{{--                width: 900,--}}
{{--                height: 600,--}}
{{--                onInit: function (finder) {--}}
{{--                    finder.on('files:choose', function (evt) {--}}
{{--                        var file = evt.data.files.first();--}}
{{--                        var url = file.getUrl();--}}
{{--                        elemInput.val(url);--}}

{{--                    });--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}


{{--    </script>--}}

    <script>
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
        $('[name="input_img"]').change(function () {
            $('#img_preview').hide();
        });
        $('select[name="filter_type"]').change(function () {
            $('#price_wrapper').removeClass('single-mode multi-mode range-mode pack-mode');
            if ([4, 5].indexOf(parseInt($(this).val())) != -1) {
                $('#price_wrapper').addClass('multi-mode');
            } else if ([3].indexOf(parseInt($(this).val())) != -1) {
                $('#price_wrapper').addClass('single-mode');
            } else if ([7].indexOf(parseInt($(this).val())) != -1) {
                $('#price_wrapper').addClass('pack-mode');
            } else {
                $('#price_wrapper').addClass('range-mode');
            }
        });
        $('select[name="filter_type"]').change();
        function UpdatePack() {

            if ($('.pack-discount:visible').length != 0) {
                $('table tbody tr').each((idx, elm) => {
                    $('.pack-price').each((inpi, inpe) => {
                        console.log('UpdatePack');

                        var id = $(inpe).attr('id');
                        //nếu nó ko có id ( ko phải là dạng chọn nhiều GIỐNG NHAU)
                        if (typeof id == 'undefined' || id =="") {
                            var price = parseInt($('.pack-price', elm).val());
                            var discount = parseFloat($('.pack-discount', elm).val());
                            var rate = parseInt($('[name="input_pack_rate"]').val());
                            var ratio = parseFloat($('.pack-ratio', elm).val());



                            discountFinal= parseFloat((discount/ratio*100));
                            discountFinal=  Math.floor(discountFinal *10)/10;

                            $('.pack-total', elm).val(parseInt((price / 1000) * rate * discountFinal));
                            $('.pack-discount-display', elm).val(discountFinal);
                            $('.pack-price-display', elm).val(parseInt((price *ratio/ 100))+{{$ratioOfShop->additional_amount}});

                        }
                        //nếu nó ko có id ( ko phải là dạng chọn nhiều máy chủ + Tính giá khác KHÁC NHAU)
                        else {

                            var price = parseInt($('#' + id + '.pack-price', elm).val());
                            var discount = parseFloat($('#' + id + '.pack-discount', elm).val());
                            var rate = parseInt($('[name="input_pack_rate"]').val());
                            var ratio = parseFloat($('.pack-ratio', elm).val());


                            discountFinal= parseFloat((discount/ratio*100));
                            discountFinal=  Math.floor(discountFinal *10)/10;

                            $('#' + id + '.pack-total', elm).val(parseInt((price / 1000) * rate * discountFinal));
                            $('#' + id + '.pack-discount-display', elm).val(discountFinal);
                            $('#' + id + '.pack-price-display', elm).val(parseInt((price *ratio/ 100))+{{$ratioOfShop->additional_amount}});

                        }

                    });
                });
            }




        }
        function UpdatePrice() {
            var server_mode = parseInt($('[name="server_mode"]').val());
            var server_price = parseInt($('[name="server_price"]').val());

            //nếu là dùng server

            if (server_mode == 1 && server_price == 1) {

                console.log('dùng nhiều server+ tính giá khác nhau');
                $('#price_wrapper').addClass('server-all');
                $('.server-container .data-item').each((idx, elm) => {
                    var id = $('[name="server_id[]"]', elm).val();
                    var name = $.trim($('[name="server_data[]"]', elm).val());

                    if (name != '')
                    {


                        $('table tbody tr').each((tri, tre) => {
                            $('th', tre).each((thi, the) => {

                                if (thi == 0 || thi == 2 || thi == 3  || thi == 12) {
                                    return;
                                }
                                if ($('input', the).length == 1) {
                                    if (!$('input', the).attr('id')) {
                                        $('input', the).attr('id', 'sv' + id);
                                    }
                                }
                                var input = $('input#sv' + id, the);
                                if (input.length == 0) {
                                    input = $('<input class="form-control m-input m-input--air " type="text" id="sv' + id + '" />');
                                    TableInpEvents(input);
                                    input.appendTo(the);
                                }
                                switch (thi) {
                                    case 1:
                                        input.val(name).addClass('server-name');
                                        break;
                                    case 4:
                                        //input.attr('name', 'price-base[]');
                                        //set giá gốc ở đây theo giá đổ ra
                                        input.addClass('pack-input lock pack-price-base');
                                        break;

                                    case 5:
                                        input.attr('name', 'price' + id + '[]');
                                        input.addClass('pack-input pack-price');
                                        break;

                                    case 6:
                                        input.attr('name', 'discount' + id + '[]');
                                        input.addClass('pack-input pack-discount');
                                        break;
                                    case 7:
                                        //input.attr('name', 'ratio' + id + '[]');
                                        input.addClass('pack-input pack-ratio');
                                        input.val('{{$ratioOfShop->ratio_percent}}');
                                        break;

                                    case 8:

                                        input.addClass('pack-input lock pack-discount-display');
                                        break;

                                    case 9:
                                        //input.attr('name', 'ratio' + id + '[]');
                                        input.addClass('pack-input lock pack-price-display');
                                        break;
                                    case 10:
                                        input.attr('name', 'total' + id + '[]');
                                        input.addClass('pack-input lock pack-total');
                                        break;
                                    // case 10:
                                    //     input.attr('name', 'discount' + id + '[]');
                                    //     input.addClass('pack-input pack-price-display');
                                    //     break;

                                    // case 7:
                                    //     input.attr('name', 'day' + id + '[]');
                                    //     break;
                                    // case 8:
                                    //     input.attr('name', 'punish_price' + id + '[]');
                                    //     break;
                                    // case 9:
                                    //     input.attr('name', 'praise_day' + id + '[]');
                                    //     break;
                                    case 11:
                                        input.attr('name', 'praise_price' + id + '[]');
                                        break;
                                }
                            });
                        });
                    }
                });
            }
            else {
                console.log('ko dùng nhiều server+ ko tính giá khác nhau');


                $('#price_wrapper').removeClass('server-all');
                $('table tbody tr').each((tri, tre) => {
                    $('th', tre).each((thi, the) => {

                        $('input', the).each((inpi, inpe) => {
                            if (inpi != 0) {
                                $(inpe).remove();
                            }
                            else{
                                $(inpe).attr("id","");
                            }
                        });
                        input = $('input', the);

                        if (input.length == 0)
                            return;

                        switch (thi) {
                            case 1:
                                input.val(name).addClass('server-name');
                                break;
                            case 4:
                                input.attr('name', 'price-base[]');
                                input.addClass('pack-input pack-price-base');
                                break;
                            case 5:
                                input.attr('name', 'price[]');
                                input.addClass('pack-input pack-price ');
                                break;
                            case 6:
                                input.attr('name', 'discount[]');
                                input.addClass('pack-input pack-discount');
                                break;
                            case 7:
                                // input.attr('name', 'ratio[]');
                                input.addClass('pack-input pack-ratio');
                                 input.val('{{$ratioOfShop->ratio_percent}}');
                                break;
                            case 9:
                                // input.attr('name', 'ratio[]');
                                input.addClass('pack-ratio-display');
                                break;

                            case 10:
                                input.attr('name', 'total[]');
                                input.addClass('pack-input pack-total');
                                break;
                            case 11:
                                input.attr('name', 'praise_price[]');
                                break;
                            // // case 7:
                            // //     input.attr('name', 'day[]');
                            // //     break;
                            // // case 8:
                            // //     input.attr('name', 'punish_price[]');
                            // //     break;
                            // // case 9:
                            // //     input.attr('name', 'praise_day[]');
                            // //     break;
                            // case 12:
                            //     input.attr('name', 'praise_price[]');
                            //     break;
                        }
                    });
                });
            }
        }
        UpdatePrice();
        $('[name="input_pack_rate"]').keyup(function () {

            UpdatePack();
        });

        // $('.pack-price').keyup(function () {
        //     console.log('update pack in price');
        //     UpdatePack();
        // });

        UpdatePack();
        function TableInpEvents(selector) {

            $(selector).focus(function () {
                var th = $(this).closest('tr');
                var index = th.index();
                var count = $('tbody tr').length;

                if (th.is(':last-child')) {

                    console.log('clone');
                    var a = $(this).closest('tr').first().clone(true);
                    $('input', a).not('.server-name,.pack-ratio,.pack-price-base,.pack-price-display').val('');

                    // a.appendTo($('tbody'));
                    //TableEvents(a);
                }
            }).blur(function () {
                var th = $(this).closest('tr');

                console.log('Tinh toan tai TableInpEvents')
                if ($(this).hasClass('pack-input')) {
                    var id = $(this).attr('id');

                    // $( "#filter_type option:selected" ).text();
                    var filter_type=$( "select[name=\"filter_type\"] option:selected").val();

                    //nếu đang là dạng nhập tiền thanh toán thì sẽ tính toán có cả discount
                    if(filter_type==7){
                        console.log('filter_type 7 - tính toán có cả discount');
                        if (typeof id == 'undefined' ||id =="") {
                            var price = parseInt($('.pack-price', th).val());
                            var discount = parseFloat($('.pack-discount', th).val());
                            var rate = parseFloat($('[name="input_pack_rate"]').val());
                            var ratio = parseFloat($('.pack-ratio', th).val());
                            discountFinal= parseFloat((discount/ratio*100));
                            discountFinal=  Math.floor(discountFinal *10)/10;

                            $('.pack-discount-display', th).val(discountFinal) ;
                            $('.pack-total', th).val(parseInt((price / 1000) * rate *discountFinal)) ;

                        } else {

                            var price = parseInt($('#' + id + '.pack-price', th).val());
                            var discount = parseFloat($('#' + id + '.pack-discount', th).val());
                            var rate = parseInt($('[name="input_pack_rate"]').val());
                            var ratio = parseFloat($('.pack-ratio', th).val());

                            discountFinal= parseFloat((discount/ratio*100));
                            discountFinal=  Math.floor(discountFinal *10)/10;
                            $('#' + id + '.pack-total', th).val(parseInt((price / 1000) * rate * discountFinal)) ;
                            $('#' + id + '.pack-discount-display', th).val(discountFinal) ;
                            $('#' + id + '.pack-price-display', th).val(parseInt((price / 1000) * rate * discountFinal)) ;

                        }
                    }
                    //nếu ko thì sẽ tính toán ko  discount
                    else{
                        console.log('tính toán ko có discount');
                        if (typeof id == 'undefined' || id =="") {

                            var price = parseInt($('.pack-price', th).val());
                            var ratio = parseFloat($('.pack-ratio', th).val());

                            $('.pack-price-display', th).val(parseInt(((price )  * ratio)/100)+{{$ratioOfShop->additional_amount}}) ;

                        } else {

                            var price = parseInt($('#' + id + '.pack-price', th).val());
                            var ratio = parseFloat($('.pack-ratio', th).val());

                            $('#' + id + '.pack-price-display', th).val(parseInt(((price)  * ratio)/100)+{{$ratioOfShop->additional_amount}}) ;
                        }

                    }


                }
            });
        }
        function TableEvents(selector) {

            TableInpEvents($('input', selector));
            $('.btnUp', selector).click(function () {
                var item = $(this).closest('tr');
                var prev = item.prev();
                if (prev.length != 0) {
                    item.insertBefore(prev);
                }
            });
            $('.btnDown', selector).click(function () {
                var item = $(this).closest('tr');
                var next = item.next();
                if (next.length != 0) {
                    item.insertAfter(next);
                }
            });
            $('.btnRemove', selector).click(function () {
                var fcount = $(this).closest('tbody').find('tr').length;
                if (fcount == 1)
                    return;
                $(this).closest('tr').remove();
                Update();
            });
        }
        TableEvents('table');
        function events() {
            $('.server-container input').focus(function () {

                var container = $(this).closest('.server-container');
                var index = $(this).closest('.data-item').index();
                var count = container.find('.data-item').length;
                if (index == count) {
                    console.log('clone template server');
                    // var a = $('.data-item', container).first().clone(true);
                    // $('input[type="text"]', a).val('');
                    // var newid = 0;
                    // while ($('.data-item [name="server_id[]"][value="' + newid + '"]', container).length != 0) {
                    //     newid++;
                    // }
                    // $('[name="server_id[]"]', a).attr('value', newid).val(newid);
                    // a.appendTo(container);
                }
                Update();
            }).blur(function () {
                UpdatePrice();
            });
            $('[name="filter"]').change(function () {
                UpdatePrice();
            });
            $('[name="server_mode"]').change(function () {
                if ($(this).val() == '1') {
                    $('.server-container,.server-price').show();
                } else {
                    $('.server-container,.server-price').hide();
                }
                UpdatePrice();
            });
            $('[name="server_price"]').change(function () {
                UpdatePrice();
            });
            $('.server-container .btnRemoveOpt').click(function () {
                var fcount = $('.server-container .data-item').length;
                if (fcount == 1)
                    return;
                var id = $(this).closest('.data-item').find('[name="server_id[]"]').val();
                $('table tbody tr').each((tri, tre) => {
                    $('input#sv' + id, tre).remove();
                });
                $(this).closest('.data-item').remove();
            });
            $('.server-container .btnUpOpt').click(function () {
                var item = $(this).closest('.data-item');
                var prev = item.prev();
                if (prev.length != 0 && prev.hasClass('data-item')) {
                    item.insertBefore(prev);
                }
            });
            $('.server-container .btnDownOpt').click(function () {
                var item = $(this).closest('.data-item');
                var next = item.next();
                if (next.length != 0) {
                    item.insertAfter(next);
                }
            });
        }
        events();
        function SendEvents(selector) {
            $('input', selector).focus(function () {
                var container = $(this).closest('.cat-container');
                var index = $(this).closest('.data-item').index();
                var count = container.find('.data-item').length;
                if (index == count) {
                    var a = $('.data-item', container).first().clone();
                    $('input[type="text"]', a).val('');
                    a.appendTo(container);
                    SendEvents(a);
                }
                Update();
            });
            $('.btnRemoveOpt', selector).click(function () {
                var container = $(this).closest('.cat-container');
                var count = container.find('.data-item').length;
                if (count == 1)
                    return;
                $(this).closest('.data-item').remove();
            });
            $('.btnUpOpt', selector).click(function () {
                var item = $(this).closest('.data-item');
                var prev = item.prev();
                if (prev.length != 0 && prev.hasClass('data-item')) {
                    item.insertBefore(prev);
                }
            });
            $('.btnDownOpt', selector).click(function () {
                var item = $(this).closest('.data-item');
                var next = item.next();
                if (next.length != 0) {
                    item.insertAfter(next);
                }
            });
            $('select[name="send_type[]"]', selector).change(function () {
                if ([6].indexOf(parseInt($(this).val())) == -1) {
                    $(this).closest('.cat-item').find('.cat-container').hide();
                } else {
                    $(this).closest('.cat-item').find('.cat-container').show();
                }
            });
            $('.btnRemove', selector).click(function () {
                var fcount = $(this).closest('.m-form__group').find('>.cat-item').length;
                if (fcount == 1)
                    return;
                $(this).closest('.cat-item').remove();
                Update();
            });
            $('.btnUp', selector).click(function () {
                var item = $(this).closest('.cat-item');
                var prev = item.prev();
                if (prev.length != 0) {
                    item.insertBefore(prev);
                }
            });
            $('.btnDown', selector).click(function () {
                var item = $(this).closest('.cat-item');
                var next = item.next();
                if (next.length != 0) {
                    item.insertAfter(next);
                }
            });
        }
        SendEvents('#field_send_container');
        function Update() {
            $('.cat-container').each(function (idx, elm) {
                $('.data-item input.send-data', elm).attr('name', 'send_data' + idx + '[]');
                $('.data-item input.send-id', elm).attr('name', 'send_id' + idx + '[]');
            });
        }
        Update();
        $('.summernote').summernote({
            height: 200,
        });

        $('[name="input_notify"]').summernote({
            height: 100,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']]
            ]
        });


        $('#updateConfigBaseModal').on('show.bs.modal', function(e) {
            //get data-id attribute of the clicked element
            var id = $(e.relatedTarget).attr('rel')
            $('#updateConfigBaseModal .id').attr('value', id);
        });

    </script>
@endsection


