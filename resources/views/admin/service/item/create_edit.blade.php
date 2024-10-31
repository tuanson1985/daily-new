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

    <div class="row marginauto" >
        <div class="col-md-12 left__right">
            <div class="card card-custom">
                <div class="card-header card-header-tabs-line nav-tabs-line-3x">
                    <div class="card-toolbar">
                        <ul class="nav nav-tabs nav-bold nav-tabs-line nav-tabs-line-3x">
                            <!--begin::Item-->
                            <li class="nav-item mr-3 ">
                                <a class="nav-link active" data-toggle="tab" href="#kt_user_edit_tab_1">
                                    <span class="nav-text font-size-lg">{{ __('Thông tin cơ bản.') }}</span>
                                </a>
                            </li>
                        @if(isset($data))
                            <!--begin::Item-->
                                <li class="nav-item mr-3 btn-show-log-edit">
                                    <a class="nav-link " data-toggle="tab" href="#kt_user_edit_tab_2">
                                        <span class="nav-text font-size-lg">{{ __('Lịch sử cập nhật.') }}</span>
                                    </a>
                                </li>
                        @endif
                        <!--end::Item-->
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0"  >
                    <div class="tab-content">
                        <div class="tab-pane show active" id="kt_user_edit_tab_1" role="tabpanel">
                            <div class="card-body p-0" style="background-color: #eef0f8">
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
                                                    <div class="col-12 col-md-6">
                                                        <label>{{ __('Danh mục cha') }}</label>
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
                                                    @if(isset($data) && Auth::user()->can('setup-rbx-api') && ($data->idkey == 'roblox_buygamepass' || $data->idkey == 'roblox_buyserver'))
                                                    <div class="col-12 col-md-6">
                                                        <label>{{ __('SETUP API RBX: ') }} <span style="color: red;">(Nếu không chọn mặc định ncc DAILY)</span></label>
                                                        <select name="url_type" class="form-control">
                                                            <option value="">=== Chọn nhà cung cấp ===</option>
                                                            <option
                                                                @if(!isset($data->url_type) || (isset($data->url_type) && $data->url_type == 1))
                                                                    selected
                                                                @endif
                                                                value="1"> DAILY </option>
                                                            @foreach(config('module.service-purchase-auto.supplier')??[] as $key => $rbx)
                                                                @if($key != 1)
                                                                    <option
                                                                        @if(isset($data->url_type) && $data->url_type == $key)
                                                                        selected
                                                                        @endif
                                                                        value="{{ $key }}"> {{ $rbx }} </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @endif
                                                </div>


                                                {{-----title------}}
                                                <div class="form-group row">
                                                    <div class="col-6 col-md-4">
                                                        <label>{{ __('Tiêu đề') }}</label>
                                                        <input type="text" id="title_gen_slug" name="title" value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus="true"
                                                               placeholder="{{ __('Tiêu đề') }}" maxlength="120"
                                                               class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}">
                                                        @if ($errors->has('title'))
                                                            <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>{{ __('Game') }}</label>
                                                        <select data-placeholder="-- {{__('Chọn game')}} --" name="providers_id" class="form-control select2 col-md-12" id="kt_select2_1">
                                                            <option value="">Chọn game</option>
                                                            @foreach($providers as $provider)
                                                                <option
                                                                    @if(isset($data))
                                                                    @if($data->providers_id && $data->providers_id == $provider->id)
                                                                    selected
                                                                    @endif
                                                                    @endif
                                                                    value="{{ $provider->id }}">{{ $provider->title }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if($errors->has('providers_id'))
                                                            <div class="form-control-feedback">{{ $errors->first('providers_id') }}</div>
                                                        @endif
                                                    </div>
                                                    {{-- Loại vật phẩm --}}
                                                    <div class="col-12 col-md-4">
                                                        <label  class="form-control-label">{{ __('Loại game:') }}</label>
                                                        @php
                                                            $game_type = $data->params_plus->game_type??"";
                                                        @endphp
                                                        <select class="form-control" name="params_plus[game_type]">
                                                            <option value=""> === Chọn loại game === </option>
                                                            @foreach(config('module.service.game_type') as $k_t => $type)
                                                                <option
                                                                    @if($k_t == $game_type)
                                                                    selected
                                                                    @endif
                                                                    value="{{ $k_t }}"> {{ $type }} </option>
                                                            @endforeach
                                                        </select>
                                                        @if($errors->has('params_plus.game_type'))
                                                            <div class="form-control-feedback">{{ $errors->first('params_plus.game_type') }}</div>
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

                                                @if(config('module.'.$module.'.key') == 'article' )
                                                    <div class="form-group row">
                                                        <div class="col-12 col-md-12">
                                                            <label for="google_html">{{ __('Google html') }}</label>
                                                            <textarea id="google_html" name="google_html" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ old('google_html', isset($data) ? $data->google_html : null) }}</textarea>
                                                            @if ($errors->has('google_html'))
                                                                <span class="form-text text-danger">{{ $errors->first('google_html') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

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


                                                {{-----shop_access------}}
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
                                                        {{Form::select('idkey',[''=>'-- Không chọn --']+config('module.service.idkey'),old('idkey', isset($data) ? $data->idkey : null),array('class'=>'form-control'))}}
                                                        @if($errors->has('idkey'))
                                                            <div class="form-control-feedback">{{ $errors->first('idkey') }}</div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row">

                                                    <div class="col-12 col-md-2">
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
                                                                                <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
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
                                                                                <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
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
                                                                        <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
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
                                                                    <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
                                                                    <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data[]" placeholder="Tên máy chủ">
                                                                    <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data_minValue[]" placeholder="Giá trị tối thiểu">
                                                                    <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data_maxValue[]" placeholder="Giá trị tối đa">
                                                                    <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                                    <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </div>

                                                    <div  class="btnAddServer {{old('server_price', isset($data) ? \App\Library\Helpers::DecodeJson('server_mode',$data->params) : null)==1?"btnAddServer_block":"btnAddServer_none" }}">
                                                        <button id="btnAddServer" class="btn btn-success">Thêm máy chủ</button>
                                                    </div>



                                                </div>




                                                <div class="form-group row">

                                                    {{--                        <div class="col-12 col-md-2">--}}
                                                    {{--                        </div>--}}
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
                                                                    @if(isset($data) && !empty(\App\Library\Helpers::DecodeJson('input_pack_min',$data->params)))
                                                                        <input value="{{old('input_pack_min', isset($data) ? number_format(\App\Library\Helpers::DecodeJson('input_pack_min',$data->params),0,'.',',') : null)}}" type="text" class="form-control m-input m-input--air input_pack_min number_price" name="input_pack_min" placeholder="Số tiền thấp nhất">
                                                                    @else
                                                                        <input value="" type="text" class="form-control m-input m-input--air input_pack_min number_price" name="input_pack_min" placeholder="Số tiền thấp nhất">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-md-4">

                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">Số tiền cao nhất</span>
                                                                    </div>
                                                                    @if(isset($data) && !empty(\App\Library\Helpers::DecodeJson('input_pack_max',$data->params)))
                                                                        <input value="{{old('input_pack_max', isset($data) ? number_format(\App\Library\Helpers::DecodeJson('input_pack_max',$data->params),0,'.',',') : null)}}" type="text" class="form-control m-input m-input--air input_pack_max number_price" name="input_pack_max" placeholder="Số tiền cao nhất">
                                                                    @else
                                                                        <input value="" type="text" class="form-control m-input m-input--air input_pack_max number_price" name="input_pack_max" placeholder="Số tiền cao nhất">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-md-4">

                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"> Tỉ lệ quy đổi trên 1k</span>
                                                                    </div>
                                                                    @if(isset($data) && !empty(\App\Library\Helpers::DecodeJson('input_pack_rate',$data->params)))
                                                                        <input value="{{old('input_pack_rate', isset($data) ? number_format(\App\Library\Helpers::DecodeJson('input_pack_rate',$data->params),0,'.',',') : null)}}" type="text" class="form-control m-input m-input--air" name="input_pack_rate" placeholder="Tỉ lệ">
                                                                    @else
                                                                        <input value="" type="text" class="form-control m-input m-input--air" name="input_pack_rate" placeholder="Tỉ lệ">
                                                                    @endif
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>

                                                    <div class="form-group row" >
                                                        <div id="field_filter_container">
                                                            <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width: 92px;" class="pack"></th>
                                                                    <th class="all">Máy chủ</th>
                                                                    <th class="keyword">keyword</th>
                                                                    <th class="range muilti single">Thuộc tính</th>
                                                                    <th class="pack">Thuộc tính</th>
                                                                    <th class="all">Giá gốc</th>
                                                                    <th class="pack">Hệ số bán ra</th>
                                                                    <th class="pack">= (Tiền x Hệ số)</th>
                                                                    <th class="all">Tiền thưởng</th>
                                                                    <th class="keyword">{{__('Idkey')}}</th>
                                                                    <th class="range muilti pack">Xóa</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @if(isset($data))

                                                                    @php
                                                                        $keyword =  \App\Library\Helpers::DecodeJson('keyword',$data->params);
                                                                        $name =  \App\Library\Helpers::DecodeJson('name',$data->params);
                                                                    @endphp

                                                                    @if(!empty($name) && count(array($name))>0)

                                                                        @for ($i = 0; $i < count($name); $i++)

                                                                            @if($name[$i]!="" && $name[$i]!=null)

                                                                                <tr>
                                                                                    <th class="pack">
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

                                                                                    <th class="keyword"><input value="{{$keyword[$i]??''}}" type="text" class="form-control m-input m-input--air {{ empty($keyword[$i]) ? 'pack-slug' : '' }} keyword_slug keyword_service" name="keyword[]" placeholder="keyword"></th>
                                                                                    <th class="all"><input value="{{$name[$i]}}" type="text" class="form-control m-input m-input--air attribute_service" name="name[]" placeholder="Tên thuộc tính"></th>

                                                                                    <th class="all">

                                                                                        @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")

                                                                                            @php
                                                                                                $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                                                            @endphp

                                                                                            @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)

                                                                                                @if($server_data[$p]!=null)
                                                                                                    @php
                                                                                                        $price =  \App\Library\Helpers::DecodeJson('price'.$p,$data->params);
                                                                                                    @endphp

                                                                                                    <input id="sv{{$p}}" value="{{ isset($price) ? number_format($price[$i],0,'.',',') : '' }}" type="text" class="form-control m-input m-input--air pack-input pack-price number_price" name="price{{$p}}[]" placeholder="Giá">

                                                                                                @endif

                                                                                            @endfor

                                                                                        @else

                                                                                            @php
                                                                                                $price =  \App\Library\Helpers::DecodeJson('price',$data->params);
                                                                                            @endphp

                                                                                            <input  value="{{ isset($price) ? number_format($price[$i],0,'.',',') : '' }}" type="text" class="form-control m-input m-input--air pack-input pack-price number_price" name="price[]" placeholder="Giá">

                                                                                        @endif


                                                                                    </th>

                                                                                    <th class="pack">
                                                                                        @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")
                                                                                            @php
                                                                                                $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                                                            @endphp

                                                                                            @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                                                                @if($server_data[$p]!=null)
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

                                                                                    <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total" name="total[]" placeholder="Tiền"></th>

                                                                                    <th class="all">

                                                                                        @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")
                                                                                            @php
                                                                                                $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                                                            @endphp

                                                                                            @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                                                                @if($server_data[$p]!=null)
                                                                                                    @php
                                                                                                        $praise_price =  \App\Library\Helpers::DecodeJson('praise_price'.$p,$data->params);
                                                                                                    @endphp

                                                                                                    <input id="sv{{$p}}" value="{{$praise_price[$i]??""}}" type="text" class="form-control m-input m-input--air " name="praise_price{{$p}}[]" placeholder="Phút">
                                                                                                @endif
                                                                                            @endfor
                                                                                        @else
                                                                                            @php
                                                                                                $praise_price =  \App\Library\Helpers::DecodeJson('praise_price',$data->params);
                                                                                            @endphp
                                                                                            <input  value="{{$praise_price[$i]??""}}" type="text" class="form-control m-input m-input--air " name="praise_day[]" placeholder="Tiền">
                                                                                        @endif
                                                                                    </th>
                                                                                    <th class="keyword">

                                                                                        @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")

                                                                                            @php
                                                                                                $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                                                            @endphp

                                                                                            @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)

                                                                                                @if($server_data[$p]!=null)
                                                                                                    @if(!empty(\App\Library\Helpers::DecodeJson('service_idkey'.$p,$data->params)))
                                                                                                        @php
                                                                                                            $service_idkey =  \App\Library\Helpers::DecodeJson('service_idkey'.$p,$data->params);
                                                                                                            $service = null;
                                                                                                            if (empty($service_idkey[$i])){
                                                                                                                if(!empty($keyword[$i])){
                                                                                                                    $service = Str::slug($keyword[$i]);
                                                                                                                }
                                                                                                            }else{
                                                                                                                $service = $service_idkey[$i];
                                                                                                            }

                                                                                                        @endphp
                                                                                                        <input id="sv{{$p}}" value="{{ $service }}" type="text" class="form-control m-input m-input--air pack-input pack-service pack-service_idkey" name="service_idkey{{$p}}[]" placeholder="IDKEY">
                                                                                                    @else
                                                                                                        @php
                                                                                                            $service_idkey =  \App\Library\Helpers::DecodeJson('service_idkey'.$p,$data->params);
                                                                                                            $service = null;
                                                                                                            if(!empty($keyword[$i])){
                                                                                                                $service = Str::slug($keyword[$i]);
                                                                                                            }

                                                                                                        @endphp
                                                                                                        <input id="sv{{$p}}" value="" type="text" class="form-control m-input m-input--air pack-input pack-service pack-service_idkey" name="service_idkey{{$p}}[]" placeholder="IDKEY">
                                                                                                    @endif
                                                                                                @endif

                                                                                            @endfor

                                                                                        @else
                                                                                            @if(!empty(\App\Library\Helpers::DecodeJson('service_idkey',$data->params)))
                                                                                                @php
                                                                                                    $service_idkey =  \App\Library\Helpers::DecodeJson('service_idkey',$data->params);
                                                                                                    $service = null;

                                                                                                    if (empty($service_idkey[$i])){
                                                                                                        if(!empty($keyword[$i])){
                                                                                                            $service = Str::slug($keyword[$i]);
                                                                                                        }
                                                                                                    }else{
                                                                                                        $service = $service_idkey[$i];
                                                                                                    }

                                                                                                @endphp
                                                                                                <input  value="{{ $service }}" type="text" class="form-control m-input m-input--air pack-service pack-input pack-service_idkey" name="service_idkey[]" placeholder="IDKEY">
                                                                                            @else
                                                                                                @php
                                                                                                    $service = null;
                                                                                                    if(!empty($keyword[$i])){
                                                                                                        $service = Str::slug($keyword[$i]);
                                                                                                    }
                                                                                                @endphp
                                                                                                <input  value="{{ $service }}" type="text" class="form-control m-input m-input--air pack-input pack-service pack-service_idkey" name="service_idkey[]" placeholder="IDKEY">
                                                                                            @endif
                                                                                        @endif


                                                                                    </th>
                                                                                    <th class="range muilti pack">
                                                                                        <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                                                            <i class="la la-trash-o"></i>
                                                                                        </a>
                                                                                    </th>
                                                                                </tr>

                                                                            @else

                                                                                {{--nếu cấu hình lỗi thì vẫn hiện 1 ô cấu hình mặc định--}}
                                                                                <tr>
                                                                                    <th class="pack">
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
                                                                                    <th class="keyword"><input value="" type="text" class="form-control m-input m-input--air keyword_slug pack-slug keyword_service" name="keyword[]" placeholder="keyword"></th>
                                                                                    <th class="all"><input type="text" class="form-control m-input m-input--air attribute_service" name="name[]" placeholder="Tên thuộc tính"></th>
                                                                                    <th class="all"><input type="text" class="form-control m-input m-input--air pack-input pack-price number_price" name="price[]" placeholder="Giá"></th>
                                                                                    <th class="pack"><input value="1" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số"></th>
                                                                                    <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total" name="total[]" placeholder="Tiền"></th>
                                                                                    <th class="all"><input type="text" class="form-control m-input m-input--air" name="day[]" placeholder="Phút"></th>
                                                                                    {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="punish_price[]" placeholder="Tiền"></th>--}}
                                                                                    {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_day[]" placeholder="Phút"></th>--}}
                                                                                    {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_price[]" placeholder="Tiền"></th>--}}
                                                                                    <th class="keyword"><input type="text" class="form-control m-input m-input--air pack-input pack-service pack-service_idkey" name="service_idkey[]" placeholder="IDKEY"></th>

                                                                                    <th class="range muilti pack">
                                                                                        <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                                                            <i class="la la-trash-o"></i>
                                                                                        </a>
                                                                                    </th>
                                                                                </tr>
                                                                            @endif
                                                                        @endfor

                                                                    @else
                                                                        <tr>
                                                                            <th class="pack">
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
                                                                            <th class="keyword"><input value="" type="text" class="form-control m-input m-input--air pack-slug keyword_slug keyword_service" name="keyword[]" placeholder="keyword"></th>
                                                                            <th class="all"><input type="text" class="form-control m-input m-input--air attribute_service" name="name[]" placeholder="Tên thuộc tính"></th>
                                                                            <th class="all"><input type="text" class="form-control m-input m-input--air pack-input pack-price number_price" name="price[]" placeholder="Giá"></th>
                                                                            <th class="pack"><input value="1" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số"></th>
                                                                            <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total" name="total[]" placeholder="Tiền"></th>
                                                                            <th class="all"><input type="text" class="form-control m-input m-input--air" name="day[]" placeholder="Phút"></th>
                                                                            {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="punish_price[]" placeholder="Tiền"></th>--}}
                                                                            {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_day[]" placeholder="Phút"></th>--}}
                                                                            {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_price[]" placeholder="Tiền"></th>--}}
                                                                            <th class="keyword"><input type="text" class="form-control m-input m-input--air pack-input pack-service pack-service_idkey" name="service_idkey[]" placeholder="IDKEY"></th>

                                                                            <th class="range muilti pack">
                                                                                <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                                                    <i class="la la-trash-o"></i>
                                                                                </a>
                                                                            </th>
                                                                        </tr>
                                                                    @endif
                                                                @else

                                                                    <tr>
                                                                        <th class="pack">
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
                                                                        <th class="keyword"><input value="" type="text" class="form-control m-input m-input--air keyword_slug pack-slug keyword_service" name="keyword[]" placeholder="keyword"></th>
                                                                        <th class="all"><input type="text" class="form-control m-input m-input--air attribute_service" name="name[]" placeholder="Tên thuộc tính"></th>
                                                                        <th class="all"><input type="text" class="form-control m-input m-input--air pack-input pack-price number_price" name="price[]" placeholder="Giá"></th>
                                                                        <th class="pack"><input value="1" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số"></th>
                                                                        <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total" name="total[]" placeholder="Tổng tiền"></th>
                                                                        {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="day[]" placeholder="Phút"></th>--}}
                                                                        {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="punish_price[]" placeholder="Tiền"></th>--}}
                                                                        {{--<th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_day[]" placeholder="Phút"></th>--}}
                                                                        <th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_price[]" placeholder="Tiền"></th>
                                                                        <th class="keyword"><input type="text" class="form-control m-input m-input--air pack-input pack-service pack-service_idkey" name="service_idkey[]" placeholder="IDKEY"></th>

                                                                        <th class="range muilti pack">
                                                                            <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                                                <i class="la la-trash-o"></i>
                                                                            </a>
                                                                        </th>
                                                                    </tr>


                                                                @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row" >
                                                        <div class="col-auto">
                                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#showFinterExcel">Thêm nhanh</button>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button type="button" class="btn btn-success kt_chect_trung">Check trùng</button>
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
                                                                                        <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
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
                                                                                                            <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
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
                                                                                                    <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
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
                                                                                                <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
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
                                                                                        <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
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
                                                                                            <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
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
                                                                                <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
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
                                                                                    <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
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
                                                                            <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
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
                                                                                <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
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
                                                        <div class="text-right">
                                                            <button id="btnAddSend" type="button" class="btn btn-primary m-btn m-btn--air">
                                                                + Thêm thuộc tính
                                                            </button>
                                                        </div>
                                                        <div class="text-right mt-5">
                                                            <span style="margin-top:15px;" class="m-form__help text-right">Cho phép cấu hình tối đa 5 thuộc tính động</span>
                                                        </div>
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
                                                            @endif                             </div>
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

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!--end::Row-->
                        </div>
                        @if(isset($data))
                            <div class="tab-pane show " id="kt_user_edit_tab_2" role="tabpanel">
                                <div class="card-body">
                                    <div class="row">
                                        {{--ID--}}
                                        <div class="form-group col-12 col-sm-12 col-lg-12 d-flex justify-content-between">
                                            <div class="w-100">
                                                <h4 class="card-label">
                                                    {{ __('Lịch sử cấu hình') }} <i class="mr-2"></i>
                                                </h4>
                                                <div class="color-note d-flex mt-8">
                                                    <div class="empty mr-2"></div> <span>Empty</span>
                                                    <div class="replace mr-2 ml-4"></div> <span>Replace</span>
                                                    <div class="delete mr-2 ml-4"></div> <span>Delete</span>
                                                    <div class="insert mr-2 ml-4"></div> <span>Insert</span>
                                                </div>
                                                <div class="row mt-8">
                                                    <div class="col-lg-2 col-12">
                                                        <div class="card f_card-custom">
                                                            <div class="card-header" style="padding: 16px; min-height: auto; border:none">
                                                                <h3 class="card-label" style="font-size: 16px; font-weight: 600;margin-bottom:0;">
                                                                    {{ __('Lịch sử thay đổi') }}
                                                                </h3>
                                                            </div>
                                                            <div class="card-body" style="padding: 0 16px">
                                                                <div id="variantLogList">
                                                                    {{--                                                                                    <div class="spinner spinner-success mr-15"></div>--}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-10 col-12">
                                                        <div class="card f_card-custom" style="">
                                                            <div class="card-header" style="padding: 16px; min-height: auto; border:none;justify-content: space-between;display: flex">
                                                                <h3 class="card-label" style="font-size: 16px; font-weight: 600;margin-bottom:0;">
                                                                    {{ __('Chi tiết thay đổi') }}
                                                                </h3>
                                                                <input type="hidden" value="" class="id_edit_setting">
                                                                <button type="button" class="btn btn-info btt_rechange_setting">Khôi phục</button>
                                                            </div>
                                                            <div class="card-body" style="padding: 0 16px">
                                                                <div id="logDetail">

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!--end::Row-->
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ Form::close() }}

    <div class="modal fade" id="showFinterExcel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-weight: 500;font-size: 18px"> {{__('Thêm thuộc tính.')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-5">
                            <select class="form-control option-finter">
                                <option value="0">Không giữ lại dữ liệu cũ</option>
                                <option value="1">Giữ lại thông tin cũ</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <textarea class="form-control" id="textarea-finter"  style="height: 331px;"> </textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                    <button type="button" class="btn btn-success btn-add-finter">{{__('Thêm')}}</button>

                </div>
            </div>
        </div>
    </div>

    <!-- rechargeModal -->
    <div class="modal fade" id="modalRechange">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service.rechange',0),'class'=>'form-horizontal form-submit-ajax','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự không phục dữ liệu?')}}

                </div>
                <div class="modal-footer">
                    <input type="hidden" value="" name="id_edit" class="id_edit">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success m-btn m-btn--custom">Xác nhận</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection

{{-- Styles Section --}}
@section('styles')

    <style>

        .keyword-mode .keyword-settings{
            display: block;
        }

        .keyword-settings{
            display: none;
        }
        .keyword-mode th.keyword{
            display: table-cell;
        }

        .btnAddServer_block{
            display: block;
        }
        .btnAddServer_none{
            display: none;
        }
        .btnAddServer{
            width: 100%;
            text-align: right;
            margin-top: 15px;
            margin-right: 15px;
        }

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
        th.all{
            display: table-cell;
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
        .range-mode tbody tr:nth-child(1) th:nth-child(1),.range-mode tbody tr:nth-child(1) th:nth-child(5),.range-mode tbody tr:nth-child(1) th:nth-child(12){
            pointer-events: initial;
        }
        .range-mode tbody tr:nth-child(1) th:nth-child(5) input{
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

        tbody th:nth-child(2), tbody th:nth-child(8){
            pointer-events: none;
        }

        tbody th:nth-child(2) input, tbody th:nth-child(8) input{
            background-color: #eae9e9;
        }

        .server-all tbody th:nth-child(2),.server-all thead th:nth-child(2){
            display: table-cell;
        }

        .pack-service{
            pointer-events: none;
            background-color: #eae9e9;
        }

        .btnUp,.btnDown{
            padding: 4px !important;
        }
        #field_filter_container{width: 100%}
    </style>
@endsection
<link rel="stylesheet" type="text/css" href="/assets/backend/assets/css/diffview.css?v={{time()}}"/>
{{-- Scripts Section --}}
@section('scripts')
    <script type="text/javascript" src="/assets/backend/assets/js/diffview.js?v={{time()}}"></script>
    <script type="text/javascript" src="/assets/backend/assets/js/difflib.js?v={{time()}}"></script>
    <script>

        $(document).ready(function () {

            $('body').on('input','.number_price',function(){
                // Lấy giá trị nhập vào
                var inputValue = $(this).val().replace(/[^0-9.]/g, ''); // Lọc chỉ giữ lại chữ số và dấu chấm
                var parts = inputValue.split('.'); // Tách phần nguyên và phần thập phân
                // Định dạng phần nguyên
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                // Giới hạn phần thập phân chỉ có tối đa 2 chữ số
                if (parts.length > 1) {
                    parts[1] = parts[1].slice(0, 2);
                }
                // Gán giá trị mới vào ô input
                $(this).val(parts.join('.'));
            })

            function number_price(price){
                var inputValue = price; // Lọc chỉ giữ lại chữ số và dấu chấm
                var parts = inputValue.split('.'); // Tách phần nguyên và phần thập phân
                // Định dạng phần nguyên
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                // Giới hạn phần thập phân chỉ có tối đa 2 chữ số
                if (parts.length > 1) {
                    parts[1] = parts[1].slice(0, 2);
                }
                // Gán giá trị mới vào ô input
                return parts.join('.');
            }

            $('body').on('click', '.btn-add-finter', function(e) {

                let check_server = 0;
                //Kiểm tra chọn máy chủ
                let check_server_mode = parseInt($('[name="server_mode"]').val());
                //Kiểm tra máy chủ tính giá khác nhau hay không
                let check_server_price = parseInt($('[name="server_price"]').val());

                let option_finter = parseInt($('.option-finter').val());

                if (check_server_mode == 1 && check_server_price == 1){
                    check_server = 1;
                }

                if (check_server == 0){
                    let check_filter_type = parseInt($('[name="filter_type"]').val());

                    if (check_filter_type == 4 || check_filter_type == 5 || check_filter_type == 6){

                        // if (check)
                        let text = document.getElementById("textarea-finter").value;

                        let rows = text.split('\n');
                        let cols = [];
                        //
                        rows.forEach(row => {
                            let col = row.split('\t');
                            cols.push(col);
                        })

                        let field_filter_container = $('#field_filter_container');
                        let field_filter_container_body = field_filter_container.find('tbody');
                        if (option_finter == 0){
                            field_filter_container_body.html('');
                        }

                        if (cols.length){
                            cols.forEach(function(item, index) {
                                if (check_filter_type == 6){
                                    if (index == 0 && option_finter == 0){

                                        let html = `<tr>
                                            <th class="pack">
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
                                            <th class="all"><input value="${item[0]}" type="text" class="form-control m-input m-input--air" name="name[]" placeholder="Tên thuộc tính"></th>
                                            <th class="all">
                                                    <input value="0" type="text" class="form-control m-input m-input--air pack-input pack-price" name="price[]" placeholder="Giá">
                                            </th>
                                            <th class="pack">
                                                <input value="" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số">
                                            </th>
                                            <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total" name="total[]" placeholder="Tiền"></th>
                                            <th class="all">
                                                <input value="" type="text" class="form-control m-input m-input--air " name="praise_price[]" placeholder="Tiền">
                                            </th>
                                            <th class="range muilti pack">
                                                <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                    <i class="la la-trash-o"></i>
                                                </a>
                                            </th>
                                        </tr>`;

                                        field_filter_container_body.append(html);
                                    }else {
                                        let index_1 = number_price(item[1]);
                                        let html = `<tr>
                                            <th class="pack">
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
                                            <th class="all"><input value="${item[0]}" type="text" class="form-control m-input m-input--air" name="name[]" placeholder="Tên thuộc tính"></th>
                                            <th class="all">
                                                <input value="${index_1}" type="text" class="form-control m-input m-input--air pack-input pack-price " name="price[]" placeholder="Giá">
                                            </th>
                                            <th class="pack">
                                                <input value="1" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số">
                                            </th>

                                            <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total" name="total[]" placeholder="Tiền"></th>
                                            <th class="all">
                                                <input value="" type="text" class="form-control m-input m-input--air " name="praise_price[]" placeholder="Tiền">
                                            </th>
                                            <th class="all"><input value="" type="text" class="form-control m-input m-input--air pack-service pack-service_idkey" name="service_idkey[]" placeholder="IDKEY"></th>
                                            <th class="range muilti pack">
                                                <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                    <i class="la la-trash-o"></i>
                                                </a>
                                            </th>
                                        </tr>`;

                                        field_filter_container_body.append(html);
                                    }
                                }else {
                                    let item_0 = item[0];
                                    let praise_price = item[3]??'';
                                    let slug_idkey = convertToSlugV2(item_0);
                                    let index_2 = number_price(item[2]);
                                    let html = `<tr>
                                    <th class="pack">
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
                                    <th class="all"><input value="${item[0]}" type="text" class="form-control m-input m-input--air pack-slug keyword_slug keyword_service" name="keyword[]" placeholder="keyword"></th>
                                    <th class="all"><input value="${item[1]}" type="text" class="form-control m-input m-input--air attribute_service" name="name[]" placeholder="Tên thuộc tính"></th>
                                    <th class="all">
                                        <input value="${index_2}" type="text" class="form-control m-input m-input--air pack-input pack-price" name="price[]" placeholder="Giá">
                                    </th>
                                    <th class="pack">
                                        <input value="1" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số">
                                    </th>

                                    <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total" name="total[]" placeholder="Tiền"></th>
                                    <th class="all">
                                        <input value="${praise_price}" type="text" class="form-control m-input m-input--air " name="praise_price[]" placeholder="Tiền">
                                    </th>
                                    <th class="all"><input value="${slug_idkey}" type="text" class="form-control m-input m-input--air pack-service pack-service_idkey" name="service_idkey[]" placeholder="IDKEY"></th>

                                    <th class="range muilti pack">
                                        <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                            <i class="la la-trash-o"></i>
                                        </a>
                                    </th>
                                </tr>`;

                                    field_filter_container_body.append(html);
                                }
                            });

                        }

                    }


                }else{

                }

            })

            function convertToSlugV2(title) {
                var slug;
                //Đổi chữ hoa thành chữ thường
                slug = title.toLowerCase();
                //Đổi ký tự có dấu thành không dấu
                slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
                slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
                slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
                slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
                slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
                slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
                slug = slug.replace(/đ/gi, 'd');
                //Xóa các ký tự đặt biệt
                slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\<|\'|\"|\:|\;|_/gi, '');
                //Đổi khoảng trắng thành ký tự gạch ngang
                slug = slug.replace(/ /gi, "-");
                //Đổi nhiều ký tự gạch ngang liên tiếp thành 1 ký tự gạch ngang
                //Phòng trường hợp người nhập vào quá nhiều ký tự trắng
                slug = slug.replace(/\-\-\-\-\-/gi, '-');
                slug = slug.replace(/\-\-\-\-/gi, '-');
                slug = slug.replace(/\-\-\-/gi, '-');
                slug = slug.replace(/\-\-/gi, '-');
                //Xóa các ký tự gạch ngang ở đầu và cuối
                slug = '@' + slug + '@';
                slug = slug.replace(/\@\-|\-\@|\@/gi, '');
                // trả về kết quả
                return slug;
            }
        })

    </script>
    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>
    <script>

        "use strict";
        $(document).ready(function () {
            // kt_chect_trung
            $('body').on('click','.kt_chect_trung',function (e) {
                var keywords = $('input[name="keyword[]"]').map(function() {
                    return this.value;
                }).get();

                var slugs = [];

                // Duyệt qua mảng keywords để tạo mảng slugs
                $.each(keywords, function (index, keyword) {
                    var slug = $.trim(keyword).replace(/\s+/g, '-').toLowerCase();
                    slugs.push(slug);
                });

                var countValues = {};
                var duplicateIndexes = [];
                // Đếm số lần xuất hiện của mỗi slug
                $.each(slugs, function (index, slug) {
                    countValues[slug] = (countValues[slug] || 0) + 1;

                    if (countValues[slug] === 2) {
                        duplicateIndexes.push(index);
                    }
                });
                var duplicatesExist = false;
                var duplicates = {};

                // Lọc ra những giá trị có số lần xuất hiện lớn hơn 1
                $.each(countValues, function (slug, count) {
                    if (count > 1) {
                        duplicatesExist = true;
                        duplicates[slug] = count;
                    }
                });
                console.log(duplicateIndexes)
                // Hiển thị kết quả
                if (duplicatesExist){
                    toast(JSON.stringify(duplicates), 'error');
                    $('input[name="keyword[]"]').map(function(findex, fkeyword) {
                        var cindex = $.inArray(findex, duplicateIndexes);

                        if (cindex !== -1) {
                            // Nếu có, hiển thị thông báo
                            $(this).css('border-color','#f64e60');
                            console.log('Giá trị ' + findex + ' có trong mảng tại vị trí ' + cindex);
                        } else {
                            // Nếu không, hiển thị thông báo khác
                            console.log('Giá trị ' + findex + ' không có trong mảng');
                        }
                    });
                }else {
                    toast('Không có keyword nào trùng');
                    $('input[name="keyword[]"]').map(function(findex, fkeyword) {
                        $(this).css('border-color','#e4e6ef');
                    });
                }
                // In giá trị ra console
            })

            $('body').on('change','.pack-slug',function (e) {
                let name = $(this).val();
                let slug_name = convertToSlug(name);

                let parrent_keyword = $(this).parent().parent();
                let child_service = parrent_keyword.find('.pack-service');

                child_service.val(slug_name);
            })

            function convertToSlug(title) {
                var slug;
                //Đổi chữ hoa thành chữ thường
                slug = title.toLowerCase();
                //Đổi ký tự có dấu thành không dấu
                slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
                slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
                slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
                slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
                slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
                slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
                slug = slug.replace(/đ/gi, 'd');
                //Xóa các ký tự đặt biệt
                slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\<|\'|\"|\:|\;|_/gi, '');
                //Đổi khoảng trắng thành ký tự gạch ngang
                slug = slug.replace(/ /gi, "-");
                //Đổi nhiều ký tự gạch ngang liên tiếp thành 1 ký tự gạch ngang
                //Phòng trường hợp người nhập vào quá nhiều ký tự trắng
                slug = slug.replace(/\-\-\-\-\-/gi, '-');
                slug = slug.replace(/\-\-\-\-/gi, '-');
                slug = slug.replace(/\-\-\-/gi, '-');
                slug = slug.replace(/\-\-/gi, '-');
                //Xóa các ký tự gạch ngang ở đầu và cuối
                slug = '@' + slug + '@';
                slug = slug.replace(/\@\-|\-\@|\@/gi, '');
                // trả về kết quả
                return slug;
            }

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
            $('#price_wrapper').removeClass('single-mode multi-mode range-mode pack-mode keyword-mode');
            if ([4, 5].indexOf(parseInt($(this).val())) != -1) {
                $('#price_wrapper').addClass('multi-mode');
                $('#price_wrapper').addClass('keyword-mode');
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
                        var id = $(inpe).attr('id');
                        if (typeof id == 'undefined') {
                            let pack_price = $('.pack-price', elm).val();
                            if (pack_price){
                                pack_price = pack_price.replace(/,/g, ''); // Loại bỏ dấu phẩy
                            }
                            var price = parseFloat(pack_price).toFixed(0); // Chuyển chuỗi thành số và giữ hai chữ số thập phân

                            var discount = parseFloat($('.pack-discount', elm).val());
                            let input_pack_rate = $('[name="input_pack_rate"]').val();
                            input_pack_rate = input_pack_rate.replace(/,/g, ''); // Loại bỏ dấu phẩy
                            var rate = parseFloat(input_pack_rate).toFixed(0); // Chuyển chuỗi thành số và giữ hai chữ số thập phân

                            let pack_total = parseInt((price / 1000) * rate * discount);
                            pack_total = pack_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                            $('.pack-total', elm).val(pack_total);
                        } else {

                            let pack_price = $('#' + id + '.pack-price', elm).val();
                            if (pack_price){
                                pack_price = pack_price.replace(/,/g, ''); // Loại bỏ dấu phẩy
                            }
                            var price = parseFloat(pack_price).toFixed(0); // Chuyển chuỗi thành số và giữ hai chữ số thập phân

                            var discount = parseFloat($('#' + id + '.pack-discount', elm).val());

                            let input_pack_rate = $('[name="input_pack_rate"]').val();
                            input_pack_rate = input_pack_rate.replace(/,/g, ''); // Loại bỏ dấu phẩy
                            var rate = parseFloat(input_pack_rate).toFixed(0); // Chuyển chuỗi thành số và giữ hai chữ số thập phân

                            let pack_total = parseInt((price / 1000) * rate * discount);
                            pack_total = pack_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                            $('#' + id + '.pack-total', elm).val(pack_total);
                        }

                    });
                });
            }
        }
        function UpdatePrice() {
            var server_mode = parseInt($('[name="server_mode"]').val());
            var server_price = parseInt($('[name="server_price"]').val());
            if (server_mode == 1 && server_price == 1) {
                $('#price_wrapper').addClass('server-all');
                $('.server-container .data-item').each((idx, elm) => {
                    var id = $('[name="server_id[]"]', elm).val();
                    var name = $.trim($('[name="server_data[]"]', elm).val());
                    if (name != '') {
                        $('table tbody tr').each((tri, tre) => {
                            $('th', tre).each((thi, the) => {
                                if (thi == 0 || thi == 2 || thi ==4 || thi ==3 || thi == 8) {
                                    return;
                                }
                                if ($('input', the).length == 1) {
                                    if (!$('input', the).attr('id')) {
                                        $('input', the).attr('id', 'sv' + id);
                                    }
                                }
                                var input = $('input#sv' + id, the);
                                if (input.length == 0) {
                                    input = $('<input class="form-control m-input m-input--air" type="text" id="sv' + id + '" />');
                                    TableInpEvents(input);
                                    input.appendTo(the);
                                }
                                switch (thi) {
                                    case 1:
                                        input.val(name).addClass('server-name');
                                        break;
                                    case 5:
                                        input.attr('name', 'price' + id + '[]');
                                        input.addClass('pack-input pack-price number_price');
                                        break;
                                    case 6:
                                        input.attr('name', 'discount' + id + '[]');
                                        input.addClass('pack-input pack-discount');
                                        break;
                                    case 7:
                                        input.attr('name', 'total' + id + '[]');
                                        input.addClass('pack-input pack-total');
                                        break;
                                    // case 7:
                                    //     input.attr('name', 'day' + id + '[]');
                                    //     break;
                                    // case 8:
                                    //     input.attr('name', 'punish_price' + id + '[]');
                                    //     break;
                                    // case 9:
                                    //     input.attr('name', 'praise_day' + id + '[]');
                                    //     break;
                                    case 8:
                                        input.attr('name', 'praise_price' + id + '[]');
                                        break;
                                }
                            });
                        });
                    }
                });
            } else {
                $('#price_wrapper').removeClass('server-all');
                $('table tbody tr').each((tri, tre) => {
                    $('th', tre).each((thi, the) => {
                        $('input', the).each((inpi, inpe) => {
                            if (inpi != 0) {
                                $(inpe).remove();
                            }
                        });
                        input = $('input', the);
                        if (input.length == 0)
                            return;
                        switch (thi) {
                            case 1:
                                input.val(name).addClass('server-name');
                                break;
                            case 5:
                                input.attr('name', 'price[]');
                                input.addClass('pack-input pack-price number_price');
                                break;
                            case 6:
                                input.attr('name', 'discount[]');
                                input.addClass('pack-input pack-discount');
                                break;
                            case 7:
                                input.attr('name', 'total[]');
                                input.addClass('pack-input pack-total');
                                break;
                            // case 7:
                            //     input.attr('name', 'day[]');
                            //     break;
                            // case 8:
                            //     input.attr('name', 'punish_price[]');
                            //     break;
                            // case 9:
                            //     input.attr('name', 'praise_day[]');
                            //     break;
                            case 8:
                                input.attr('name', 'praise_price[]');
                                break;
                        }
                    });
                });
            }
        }
        UpdatePrice();
        $('[name="input_pack_rate"]').keyup(function () {
            UpdatePack();
        });
        UpdatePack();
        function TableInpEvents(selector) {
            $(selector).focus(function () {
                var th = $(this).closest('tr');
                var index = th.index();
                var count = $('tbody tr').length;
                if (th.is(':last-child')) {
                    var a = $(this).closest('tr').first().clone(true);
                    $('input', a).not('.server-name').val('');
                    let keyword_slug = a.find('.keyword_slug');
                    if (keyword_slug){
                        keyword_slug.addClass('pack-slug');
                    }
                    a.appendTo($('tbody'));
                    //TableEvents(a);
                }
            }).blur(function () {
                var th = $(this).closest('tr');
                if ($(this).hasClass('pack-input')) {
                    var id = $(this).attr('id');

                    if (typeof id == 'undefined') {
                        let pack_price = $('.pack-price', th).val();
                        if (pack_price){
                            pack_price = pack_price.replace(/,/g, ''); // Loại bỏ dấu phẩy
                        }
                        var price = parseFloat(pack_price).toFixed(0); // Chuyển chuỗi thành số và giữ hai chữ số thập phân

                        var discount = parseFloat($('.pack-discount', th).val());

                        let input_pack_rate = $('[name="input_pack_rate"]').val();
                        input_pack_rate = input_pack_rate.replace(/,/g, ''); // Loại bỏ dấu phẩy
                        var rate = parseFloat(input_pack_rate).toFixed(0); // Chuyển chuỗi thành số và giữ hai chữ số thập phân

                        let pack_total = parseInt((price / 1000) * rate * discount);
                        pack_total = pack_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                        $('.pack-total', th).val(pack_total);
                    } else {

                        let pack_price = $('#' + id + '.pack-price', th).val();
                        if (pack_price){
                            pack_price = pack_price.replace(/,/g, ''); // Loại bỏ dấu phẩy
                        }
                        var price = parseFloat(pack_price).toFixed(0); // Chuyển chuỗi thành số và giữ hai chữ số thập phân

                        var discount = parseFloat($('#' + id + '.pack-discount', th).val());

                        let input_pack_rate = $('[name="input_pack_rate"]').val();
                        input_pack_rate = input_pack_rate.replace(/,/g, ''); // Loại bỏ dấu phẩy
                        var rate = parseFloat(input_pack_rate).toFixed(0); // Chuyển chuỗi thành số và giữ hai chữ số thập phân

                        let pack_total = parseInt((price / 1000) * rate * discount);
                        pack_total = pack_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                        $('#' + id + '.pack-total', th).val(pack_total);
                    }

                }
            });
        }
        function TableEvents(selector) {
            TableInpEvents($('input', selector));
            $('body').on('click', '.btnUp', function(){
            // $('.btnUp', selector).click(function () {
                var item = $(this).closest('tr');
                var prev = item.prev();
                if (prev.length != 0) {
                    item.insertBefore(prev);
                }
            });
            $('body').on('click', '.btnDown', function(){
            // $('.btnDown', selector).click(function () {
                var item = $(this).closest('tr');
                var next = item.next();
                if (next.length != 0) {
                    item.insertAfter(next);
                }
            });
            $('body').on('click', '.btnRemove', function(){
            // $('.btnRemove', selector).click(function () {
                var fcount = $(this).closest('tbody').find('tr').length;
                if (fcount == 1)
                    return;
                $(this).closest('tr').remove();
                Update();
            });
        }
        TableEvents('table');
        function events() {
            $("#btnAddServer").click(function(e) {
                e.preventDefault();
                var container = $('.server-container');
                var index = $(this).closest('.data-item').index();
                var count = container.find('.data-item').length;
                console.log(container);
                console.log(index);
                console.log(count);

                var a = $('.data-item', container).first().clone(true);
                $('input[type="text"]', a).val('');
                var newid = 0;
                while ($('.data-item [name="server_id[]"][value="' + newid + '"]', container).length != 0) {
                    newid++;
                }
                $('[name="server_id[]"]', a).attr('value', newid).val(newid);
                a.appendTo(container);
                Update();
            }).blur(function () {
                UpdatePrice();
            });


            // $('.server-container input').focus(function () {
            //     var container = $(this).closest('.server-container');
            //     var index = $(this).closest('.data-item').index();
            //     var count = container.find('.data-item').length;
            //     if (index == count) {
            //         var a = $('.data-item', container).first().clone(true);
            //         $('input[type="text"]', a).val('');
            //         var newid = 0;
            //         while ($('.data-item [name="server_id[]"][value="' + newid + '"]', container).length != 0) {
            //             newid++;
            //         }
            //         $('[name="server_id[]"]', a).attr('value', newid).val(newid);
            //         a.appendTo(container);
            //     }
            //     Update();
            // }).blur(function () {
            //     UpdatePrice();
            // });
            $('[name="filter"]').change(function () {
                UpdatePrice();
            });
            $('[name="server_mode"]').change(function () {
                if ($(this).val() == '1') {
                    $('.server-container,.server-price').show();
                    $('.btnAddServer').removeClass('btnAddServer_none');
                    $('.btnAddServer').addClass('btnAddServer_block');
                } else {
                    $('.server-container,.server-price').hide();
                    $('.btnAddServer').addClass('btnAddServer_none');
                    $('.btnAddServer').removeClass('btnAddServer_block');
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

        // log edit
        $(document).ready(function () {

            $('body').on('click', '.btt_rechange_setting', function(e) {
                let id_edit_setting  = $('.id_edit_setting').val();
                $('#modalRechange .id_edit').val(id_edit_setting);
                $('#modalRechange').modal('show')
            });

            $('body').on('click', '.btn-show-log-edit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "{{route('admin.'.$module.'.get-log-edit')}}",
                    data: {
                        '_token':'{{csrf_token()}}',
                        id: {{ isset($data->id) ? $data->id : 'null' }},
                    },
                    beforeSend: function (xhr) {
                        // $('#variantLogList').empty().html('<div class="spinner spinner-success mr-15 justify-content-center"></div>');

                        $('#variantLogList').empty();
                    },
                    success: function (data) {
                        // $('#variantLogList').empty();
                        if( data.status == 1 && data.data.length > 0 ){
                            renderVariantHistoryChanges(data.data);
                            let logChoosen = $('#variantLogList .variant-log-item').first();
                            let logChoosenId = logChoosen.data('id');
                            logChoosen.addClass('log-active');
                            loadHistoryChangesDetail(logChoosenId);
                        }
                        else{
                            $('#variantLogList').append(`<p>${data.message}</p>`);
                            $('#logDetail').empty();
                        }
                    },
                    error: function (data) {
                        toast('{{__('Có lỗi phát sinh vui lòng thử lại')}}', 'error');
                    },
                    complete: function (data) {

                    }
                });
            });
            $('body').on('click', '.variant-log-item', function(e) {
                let logId  = $(this).data('id');
                loadHistoryChangesDetail(logId)
            });
            function renderVariantHistoryChanges (data) {
                data.forEach(changeHistory => {
                    let html = '';
                    html += `<div class="variant-log-item variant-log-item-${changeHistory.id} d-flex align-items-center rounded p-2" data-id="${changeHistory.id}">`;
                    html += `<div class="d-flex flex-column flex-grow-1">`;
                    html += `<a href="javascript:void(0)" class="font-weight-bold text-dark-75 ">${changeHistory.author.username}</a>`;
                    html += `<a href="javascript:void(0)" class="font-weight-bold text-dark-75 ">${changeHistory.created_at}</a>`;
                    html += `</div>`;
                    html += `</div>`;
                    $('#variantLogList').append(html);
                });
            }
            function loadHistoryChangesDetail (logId) {
                $.ajax({
                    type: "POST",
                    url: "{{route('admin.'.$module.'.get-log-edit-detail')}}",
                    data: {
                        '_token':'{{csrf_token()}}',
                        id: logId,
                    },
                    beforeSend: function (xhr) {
                        $('.variant-log-item').removeClass('log-active');
                        $(`.variant-log-item-${logId}`).addClass('log-active');
                        $('#logDetail').empty();
                    },
                    success: function (data) {
                        if( data.status == 1 && data.data ){
                            $('.id_edit_setting').val(data.data.id);
                            activateDiffChecker(data.data);
                        }
                        else{
                            toast(data.message, 'error');
                        }
                    },
                    error: function (data) {
                        toast('{{__('Có lỗi phát sinh vui lòng thử lại')}}', 'error');
                    },
                    complete: function (data) {

                    }
                });
            }

            function activateDiffChecker (logDetail) {
                "use strict";
                let params_before = JSON.parse(logDetail.params_before);
                let params_after = JSON.parse(logDetail.params_after);
                //Chuyển JSON về dạng chữ để dễ so sánh và đọc
                let text_params_before = "";
                let text_params_after = "";
                text_params_before = prettyJSON(params_before);
                text_params_after = prettyJSON(params_after);
                let byId = function (id) { return document.getElementById(id); },
                    baseDesc = difflib.stringAsLines(text_params_before),
                    newtxtDesc = difflib.stringAsLines(text_params_after),
                    smDesc = new difflib.SequenceMatcher(baseDesc, newtxtDesc),
                    opcodesDesc = smDesc.get_opcodes(),
                    logDetailBlock = byId("logDetail"),
                    contextSize = null;

                logDetailBlock.innerHTML = "";
                contextSize = contextSize || null;

                logDetailBlock.appendChild(diffview.buildView({
                    baseTextLines: baseDesc,
                    newTextLines: newtxtDesc,
                    opcodes: opcodesDesc,
                    baseTextName: '{{ __("Dữ liệu cũ") }}',
                    newTextName: '{{ __("Dữ liệu mới") }}',
                    contextSize: null,
                    viewType: 0
                }));
            }
            function prettyJSON (jsonData) {
                return JSON.stringify(jsonData, null, 3);
            }
        });
    </script>
@endsection


