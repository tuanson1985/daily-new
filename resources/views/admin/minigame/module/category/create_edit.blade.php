{{-- Extends layout --}}
@extends('admin._layouts.master')

@section('action_area')
    @php
        $data_viewed = Cookie::get('phanphoi')??'';
    @endphp
    <div class="d-flex align-items-center text-right">
        <a class="btn btn-light-primary font-weight-bolder mr-2 btnback">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>

        <div class="btn-group">
            <div class="btn-group">


                <button
                    type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
                    <i class="ki ki-check icon-sm"></i>
                    @if(isset($data))
                        {{__('Cập nhật')}}
                    @else
                        {{__('Thêm mới')}}
                    @endif
                </button>


            </div>
            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split btn-submit-dropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <ul class="nav nav-hover flex-column">
                    <li class="nav-item">
                        <button class="nav-link btn-submit-custom" data-form="formMain">
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
        <input type="hidden" value="{{$data->id}}" name="group_id" id="group_id">
        <input type="hidden" value="editminigame" name="edit_flag" id="edit_flag">
    @else
        {{Form::open(array('route'=>array('admin.'.$module.'.store'),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}

        <input type="hidden" name="arrayshopid" class="array_shop_id">
    @endif
    @csrf
    <input type="hidden" name="submit-close" id="submit-close">
    <div class="card card-custom " id="kt_page_sticky_card">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">
                    <h3 class="card-label">
                        {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                    </h3>
                </h3>
            </div>
            <div class="card-toolbar"></div>
        </div>

        <div class="card-body replication">

            <ul class="nav nav-tabs" role="tablist">

                <li class="nav-item nav-item-replication">
                    <a class="nav-link {{ isset($data_viewed) && $data_viewed !='' ? '' : 'show active' }} nav_thong-tin-hien-thi" data-toggle="tab" href="#system" role="tab" aria-selected="true">
                        <span class="nav-text">Thông tin hiển thị </span>
                    </a>
                </li>

                <li class="nav-item nav-item-replication cpthanhtoan">
                    <a class="nav-link" data-toggle="tab" href="#expenses" role="tab" aria-selected="false">
                        <span class="nav-text">Chi phí và thanh toán</span>
                    </a>
                </li>
                @if(isset($data))
                    <li class="nav-item nav-item-replication cauhinhgiaithuong">
                        <a class="nav-link" data-toggle="tab" href="#prize" role="tab" aria-selected="false">
                            <span class="nav-text">Cấu hình giải thưởng</span>
                        </a>
                    </li>
                @endif
                @if(isset($dataCategory))
                    <li class="nav-item nav-item-replication">
                        <a class="nav-link" data-toggle="tab" href="#seedding" role="tab" aria-selected="false">
                            <span class="nav-text">Seeding</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item nav-item-replication">
                    <a class="nav-link" data-toggle="tab" href="#desc-seo" role="tab" aria-selected="false">
                        <span class="nav-text">Mô tả và SEO</span>
                    </a>
                </li>

                {{--                    @if(isset($data))--}}
                <li class="nav-item nav-item-replication phanphoi ">
                    <a class="nav-link {{ isset($data_viewed) && $data_viewed !='' ? 'show active' : '' }} nav_phanphoi" data-toggle="tab" href="#dulication" role="tab" aria-selected="false">
                        <span class="nav-text">Phân phối</span>
                    </a>
                </li>
{{--                @if(isset($log_edit) && count($log_edit))--}}
{{--                    <li class="nav-item nav-item-replication">--}}
{{--                        <a class="nav-link" data-toggle="tab" href="#logEdit" role="tab" aria-selected="false">--}}
{{--                            <span class="nav-text">Log edit</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                @endif--}}
                {{--                    @endif--}}
            </ul>

            <div class="tab-content tab-content-replication">
                <!-- Thông tin hiển thị -->
                <div class="tab-pane {{ isset($data_viewed) && $data_viewed != '' ? '' : 'show active' }}" id="system" role="tabpanel">
                    <div class="row marginauto blook-row">
                        <div class="col-md-12 left-right">
                            <!-- Block 1 -->
                            <div class="row marginauto blook-item-row">
                                <div class="col-md-10 pl-0 pr-0">
                                    <div class="row marginauto">
                                        <div class="col-md-12 left-right blook-item-title">
                                            <span>Thông tin minigame</span>
                                        </div>
                                        <div class="alert alert-warning " style="margin-top: 4px" role="alert">
                                            Thông tin chung sẽ được đồng bộ cho tất cả các điểm bán được phân phối.
                                        </div>
                                        <div class="col-md-12 left-right blook-item-body">
                                            {{-- position --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="position" class="form-control-label label-w">{{ __('Loại minigame') }}</label>
                                                    {{Form::select('position',[''=>'-- Không chọn --']+(config('module.minigame.minigame_type')??[]) ,\Request::get('position'),array('class'=>'form-control col-md-12',' data-required'=>'1','id'=>'position'))}}
                                                    @if($errors->has('position'))
                                                        <div class="form-control-feedback">{{ $errors->first('position') }}</div>
                                                    @endif
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="meta" class="form-control-label label-w">{{ __('Nhóm vận hành') }}</label>
                                                        @php
                                                            $meta = null;
                                                            if(isset($data) && isset($data->customs) && count($data->customs)){
                                                                if (isset($data->customs[0]->meta) && $data->customs[0]->meta['shop_group']){
                                                                    $meta = $data->customs[0]->meta['shop_group'];
                                                                }
                                                            }
                                                        @endphp
                                                        <select name="meta" class="form-control" id="meta">
                                                            <option value="">--- Chọn nhóm vận hành ---</option>
                                                            @foreach($shop_group as $nvh)

                                                                @if(isset($data) && isset($meta))
                                                                    @if((int)$meta == $nvh->id)
                                                                        <option selected value="{{ $nvh->id }}">{{ $nvh->title }}</option>
                                                                    @else
                                                                        <option value="{{ $nvh->id }}">{{ $nvh->title }}</option>
                                                                    @endif
                                                                @else
                                                                    <option value="{{ $nvh->id }}">{{ $nvh->title }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{--params[game_type]--}}
                                            <div class="form-group">
                                                <label class="form-control-label label-w">Loại vật phẩm:</label>
                                                <select id="params[game_type]" data-required="1" name="params[game_type]" class="form-control col-md-6 game_type">
                                                    <option value="">--Chọn loại vật phẩm--</option>
                                                    @foreach(config('module.minigame.game_type') as $key => $value)
                                                        @if(isset($data->params->game_type) && $data->params->game_type == $key)
                                                            <option value="{{$key}}" selected="selected">{{$value}}</option>
                                                        @else
                                                            <option value="{{$key}}">{{$value}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @if($errors->has('params[game_type]'))
                                                    <span class="form-control-feedback">{{ $errors->first('params[game_type]') }}</span>
                                                @endif
                                            </div>
                                            @if(isset($data))
                                                <div class="form-group">
                                                    <label for="order">{{ __('Thứ tự') }}</label>
                                                    <input type="text" name="order" value="{{ old('order', isset($data) ? $data->order : null) }}"
                                                           placeholder="{{ __('Thứ tự') }}"
                                                           class="form-control {{ $errors->has('order') ? ' is-invalid' : '' }} col-md-6">
                                                    @if ($errors->has('order'))
                                                        <span class="form-text text-danger">{{ $errors->first('order') }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                @if(session('shop_id'))
                                                    <div class="form-group">
                                                        <label for="order">{{ __('Thứ tự') }}</label>
                                                        <input type="text" name="order" value="{{ old('order', isset($data) ? $data->order : null) }}"
                                                               placeholder="{{ __('Thứ tự') }}"
                                                               class="form-control {{ $errors->has('order') ? ' is-invalid' : '' }} col-md-6">
                                                        @if ($errors->has('order'))
                                                            <span class="form-text text-danger">{{ $errors->first('order') }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row marginauto block-hr"></div>
                            <!-- Block 2 -->
                            <div class="row marginauto blook-item-row">
                                <div class="col-md-12 left-right blook-item-title">
                                    <span>Tên và mô tả</span>
                                </div>

                                <div class="col-md-12 left-right blook-item-body">

                                    {{-----title------}}
                                    <div class="form-group row">
                                        <div class="col-12 col-md-12">
                                            <label class="label-w">{{ __('Tiêu đề') }}</label>
                                            <input type="text" id="title_gen_slug" data-required="1" name="title" value="{{ old('title', isset($data_custom) ? $data_custom->title : null) }}" autofocus
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
                                            <label class="label-w">{{ __('Permalink') }}:</label>

                                            <span class="">
                                                    <a  id="permalink" class="permalink" target="_blank" href="{{$shopurl==''?Request::getSchemeAndHttpHost():$shopurl}}/minigame-{{ old('slug', isset($data_custom) ? $data_custom->slug : null) }}">

                                                    <span class="default-slug">{{$shopurl==''?Request::getSchemeAndHttpHost():$shopurl}}/minigame-<span id="label-slug" data-override-edit="0">{{ old('slug', isset($data_custom) ? $data_custom->slug : null) }}</span></span>

                                                    </a>
                                                    <input type="text" value=""  class="form-control" id="input-slug-edit" style="width: auto !important;display: none"/>
                                                    <a  class="btn btn-light-primary font-weight-bolder mr-2" id="btn-slug-edit">Chỉnh sửa</a>
                                                    <a  class="btn btn-light-primary font-weight-bolder mr-2" id="btn-slug-ok" style="display: none">OK</a>
                                                    <a  class="btn btn-secondary  button-link mr-2" id="btn-slug-cancel" style="display: none">Cancel</a>

                                                    <input type="hidden" id="current-slug" name="slug" value="{{ old('slug', isset($data_custom) ? $data_custom->slug : null) }}">
                                                    <input type="hidden" id="is_slug_override" name="is_slug_override" value="{{ old('is_slug_override', isset($data) ? $data->is_slug_override : null) }}" >
                                                </span>
                                        </div>

                                    </div>

                                    {{-----description------}}
                                    <div class="form-group row">
                                        <div class="col-12 col-md-12">
                                            <label class="label-w" for="locale">{{ __('Mô tả') }}:</label>
                                            <textarea id="description" name="description" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" >{{ old('description', isset($data_custom) ? $data_custom->description : null) }}</textarea>
                                            @if ($errors->has('description'))
                                                <span class="form-text text-danger">{{ $errors->first('description') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-----The le------}}
                                    <div class="form-group row">
                                        <div class="col-12 col-md-12">
                                            <label class="label-w" for="locale">{{ __('Thể lệ') }}</label>
                                            <textarea id="params[thele]" name="params[thele]" class="form-control ckeditor-source" data-height="300" data-startup-mode="" >{{ old('params[thele]', isset($data_custom->params->thele) ? $data_custom->params->thele : null) }}</textarea>
                                            @if ($errors->has('params[thele]'))
                                                <span class="form-text text-danger">{{ $errors->first('params[thele]') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-----Phan thuong------}}
                                    <div class="form-group row">
                                        <div class="col-12 col-md-12">
                                            <label class="label-w" for="locale">{{ __('Quà đua top') }}</label>
                                            <textarea id="params[phanthuong]" name="params[phanthuong]" class="form-control ckeditor-source" data-height="300" data-startup-mode="" >{{ old('params[phanthuong]', isset($data_custom->params->phanthuong) ? $data_custom->params->phanthuong : null) }}</textarea>
                                            @if ($errors->has('params[phanthuong]'))
                                                <span class="form-text text-danger">{{ $errors->first('params[phanthuong]') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row marginauto block-hr"></div>
                            <!-- Block 3 -->
                            <div class="row marginauto blook-item-row">
                                <div class="col-md-12 left-right blook-item-title">
                                    <span>Hình ảnh</span>
                                </div>
                                <div class="col-md-12 left-right blook-item-body">
                                    {{-----gallery block------}}
                                    <div class="form-group row">
                                        {{-----image------}}
                                        <div class="col-md-4">
                                            <label class="label-w" for="locale">{{ __('Hình đại diện') }}:</label>
                                            <div class="">
                                                <div class="fileinput ck-parent" data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                                        @if(old('image', isset($data_custom) ? $data_custom->image : null)!="")
                                                            <img class="ck-thumb" src="{{ old('image', isset($data_custom) ? \App\Library\MediaHelpers::media($data_custom->image) : null) }}">
                                                        @else
                                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                        @endif
                                                        <input class="ck-input" type="hidden" name="image" value="{{ old('image', isset($data_custom) ? $data_custom->image : null) }}">

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
                                            <label class="label-w" for="locale">{{ __('Hình Banner') }}:</label>
                                            <div class="">
                                                <div class="fileinput ck-parent" data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                                        @if(old('image_banner', isset($data_custom) ? $data_custom->image_banner : null)!="")
                                                            <img class="ck-thumb" src="{{ old('image_banner', isset($data_custom) ? \App\Library\MediaHelpers::media($data_custom->image_banner) : null) }}">
                                                        @else
                                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                        @endif
                                                        <input class="ck-input" type="hidden" name="image_banner" value="{{ old('image_banner', isset($data_custom) ? $data_custom->image_banner : null) }}">

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
                                            <label class="label-w" for="locale">{{ __('Nút chơi') }}:</label>
                                            <div class="">
                                                <div class="fileinput ck-parent" data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                                        @if(old('image_icon', isset($data_custom) ? $data_custom->image_icon : null)!="")
                                                            <img class="ck-thumb" src="{{ old('image_icon', isset($data_custom) ? \App\Library\MediaHelpers::media($data_custom->image_icon) : null) }}">
                                                        @else
                                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                        @endif
                                                        <input class="ck-input" type="hidden" name="image_icon" value="{{ old('image_icon', isset($data_custom) ? $data_custom->image_icon : null) }}">

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

                                        @if(\Request::get('position')=="smashwheel"
                                        || \Request::get('position')=="rubywheel"
                                        || \Request::get('position')=="flip"
                                        || \Request::get('position')=="squarewheel")
                                            <div class="col-md-4">
                                                @if(\Request::get('position')=="rubywheel")
                                                    <label class="label-w" for="params[image_static]">{{ __('Ảnh vòng chơi') }}:</label>
                                                @elseif(\Request::get('position')=="flip")
                                                    <label class="label-w" for="params[image_static]">{{ __('Ảnh hình lật') }}:</label>
                                                @elseif(\Request::get('position')=="squarewheel")
                                                    <label class="label-w" for="params[image_static]">{{ __('Ảnh hình xoay') }}:</label>
                                                @elseif(\Request::get('position')=="smashwheel")
                                                    <label class="label-w" for="params[image_static]">{{ __('Ảnh tĩnh ban đầu') }}:</label>
                                                @endif
                                                <div class="">
                                                    <div class="fileinput ck-parent" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">
                                                            @if(old('params[image_static]', isset($data_custom->params->image_static) ? $data_custom->params->image_static : null)!="")
                                                                <img class="ck-thumb" src="{{ old('params[image_static]', isset($data_custom->params) ? \App\Library\MediaHelpers::media($data_custom->params->image_static) : null) }}">
                                                            @else
                                                                <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                            @endif
                                                            <input class="ck-input" type="hidden" name="params[image_static]" value="{{ old('params[image_static]', isset($data_custom->params->image_static) ? $data_custom->params->image_static : null) }}">
                                                        </div>
                                                        <div>
                                                            <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                                            <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('params[image_static]'))
                                                        <span class="form-text text-danger">{{ $errors->first('params[image_static]') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <input class="ck-input" type="hidden" name="params[image_static]" value="">
                                        @endif

                                        @if(\Request::get('position')=="smashwheel")
                                            <div class="col-md-4">
                                                <label class="label-w" for="params[image_animation]">{{ __('Ảnh động khi click chơi') }}:</label>
                                                <div class="">
                                                    <div class="fileinput ck-parent" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">
                                                            @if(old('params[image_animation]', isset($data_custom->params->image_animation) ? $data_custom->params->image_animation : null)!="")
                                                                <img class="ck-thumb" src="{{ old('params[image_animation]', isset($data_custom->params) ? \App\Library\MediaHelpers::media($data_custom->params->image_animation) : null) }}">
                                                            @else
                                                                <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                            @endif
                                                            <input class="ck-input" type="hidden" name="params[image_animation]" value="{{ old('params[image_animation]', isset($data_custom->params->image_animation) ? $data_custom->params->image_animation : null) }}">

                                                        </div>
                                                        <div>
                                                            <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                                            <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('params[image_animation]'))
                                                        <span class="form-text text-danger">{{ $errors->first('params[image_animation]') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <input class="ck-input" type="hidden" name="params[image_animation]" value="">
                                        @endif

                                        @if(\Request::get('position')=="smashwheel"
                                        || \Request::get('position')=="slotmachine"
                                        || \Request::get('position')=="slotmachine5")
                                            <div class="col-md-4">
                                                <label class="label-w" for="params[image_background]">{{ __('Ảnh nền') }}:</label>
                                                <div class="">
                                                    <div class="fileinput ck-parent" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                                            @if(old('params[image_background]', isset($data_custom->params->image_background) ? $data_custom->params->image_background : null)!="")
                                                                <img class="ck-thumb" src="{{ old('params[image_background]', isset($data_custom->params) ? \App\Library\MediaHelpers::media($data_custom->params->image_background) : null) }}">
                                                            @else
                                                                <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                            @endif
                                                            <input class="ck-input" type="hidden" name="params[image_background]" value="{{ old('params[image_background]', isset($data_custom->params->image_background) ? $data_custom->params->image_background : null) }}">

                                                        </div>
                                                        <div>
                                                            <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                                            <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('params[image_background]'))
                                                        <span class="form-text text-danger">{{ $errors->first('params[image_background]') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <input class="ck-input" type="hidden" name="params[image_background]" value="">
                                        @endif
                                        <div class="col-md-4">
                                            <label class="label-w" for="params[image_percent_sale]">{{ __('Ảnh giảm giá') }}:</label>
                                            <div class="">
                                                <div class="fileinput ck-parent" data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                                        @if(old('params[image_percent_sale]', isset($data_custom->params->image_percent_sale) ? $data_custom->params->image_percent_sale : null)!="")
                                                            <img class="ck-thumb" src="{{ old('params[image_percent_sale]', isset($data_custom->params) ? \App\Library\MediaHelpers::media($data_custom->params->image_percent_sale) : null) }}">
                                                        @else
                                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                        @endif
                                                        <input class="ck-input" type="hidden" name="params[image_percent_sale]" value="{{ old('params[image_percent_sale]', isset($data_custom->params->image_percent_sale) ? $data_custom->params->image_percent_sale : null) }}">

                                                    </div>
                                                    <div>
                                                        <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                                        <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                                    </div>
                                                </div>
                                                @if ($errors->has('params[image_percent_sale]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[image_percent_sale]') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="label-w" for="params[image_view_all]">{{ __('Ảnh button xem tất cả') }}:</label>
                                            <div class="">
                                                <div class="fileinput ck-parent" data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                                        @if(old('params[image_view_all]', isset($data_custom->params->image_view_all) ? $data_custom->params->image_view_all : null)!="")
                                                            <img class="ck-thumb" src="{{ old('params[image_view_all]', isset($data_custom->params) ? \App\Library\MediaHelpers::media($data_custom->params->image_view_all) : null) }}">
                                                        @else
                                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                        @endif
                                                        <input class="ck-input" type="hidden" name="params[image_view_all]" value="{{ old('params[image_view_all]', isset($data_custom->params->image_view_all) ? $data_custom->params->image_view_all : null) }}">

                                                    </div>
                                                    <div>
                                                        <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                                        <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                                    </div>
                                                </div>
                                                @if ($errors->has('params[image_view_all]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[image_view_all]') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row marginauto block-hr"></div>
                            <!-- Block 4 -->
                            <div class="row marginauto blook-item-row">
                                <div class="col-md-12 left-right blook-item-title">
                                    <span>Link</span>
                                </div>
                                <div class="col-md-12 left-right blook-item-body">
                                    {{-- target --}}
                                    <div class="form-group row">
                                        <div class="col-12 col-md-4">
                                            <label for="target" class="form-control-label label-w">{{ __('Kiểu mở link:') }}</label>
                                            {{Form::select('target',[''=>'-- Không chọn --',1=>"Mở tab mới",2=>"Mở popup"],old('target', isset($data) ? $data->target : null),array('class'=>'form-control'))}}
                                            @if($errors->has('target'))
                                                <div class="form-control-feedback">{{ $errors->first('target') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <label for="target" class="form-control-label label-w">{{ __('Url Link:') }}</label>
                                            <input type="text"  name="url" value="{{ old('url', isset($data) ? $data->url : null) }}"
                                                   placeholder="{{ __('Url Link') }}"
                                                   class="form-control {{ $errors->has('url') ? ' is-invalid' : '' }}">
                                            @if($errors->has('url'))
                                                <div class="form-control-feedback">{{ $errors->first('url') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <!-- Chi phí và thanh toán -->
                <div class="tab-pane" id="expenses" role="tabpanel">
                    <div class="row marginauto blook-row">
                        <div class="col-md-12 left-right">
                            <!-- Block 1 -->
                            <div class="row marginauto blook-item-row">
                                <div class="col-md-12 left-right blook-item-title">
                                    <span>Chi phí</span>
                                </div>
                                <div class="alert alert-warning" style="margin-top: 4px" role="alert">
                                    Nếu chỉnh sửa các mục thông tin chung, thông tin sẽ được đồng bộ cho tất cả các điểm bán được phân phối.
                                </div>
                                <div class="col-md-12 left-right blook-item-body">

                                    <div class="form-group row">
                                        <div class="col-12 col-md-3" id="price_fisrt">
                                            <label  class="form-control-label label-w">{{ __('Phí 1 lần chơi (đ)') }}</label>
                                            <input type="text" data-required="1" min='1000' id="price_format"  name="price_format" value="{{ old('price', isset($data) ? number_format($data->price, 0, ',', '.'): null) }}"
                                                   placeholder="{{ __('Phí 1 lần chơi (đ)') }}"
                                                   class="form-control {{ $errors->has('price') ? ' is-invalid' : '' }}">

                                            <span class="error-text-c-1" style="color: #F14B5E;margin-top: 4px"></span>

                                            <input type="hidden" name="price" class="price" value="{{ old('price', isset($data) ? $data->price : null) }}">

                                            @if($errors->has('price'))
                                                <div class="form-control-feedback">{{ $errors->first('price') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-12 col-md-3" id="price_three">
                                            <label class="label-w">{{ __('Phí 3 lần chơi') }}</label>
                                            <input type="text" min='10000' id="price_sticky_3_format" name="price_sticky_3_format" value="{{ old('params[price_sticky_3]', isset($data->params) ? (isset($data->params->price_sticky_3) && $data->params->price_sticky_3 != '' ? number_format($data->params->price_sticky_3, 0, ',', '.') : null)  : null) }}" autofocus
                                                   placeholder="{{ __('3 lần chơi') }}" maxlength="120"
                                                   class="form-control {{ $errors->has('price_sticky_3_format') ? ' is-invalid' : '' }}">

                                            <input type="hidden" name="params[price_sticky_3]" class="params_price_sticky_3" value="{{ old('params[price_sticky_3]', isset($data->params) ? $data->params->price_sticky_3 : null) }}">

                                            <span class="error-text-c-3" style="color: #F14B5E;margin-top: 4px"></span>
                                            @if ($errors->has('params[price_sticky_3]'))
                                                <span class="form-text text-danger">{{ $errors->first('params[price_sticky_3]') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-12 col-md-3" id="price_five">
                                            <label class="label-w">{{ __('Phí 5 lần chơi') }}</label>
                                            <input type="text" min='10000' id="price_sticky_5_format" name="price_sticky_5_format" value="{{ old('params[price_sticky_5]', isset($data->params) ? (isset($data->params->price_sticky_5) && $data->params->price_sticky_5 != '' ? number_format($data->params->price_sticky_5, 0, ',', '.') : null) : null) }}" autofocus
                                                   placeholder="{{ __('5 lần chơi') }}" maxlength="120"
                                                   class="form-control {{ $errors->has('params[price_sticky_5]') ? ' is-invalid' : '' }}">
                                            <span class="error-text-c-5" style="color: #F14B5E;margin-top: 4px"></span>
                                            <input type="hidden" name="params[price_sticky_5]" class="params_price_sticky_5" value="{{ old('params[price_sticky_5]', isset($data->params) ? $data->params->price_sticky_5 : null) }}">

                                            @if ($errors->has('params[price_sticky_5]'))
                                                <span class="form-text text-danger">{{ $errors->first('params[price_sticky_5]') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-12 col-md-3" id="price_seven">
                                            <label class="label-w">{{ __('Phí 7 lần chơi') }}</label>
                                            <input type="text" min='10000' id="price_sticky_7_format" name="price_sticky_7_format" value="{{ old('params[price_sticky_7]', isset($data->params) ? (isset($data->params->price_sticky_7) && $data->params->price_sticky_7 != '' ? number_format($data->params->price_sticky_7, 0, ',', '.') : null) : null) }}" autofocus
                                                   placeholder="{{ __('7 lần chơi') }}" maxlength="120"
                                                   class="form-control {{ $errors->has('params[price_sticky_7]') ? ' is-invalid' : '' }}">
                                            <span class="error-text-c-7" style="color: #F14B5E;margin-top: 4px"></span>
                                            <input type="hidden" name="params[price_sticky_7]" class="params_price_sticky_7" value="{{ old('params[price_sticky_7]', isset($data->params) ? $data->params->price_sticky_7 : null) }}">

                                            @if ($errors->has('params[price_sticky_7]'))
                                                <span class="form-text text-danger">{{ $errors->first('params[price_sticky_7]') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-12 col-md-3 pt-3 pt-lg-4" id="price_ten">
                                            <label class="label-w">{{ __('Phí 10 lần chơi') }}</label>
                                            <input type="text" min='10000' id="price_sticky_10_format" name="price_sticky_10_format" value="{{ old('params[price_sticky_10]', isset($data->params) ? (isset($data->params->price_sticky_10) && $data->params->price_sticky_10 != '' ? number_format($data->params->price_sticky_10, 0, ',', '.') : null) : null) }}" autofocus
                                                   placeholder="{{ __('10 lần chơi') }}" maxlength="120"
                                                   class="form-control {{ $errors->has('params[price_sticky_10]') ? ' is-invalid' : '' }}">
                                            <span class="error-text-c-10" style="color: #F14B5E;margin-top: 4px"></span>
                                            <input type="hidden" name="params[price_sticky_10]" class="params_price_sticky_10" value="{{ old('params[price_sticky_10]', isset($data->params) ? $data->params->price_sticky_10 : null) }}">

                                            @if ($errors->has('params[price_sticky_10]'))
                                                <span class="form-text text-danger">{{ $errors->first('params[price_sticky_10]') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row marginauto block-hr"></div>
                            <!-- Block 2 -->
                            <div class="row marginauto blook-item-row">
                                <div class="col-md-12 left-right blook-item-title">
                                    <span>Thanh toán</span>
                                </div>
                                <div class="alert alert-warning" style="margin-top: 4px" role="alert">
                                    Thông tin chung sẽ được đồng bộ cho tất cả các điểm bán được phân phối.
                                </div>
                                <div class="col-md-12 left-right blook-item-body">
                                    <div class="form-group row">
                                        <div class="col-12 col-md-3">
                                            <label  class="form-control-label label-w">Loại tiền thanh toán</label>
                                            <select name="params[type_charge]" class="form-control " id="type_charge">
                                                <option value="0">Tiền thật</option>
                                                <option value="1">Tiền khóa</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-12 col-md-2">
                                            <label class="label-w" for="params[voucher]">{{ __('Cho phép sử dụng mã giảm giá') }}</label>
                                            <span class="switch switch-outline switch-icon switch-success">
                                                    <label>
                                                        <input type="checkbox" id="params[voucher]" name="params[voucher]" value="1"
                                                               @if(isset($data->params->voucher) && $data->params->voucher == 1)
                                                               checked
                                                           @else
                                                               @endif
                                                        />
                                                        <span></span>
                                                    </label>
                                                </span>
                                        </div>

                                        <!-- Tích điểm -->
                                        <div class="col-12 col-md-2">

                                            <label class="label-w" for="params[point]">{{ __('Tích điểm') }}</label>
                                            <span class="switch switch-outline switch-icon switch-success">
                                                    <label>
                                                        <input type="checkbox" id="params[point]" name="params[point]" value="1"
                                                               @if(isset($data->params->point) && $data->params->point == 1)
                                                               checked
                                                           @else
                                                               @endif
                                                        />
                                                        <span></span>
                                                    </label>
                                                </span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-12 col-md-3">
                                            <label class="label-w">{{ __('Random điểm nhận được') }}</label>
                                            <input type="number" min='1' id="params[random_point_from]" name="params[random_point_from]" value="{{ old('params[random_point_from]', isset($data->params->random_point_from) ? $data->params->random_point_from : null) }}" autofocus
                                                   placeholder="{{ __('từ') }}" maxlength="120"
                                                   class="form-control {{ $errors->has('params[random_point_from]') ? ' is-invalid' : '' }}">
                                            @if ($errors->has('params[random_point_from]'))
                                                <span class="form-text text-danger">{{ $errors->first('params[random_point_from]') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <label>&nbsp;</label>
                                            <input type="number" min='1' id="params[random_point_to]" name="params[random_point_to]" value="{{ old('params[random_point_to]', isset($data->params->random_point_to) ? $data->params->random_point_to : null) }}" autofocus
                                                   placeholder="{{ __('đến') }}" maxlength="120"
                                                   class="form-control {{ $errors->has('params[random_point_to]') ? ' is-invalid' : '' }}">
                                            @if ($errors->has('params[random_point_to]'))
                                                <span class="form-text text-danger">{{ $errors->first('params[random_point_to]') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <!-- Chơi thử -->
                                        <div class="col-12 col-md-2">
                                            <label class="label-w" for="params[is_try]">{{ __('Chơi thử') }}</label>
                                            <span class="switch switch-outline switch-icon switch-success">
                                                    <label>
                                                        <input type="checkbox" id="params[is_try]" name="params[is_try]" value="1"
                                                               @if(isset($data->params->is_try) && $data->params->is_try == 1)
                                                               checked
                                                           @else
                                                               @endif
                                                        />
                                                        <span></span>
                                                    </label>
                                                </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            @if(isset($data))
                <!-- Cấu hình giải thưởng -->
                    <div class="tab-pane" id="prize" role="tabpanel">
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-12 left-right blook-item-title">
                                        <div class="row marginauto">
                                            <div class="col-md-8">
                                                <span>Cấu hình giải thưởng</span>
                                            </div>
                                            <div class="col-auto" style="margin-left: auto">
                                                <button type="button" class="btn btn-success btn__clone__giaithuong">Clone giải thưởng</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-warning" style="margin-top: 4px" role="alert">
                                        Thông tin chung sẽ được đồng bộ cho tất cả các điểm bán được phân phối.
                                    </div>
                                    <div class="col-md-12 left-right blook-item-body">
                                        <table class="table table-bordered table-hover table-checkable pt-3 pt-lg-4" id="kt_datatable_chgt">
                                        </table>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
            @endif


            <!-- Seeding -->
                @if(isset($dataCategory))
                    <div class="tab-pane" id="seedding" role="tabpanel">
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-12 left-right blook-item-title">
                                        <span>Seeding</span>
                                    </div>
                                    <div class="col-md-12 left-right blook-item-body">
                                        <div class="form-group row">
                                            <div class="col-12 col-md-6">
                                                <label class="label-w">{{ __('Số lượt đã chơi (hiển thị ở trang chủ)') }}</label>
                                                <input type="number" min='0' id="params[fake_num_play]" name="params[fake_num_play]" value="{{ old('params[fake_num_play]', isset($data_custom->params->fake_num_play) ? $data_custom->params->fake_num_play : null) }}" autofocus
                                                       placeholder="{{ __('Số lượt đã chơi (hiển thị ở trang chủ)') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[fake_num_play]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[fake_num_play]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[fake_num_play]') }}</span>
                                                @endif
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="label-w">{{ __('Giảm giá') }}</label>
                                                <input type="number" onkeyup="checkMax('percent_sale',100)" min='0' max="100" id="percent_sale" name="params[percent_sale]" value="{{ old('params[percent_sale]', isset($data_custom->params->percent_sale) ? $data_custom->params->percent_sale : null) }}" autofocus
                                                       placeholder="{{ __('Giảm giá') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[percent_sale]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[percent_sale]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[percent_sale]') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-12 col-md-6">
                                                <label class="label-w">{{ __('Tên user trong lượt quay gần đây (Cách nhau bởi dấu ,)') }}</label>
                                                <input type="text" id="user_wheel" name="params[user_wheel]" value="{{ old('params[user_wheel]', isset($data_custom->params->user_wheel) ? $data_custom->params->user_wheel : null) }}" autofocus
                                                       placeholder="{{ __('Tên user') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[user_wheel]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[user_wheel]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[user_wheel]') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-12 col-md-6">
                                                <label class="label-w">{{ __('Tên giải thưởng trong lượt quay gần đây (Cách nhau bởi dấu ,)') }}</label>
                                                <input type="text" id="user_wheel_order" name="params[user_wheel_order]" value="{{ old('params[user_wheel_order]', isset($data_custom->params->user_wheel_order) ? $data_custom->params->user_wheel_order : null) }}" autofocus
                                                       placeholder="{{ __('Tên giải thưởng') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[user_wheel_order]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[user_wheel_order]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[user_wheel_order]') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-12 col-md-6">
                                                <label class="label-w">{{ __('Id user idol (Cách nhau bởi dấu ,)') }}</label>
                                                <input type="text" id="user_wheel_idol" name="params[user_wheel_idol]" value="{{ old('params[user_wheel_idol]', isset($data_custom->params->user_wheel_idol) ? $data_custom->params->user_wheel_idol : null) }}" autofocus
                                                       placeholder="{{ __('Tên user') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[user_wheel_idol]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[user_wheel]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[user_wheel_idol]') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-12 col-md-6">
                                                <label class="label-w">{{ __('Giải thưởng idol trúng (Cách nhau bởi dấu ,)') }}</label>
                                                <input type="text" id="user_wheel_order_idol" name="params[user_wheel_order_idol]" value="{{ old('params[user_wheel_order_idol]', isset($data_custom->params->user_wheel_order_idol) ? $data_custom->params->user_wheel_order_idol : null) }}" autofocus
                                                       placeholder="{{ __('Tên giải thưởng') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[user_wheel_order_idol]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[user_wheel_order_idol]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[user_wheel_order_idol]') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row marginauto block-hr"></div>
                                <!-- Block 2 -->
                                <div class="row marginauto blook-item-row" id="seeding">
                                    <div class="col-md-12 left-right blook-item-title">
                                        <span>Seeding package</span>
                                    </div>
                                    <div class="col-md-12 left-right blook-item-body">

                                        <div class="form-group row">
                                            <div class="col-4 col-md-4">
                                                <label for="idkey" class="form-control-label label-w">{{ __('Seeding package') }}</label>
                                                {{Form::select('idkey',[''=>'-- Chọn seeding package --']+$dataCategory->pluck('title','id')->toArray(),old('idkey', isset($data_custom) ? $data_custom->idkey : null),array('id'=>'idkey','class'=>'form-control datatable-input',))}}
                                                @if($errors->has('idkey'))
                                                    <div class="form-control-feedback">{{ $errors->first('idkey') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        @foreach($dataCategory as $item)
                                            <input type="hidden" id="package{{$item->id}}" value="{{json_encode($item->params)}}" data-date="{{isset($item->started_at)?date('d/m/Y H:i:s', strtotime($item->started_at)) : ''}}">
                                        @endforeach

                                        <div class="form-group row">
                                            <div class="col-3 col-md-3">
                                                <label class="label-w">{{ __('Số user top') }}</label>
                                                <input type="number" id="acc_show_num" name="params[acc_show_num]" value="{{ old('params[acc_show_num]', isset($data_custom->params->acc_show_num) ? $data_custom->params->acc_show_num : null) }}" autofocus
                                                       placeholder="{{ __('Số user top') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[acc_show_num]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[acc_show_num]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[acc_show_num]') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-3 col-md-3">
                                                <label class="label-w">{{ __('Số lượt chơi') }}</label>
                                                <input type="number" id="play_num_from" name="params[play_num_from]" value="{{ old('params[play_num_from]', isset($data_custom->params->play_num_from) ? $data_custom->params->play_num_from : null) }}" autofocus
                                                       placeholder="{{ __('từ') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[play_num_from]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[play_num_from]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[play_num_from]') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-3 col-md-3">
                                                <label>&nbsp;</label>
                                                <input type="number" id="play_num_to" name="params[play_num_to]" value="{{ old('params[play_num_to]', isset($data_custom->params->play_num_to) ? $data_custom->params->play_num_to : null) }}" autofocus
                                                       placeholder="{{ __('đến') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[play_num_to]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[play_num_to]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[play_num_to]') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-3 col-md-3">
                                                <label class="label-w">{{ __('Số người đang chơi') }}</label>
                                                <input type="number" id="user_num_from" name="params[user_num_from]" value="{{ old('params[user_num_from]', isset($data_custom->params->user_num_from) ? $data_custom->params->user_num_from : null) }}" autofocus
                                                       placeholder="{{ __('từ') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[user_num_from]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[user_num_from]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[user_num_from]') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-3 col-md-3">
                                                <label>&nbsp;</label>
                                                <input type="number" id="user_num_to" name="params[user_num_to]" value="{{ old('params[user_num_to]', isset($data_custom->params->user_num_to) ? $data_custom->params->user_num_to : null) }}" autofocus
                                                       placeholder="{{ __('đến') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[user_num_to]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[user_num_to]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[user_num_to]') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-3 col-md-3">
                                                <label class="label-w">{{ __('Số lượt chơi gần đây') }}</label>
                                                <input type="number" id="play_num_near" name="params[play_num_near]" value="{{ old('params[play_num_near]', isset($data_custom->params->play_num_near) ? $data_custom->params->play_num_near : null) }}" autofocus
                                                       placeholder="{{ __('Số lượt chơi gần đây') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[play_num_near]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[play_num_near]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[play_num_near]') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-3 col-md-3">
                                                <label class="label-w">{{ __('Số trúng giải đặc biệt') }}</label>
                                                <input type="number" id="special_num_from" name="params[special_num_from]" value="{{ old('params[special_num_from]', isset($data_custom->params->special_num_from) ? $data_custom->params->special_num_from : null) }}" autofocus
                                                       placeholder="{{ __('từ') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[special_num_from]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[special_num_from]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[special_num_from]') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-3 col-md-3">
                                                <label>&nbsp;</label>
                                                <input type="number" id="special_num_to" name="params[special_num_to]" value="{{ old('params[special_num_to]', isset($data_custom->params->special_num_to) ? $data_custom->params->special_num_to : null) }}" autofocus
                                                       placeholder="{{ __('đến') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[special_num_to]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[special_num_to]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[special_num_to]') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-3 col-md-3">
                                                <label class="label-w">{{ __('Số giải thưởng còn lại') }}</label>
                                                <input type="number" id="gift_num_exist" name="params[gift_num_exist]" value="{{ old('params[gift_num_exist]', isset($data_custom->params->gift_num_exist) ? $data_custom->params->gift_num_exist : null) }}" autofocus
                                                       placeholder="{{ __('Số giải thưởng còn lại') }}" maxlength="120"
                                                       class="form-control {{ $errors->has('params[gift_num_exist]') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('params[gift_num_exist]'))
                                                    <span class="form-text text-danger">{{ $errors->first('params[gift_num_exist]') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-12 col-md-3">
                                                <label class="label-w">{{ __('Đếm ngược') }}</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                                           name="started_at"
                                                           @if( isset($data_custom->started_at) && $data_custom->started_at!="0000-00-00 00:00:00" )
                                                           value="{{ old('started_at', isset($data_custom->started_at) ? date('d/m/Y H:i:s', strtotime($data_custom->started_at)) : "") }}"
                                                           @else
                                                           value="{{ old('started_at', "") }}"
                                                           @endif
                                                           placeholder="{{ __('Đếm ngược') }}" autocomplete="off"
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

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            <!-- Mô tả và SEO -->
                <div class="tab-pane" id="desc-seo" role="tabpanel">
                    <div class="row marginauto blook-row">
                        <div class="col-md-12 left-right">


                            <!-- Block 1 -->
                            <div class="row marginauto blook-item-row">
                                <div class="col-md-12 left-right blook-item-title">
                                    <span>Tối ưu seo</span>
                                </div>
                                <div class="col-md-12 left-right blook-item-body">
                                    {{-----seo_title------}}
                                    <div class="form-group row">
                                        <div class="col-12 col-md-12">
                                            <label class="label-w">{{ __('Tiêu đề Trang (<title>)') }}</label>
                                            <input type="text" id="seo_title" name="seo_title" value="{{ old('seo_title', isset($data_custom) ? $data_custom->seo_title : null) }}"
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

                                            <label class="label-w">{{ __('Mô Tả Trang ( <meta Description> )') }}</label>
                                            <input type="text" id="seo_description" name="seo_description" value="{{ old('seo_description', isset($data_custom) ? $data_custom->seo_description : null) }}"
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
                                            <h3 id="google_title" class="title_google" style="color:#1a0dab;font-size: 18px;font-family: arial,sans-serif;padding:0;margin: 0;">{{ old('title', isset($data_custom) ? $data_custom->title : null) }}</h3>
                                            <div style="color:#006621;font-size: 14px;font-family: arial,sans-serif;">
                                                <span class="prefix_url">{{Request::getSchemeAndHttpHost()}}/</span><span id="google_slug" class="google_slug">{{ old('slug', isset($data_custom) ? $data_custom->slug : null) }}</span>
                                            </div>
                                            <div id="google_description" class="google_description" style="color: #545454;font-size: small;font-family: arial,sans-serif;">{{ old('description', isset($data_custom) ? $data_custom->description : null) !=""??"Mô tả seo website không vượt quá 160 kí tự. Là những đoạn mô tả ngắn gọn về website, bài viết..." }}</div>
                                        </div>
                                    </fieldset>


                                    {{-----seo_robots------}}
                                    <div class="form-group row">
                                        <div class="col-12 col-md-12">
                                            <label class="label-w">{{ __('Index, Follow') }}</label>
                                            <span class="switch switch-outline switch-icon switch-success">
                                                    <label><input type="checkbox" checked /><span></span></label>
                                                </span>
                                        </div>

                                    </div>

                                    <div class="test-fuck">
                                        {!!  clean( old('content', isset($data_custom) ? $data_custom->content : null)) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row marginauto block-hr"></div>

                            <!-- Block 2 -->
                            <div class="row marginauto blook-item-row">
                                <div class="col-md-12 left-right blook-item-title">
                                    <span>Nội dung chi tiết</span>
                                </div>
                                <div class="col-md-12 left-right blook-item-body">

                                    {{-----content------}}
                                    <div class="form-group row">
                                        <div class="col-12 col-md-12">
                                            <label class="label-w" for="locale">{{ __('Nội dung') }}</label>
                                            <textarea id="content" name="content" class="form-control ckeditor-source" data-height="300" data-startup-mode="" >{{ old('content', isset($data_custom) ? $data_custom->content : null) }}</textarea>
                                            @if ($errors->has('content'))
                                                <span class="form-text text-danger">{{ $errors->first('content') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--                    @if(isset($data))--}}

                @if(isset($created))
                <!-- Phân phối -->
                    <div class="tab-pane {{ isset($data_viewed) && $data_viewed != '' ? 'show active' : '' }}" id="dulication" role="tabpanel">
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-12 left-right blook-item-title">
                                        <div class="row marginauto">
                                            <div class="col-auto left-right">
                                                <span>Danh sách wesite phân phối</span>
                                            </div>
                                            <div class="col-auto" style="margin-left: auto">
                                                {{--                                                <button class="btn btn-danger add-webits-created" data-shop="3" type="button">Xóa</button>--}}
                                                <button class="btn btn-primary add-webits-created" data-shop="1" type="button">Thêm điểm bán</button>
{{--                                                <button class="btn btn-success add-webits-created" data-shop="2" type="button">Thêm nhóm điểm bán</button>--}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 left-right blook-item-body">
                                        <table class="table table-bordered table-hover" id="kt_datatable_created">
                                            <thead style="background: #f3f6f9;border-radius: 4px">
                                            <tr role="row">
                                                <th class="ckb_item sorting_disabled" rowspan="1" colspan="1">
                                                    <label class="checkbox checkbox-lg checkbox-outline">
                                                        <input type="checkbox" id="btnCheckAllppCreated">&nbsp;
                                                        <span></span>
                                                    </label>
                                                </th>
                                                <th>ID</th>
                                                <th>Tên điểm bán/nhóm điểm bán</th>
                                                <th>Trạng thái minigame</th>
                                                <th>Thao tác</th>
                                            </tr>
                                            </thead>
                                            <tbody class="data_add_shop">
                                            <tr class="text-center"><td colspan="7">Chưa phân phối đến điểm bán nào</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @else
                    <div class="tab-pane {{ isset($data_viewed) && $data_viewed != '' ? 'show active' : '' }}" id="dulication" role="tabpanel">
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                @if(session('shop_id'))
                                    <div class="row marginauto blook-item-row">
                                        <div class="col-md-12 left-right blook-item-title">
                                            <span>Trạng thái</span>
                                        </div>
                                        <div class="col-md-12 left-right blook-item-body">
                                            @if(session('shop_id'))
                                                {{-- status --}}
                                                <div class="form-group row">
                                                    <div class="col-12 col-md-4">
                                                        <label for="status" class="form-control-label label-w">{{ __('Trạng thái') }}</label>
                                                        @if(isset($c_data->customs) && count($c_data->customs) > 0)
                                                            @if($c_data->customs[0]->status == 1)
                                                                <input type="text" style="background: #F3F3F7;" readonly class="form-control c_data_status" value="Hoạt động">
                                                            @else
                                                                <input type="text" style="background: #F3F3F7;" readonly class="form-control c_data_status" value="Khóa">
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            @if(session('shop_id'))
                                                <div class="form-group row">
                                                    <div class="col-12 col-md-4">
                                                        <label class="label-w">{{ __('Thời gian tạo') }}</label>
                                                        @if(isset($c_data->customs) && count($c_data->customs) > 0)
                                                            <input style="background: #F3F3F7;" type="text" readonly class="form-control" value="{{ $c_data->customs[0]->created_at }}">
                                                        @endif
                                                    </div>

                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                @endif
                                <div class="row marginauto block-hr"></div>
                                <!-- Block 2 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-12 left-right blook-item-title">
                                        <div class="row marginauto">
                                            <div class="col-auto left-right">
                                                <span>Danh sách website phân phối</span>
                                            </div>
                                            <div class="col-auto" style="margin-left: auto">
                                                <button class="btn btn-danger add-webits" data-shop="4" type="button">Xóa</button>
{{--                                                <button class="btn btn-warning add-webits" data-shop="3" type="button">Kích hoạt</button>--}}
                                                <button class="btn btn-primary add-webits" data-shop="1" type="button">Thêm điểm bán</button>
{{--                                                <button class="btn btn-success add-webits" data-shop="2" type="button">Thêm nhóm điểm bán</button>--}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 left-right blook-item-body" id="kt_datatable_pp">
                                        <table class="table table-bordered table-hover table-checkable " id="kt_datatable">
                                        </table>
                                    </div>
                                </div>

                                @if(isset($data_shop))
                                    <div class="row marginauto block-hr"></div>
                                    <!-- Block 4 -->
                                    <div class="row marginauto blook-item-row">
                                        <div class="col-md-12 left-right blook-item-title">
                                            <span>Thông tin nhân bản</span>
                                        </div>
                                        @if(isset($data_shop->parent))
                                            <div class="col-md-12 left-right" style="margin-top: 8px">
                                                <span class="label-w" style="font-size: 14px">Thông tin minigame gốc</span>
                                            </div>
                                            <div class="col-md-12 left-right c_blook-item-body">

                                                <table class="table table-bordered table-hover table-checkable dataTable no-footer dtr-inline">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="sorting_desc" style="width: 13px;">ID</th>
                                                        @if(isset($data_shop->parent_id) && $data_shop->parent_id > 0)
                                                            <th class="sorting" colspan="1" style="width: 46px;">Tên minigame gốc</th>
                                                        @endif

                                                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 32px;">URL</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(isset($data_shop->parent->customs) && count($data_shop->parent->customs) > 0)
                                                        @foreach($data_shop->parent->customs as $custom)
                                                            @if(session('shop_id'))

                                                                @if($custom->shop->id == session('shop_id'))
                                                                    @php
                                                                        $r_domain = route('admin.'.$c_module.'.edit',$data_shop->parent_id)."?position=".$data_shop->parent->position;
                                                                    @endphp
                                                                    <tr class="odd">
                                                                        <td class="sorting_1">{{ $custom->group_id??($data_shop->parent_id??'') }}</td>
                                                                        <td class="sorting_1">{{ $custom->title??($data_shop->title??'') }}</td>
                                                                        <td>
                                                                            <a target="_blank" href="{{ $r_domain }}">{{ $r_domain }}</a>
                                                                        </td>
                                                                    </tr>
                                                                @endif

                                                            @endif
                                                        @endforeach
                                                    @else

                                                        @php
                                                            $r_domainn = route('admin.'.$c_module.'.edit',$data_shop->parent->id)."?position=".$data_shop->parent->position;
                                                        @endphp
                                                        <tr class="odd">
                                                            <td class="sorting_1">{{ $data_shop->parent->id }}</td>
                                                            <td class="sorting_1">{{ $data_shop->parent->title }}</td>
                                                            <td>
                                                                <a target="_blank" href="{{ $r_domainn }}">{{ $r_domainn }}</a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    </tbody>
                                                </table>
                                                {{--Thông tin khác--}}
                                            </div>
                                        @endif
                                        @if(isset($data_shop->childs))
                                            <div class="col-md-12 left-right" style="margin-top: 8px">
                                                <span class="label-w" style="font-size: 14px">Thông tin minigame được nhân bản</span>
                                            </div>
                                            <div class="col-md-12 left-right c_blook-item-body">

                                                <table class="table table-bordered table-hover table-checkable dataTable no-footer dtr-inline">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="sorting_desc" style="width: 13px;">ID</th>
                                                        <th class="sorting" colspan="1" style="width: 46px;">Tên minigame được nhân bản</th>
                                                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 32px;">URL</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(isset($data_shop->childs))
                                                        @forelse ($data_shop->childs as $item)
                                                            @if(isset($item->customs) && count($item->customs) > 0)
                                                                @foreach($item->customs as $custom)

                                                                    {{--                                                                    @dd($data_shop->customs)--}}
                                                                    @if(session('shop_id'))
                                                                        @if($custom->shop->id == session('shop_id'))

                                                                            @php
                                                                                $r_item_domain = route('admin.'.$c_module.'.edit',$item->id)."?position=".$item->position;
                                                                            @endphp
                                                                            <tr class="odd">
                                                                                <td class="sorting_1">{{ $item->id??($item->id??'') }}</td>
                                                                                <td class="sorting_1">{{ $custom->title??($item->title??'') }}</td>
                                                                                <td>
                                                                                    <a target="_blank" href="{{ $r_item_domain }}">{{ $r_item_domain }}</a>
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    @else
                                                                        {{--                                                                            @php--}}
                                                                        {{--                                                                                $r_item_domain = 'https://'.$custom->shop->domain.'/minigame-'.$custom->slug;--}}
                                                                        {{--                                                                            @endphp--}}
                                                                        {{--                                                                            <tr class="odd">--}}
                                                                        {{--                                                                                <td class="sorting_1">{{ $custom->id??($item->id??'') }}</td>--}}
                                                                        {{--                                                                                <td class="sorting_1">{{ $custom->title??($item->title??'') }}</td>--}}
                                                                        {{--                                                                                <td>--}}
                                                                        {{--                                                                                    <a target="_blank" href="{{ $r_item_domain }}">{{ $r_item_domain }}</a>--}}
                                                                        {{--                                                                                </td>--}}
                                                                        {{--                                                                            </tr>--}}
                                                                    @endif

                                                                @endforeach
                                                            @else
                                                                @php
                                                                    $rn_item_domain = route('admin.'.$c_module.'.edit',$item->id)."?position=".$item->position;
                                                                @endphp
                                                                <tr class="odd">
                                                                    <td class="sorting_1">{{ $item->id }}</td>
                                                                    <td class="sorting_1">{{ $item->title }}</td>
                                                                    <td>
                                                                        <a target="_blank" href="{{ $rn_item_domain }}">{{ $rn_item_domain }}</a>
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                        @empty
                                                            <td class="sorting_1" scope="3">Chưa có nhân bản nào</td>
                                                        @endforelse
                                                    @else
                                                        <tr class="odd justify-content-center">
                                                            <td class="sorting_1" scope="3">Chưa có nhân bản nào</td>
                                                        </tr>
                                                    @endif
                                                    </tbody>
                                                </table>
                                                {{--Thông tin khác--}}
                                            </div>
                                        @endif
                                    </div>

                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            <!-- Log edit -->
{{--                @if(isset($log_edit) && count($log_edit))--}}
{{--                    <div class="tab-pane" id="logEdit" role="tabpanel">--}}
{{--                        <div class="row marginauto blook-row">--}}
{{--                            <div class="col-md-12 left-right">--}}
{{--                                <!-- Block 1 -->--}}
{{--                                <div class="row marginauto blook-item-row">--}}
{{--                                    <div class="col-md-12 left-right blook-item-title">--}}
{{--                                        <span>Log edit</span>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-12 left-right blook-item-body">--}}
{{--                                        <div class="col-md-4 pl-0 pr-0">--}}
{{--                                            @if(isset($log_edit) && count($log_edit))--}}
{{--                                                <div class="card card-custom gutter-b" style="background: #ffffff;box-shadow: none">--}}
{{--                                                    <div class="card-header">--}}
{{--                                                        <div class="card-title">--}}
{{--                                                            <h3 class="card-label">--}}
{{--                                                                Log edit <i class="mr-2"></i>--}}
{{--                                                            </h3>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}

{{--                                                    <div class="card-body" style="padding-top: 24px">--}}
{{--                                                        --}}{{--                                                         status--}}
{{--                                                        <div class="form-group row">--}}
{{--                                                            @foreach($log_edit as $key_log => $log)--}}
{{--                                                                <div class="col-12 col-md-12" style="padding-top: 16px">--}}
{{--                                                                    <ul style="float: left;padding-left: 0;margin-bottom: 0">--}}
{{--                                                                        <li style="list-style: none;float: left;">--}}
{{--                                                                            <span style="font-weight: bold">{{ $key_log + 1 }}.</span>--}}
{{--                                                                        </li>--}}
{{--                                                                        <li style="list-style: none;float: left;margin-left: 8px">--}}
{{--                                                                            <span style="background: #A7ABC3;padding: 6px 8px;border-radius: 4px"><i class="menu-icon fas fa-user" style="color: #ffffff;font-size: 12px"></i></span>--}}
{{--                                                                        </li>--}}
{{--                                                                        <li style="list-style: none;float: left;margin-left: 4px">{{ $log->author->username }}</li>--}}
{{--                                                                        <li style="list-style: none;float: left;margin-left: 8px">--}}
{{--                                                                            <a href="/admin/minigame-category/{{ $data->id }}/revision/{{ $log->id }}">{{ $log->created_at }}</a>--}}
{{--                                                                            <a href="javascript:void(0)">{{ $log->time_now }}</a>--}}

{{--                                                                        </li>--}}
{{--                                                                        @if(isset($log->type))--}}
{{--                                                                            <li style="list-style: none;float: left;margin-left: 8px">--}}
{{--                                                                                [ {{ config('module.article.log_edit.'.$log->type) }} ]--}}
{{--                                                                            </li>--}}
{{--                                                                        @endif--}}
{{--                                                                    </ul>--}}
{{--                                                                </div>--}}
{{--                                                            @endforeach--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            @endif--}}
{{--                                        </div>--}}

{{--                                    </div>--}}
{{--                                </div>--}}

{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endif--}}
            </div>
        </div>
    </div>
    {{ Form::close() }}

    @if(isset($data))
        <div class="modal fade bd-example-modal-lg" id="customModal">
            <div class="modal-dialog  modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Sửa thông tin custom:')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body ds-shop-body">
                        <br>
                        <div class="row">
                            <div class="col-md-6" style="padding: 0 8px">
                                <div class="form-group row">
                                    <div class="col-12 col-md-12">
                                        <label class="form-control-label label-w">{{ __('Tên giải thưởng gốc:') }}</label>
                                        <input disabled type="text" id="title_minigame" value="" autofocus
                                               placeholder="{{ __('Tên giải thưởng') }}" maxlength="120"
                                               class="form-control">
                                        {{--                                    <input type="hidden" id="id_minigame" value="">--}}
                                        {{--                                    <input type="hidden" id="parent_id" value="">--}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding: 0 8px">
                                <div class="form-group row">
                                    {{-----image------}}
                                    <div class="col-md-10">
                                        <label class="form-control-label label-w" for="locale">{{ __('Ảnh gốc') }}:</label>
                                        <div class="">
                                            <div class="fileinput ck-parent" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 150px; height: 150px">
                                                    <img class="ck-thumb image_minigame" src="">
                                                    <input class="ck-input" type="hidden" id="image_minigame" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6" style="padding: 0 8px">
                                <div class="form-group row">
                                    <div class="col-12 col-md-12">
                                        <label class="form-control-label label-w">{{ __('Tên giải thưởng custom') }}</label>
                                        <input type="text" id="title_custom" value="" autofocus
                                               placeholder="{{ __('Tên giải thưởng') }}" maxlength="120"
                                               class="form-control">
                                        <input type="hidden" id="id_custom" value="">
                                        <input type="hidden" id="parent_id" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding: 0 8px">
                                <div class="form-group row">
                                    {{-----image------}}
                                    <div class="col-md-10">
                                        <label class="form-control-label label-w" for="locale">{{ __('Ảnh custom') }}:</label>
                                        <div class="">
                                            <div class="fileinput ck-parent" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 150px; height: 150px">
                                                    <img class="ck-thumb image_custom" src="">
                                                    <input class="ck-input" type="hidden" id="image_custom" value="">
                                                </div>
                                                <div>
                                                    <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                                    <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Trở lại')}}</button>
                        <button type="button" class="btn btn-warning m-btn m-btn--custom btn_custom_update">{{__('Cập nhật')}}</button>
                    </div>
                </div>
            </div>
        </div>

    @endif

    @if(isset($created))
        <div class="modal fade bd-example-modal-lg" id="dsShopCreated">
            <div class="modal-dialog" style="max-width: 766px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Danh sách điểm bán:')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body ds-shop-body">
                        <div class="row marginauto">
                            <!--begin: Search Form-->
                            <div class="col-md-12">
                                <form class="mb-10">
                                    <div class="row">
                                        {{--ID--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                </div>
                                                <input type="text" class="form-control datatable-input_add-shop-created" id="id" placeholder="{{__('ID')}}">
                                            </div>
                                        </div>
                                        {{--title--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                </div>
                                                <input type="text" class="form-control datatable-input_add-shop-created" id="domain"
                                                       placeholder="{{__('Shop')}}">
                                            </div>
                                        </div>


                                        @if(isset($shop_group))
                                            <div class="form-group col-12 col-sm-6 col-lg-4">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                    </div>
                                                    <select name="group_shop" id="group_shop" class="form-control datatable-input_add-shop-created">
                                                        <option value="">Chọn nhóm shop</option>
                                                        @foreach($shop_group as $key)
                                                            <option value="{{ $key->id }}">{{ $key->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button class="btn btn-primary btn-primary--icon" id="kt_search_add-shop-created">
                                            <span>
                                                <i class="la la-search"></i>
                                                <span>Tìm kiếm</span>
                                            </span>
                                            </button>&#160;&#160;
                                            <button class="btn btn-secondary btn-secondary--icon" id="kt_reset_add-shop-created">
                                            <span>
                                                <i class="la la-close"></i>
                                                <span>Reset</span>
                                            </span>
                                            </button>
                                        </div>

                                    </div>
                                </form>
                            </div>

                            <!--begin: Search Form-->
                        </div>

                        <div class="row marginauto ds-shop-search pt-lg-4 pt-3">
                            <div class="col-md-12 scroll-default left-right">
                                <table class="table table-bordered table-hover table-checkable " id="kt_datatable_addwebits_created">
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="data_shop" class="data_shop" >
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Trở lại')}}</button>
                        <button type="button" class="btn btn-warning m-btn m-btn--custom add-shop-created">{{__('Thêm Shop')}}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-lg" id="dsNhomShopCreated">
            <div class="modal-dialog" style="max-width: 766px">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: none;justify-content: center">
                        <h5 class="modal-title" style="font-weight: 700" id="exampleModalLabel"> {{__('DANH SÁCH NHÓM ĐIỂM BÁN')}}</h5>
                    </div>
                    <div class="modal-body ds-shop-body">
                        <div class="row marginauto">
                            <!--begin: Search Form-->
                            <div class="col-md-12 p-0">
                                <div class="row marginauto blook-item-row p-0">
                                    <!-- <div class="col-md-12 left-right blook-item-title">
                                        <span>Danh sách nhóm website</span>
                                    </div> -->
                                    <div class="col-md-12 left-right blook-item-body">

                                        <table class="table table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table-group-shop-created">
                                            <thead>
                                            <tr role="row">

                                                <th class="sorting_desc" style="width: 13px;">
                                                    <label class="checkbox checkbox-lg checkbox-outline">
                                                        <input type="checkbox" id="btnCheckAllGroupCreated">&nbsp<span></span>
                                                    </label>
                                                </th>
                                                <th class="sorting_desc" style="width: 13px;">ID</th>
                                                <th class="sorting" colspan="1" style="width: 46px;">Tên nhóm điểm bán</th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 32px;">Trạng thái</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($shop_group))
                                                @foreach($shop_group as $key => $item)
                                                    <tr class="odd">
                                                        <td>
                                                            <label class="checkbox checkbox-lg checkbox-outline checkbox-item">
                                                                <input class="item-checkbox" type="checkbox" data-id="{{ $item->id }}" rel="{{ $item->id }}" id="">&nbsp<span></span>
                                                            </label>
                                                        </td>
                                                        <td class="sorting_1">{{ $item->id }}</td>
                                                        <td class="sorting_1">{{ $item->title }}</td>
                                                        <td>
                                                            @if($item->status == 1)
                                                                <span class="badge badge-success">Hoạt động</span>
                                                            @else
                                                                <span class="badge badge-danger">Khóa</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                        {{--Thông tin khác--}}
                                    </div>
                                </div>
                            </div>

                            <!--begin: Search Form-->
                        </div>


                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="data_shop" class="data_shop" >
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="button" class="btn btn-warning m-btn m-btn--custom add-group-shop-created">{{__('Thêm Shop')}}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteShopCreated">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body r_data-title">
                        {{__('Bạn thực sự muốn bỏ phân phối shop này?')}}
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="delete_created" class="delete_created">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-primary m-btn m-btn--custom b-custom btn_delete_created">{{__('Xóa')}}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(isset($data))
        <div class="modal fade bd-example-modal-lg" id="dsShop">
            <div class="modal-dialog" style="max-width: 766px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Danh sách điểm bán:')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body ds-shop-body">
                        <div class="row marginauto">
                            <!--begin: Search Form-->
                            <div class="col-md-12">
                                <form class="mb-10">
                                    <div class="row">
                                        {{--ID--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                </div>
                                                <input type="text" class="form-control datatable-input_add-shop" id="id" placeholder="{{__('ID')}}">
                                            </div>
                                        </div>
                                        {{--title--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                </div>
                                                <input type="text" class="form-control datatable-input_add-shop" id="domain"
                                                       placeholder="{{__('Shop')}}">
                                            </div>
                                        </div>


                                        @if(isset($shop_group))
                                            <div class="form-group col-12 col-sm-6 col-lg-4">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                    </div>
                                                    <select name="group_shop" id="group_shop" class="form-control datatable-input_add-shop">
                                                        <option value="">Chọn nhóm shop</option>
                                                        @foreach($shop_group as $key)
                                                            <option value="{{ $key->id }}">{{ $key->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif

                                        {{--started_at--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Từ</span>
                                                </div>
                                                <input type="text" name="started_at" id="started_at" autocomplete="off"
                                                       class="form-control datatable-input_add-shop  datetimepicker-input datetimepicker-default"
                                                       placeholder="{{__('Thời gian bắt đầu')}}" data-toggle="datetimepicker">

                                            </div>
                                        </div>

                                        {{--ended_at--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Đến</span>
                                                </div>
                                                <input type="text" name="ended_at" id="ended_at" autocomplete="off"
                                                       class="form-control datatable-input_add-shop   datetimepicker-input datetimepicker-default"
                                                       placeholder="{{__('Thời gian kết thúc')}}" data-toggle="datetimepicker">

                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button class="btn btn-primary btn-primary--icon" id="kt_search_add-shop">
                                            <span>
                                                <i class="la la-search"></i>
                                                <span>Tìm kiếm</span>
                                            </span>
                                            </button>&#160;&#160;
                                            <button class="btn btn-secondary btn-secondary--icon" id="kt_reset_add-shop">
                                            <span>
                                                <i class="la la-close"></i>
                                                <span>Reset</span>
                                            </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!--begin: Search Form-->
                        </div>

                        <div class="row marginauto ds-shop-search pt-lg-4 pt-3">
                            <div class="col-md-12 left-right scroll-default">
                                <table class="table table-bordered table-hover table-checkable " id="kt_datatable_addwebits">
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{Form::open(array('route'=>array('admin.'.$module.'.distribution',$data->id),'class'=>'form-horizontal distribution-form','id'=>'form-save','method'=>'POST'))}}
                        <input type="hidden" name="id" class="id"/>
                        <input type="hidden" name="object_shop" class="object_shop" >
                        <input type="hidden" name="addshop" class="addshop" value="0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Trở lại')}}</button>
                        <button type="submit" class="btn btn-warning m-btn m-btn--custom">{{__('Cập nhật')}}</button>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-lg" id="dsNhomShop">
            <div class="modal-dialog" style="max-width: 766px">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: none;justify-content: center">
                        <h5 class="modal-title" style="font-weight: 700" id="exampleModalLabel"> {{__('DANH SÁCH NHÓM ĐIỂM BÁN')}}</h5>
                    </div>
                    <div class="modal-body ds-shop-body">
                        <div class="row marginauto">
                            <!--begin: Search Form-->
                            <div class="col-md-12 p-0">
                                <div class="row marginauto blook-item-row p-0">
                                    <!-- <div class="col-md-12 left-right blook-item-title">
                                        <span>Danh sách nhóm website</span>
                                    </div> -->
                                    <div class="col-md-12 left-right blook-item-body">

                                        <table class="table table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table-group-shop">
                                            <thead>
                                            <tr role="row">

                                                <th class="sorting_desc" style="width: 13px;">
                                                    <label class="checkbox checkbox-lg checkbox-outline">
                                                        <input type="checkbox" id="btnCheckAllGroup">&nbsp<span></span>
                                                    </label>
                                                </th>
                                                <th class="sorting_desc" style="width: 13px;">ID</th>
                                                <th class="sorting" colspan="1" style="width: 46px;">Tên nhóm điểm bán</th>
                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 32px;">Trạng thái</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($shop_group))
                                                @foreach($shop_group as $key => $item)
                                                    <tr class="odd">
                                                        <td>
                                                            <label class="checkbox checkbox-lg checkbox-outline checkbox-item checkbox-item_{{ $item->id }}">
                                                                <input class="item-checkbox" type="checkbox" data-id="{{ $item->id }}" rel="{{ $item->id }}" id="">&nbsp<span></span>
                                                            </label>
                                                        </td>
                                                        <td class="sorting_1">{{ $item->id }}</td>
                                                        <td class="sorting_1">{{ $item->title }}</td>
                                                        <td>
                                                            @if($item->status == 1)
                                                                <span class="badge badge-success">Hoạt động</span>
                                                            @else
                                                                <span class="badge badge-danger">Khóa</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                        {{--Thông tin khác--}}
                                    </div>
                                </div>
                            </div>

                            <!--begin: Search Form-->
                        </div>


                    </div>
                    <div class="modal-footer">
                        {{Form::open(array('route'=>array('admin.'.$module.'.distribution',$data->id),'class'=>'form-horizontal distribution-nhom-form','id'=>'form-save','method'=>'POST'))}}
                        <input type="hidden" name="id" class="id" value=""/>
                        <input type="hidden" name="object_shop" class="object_shop" >
                        <input type="hidden" name="addshop" class="addshop" value="1">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-warning m-btn m-btn--custom">{{__('Cập nhật')}}</button>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-lg" id="dsGiaiThuong">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Danh sách giải thưởng:')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body ds-shop-body">
                        <div class="row marginauto" style="padding-top: 24px">
                            <!--begin: Search Form-->
                            <div class="col-md-12">
                                <div class="row">
                                    {{--ID--}}
                                    <div class="form-group col-12 col-sm-6 col-lg-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                            <span class="input-group-text"><i
                                    class="la la-calendar-check-o glyphicon-th"></i></span>
                                            </div>
                                            <input type="text" class="form-control datatable-input_dsgt" id="id_dsgt" placeholder="{{__('ID')}}">
                                        </div>
                                    </div>
                                    {{--title--}}
                                    <div class="form-group col-12 col-sm-6 col-lg-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="la la-calendar-check-o glyphicon-th"></i></span>
                                            </div>
                                            <input type="text" class="form-control datatable-input_dsgt" id="title_dsgt" placeholder="{{__('Tiêu đề')}}">
                                        </div>
                                    </div>

                                    {{--valuefrom--}}
                                    <div class="form-group col-12 col-sm-6 col-lg-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="la la-calendar-check-o glyphicon-th"></i></span>
                                            </div>
                                            <input type="text" class="form-control datatable-input_dsgt" id="valuefrom_dsgt" placeholder="{{__('Giá trị VP (từ)')}}">
                                        </div>
                                    </div>

                                    {{--valueto--}}
                                    <div class="form-group col-12 col-sm-6 col-lg-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="la la-calendar-check-o glyphicon-th"></i></span>
                                            </div>
                                            <input type="text" class="form-control datatable-input_dsgt" id="valueto_dsgt" placeholder="{{__('Giá trị VP (đến)')}}">
                                        </div>
                                    </div>

                                    {{--gametype--}}
                                    <div class="form-group col-12 col-sm-6 col-lg-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="la la-calendar-check-o glyphicon-th"></i></span>
                                            </div>
                                            <select id="gametype_dsgt"
                                                    class="form-control datatable-input_dsgt" data-live-search="true"
                                                    title="-- {{__('Tất cả loại giải thưởng')}} --">
                                                <option value="">-- {{__('Tất cả loại giải thưởng')}} --</option>
                                                @if( !empty(old('parent_id')) )
                                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCategorygt,old('parent_id')) !!}
                                                @else
                                                    <?php $itSelect = [] ?>
                                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCategorygt,$itSelect) !!}
                                                @endif
                                            </select>

                                        </div>
                                    </div>

                                </div>
                                <div class="row pb-3 pb-lg-4">
                                    <div class="col-lg-12">
                                        <button class="btn btn-primary btn-primary--icon" id="kt_search_dsgt">
                                        <span>
                                            <i class="la la-search"></i>
                                            <span>Tìm kiếm</span>
                                        </span>
                                        </button>&#160;&#160;
                                        <button class="btn btn-secondary btn-secondary--icon" id="kt_reset_dsgt">
                                        <span>
                                            <i class="la la-close"></i>
                                            <span>Reset</span>
                                        </span>
                                        </button>
                                        <span style="margin-left:  16px;font-weight: 600">
                                        Số giải thưởng đã chọn: <small class="so-giai-thuong" style="font-size: 14px;background: #DA4343;color: #ffffff;border-radius: 50%;padding: 4px 8px">{{ $count_giaithuong??0 }}</small>
                                    </span>
                                    </div>
                                    <div class="col-auto pr-4 pl-0">
                                    </div>
                                </div>
                            </div>

                            <!--begin: Search Form-->
                        </div>

                        <div class="row marginauto ds-shop-search pt-lg-4 pt-3">
                            <div class="col-md-12 left-right">
                                <table class="table table-bordered table-hover table-checkable" id="kt_datatable_ctchgt">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="activeShop">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.minigame-category.activegroupshop'),'class'=>'form-horizontal','id'=>'','method'=>'POST'))}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body r_data-title">
                        {{__('Bạn thực sự muốn kích hoạt những điểm bán?')}}
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="activeid" class="active-id">
                        <input type="hidden" name="groupid" class="group-id">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-primary m-btn m-btn--custom b-custom">{{__('Kích hoạt')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="inActiveShop">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.minigame-category.deletegroupshop'),'class'=>'form-horizontal inActiveShopForm','id'=>'','method'=>'POST'))}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body r_data-title">
                        {{__('Bạn thực sự muốn bỏ phân phối điểm bán này?')}}
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="inactiveid" class="inactive-id">
                        <input type="hidden" name="groupid" class="group-id">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-danger m-btn m-btn--custom b-custom">{{__('Xóa')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteShop">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.minigame-category.deletegroupshop'),'class'=>'form-horizontal deleteShopFrom','id'=>'','method'=>'POST'))}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body r_data-title">
                        {{__('Bạn thực sự muốn bỏ phân phối điểm bán này?')}}
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="inactiveid" class="delete-id">
                        <input type="hidden" name="groupid" class="group-id">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-primary m-btn m-btn--custom b-custom">{{__('Xóa')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteGroupShop">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.minigame-category.deletegroupshop'),'class'=>'form-horizontal deleteGroupShopFrom','id'=>'','method'=>'POST'))}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body r_data-title">
                        {{__('Bạn thực sự muốn bỏ phân phối những nhóm điểm bán này?')}}
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="inactiveid" class="delete-id">
                        <input type="hidden" name="groupid" class="group-id">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-primary m-btn m-btn--custom b-custom">{{__('Xóa')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="cloneGiaiThuong">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.'.$module.'.clonegiaithuong',$data->id),'class'=>'form-horizontal','method'=>'POST'))}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body r_data-title">
                        <h3 style="font-size: 16px;padding-bottom: 16px">Chọn shop cần clone:</h3>
                        <select name="shop_clone" title="Chọn shop cần clone" class="form-control select2 col-md-5 s_clonegiaithuong"  data-placeholder="{{__('Hoặc chọn shop')}}" style="width: 100%">
                            @foreach($client as $key => $item)
                                <option value="{{ $item->id }}">{{ $item->domain }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-primary m-btn m-btn--custom b-custom">{{__('Clone')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    @endif

    <!-- delete item Modal -->
    <div class="modal fade" id="deleteItemModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn xóa?')}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="item_id" class="item_id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="button" class="btn btn-danger m-btn m-btn--custom deleteItem" data-form="form-delete">{{__('Xóa')}}</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" value="{{ isset($isReplication) ? $isReplication : '' }}" name="parentcheck" class="parentcheck">

    @if(isset($c_data_shop))
        <input type="hidden" class="c_data_shop" name="c_data_shop" value="{{ $c_data_shop }}">
    @endif

    <input type="hidden" name="cg_shop" class="cg_shop" value="{{ session('shop_id')??'' }}">

    <input type="hidden" name="t_position" class="t_position" value="{{ $position??0 }}">
    <input type="hidden" name="s_title" class="s_title" value="{{ $data_custom->title??'' }}">
    <input type="hidden" name="cookies_phanphoi" class="cookies_phanphoi" value="{{ isset($data_viewed) ? $data_viewed : '' }}">
    @if(isset($data))
        <input type="hidden" name="flastPosition" class="flastPosition" value="{{ $flastPosition??0 }}">
    @endif
    <input type="hidden" value="{{url()->current()}}" name="urlcurrent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <input type="hidden" value="" name="datasetitem" id="datasetitem">

    <input type="hidden" class="l_count_giaithuong" name="count_giaithuong" value="{{ $count_giaithuong??0 }}">

    <input type="hidden" value="0" id="datachange">
    <input type="hidden" value="0" id="datachangechung">


@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')

    <script src="/assets/backend/assets/js/wnumb.js?v={{time()}}"></script>

    <script>
        $(document).ready(function (e) {

            $('body').on('click', '.btn__clone__giaithuong',function(){
                $('#cloneGiaiThuong').modal('show');
            })

                $('.s_clonegiaithuong').select2();


            $('#datachange').val(0);
            //check có thay đổi trên form
            $('.tab-pane select').change(function(){
                $('#datachange').val(1);
            })
            $('.tab-pane input[type=text]').keyup(function(){
                $('#datachange').val(1);
            })
            $('.tab-pane input[type=number]').keyup(function(){
                $('#datachange').val(1);
            })
            $('.tab-pane input[type=checkbox]').change(function(){
                $('#datachange').val(1);
            })
            $('#datachangechung').val(0);
            //check có thay đổi trên form chung
            $('.tab-pane .ttchung select').change(function(){
                $('#datachangechung').val(1);
            })
            $('.tab-pane .ttchung input[type=text]').keyup(function(){
                $('#datachangechung').val(1);
            })
            $('.tab-pane .ttchung input[type=number]').keyup(function(){
                $('#datachangechung').val(1);
            })
            $('.tab-pane .ttchung input[type=checkbox]').change(function(){
                $('#datachangechung').val(1);
            });
            $(document).on('click', '.ds-checkbox-item-disable',function(event){
                // if (confirm('Bạn đã chọn đủ số lượng giải thưởng,Không thể chọn thêm giải thưởng.')) {
                //     return false;
                // }else {
                //     return false;
                // }
                toast("Bạn đã chọn đủ số lượng giải thưởng,Không thể chọn thêm giải thưởng.", 'error');
            })
            $(document).on('click', '.ds-checkbox-item',function(event){
                var tc_positon =  $('.t_position').val();
                var t_index = 0;
                var l_count_giaithuong = parseInt($('.l_count_giaithuong').val());
                if (this.checked){
                    l_count_giaithuong = l_count_giaithuong + 1;
                    $('.l_count_giaithuong').val(l_count_giaithuong);
                }else{
                    l_count_giaithuong = l_count_giaithuong - 1;
                    $('.l_count_giaithuong').val(l_count_giaithuong);
                }
                $(".ds-checkbox-item").each(function () {
                    if (this.checked){
                        t_index = t_index + 1;
                    }
                });
                if (parseInt(l_count_giaithuong) >= parseInt(tc_positon)){
                    $(".ds-checkbox-item").each(function () {
                        if (this.checked){
                        }else{
                            $(this).parent().addClass('ds-checkbox-item-disable');
                            $(this).next().css('background','#F3F3F7');
                            $(this).attr("disabled", true);
                        }
                    });
                }else{
                    $(".ds-checkbox-item").each(function () {
                        if (this.checked){
                        }else {
                            $(this).parent().removeClass('ds-checkbox-item-disable');
                            $(this).next().css('background','#ffffff');
                            $(this).removeAttr("disabled");
                        }
                    });
                }
                $('.so-giai-thuong').html(parseInt($('.l_count_giaithuong').val()));
                // console.log(t_index)
            })
            let test_so = wNumb({
                thousand: '.',
            });
            $('body').on('change', '#price_format',function(){
                var price_format = $(this).val();
                // price_format = price_format.replace('.', "").replace('.', "").replace('.', "");
                // let price_show = price_format.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g, '$1.');
                // price_show = price_show.split('').reverse().join('').replace(/^[\.]/, '');
                let price_show = test_so.to(test_so.from($(this).val()) * 1);
                $('#price_format').val(price_show);
                price_format = price_format.replace('.', "").replace('.', "").replace('.', "");
                var first_price = parseInt(price_format);
                var three_price = parseInt($('#price_three .params_price_sticky_3').val());
                var five_price = parseInt($('#price_five .params_price_sticky_5').val());
                var seven_price = parseInt($('#price_seven .params_price_sticky_7').val());
                var ten_price = parseInt($('#price_ten .params_price_sticky_10').val());
                var isSet = false;
                $('.error-text-c-1').html('');
                $('#price_format').css('margin-bottom','0px');
                $('#price_format').css('border-color','#e4e6ef');
                $('#price_fisrt .price').val(price_format);
            });
            $('body').on('change', '#price_sticky_3_format',function(){
                var price_format = $(this).val();
                let price_show = test_so.to(test_so.from($(this).val()) * 1);
                $('#price_sticky_3_format').val(price_show);
                price_format = price_format.replace('.', "").replace('.', "").replace('.', "");
                var first_price = parseInt($('#price_fisrt .price').val());
                var three_price = parseInt(price_format);
                var five_price = parseInt($('#price_five .params_price_sticky_5').val());
                var seven_price = parseInt($('#price_seven .params_price_sticky_7').val());
                var ten_price = parseInt($('#price_ten .params_price_sticky_10').val());
                $('.error-text-c-3').html('');
                $('#price_sticky_3_format').css('margin-bottom','0px');
                $('#price_sticky_3_format').css('border-color','#e4e6ef');
                if (!first_price){
                    $('#price_format').css('margin-bottom','4px');
                    $('#price_format').css('border-color','#F14B5E');
                    $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                    $('#price_sticky_3_format').val('');
                    return;
                }else{
                    if (three_price && three_price <= first_price){
                        $('#price_sticky_3_format').css('margin-bottom','4px');
                        $('#price_sticky_3_format').css('border-color','#F14B5E');
                        $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi lớn hơn phí 1 lần chơi.');
                    }else{
                        $('#price_sticky_3_format').css('margin-bottom','0px');
                        $('#price_sticky_3_format').css('border-color','#e4e6ef');
                        $('.error-text-c-3').html('');
                    }
                }
                $('#price_three .params_price_sticky_3').val(price_format);
            });

            $('body').on('change', '#price_sticky_5_format',function(){
                var price_format = $(this).val();
                let price_show = test_so.to(test_so.from($(this).val()) * 1);
                $('#price_sticky_5_format').val(price_show);
                price_format = price_format.replace('.', "").replace('.', "").replace('.', "");
                var first_price = parseInt($('#price_fisrt .price').val());
                var three_price = parseInt($('#price_three .params_price_sticky_3').val());
                var five_price = parseInt(price_format);
                var seven_price = parseInt($('#price_seven .params_price_sticky_7').val());
                var ten_price = parseInt($('#price_ten .params_price_sticky_10').val());
                $('.error-text-c-5').html('');
                $('#price_sticky_5_format').css('margin-bottom','0px');
                $('#price_sticky_5_format').css('border-color','#e4e6ef');
                var isSet = false;
                if (!first_price){
                    if (!three_price){
                        $('#price_format').css('margin-bottom','4px');
                        $('#price_format').css('border-color','#F14B5E');
                        $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                        $('#price_sticky_3_format').css('margin-bottom','4px');
                        $('#price_sticky_3_format').css('border-color','#F14B5E');
                        $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                        $('#price_sticky_5_format').val('');
                        return;
                    }else{
                        $('#price_format').css('margin-bottom','4px');
                        $('#price_format').css('border-color','#F14B5E');
                        $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                        $('#price_sticky_5_format').val('');
                        return;
                    }
                }else{
                    if (!three_price){
                        $('#price_sticky_3_format').css('margin-bottom','4px');
                        $('#price_sticky_3_format').css('border-color','#F14B5E');
                        $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                        $('#price_sticky_5_format').val('');
                        return;
                    }else{
                        if (parseInt(five_price) <= parseInt(three_price)){
                            $('#price_sticky_5_format').css('margin-bottom','4px');
                            $('#price_sticky_5_format').css('border-color','#F14B5E');
                            $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi lớn hơn 3 lần chơi.');
                        }else{
                            $('#price_sticky_5_format').css('margin-bottom','0px');
                            $('#price_sticky_5_format').css('border-color','#e4e6ef');
                            $('.error-text-c-5').html('');
                        }
                    }
                }
                $('#price_five .params_price_sticky_5').val(price_format);
            });
            $('body').on('change', '#price_sticky_7_format',function(){
                var price_format = $(this).val();
                let price_show = test_so.to(test_so.from($(this).val()) * 1);
                $('#price_sticky_7_format').val(price_show);
                price_format = price_format.replace('.', "").replace('.', "").replace('.', "");
                var first_price = $('#price_fisrt .price').val();
                var three_price = $('#price_three .params_price_sticky_3').val();
                var five_price = $('#price_five .params_price_sticky_5').val();
                var seven_price = price_format;
                var ten_price = $('#price_ten .params_price_sticky_10').val();
                $('.error-text-c-7').html('');
                $('#price_sticky_7_format').css('margin-bottom','0px');
                $('#price_sticky_7_format').css('border-color','#e4e6ef');
                var isSet = false;
                if (!first_price){
                    if (!three_price){
                        if (!five_price){
                            $('#price_format').css('margin-bottom','4px');
                            $('#price_format').css('border-color','#F14B5E');
                            $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                            $('#price_sticky_3_format').css('margin-bottom','4px');
                            $('#price_sticky_3_format').css('border-color','#F14B5E');
                            $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                            $('#price_sticky_5_format').css('margin-bottom','4px');
                            $('#price_sticky_5_format').css('border-color','#F14B5E');
                            $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                            $('#price_sticky_7_format').val('');
                            return;
                        }else {
                            $('#price_format').css('margin-bottom','4px');
                            $('#price_format').css('border-color','#F14B5E');
                            $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                            $('#price_sticky_3_format').css('margin-bottom','4px');
                            $('#price_sticky_3_format').css('border-color','#F14B5E');
                            $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                            $('#price_sticky_7_format').val('');
                            return;
                        }
                    }else{
                        if (!five_price){
                            $('#price_format').css('margin-bottom','4px');
                            $('#price_format').css('border-color','#F14B5E');
                            $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                            $('#price_sticky_5_format').css('margin-bottom','4px');
                            $('#price_sticky_5_format').css('border-color','#F14B5E');
                            $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                            $('#price_sticky_7_format').val('');
                            return;
                        }else {
                            $('#price_format').css('margin-bottom','4px');
                            $('#price_format').css('border-color','#F14B5E');
                            $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                            $('#price_sticky_7_format').val('');
                            return;
                        }
                    }
                }else {
                    if (!three_price){
                        if (!five_price){
                            $('#price_sticky_3_format').css('margin-bottom','4px');
                            $('#price_sticky_3_format').css('border-color','#F14B5E');
                            $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                            $('#price_sticky_5_format').css('margin-bottom','4px');
                            $('#price_sticky_5_format').css('border-color','#F14B5E');
                            $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                            $('#price_sticky_7_format').val('');
                            return;
                        }else {
                            $('#price_sticky_3_format').css('margin-bottom','4px');
                            $('#price_sticky_3_format').css('border-color','#F14B5E');
                            $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                            $('#price_sticky_7_format').val('');
                            return;
                        }
                    }else{
                        if (!five_price){
                            $('#price_sticky_5_format').css('margin-bottom','4px');
                            $('#price_sticky_5_format').css('border-color','#F14B5E');
                            $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                            $('#price_sticky_7_format').val('');
                            return;
                        }else {
                            if (parseInt(seven_price) <= parseInt(five_price)){
                                $('#price_sticky_7_format').css('margin-bottom','4px');
                                $('#price_sticky_7_format').css('border-color','#F14B5E');
                                $('.error-text-c-7').html('Vui lòng nhập Phí 7 lần chơi lớn hơn 5 lần chơi.');
                            }else{
                                $('#price_sticky_7_format').css('margin-bottom','0px');
                                $('#price_sticky_7_format').css('border-color','#e4e6ef');
                                $('.error-text-c-7').html('');
                            }
                        }
                    }
                }
                $('#price_seven .params_price_sticky_7').val(price_format);
            });
            $('body').on('change', '#price_sticky_10_format',function(){
                var price_format = $(this).val();
                let price_show = test_so.to(test_so.from($(this).val()) * 1);
                $('#price_sticky_10_format').val(price_show);
                price_format = price_format.replace('.', "").replace('.', "").replace('.', "");
                var first_price = $('#price_fisrt .price').val();
                var three_price = $('#price_three .params_price_sticky_3').val();
                var five_price = $('#price_five .params_price_sticky_5').val();
                var seven_price = $('#price_seven .params_price_sticky_7').val();
                var ten_price = price_format;
                var isSet = false;
                $('.error-text-c-10').html('');
                $('#price_sticky_10_format').css('margin-bottom','0px');
                $('#price_sticky_10_format').css('border-color','#e4e6ef');
                if (!first_price){
                    if (!three_price){
                        if (!five_price){
                            if (!seven_price){
                                $('#price_format').css('margin-bottom','4px');
                                $('#price_format').css('border-color','#F14B5E');
                                $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                                $('#price_sticky_3_format').css('margin-bottom','4px');
                                $('#price_sticky_3_format').css('border-color','#F14B5E');
                                $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                                $('#price_sticky_5_format').css('margin-bottom','4px');
                                $('#price_sticky_5_format').css('border-color','#F14B5E');
                                $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                                $('#price_sticky_7_format').css('margin-bottom','4px');
                                $('#price_sticky_7_format').css('border-color','#F14B5E');
                                $('.error-text-c-7').html('Vui lòng nhập Phí 7 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }else{
                                $('#price_format').css('margin-bottom','4px');
                                $('#price_format').css('border-color','#F14B5E');
                                $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                                $('#price_sticky_3_format').css('margin-bottom','4px');
                                $('#price_sticky_3_format').css('border-color','#F14B5E');
                                $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                                $('#price_sticky_5_format').css('margin-bottom','4px');
                                $('#price_sticky_5_format').css('border-color','#F14B5E');
                                $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }
                        }else{
                            if (!seven_price){
                                $('#price_format').css('margin-bottom','4px');
                                $('#price_format').css('border-color','#F14B5E');
                                $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                                $('#price_sticky_3_format').css('margin-bottom','4px');
                                $('#price_sticky_3_format').css('border-color','#F14B5E');
                                $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                                $('#price_sticky_7_format').css('margin-bottom','4px');
                                $('#price_sticky_7_format').css('border-color','#F14B5E');
                                $('.error-text-c-7').html('Vui lòng nhập Phí 7 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }else{
                                $('#price_format').css('margin-bottom','4px');
                                $('#price_format').css('border-color','#F14B5E');
                                $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                                $('#price_sticky_3_format').css('margin-bottom','4px');
                                $('#price_sticky_3_format').css('border-color','#F14B5E');
                                $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }
                        }
                    }else{
                        if (!five_price){
                            if (!seven_price){
                                $('#price_format').css('margin-bottom','4px');
                                $('#price_format').css('border-color','#F14B5E');
                                $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                                $('#price_sticky_5_format').css('margin-bottom','4px');
                                $('#price_sticky_5_format').css('border-color','#F14B5E');
                                $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                                $('#price_sticky_7_format').css('margin-bottom','4px');
                                $('#price_sticky_7_format').css('border-color','#F14B5E');
                                $('.error-text-c-7').html('Vui lòng nhập Phí 7 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }else{
                                $('#price_format').css('margin-bottom','4px');
                                $('#price_format').css('border-color','#F14B5E');
                                $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                                $('#price_sticky_5_format').css('margin-bottom','4px');
                                $('#price_sticky_5_format').css('border-color','#F14B5E');
                                $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }
                        }else{
                            if (!seven_price){
                                $('#price_format').css('margin-bottom','4px');
                                $('#price_format').css('border-color','#F14B5E');
                                $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                                $('#price_sticky_7_format').css('margin-bottom','4px');
                                $('#price_sticky_7_format').css('border-color','#F14B5E');
                                $('.error-text-c-7').html('Vui lòng nhập Phí 7 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }else{
                                $('#price_format').css('margin-bottom','4px');
                                $('#price_format').css('border-color','#F14B5E');
                                $('.error-text-c-1').html('Vui lòng nhập Phí 1 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }
                        }
                    }
                }else{
                    if (!three_price){
                        if (!five_price){
                            if (!seven_price){
                                $('#price_sticky_3_format').css('margin-bottom','4px');
                                $('#price_sticky_3_format').css('border-color','#F14B5E');
                                $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                                $('#price_sticky_5_format').css('margin-bottom','4px');
                                $('#price_sticky_5_format').css('border-color','#F14B5E');
                                $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                                $('#price_sticky_7_format').css('margin-bottom','4px');
                                $('#price_sticky_7_format').css('border-color','#F14B5E');
                                $('.error-text-c-7').html('Vui lòng nhập Phí 7 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }else{
                                $('#price_sticky_3_format').css('margin-bottom','4px');
                                $('#price_sticky_3_format').css('border-color','#F14B5E');
                                $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                                $('#price_sticky_5_format').css('margin-bottom','4px');
                                $('#price_sticky_5_format').css('border-color','#F14B5E');
                                $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }
                        }else{
                            if (!seven_price){
                                $('#price_sticky_3_format').css('margin-bottom','4px');
                                $('#price_sticky_3_format').css('border-color','#F14B5E');
                                $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                                $('#price_sticky_7_format').css('margin-bottom','4px');
                                $('#price_sticky_7_format').css('border-color','#F14B5E');
                                $('.error-text-c-7').html('Vui lòng nhập Phí 7 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }else{
                                $('#price_sticky_3_format').css('margin-bottom','4px');
                                $('#price_sticky_3_format').css('border-color','#F14B5E');
                                $('.error-text-c-3').html('Vui lòng nhập Phí 3 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }
                        }
                    }else{
                        if (!five_price){
                            if (!seven_price){
                                $('#price_sticky_5_format').css('margin-bottom','4px');
                                $('#price_sticky_5_format').css('border-color','#F14B5E');
                                $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                                $('#price_sticky_7_format').css('margin-bottom','4px');
                                $('#price_sticky_7_format').css('border-color','#F14B5E');
                                $('.error-text-c-7').html('Vui lòng nhập Phí 7 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }else{
                                $('#price_sticky_5_format').css('margin-bottom','4px');
                                $('#price_sticky_5_format').css('border-color','#F14B5E');
                                $('.error-text-c-5').html('Vui lòng nhập Phí 5 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }
                        }else{
                            if (!seven_price){
                                $('#price_sticky_7_format').css('margin-bottom','4px');
                                $('#price_sticky_7_format').css('border-color','#F14B5E');
                                $('.error-text-c-7').html('Vui lòng nhập Phí 7 lần chơi.');
                                $('#price_sticky_10_format').val('');
                                return;
                            }else{
                                if (parseInt(ten_price) <= parseInt(seven_price)){
                                    $('#price_sticky_10_format').css('margin-bottom','4px');
                                    $('#price_sticky_10_format').css('border-color','#F14B5E');
                                    $('.error-text-c-10').html('Vui lòng nhập Phí 10 lần chơi lớn hơn 7 lần chơi.');
                                }else{
                                    $('#price_sticky_10_format').css('margin-bottom','0px');
                                    $('#price_sticky_10_format').css('border-color','#e4e6ef');
                                    $('.error-text-c-10').html('');
                                }
                            }
                        }
                    }
                }
                $('#price_ten .params_price_sticky_10').val(price_format)
            });
        })
    </script>


    @if(isset($data_shop_str))
        <input type="hidden" id="id_shop_pp" value="{{$data_shop_str??''}}">
        <script>
            $(document).ready(function () {
                var data_shop_str = $('#id_shop_pp').val();
                if (data_shop_str){
                    data_shop_str = data_shop_str.split('|');
                    var sw_arr = [];
                    $('#select-client').find('option').each(function () {
                        var is_sw = true;
                        var sw_id = $(this).val();
                        $.each(data_shop_str,function(key62,value62){
                            if (parseInt(value62) == parseInt(sw_id)) {
                                is_sw = false;
                            }
                        });
                        if (is_sw){
                            sw_arr.push(parseInt($(this).val()));
                        }
                    })
                    $.each(sw_arr,function(key63,value63){
                        $('#select-client').find('option').each(function () {
                            if (parseInt(value63) == parseInt($(this).val())) {
                                $(this).remove();
                            }
                            if ($(this).val() == 0 || $(this).val() == null || $(this).val() == '' || $(this).val() == undefined){
                                $(this).remove();
                            }
                        })
                    })
                }
            })
        </script>
    @endif
    @if(isset($shop_group))
        <script>
            $(document).ready(function () {
                $('body').on('change','#kt_datatable_addwebits_created .checkbox-item',function(){
                    var itemselect = '';
                    $('#kt_datatable_addwebits_created .checkbox-item input[type="checkbox"]:checked').each(function (index, elem)  {
                        let id = parseInt($(this).data('id'));
                        if (itemselect != '') {
                            itemselect += '|';
                        }
                        itemselect += id;
                    })
                    $('#dsShopCreated .data_shop').val('');
                    $('#dsShopCreated .data_shop').val(itemselect);
                });
                $('body').on('change','#btnCheckAllGroupCreated',function(){
                    var itemselect = '';
                    var d_shop = {!! $shop_group !!};
                    $('#table-group-shop-created .checkbox-item input[type="checkbox"]:checked').each(function (index, elem)  {
                        let id = parseInt($(this).data('id'));
                        $.each(d_shop,function(key3,value3){
                            if (value3.id == id) {
                                $.each(value3.shop,function(key4,value4){
                                    if (itemselect != '') {
                                        itemselect += '|';
                                    }
                                    itemselect += value4.id;
                                })
                            }
                        })
                    });
                    $('#dsNhomShopCreated .data_shop').val('');
                    $('#dsNhomShopCreated .data_shop').val(itemselect);
                });
                $('body').on('change','#table-group-shop-created .checkbox-item',function(){
                    var d_shop = {!! $shop_group !!};
                    var itemselect = '';
                    $('#table-group-shop-created .checkbox-item input[type="checkbox"]:checked').each(function (index, elem)  {
                        let id = parseInt($(this).data('id'));
                        $.each(d_shop,function(key5,value5){
                            if (value5.id == id) {
                                $.each(value5.shop,function(key6,value6){
                                    if (itemselect != '') {
                                        itemselect += '|';
                                    }
                                    itemselect += value6.id;
                                })
                            }
                        })
                    });
                    $('#dsNhomShopCreated .data_shop').val('');
                    $('#dsNhomShopCreated .data_shop').val(itemselect);
                });
                $('body').on('click','#dsShopCreated .add-shop-created',function(e){
                    e.preventDefault();
                    var d_shop = {!! $shop_group !!};
                    var data_shop = $('#dsShopCreated .data_shop').val();
                    var data_group_shop = $('#dsNhomShopCreated .data_shop').val();
                    $('.data_add_shop').html('');
                    if (data_group_shop){
                        var arr_group_shop = data_group_shop.split("|");
                        if (data_shop){
                            var arr_shop = data_shop.split("|");
                            $.each(arr_group_shop,function(key2,value2){
                                if (arr_shop.indexOf(value2) > -1) {} else {
                                    data_shop += '|';
                                    data_shop += value2;
                                }
                            });
                            // console.log(data_shop);
                            array_item = data_shop.split("|");
                            $.each(d_shop,function(key,value){
                                var isCheck = false;
                                let htmladd = '';
                                var val = 0;
                                htmladd += '<tr class="oddCreated" style="background: #f3f6f9;border-radius: 4px">';
                                htmladd += '<td class="ckb_item dtr-control"><label class="checkbox checkbox-lg checkbox-outline checkbox-item checkbox-item-created" data-id="' + value.id + '"><input class="group-created-checkbox" data-id="' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                                htmladd += '<td class="sorting_1"><span style="font-size: 14px;font-weight: 700">Nhóm shop</span></td>';
                                htmladd += '<td><span style="font-size: 14px;font-weight: 700">' + value.title + '</span><small class="count_shop_created count_shop_created_' + value.id + '">1</small></td>';
                                htmladd += '<td></td>';
                                htmladd += '<td><a class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger btn-delete-group-created" data-id="' + value.id + '" title="Xóa"><i class="la la-trash"></i></a></td>';
                                htmladd += '</tr>';
                                $.each(value.shop,function(keyitem,valueitem){
                                    if (valueitem){
                                        for( let i = 0 ; i < array_item.length; i++){
                                            if (valueitem.id == array_item[i]){
                                                isCheck = true;
                                                ++val;
                                                htmladd += '<tr class="groupCreated" style="background: #ffffff">';
                                                htmladd += '<td style="background: #ffffff"><label class="checkbox checkbox-lg checkbox-outline checkbox-item" data-shop="' + valueitem.id + '"><input data-id="' + valueitem.id + '" class="item-created-checkbox item-created-checkbox-' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                                                htmladd += '<td style="background: #ffffff">' + valueitem.id + '</td>';
                                                htmladd += '<td style="background: #ffffff">' + valueitem.domain + '</td>';
                                                htmladd += '<td style="background: #ffffff"></td>';
                                                htmladd += '<td style="background: #ffffff"><a data-id="22" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle delete-create-shop" data-shop="' + valueitem.id + '"><i class="la la-trash"></i></a></td>';
                                                htmladd += '</tr>';
                                            }
                                        }
                                    }
                                })
                                if (isCheck){
                                    $('.data_add_shop').append(htmladd);
                                    $('.count_shop_created_' + value.id + '').html(val);
                                }
                            });
                            $('.array_shop_id').val('');
                            $('.array_shop_id').val(data_shop);
                        }else {
                            var array_item = arr_group_shop;
                            $.each(d_shop,function(key,value){
                                var isCheck = false;
                                let htmladd = '';
                                var val = 0;
                                htmladd += '<tr class="oddCreated" style="background: #f3f6f9;border-radius: 4px">';
                                htmladd += '<td class="ckb_item dtr-control"><label class="checkbox checkbox-lg checkbox-outline checkbox-item checkbox-item-created" data-id="' + value.id + '"><input class="group-created-checkbox" data-id="' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                                htmladd += '<td class="sorting_1"><span style="font-size: 14px;font-weight: 700">Nhóm shop</span></td>';
                                htmladd += '<td><span style="font-size: 14px;font-weight: 700">' + value.title + '</span><small class="count_shop_created count_shop_created_' + value.id + '">1</small></td>';
                                htmladd += '<td></td>';
                                htmladd += '<td><a class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger btn-delete-group-created" data-id="' + value.id + '" title="Xóa"><i class="la la-trash"></i></a></td>';
                                htmladd += '</tr>';
                                $.each(value.shop,function(keyitem,valueitem){
                                    if (valueitem){
                                        for( let i = 0 ; i < array_item.length; i++){
                                            if (valueitem.id == array_item[i]){
                                                isCheck = true;
                                                ++val;
                                                htmladd += '<tr class="groupCreated" style="background: #ffffff">';
                                                htmladd += '<td style="background: #ffffff"><label class="checkbox checkbox-lg checkbox-outline checkbox-item" data-shop="' + valueitem.id + '"><input data-id="' + valueitem.id + '" class="item-created-checkbox item-created-checkbox-' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                                                htmladd += '<td style="background: #ffffff">' + valueitem.id + '</td>';
                                                htmladd += '<td style="background: #ffffff">' + valueitem.domain + '</td>';
                                                htmladd += '<td style="background: #ffffff"></td>';
                                                htmladd += '<td style="background: #ffffff"><a data-id="22" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle delete-create-shop" data-shop="' + valueitem.id + '"><i class="la la-trash"></i></a></td>';
                                                htmladd += '</tr>';
                                            }
                                        }
                                    }
                                })
                                if (isCheck){
                                    $('.data_add_shop').append(htmladd);
                                    $('.count_shop_created_' + value.id + '').html(val);
                                }
                            });
                            $('.array_shop_id').val('');
                            $('.array_shop_id').val(data_group_shop);
                        }
                    }else {
                        if (data_shop == null || data_shop == '' || data_shop == undefined){
                            var htmlr = '<tr class="text-center"><td colspan="7">Chưa phân phối đến web nào</td></tr>';
                            $('.data_add_shop').html(htmlr);
                            return false;
                        }
                        var array_item = data_shop.split("|");
                        $.each(d_shop,function(key,value){
                            var isCheck = false;
                            let htmladd = '';
                            var val = 0;
                            htmladd += '<tr class="oddCreated" style="background: #f3f6f9;border-radius: 4px">';
                            htmladd += '<td class="ckb_item dtr-control"><label class="checkbox checkbox-lg checkbox-outline checkbox-item checkbox-item-created" data-id="' + value.id + '"><input class="group-created-checkbox" data-id="' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                            htmladd += '<td class="sorting_1"><span style="font-size: 14px;font-weight: 700">Nhóm shop</span></td>';
                            htmladd += '<td><span style="font-size: 14px;font-weight: 700">' + value.title + '</span><small class="count_shop_created count_shop_created_' + value.id + '">1</small></td>';
                            htmladd += '<td></td>';
                            htmladd += '<td><a class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger btn-delete-group-created" data-id="' + value.id + '" title="Xóa"><i class="la la-trash"></i></a></td>';
                            htmladd += '</tr>';
                            $.each(value.shop,function(keyitem,valueitem){
                                if (valueitem){
                                    for( let i = 0 ; i < array_item.length; i++){
                                        if (valueitem.id == array_item[i]){
                                            isCheck = true;
                                            ++val;
                                            htmladd += '<tr class="groupCreated" style="background: #ffffff">';
                                            htmladd += '<td style="background: #ffffff"><label class="checkbox checkbox-lg checkbox-outline checkbox-item" data-shop="' + valueitem.id + '"><input data-id="' + valueitem.id + '" class="item-created-checkbox item-created-checkbox-' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                                            htmladd += '<td style="background: #ffffff">' + valueitem.id + '</td>';
                                            htmladd += '<td style="background: #ffffff">' + valueitem.domain + '</td>';
                                            htmladd += '<td style="background: #ffffff"></td>';
                                            htmladd += '<td style="background: #ffffff"><a data-id="22" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle delete-create-shop" data-shop="' + valueitem.id + '"><i class="la la-trash"></i></a></td>';
                                            htmladd += '</tr>';
                                        }
                                    }
                                }
                            })
                            if (isCheck){
                                $('.data_add_shop').append(htmladd);
                                $('.count_shop_created_' + value.id + '').html(val);
                            }
                        });
                        $('.array_shop_id').val('');
                        $('.array_shop_id').val(data_shop);
                    }
                    $('#dsShopCreated').modal('hide');
                });
                $('body').on('click','#dsNhomShopCreated .add-group-shop-created',function(e){
                    e.preventDefault();
                    var d_shop = {!! $shop_group !!};
                    var data_shop = $('#dsShopCreated .data_shop').val();
                    var data_group_shop = $('#dsNhomShopCreated .data_shop').val();
                    $('.data_add_shop').html('');
                    if (data_shop){
                        var arr_shop = data_shop.split("|");
                        if (data_group_shop){
                            var arr_group_shop = data_group_shop.split("|");
                            $.each(arr_shop,function(key2,value2){
                                if (arr_group_shop.indexOf(value2) > -1) {} else {
                                    data_group_shop += '|';
                                    data_group_shop += value2;
                                }
                            });
                            // console.log(data_shop);
                            array_item = data_group_shop.split("|");
                            $.each(d_shop,function(key,value){
                                var isCheck = false;
                                let htmladd = '';
                                var val = 0;
                                htmladd += '<tr class="oddCreated" style="background: #f3f6f9;border-radius: 4px">';
                                htmladd += '<td class="ckb_item dtr-control"><label class="checkbox checkbox-lg checkbox-outline checkbox-item checkbox-item-created" data-id="' + value.id + '"><input class="group-created-checkbox" data-id="' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                                htmladd += '<td class="sorting_1"><span style="font-size: 14px;font-weight: 700">Nhóm shop</span></td>';
                                htmladd += '<td><span style="font-size: 14px;font-weight: 700">' + value.title + '</span><small class="count_shop_created count_shop_created_' + value.id + '">1</small></td>';
                                htmladd += '<td></td>';
                                htmladd += '<td><a class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger btn-delete-group-created" data-id="' + value.id + '" title="Xóa"><i class="la la-trash"></i></a></td>';
                                htmladd += '</tr>';
                                $.each(value.shop,function(keyitem,valueitem){
                                    if (valueitem){
                                        for( let i = 0 ; i < array_item.length; i++){
                                            if (valueitem.id == array_item[i]){
                                                isCheck = true;
                                                ++val;
                                                htmladd += '<tr class="groupCreated" style="background: #ffffff">';
                                                htmladd += '<td style="background: #ffffff"><label class="checkbox checkbox-lg checkbox-outline checkbox-item" data-shop="' + valueitem.id + '"><input data-id="' + valueitem.id + '" class="item-created-checkbox item-created-checkbox-' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                                                htmladd += '<td style="background: #ffffff">' + valueitem.id + '</td>';
                                                htmladd += '<td style="background: #ffffff">' + valueitem.domain + '</td>';
                                                htmladd += '<td style="background: #ffffff"></td>';
                                                htmladd += '<td style="background: #ffffff"><a data-id="22" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle delete-create-shop" data-shop="' + valueitem.id + '"><i class="la la-trash"></i></a></td>';
                                                htmladd += '</tr>';
                                            }
                                        }
                                    }
                                })
                                if (isCheck){
                                    $('.data_add_shop').append(htmladd);
                                    $('.count_shop_created_' + value.id + '').html(val);
                                }
                            });
                            $('.array_shop_id').val('');
                            $('.array_shop_id').val(data_group_shop);
                        }else {
                            var array_item = arr_shop;
                            $.each(d_shop,function(key,value){
                                var isCheck = false;
                                let htmladd = '';
                                var val = 0;
                                htmladd += '<tr class="oddCreated" style="background: #f3f6f9;border-radius: 4px">';
                                htmladd += '<td class="ckb_item dtr-control"><label class="checkbox checkbox-lg checkbox-outline checkbox-item checkbox-item-created" data-id="' + value.id + '"><input class="group-created-checkbox" data-id="' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                                htmladd += '<td class="sorting_1"><span style="font-size: 14px;font-weight: 700">Nhóm shop</span></td>';
                                htmladd += '<td><span style="font-size: 14px;font-weight: 700">' + value.title + '</span><small class="count_shop_created count_shop_created_' + value.id + '">1</small></td>';
                                htmladd += '<td></td>';
                                htmladd += '<td><a class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger btn-delete-group-created" data-id="' + value.id + '" title="Xóa"><i class="la la-trash"></i></a></td>';
                                htmladd += '</tr>';
                                $.each(value.shop,function(keyitem,valueitem){
                                    if (valueitem){
                                        for( let i = 0 ; i < array_item.length; i++){
                                            if (valueitem.id == array_item[i]){
                                                isCheck = true;
                                                ++val;
                                                htmladd += '<tr class="groupCreated" style="background: #ffffff">';
                                                htmladd += '<td style="background: #ffffff"><label class="checkbox checkbox-lg checkbox-outline checkbox-item" data-shop="' + valueitem.id + '"><input data-id="' + valueitem.id + '" class="item-created-checkbox item-created-checkbox-' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                                                htmladd += '<td style="background: #ffffff">' + valueitem.id + '</td>';
                                                htmladd += '<td style="background: #ffffff">' + valueitem.domain + '</td>';
                                                htmladd += '<td style="background: #ffffff"></td>';
                                                htmladd += '<td style="background: #ffffff"><a data-id="22" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle delete-create-shop" data-shop="' + valueitem.id + '"><i class="la la-trash"></i></a></td>';
                                                htmladd += '</tr>';
                                            }
                                        }
                                    }
                                })
                                if (isCheck){
                                    $('.data_add_shop').append(htmladd);
                                    $('.count_shop_created_' + value.id + '').html(val);
                                }
                            });
                            $('.array_shop_id').val('');
                            $('.array_shop_id').val(data_shop);
                        }
                    }else {
                        if (data_group_shop == null || data_group_shop == '' || data_group_shop == undefined){
                            var htmlr = '<tr class="text-center"><td colspan="7">Chưa phân phối đến web nào</td></tr>';
                            $('.data_add_shop').html(htmlr);
                            return false;
                        }
                        var array_item = data_group_shop.split("|");
                        $.each(d_shop,function(key,value){
                            var isCheck = false;
                            let htmladd = '';
                            var val = 0;
                            htmladd += '<tr class="oddCreated" style="background: #f3f6f9;border-radius: 4px">';
                            htmladd += '<td class="ckb_item dtr-control"><label class="checkbox checkbox-lg checkbox-outline checkbox-item checkbox-item-created" data-id="' + value.id + '"><input class="group-created-checkbox" data-id="' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                            htmladd += '<td class="sorting_1"><span style="font-size: 14px;font-weight: 700">Nhóm shop</span></td>';
                            htmladd += '<td><span style="font-size: 14px;font-weight: 700">' + value.title + '</span><small class="count_shop_created count_shop_created_' + value.id + '">1</small></td>';
                            htmladd += '<td></td>';
                            htmladd += '<td><a class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger btn-delete-group-created" data-id="' + value.id + '" title="Xóa"><i class="la la-trash"></i></a></td>';
                            htmladd += '</tr>';
                            $.each(value.shop,function(keyitem,valueitem){
                                if (valueitem){
                                    for( let i = 0 ; i < array_item.length; i++){
                                        if (valueitem.id == array_item[i]){
                                            isCheck = true;
                                            ++val;
                                            htmladd += '<tr class="groupCreated" style="background: #ffffff">';
                                            htmladd += '<td style="background: #ffffff"><label class="checkbox checkbox-lg checkbox-outline checkbox-item" data-shop="' + valueitem.id + '"><input data-id="' + valueitem.id + '" class="item-created-checkbox item-created-checkbox-' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                                            htmladd += '<td style="background: #ffffff">' + valueitem.id + '</td>';
                                            htmladd += '<td style="background: #ffffff">' + valueitem.domain + '</td>';
                                            htmladd += '<td style="background: #ffffff"></td>';
                                            htmladd += '<td style="background: #ffffff"><a data-id="22" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle delete-create-shop" data-shop="' + valueitem.id + '"><i class="la la-trash"></i></a></td>';
                                            htmladd += '</tr>';
                                        }
                                    }
                                }
                            })
                            if (isCheck){
                                $('.data_add_shop').append(htmladd);
                                $('.count_shop_created_' + value.id + '').html(val);
                            }
                        });
                        $('.array_shop_id').val('');
                        $('.array_shop_id').val(data_group_shop);
                    }
                    $('#dsShopCreated').modal('hide');
                });
                $('#btnCheckAllGroupCreated').click(function(){
                    let checks = this.checked;
                    if (checks){
                        $('#table-group-shop-created .item-checkbox').each(function(){ this.checked = true; });
                    }else {
                        $('#table-group-shop-created .item-checkbox').each(function(){ this.checked = false; });
                    }
                });
                $('#btnCheckAllppCreated').click(function(){
                    let checks = this.checked;
                    if (checks){
                        $('#kt_datatable_created .group-created-checkbox').each(function(){ this.checked = true; });
                        $('#kt_datatable_created .item-created-checkbox').each(function(){ this.checked = true; });
                    }else {
                        $('#kt_datatable_created .group-created-checkbox').each(function(){ this.checked = false; });
                        $('#kt_datatable_created .item-created-checkbox').each(function(){ this.checked = false; });
                    }
                    var itemselect = '';
                    $('#kt_datatable_created .item-created-checkbox').each(function(){
                        if (this.checked){
                            let id = parseInt($(this).data('id'));
                            if (itemselect != '') {
                                itemselect += '|';
                            }
                            itemselect += id;
                        }
                    });
                    $('#deleteShopCreated .delete_created').val('');
                    $('#deleteShopCreated .delete_created').val(itemselect);
                });
                $('body').on('change','#kt_datatable_created .item-created-checkbox',function(){
                    var itemselect = '';
                    $('#kt_datatable_created .item-created-checkbox').each(function(){
                        if (this.checked){
                            let id = parseInt($(this).data('id'));
                            if (itemselect != '') {
                                itemselect += '|';
                            }
                            itemselect += id;
                        }
                    });
                    $('#deleteShopCreated .delete_created').val('');
                    $('#deleteShopCreated .delete_created').val(itemselect);
                });
                $('body').on('change','#kt_datatable_created .group-created-checkbox',function(){
                    var id = $(this).data('id');
                    let checks = this.checked;
                    if (checks){
                        $('#kt_datatable_created .item-created-checkbox-' + id + '').each(function(){ this.checked = true; });
                    }else {
                        $('#kt_datatable_created .item-created-checkbox-' + id + '').each(function(){ this.checked = false; });
                    }
                    var itemselect = '';
                    $('#kt_datatable_created .item-created-checkbox').each(function(){
                        if (this.checked){
                            let id = parseInt($(this).data('id'));
                            if (itemselect != '') {
                                itemselect += '|';
                            }
                            itemselect += id;
                        }
                    });
                    $('#deleteShopCreated .delete_created').val('');
                    $('#deleteShopCreated .delete_created').val(itemselect);
                });
                $('body').on('click','.delete-create-shop',function(e){
                    e.preventDefault();
                    var id = $(this).data('shop');
                    $('#deleteShopCreated .delete_created').val('');
                    $('#deleteShopCreated .delete_created').val(id);
                    $("#deleteShopCreated").modal('show');
                });
                $('body').on('click','.btn-delete-group-created',function(e){
                    e.preventDefault();
                    var id = $(this).data('id');
                    var itemselect = '';
                    $('#kt_datatable_created .item-created-checkbox-' + id + '').each(function(){
                        var c_id = $(this).data('id');
                        if (itemselect != '') {
                            itemselect += '|';
                        }
                        itemselect += c_id;
                    });
                    $('#deleteShopCreated .delete_created').val('');
                    $('#deleteShopCreated .delete_created').val(itemselect);
                    $("#deleteShopCreated").modal('show');
                });
                $('body').on('click','#deleteShopCreated .btn_delete_created',function(e){
                    e.preventDefault();
                    var d_shop = {!! $shop_group !!};
                    var array_shop_id = $('.array_shop_id').val();
                    var delete_created = $('.delete_created').val();
                    var data_array_shop_id = array_shop_id.split("|");
                    var data_delete_created = delete_created.split("|");
                    var itemselect = '';
                    $('.data_add_shop').html('');
                    $.each(data_array_shop_id,function(key7,value7){
                        if (data_delete_created.indexOf(value7) > -1) {} else {
                            if (itemselect != '') {
                                itemselect += '|';
                            }
                            itemselect += value7;
                        }
                    });
                    var array_item = itemselect.split("|");
                    $.each(d_shop,function(key,value){
                        var isCheck = false;
                        let htmladd = '';
                        var val = 0;
                        htmladd += '<tr class="oddCreated" style="background: #f3f6f9;border-radius: 4px">';
                        htmladd += '<td class="ckb_item dtr-control"><label class="checkbox checkbox-lg checkbox-outline checkbox-item checkbox-item-created" data-id="' + value.id + '"><input class="group-created-checkbox" data-id="' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                        htmladd += '<td class="sorting_1"><span style="font-size: 14px;font-weight: 700">Nhóm shop</span></td>';
                        htmladd += '<td><span style="font-size: 14px;font-weight: 700">' + value.title + '</span><small class="count_shop_created count_shop_created_' + value.id + '">1</small></td>';
                        htmladd += '<td></td>';
                        htmladd += '<td><a class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger btn-delete-group-created" data-id="' + value.id + '" title="Xóa"><i class="la la-trash"></i></a></td>';
                        htmladd += '</tr>';
                        $.each(value.shop,function(keyitem,valueitem){
                            if (valueitem){
                                for( let i = 0 ; i < array_item.length; i++){
                                    if (valueitem.id == array_item[i]){
                                        isCheck = true;
                                        ++val;
                                        htmladd += '<tr class="groupCreated" style="background: #ffffff">';
                                        htmladd += '<td style="background: #ffffff"><label class="checkbox checkbox-lg checkbox-outline checkbox-item" data-shop="' + valueitem.id + '"><input data-id="' + valueitem.id + '" class="item-created-checkbox item-created-checkbox-' + value.id + '" type="checkbox">&nbsp;<span></span></label></td>';
                                        htmladd += '<td style="background: #ffffff">' + valueitem.id + '</td>';
                                        htmladd += '<td style="background: #ffffff">' + valueitem.domain + '</td>';
                                        htmladd += '<td style="background: #ffffff"></td>';
                                        htmladd += '<td style="background: #ffffff"><a data-id="22" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle delete-create-shop" data-shop="' + valueitem.id + '"><i class="la la-trash"></i></a></td>';
                                        htmladd += '</tr>';
                                    }
                                }
                            }
                        })
                        if (isCheck){
                            $('.data_add_shop').append(htmladd);
                            $('.count_shop_created_' + value.id + '').html(val);
                        }
                    });
                    if (itemselect == null || itemselect == '' || itemselect == undefined){
                        var htmlr = '<tr class="text-center"><td colspan="7">Chưa phân phối đến web nào</td></tr>';
                        $('.data_add_shop').html(htmlr);
                    }
                    $('.array_shop_id').val('');
                    $('.array_shop_id').val(itemselect);
                    $("#deleteShopCreated").modal('hide');
                });
            })
        </script>
    @endif
    <script>
        $(document).ready(function () {
            $('body').on('keyup change','#kt_datatable_chgt .percent',function(){
                var choi_that = 0;
                $('#kt_datatable_chgt .percent').each(function () {
                    var i_choi_that = $(this).val();
                    if (i_choi_that){
                        choi_that = parseInt(choi_that) + parseInt(i_choi_that);
                    }
                });
                if (choi_that > 100){
                    $(this).val(0);
                    return false;
                }
                $('#kt_datatable_chgt .text_choi_that').html('');
                $('#kt_datatable_chgt .text_choi_that').html(choi_that + ' %');
            });
            $('body').on('keyup change','#kt_datatable_chgt .try_percent',function(){
                var choi_thu = 0;
                $('#kt_datatable_chgt .try_percent').each(function () {
                    var i_choi_thu = $(this).val();
                    if (i_choi_thu){
                        choi_thu = parseInt(choi_thu) + parseInt(i_choi_thu);
                    }
                });
                if (choi_thu > 100){
                    $(this).val(0);
                    return false;
                }
                $('#kt_datatable_chgt .text_choi_thu').html('');
                $('#kt_datatable_chgt .text_choi_thu').html(choi_thu + ' %');
            });
            //modal danh sách shop + group shop.
            $('body').on('click','.deleteGroupShop',function(){
                let group_id = $(this).data('id');
                var arrshop = '';
                $(".checkbox-item-shop-" + group_id).each(function () {
                    let id = parseInt($(this).data('shop'));
                    if (arrshop != '') {
                        arrshop += '|';
                    }
                    arrshop += id;
                });
                $('#deleteGroupShop .delete-id').val('');
                $('#deleteGroupShop .delete-id').val(arrshop);
                $('#deleteGroupShop').modal('show');
            });
            $('body').on('click','.deleteShop',function(){
                let group_id = $(this).data('id');
                $('#deleteShop .delete-id').val('');
                $('#deleteShop .delete-id').val(group_id);
                $('#deleteShop').modal('show');
            });
            $('body').on('change','.labelCheckAllAddShop',function(){
                var itemselect = '';
                $('#kt_datatable_addwebits .ckb_item input[type="checkbox"]:checked').each(function (index, elem)  {
                    if (index > 0){
                        let id = parseInt($(this).data('id'));
                        if (itemselect != '') {
                            itemselect += '|';
                        }
                        itemselect += id;
                    }
                })
                $('#dsShop .object_shop').val('');
                $('#dsShop .object_shop').val(itemselect);
            })
            $('body').on('change','#kt_datatable_addwebits .checkbox-item',function(){
                var itemselect = '';
                $('#kt_datatable_addwebits .checkbox-item input[type="checkbox"]:checked').each(function (index, elem)  {
                    let id = parseInt($(this).data('id'));
                    if (itemselect != '') {
                        itemselect += '|';
                    }
                    itemselect += id;
                })
                $('#dsShop .object_shop').val('');
                $('#dsShop .object_shop').val(itemselect);
            });
            $('body').on('change','#btnCheckAllGroup',function(){
                var itemselect = '';
                $('#table-group-shop .checkbox-item input[type="checkbox"]:checked').each(function (index, elem)  {
                    let id = parseInt($(this).data('id'));
                    if (itemselect != '') {
                        itemselect += '|';
                    }
                    itemselect += id;
                })
                $('#dsNhomShop .object_shop').val('');
                $('#dsNhomShop .object_shop').val(itemselect);
            })
            $('body').on('change','#table-group-shop .checkbox-item',function(){
                var itemselect = '';
                $('#table-group-shop .checkbox-item input[type="checkbox"]:checked').each(function (index, elem)  {
                    let id = parseInt($(this).data('id'));
                    if (itemselect != '') {
                        itemselect += '|';
                    }
                    itemselect += id;
                })
                $('#dsNhomShop .object_shop').val('');
                $('#dsNhomShop .object_shop').val(itemselect);
            })
            //Check box danh sách shop tab phân phối
            $('body').on('change', '#btnCheckAllpp', function (e) {
                var arrshop = '';
                if (this.checked) {
                    $(".checkbox-item-shop-group-input").each(function () {
                        $(this).prop("checked", true);
                    });
                    $(".checkbox-item-shop").each(function () {
                        $(this).prop("checked", true);
                        let id = parseInt($(this).data('shop'));
                        if (arrshop != '') {
                            arrshop += '|';
                        }
                        arrshop += id;
                    });
                    $('#inActiveShop .inactive-id').val('');
                    $('#inActiveShop .inactive-id').val(arrshop);
                    $('#activeShop .active-id').val('');
                    $('#activeShop .active-id').val(arrshop);
                }else {
                    $(".checkbox-item-shop-group-input").each(function () {
                        $(this).prop("checked", false);
                    });
                    $(".checkbox-item-shop").each(function () {
                        $(this).prop("checked", false);
                    });
                    $('#inActiveShop .inactive-id').val('');
                    $('#activeShop .active-id').val('');
                }
            })
            $('body').on('change', '.checkbox-item-shop-group', function (e) {
                let group_id = $(this).data('group');
                let input_group = $(this).find('input');
                $(".checkbox-item-shop-" + group_id).each(function () {
                    if (input_group.is(':checked')) {
                        $(this).prop("checked", true);
                    }else {
                        $(this).prop("checked", false);
                    }
                });
                var arrshop = '';
                $(".checkbox-item-shop").each(function () {
                    if (this.checked){
                        let id = parseInt($(this).data('shop'));
                        if (arrshop != '') {
                            arrshop += '|';
                        }
                        arrshop += id;
                    }
                });
                $('#inActiveShop .inactive-id').val('');
                $('#inActiveShop .inactive-id').val(arrshop);
                $('#activeShop .active-id').val('');
                $('#activeShop .active-id').val(arrshop);
            })
            $('body').on('change', '.checkbox-item-shop', function (e) {
                var arrshop = '';
                $(".checkbox-item-shop").each(function () {
                    if (this.checked){
                        let id = parseInt($(this).data('shop'));
                        if (arrshop != '') {
                            arrshop += '|';
                        }
                        arrshop += id;
                    }
                });
                $('#inActiveShop .inactive-id').val('');
                $('#inActiveShop .inactive-id').val(arrshop);
                $('#activeShop .active-id').val('');
                $('#activeShop .active-id').val(arrshop);
            })
            $('body').on('change', '.switch-success-group-shop', function (e) {
                let group_shop_id = $(this).data('shopgroup');
                let checks = $(this).find('input');
                var check = checkValidate();
                if (check){}else{
                    let checkt = $(this).find('input');
                    checkt.prop("checked", false);
                    return;
                }
                if (checks.is(':checked')) {
                    $('.checkbox-itemgroupshop-' + group_shop_id + '').each(function () {
                        var g_id = $(this).data('shop');
                        var shopId = $('.cg_shop').val();
                        if (shopId){
                            if (parseInt(g_id) == parseInt(shopId)){
                                $('.c_data_status').val('Hoạt động')
                            }
                        }
                        $(this).prop("checked", true);
                    });
                }else {
                    $('.checkbox-itemgroupshop-' + group_shop_id + '').each(function () {
                        var g_id = $(this).data('shop');
                        var shopId = $('.cg_shop').val();
                        if (shopId){
                            if (parseInt(g_id) == parseInt(shopId)){
                                $('.c_data_status').val('Khóa')
                            }
                        }
                        $(this).prop("checked", false);
                    });
                }
                var shopgroup = $(this).data('shopgroup');
                activeshop('group', shopgroup, '', checks.is(':checked'));
            })
            $('body').on('change', '.switch-success-group-shop-replication', function (e) {
                let nameshop;
                $(".switch-success-shop-replication").each(function () {
                    nameshop = $(this).data('name');
                });
                var check = checkValidate();
                if (check){}else{
                    let checkt = $(this).find('input');
                    checkt.prop("checked", false);
                    return;
                }
                if (confirm('' + nameshop + ' đang phân bổ minigame gốc, Bạn có muốn kích hoạt thêm minigame mới không?')) {}else {
                    let checkc = $(this).find('input');
                    checkc.prop("checked", false);
                    return false;
                }
                let group_shop_id = $(this).data('shopgroup');
                let checks = $(this).find('input');
                if (checks.is(':checked')) {
                    $('.checkbox-itemgroupshop-' + group_shop_id + '').each(function () {
                        var g_id = $(this).data('shop');
                        var shopId = $('.cg_shop').val();
                        if (shopId){
                            if (parseInt(g_id) == parseInt(shopId)){
                                $('.c_data_status').val('Hoạt động')
                            }
                        }
                        $(this).prop("checked", true);
                    });
                }else {
                    $('.checkbox-itemgroupshop-' + group_shop_id + '').each(function () {
                        var g_id = $(this).data('shop');
                        var shopId = $('.cg_shop').val();
                        if (shopId){
                            if (parseInt(g_id) == parseInt(shopId)){
                                $('.c_data_status').val('Khóa')
                            }
                        }
                        $(this).prop("checked", false);
                    });
                }
                var shopgroup = $(this).data('shopgroup');
                activeshop('group', shopgroup, '', checks.is(':checked'));
            })
            $('body').on('change', '.switch-success-shop', function (e) {
                var arrshop = '';
                var check = checkValidate();
                if (check){
                }else{
                    let checkt = $(this).find('input');
                    checkt.prop("checked", false);
                    return;
                }
                let checks = $(this).find('input');
                var shop = $(this).data('shop');
                var shopId = $('.cg_shop').val();
                if (shopId){
                    if (parseInt(shop) == parseInt(shopId)){
                        if (checks.is(':checked')){
                            $('.c_data_status').val('Hoạt động')
                        }else{
                            $('.c_data_status').val('Khóa')
                        }
                    }
                }
                activeshop('shop', '', shop, checks.is(':checked'));
            })
            function checkValidatev2(){
                var check = true;
                var item;
                $('select[name=position]').removeClass('is-invalid');
                $('input[name=title]').removeClass('is-invalid');
                $('input[name=price]').removeClass('is-invalid');
                $('.game_type').removeClass('is-invalid');
                var flastPosition = $('.flastPosition').val();
                if($('select[name=position]').val() == ""){
                    $('select[name=position]').focus();
                    $('select[name=position]').addClass('is-invalid');
                    $('.nav-link').removeClass('active');
                    $('.nav-item:eq(0) .nav-link').addClass('active');
                    $('.tab-pane').removeClass('active');
                    $('.tab-pane:eq(0)').addClass('active');
                    check = false;
                    return;
                }
                if(check){
                    if($('.game_type').val() == ""){
                        $('.game_type').focus();
                        $('.game_type').addClass('is-invalid');
                        $('.nav-link').removeClass('active');
                        $('.nav-item:eq(0) .nav-link').addClass('active');
                        $('.tab-pane').removeClass('active');
                        $('.tab-pane:eq(0)').addClass('active');
                        check = false;
                        return;
                    }
                }
                if(check){
                    if($('input[name=title]').val() == ""){
                        $('input[name=title]').focus();
                        $('input[name=title]').addClass('is-invalid');
                        $('.nav-link').removeClass('active');
                        $('.nav-item:eq(0) .nav-link').addClass('active');
                        $('.tab-pane').removeClass('active');
                        $('.tab-pane:eq(0)').addClass('active');
                        check = false;
                        return;
                    }
                }
                if(check){
                    if($('input[name=price]').val() == ""){
                        $('input[name=price]').focus();
                        $('input[name=price]').addClass('is-invalid');
                        $('.nav-link').removeClass('active');
                        $('.cpthanhtoan .nav-link').addClass('active');
                        $('.tab-pane').removeClass('active');
                        $('.tab-pane:eq(1)').addClass('active');
                        check = false;
                        return;
                    }
                }
                if(!check){
                    toastr.options =
                        {
                            "closeButton" : true,
                            "progressBar" : true,
                            'positionClass' : 'toast-top-center',
                        }
                    toastr.error("Chưa nhập mục bắt buộc");
                    item.focus();
                }
                if(check){
                    if($('input[name=price]').val() != ""){
                        if($('input[name=price]').val() < 1000){
                            toastr.options =
                                {
                                    "closeButton" : true,
                                    "progressBar" : true,
                                    'positionClass' : 'toast-top-center',
                                }
                            toastr.error("Giá thấp nhất là 1000");
                            $('input[name=price]').focus();
                            $('input[name=price]').addClass('is-invalid');
                            check = false;
                            return;
                        }
                    }
                }
                if (check){
                    if (flastPosition){
                        var ct_position = $('.t_position').val();
                        var ct_index =  0;
                        $('#kt_datatable_chgt .t_ds-checkbox-item').each(function () {
                            ct_index = ct_index + 1;
                        });
                        var cc_ton = ct_position - ct_index;
                        if (cc_ton > 0){
                            var s_title = $('.s_title').val();
                            if (s_title){
                                var s_content = 'Số lượng giải thưởng chưa đủ. Loại minigame ' + s_title + ' cần số ' + ct_position + ' giải thưởng.Vui lòng chọn thêm.';
                            }else{
                                var s_content = 'Số lượng giải thưởng chưa đủ.';
                            }
                            toastr.error(s_content);
                            check = false;
                            return;
                        }
                    }
                }
                return check;
            }
            $('body').on('change', '.switch-success-shop-replication', function (e) {
                let nameshop = $(this).data('name');
                var check = checkValidate();
                if (check){}else{
                    let checkt = $(this).find('input');
                    checkt.prop("checked", false);
                }
                if (confirm('' + nameshop + ' đang phân bổ minigame gốc, Bạn có muốn kích hoạt thêm minigame mới không?')) {}else {
                    let checkc = $(this).find('input');
                    checkc.prop("checked", false);
                    return false;
                }
                var arrshop = '';
                let checks = $(this).find('input');
                var shop = $(this).data('shop');
                var shopId = $('.cg_shop').val();
                if (shopId){
                    if (parseInt(shop) == parseInt(shopId)){
                        if (checks.is(':checked')){
                            $('.c_data_status').val('Hoạt động')
                        }else{
                            $('.c_data_status').val('Khóa')
                        }
                    }
                }
                activeshop('shop', '', shop, checks.is(':checked'));
            })
        })
        function activeshop(type, shopgroup, shop, status){
            $.ajax({
                url: '/admin/minigame-category/activeshop',
                datatype:'json',
                data:{
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    data: {
                        type: type,
                        shopgroup: shopgroup,
                        shop: shop,
                        groupid: $('#group_id').val(),
                        status: status
                    }
                },
                type: 'post',
                success: function (data) {
                    if(data.success){
                        toast(data.message);
                    }else{
                        $('.c_data_status').val('Khóa');
                        toast(data.message, 'error');
                    }
                    datatable.table().draw();
                    $('#customModal').modal('hide');
                }
            })
        }
        "use strict";
        $(document).ready(function () {
            $('#btnCheckAllGroup').click(function(){
                let checks = this.checked;
                if (checks){
                    $('#table-group-shop .item-checkbox').each(function(){ this.checked = true; });
                }else {
                    $('#table-group-shop .item-checkbox').each(function(){ this.checked = false; });
                }
            })
            $('.btnback').click(function(){
                if($('#datachange').val()==1){
                    if(confirm("Thông tin chưa được lưu. Bạn chắc chắn muốn quay lại ?")){
                        location.href = '{{route('admin.'.$module.'.index')}}'
                    }
                }else{
                    location.href = '{{route('admin.'.$module.'.index')}}'
                }
            });
            $("title_gen_slug").on("keyup change", function(e) {
                let title = $(this).val();
                $('#seo_title').val(title);
            })
            $('#title_gen_slug').donetyping(function() {
            }, 300);
            $('select[name=idkey]').change(function(){
                if($(this).val()==''){
                    $('#seeding .form-group input').val('');
                }else{
                    $('input[name=started_at]').val($('#package'+$(this).val()).data('date'));
                    var ob = jQuery.parseJSON($('#package'+$(this).val()).val());
                    $.each(ob, function(key,val) {
                        $('#'+key).val(val);
                    });
                }
            })
            //btn submit form
            $('.btn-submit-custom').click(function (e) {
                var check = checkValidate();
                if(check){
                    e.preventDefault();
                    if($('#datachangechung').val() == 1){
                        if(!confirm("Thông tin chung sẽ được đồng bộ cho tất cả các điểm bán được phân phối.")){
                            return;
                        }
                    }
                    var params_price_sticky_3 = $('.params_price_sticky_3').val();
                    if(!updatechgt(e)){
                        $('.nav-link').removeClass('active');
                        $('.nav-item:eq(2) .nav-link').addClass('active');
                        $('.tab-pane').removeClass('active');
                        $('.tab-pane:eq(2)').addClass('active');
                        return;
                    }
                    var btn = this;
                    $(".btn-submit-custom").each(function (index, value) {
                        KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                    });
                    $('.btn-submit-dropdown').prop('disabled', true);
                    setTimeout(function(){
                        //gắn thêm hành động close khi submit
                        $('#submit-close').val($(btn).data('submit-close'));
                        var formSubmit = $('#' + $(btn).data('form'));
                        formSubmit.submit();
                    },1000);
                }
            });
            $("select[name=position]").change(function(){
                window.location.href = $('input[name=urlcurrent]').val()+'?position='+$( "select[name=position]" ).val();
            });
        });
        function checkValidate(){
            var check = true;
            var item;
            $('select[name=position]').removeClass('is-invalid');
            $('input[name=title]').removeClass('is-invalid');
            $('input[name=price]').removeClass('is-invalid');
            $('.game_type').removeClass('is-invalid');
            var flastPosition = $('.flastPosition').val();
            if($('select[name=position]').val() == ""){
                $('select[name=position]').focus();
                $('select[name=position]').addClass('is-invalid');
                $('.nav-link').removeClass('active');
                $('.nav-item:eq(0) .nav-link').addClass('active');
                $('.tab-pane').removeClass('active');
                $('.tab-pane:eq(0)').addClass('active');
                check = false;
                return;
            }
            if(check){
                if($('.game_type').val() == ""){
                    $('.game_type').focus();
                    $('.game_type').addClass('is-invalid');
                    $('.nav-link').removeClass('active');
                    $('.nav-item:eq(0) .nav-link').addClass('active');
                    $('.tab-pane').removeClass('active');
                    $('.tab-pane:eq(0)').addClass('active');
                    check = false;
                    return;
                }
            }
            if(check){
                if($('input[name=title]').val() == ""){
                    $('input[name=title]').focus();
                    $('input[name=title]').addClass('is-invalid');
                    $('.nav-link').removeClass('active');
                    $('.nav-item:eq(0) .nav-link').addClass('active');
                    $('.tab-pane').removeClass('active');
                    $('.tab-pane:eq(0)').addClass('active');
                    check = false;
                    return;
                }
            }
            if(check){
                if($('input[name=price]').val() == ""){
                    $('input[name=price]').focus();
                    $('input[name=price]').addClass('is-invalid');
                    $('.nav-link').removeClass('active');
                    $('.cpthanhtoan .nav-link').addClass('active');
                    $('.tab-pane').removeClass('active');
                    $('.tab-pane:eq(1)').addClass('active');
                    check = false;
                    return;
                }
            }
            if(!check){
                toastr.options =
                    {
                        "closeButton" : true,
                        "progressBar" : true,
                        'positionClass' : 'toast-top-center',
                    }
                toastr.error("Chưa nhập mục bắt buộc");
                item.focus();
            }
            if(check){
                if($('input[name=price]').val() != ""){
                    if($('input[name=price]').val() < 1000){
                        toastr.options =
                            {
                                "closeButton" : true,
                                "progressBar" : true,
                                'positionClass' : 'toast-top-center',
                            }
                        toastr.error("Giá thấp nhất là 1000");
                        $('input[name=price]').focus();
                        $('input[name=price]').addClass('is-invalid');
                        check = false;
                        return;
                    }
                }
            }
            if (check){
                if (flastPosition){
                    var ct_position = $('.t_position').val();
                    var ct_index =  0;
                    $('#kt_datatable_chgt .t_ds-checkbox-item').each(function () {
                        ct_index = ct_index + 1;
                    });
                    var cc_ton = ct_position - ct_index;
                    if (cc_ton > 0){
                        var s_title = $('.s_title').val();
                        if (s_title){
                            var s_content = 'Số lượng giải thưởng chưa đủ. Loại minigame ' + s_title + ' cần số ' + ct_position + ' giải thưởng.Vui lòng chọn thêm.';
                        }else{
                            var s_content = 'Số lượng giải thưởng chưa đủ.';
                        }
                        toastr.error(s_content);
                        check = false;
                        return;
                    }
                }
            }
            return check;
        }
    </script>
    @if(auth()->user()->hasRole('admin') || auth()->user()->can('minigame-folder-image-show'))
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
        function checkMax(id, max){
            if($('#'+id).val() > max){
                $('#'+id).val(max);
            }
        }
    </script>
    @else
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
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_minigame', [$folder_image,$data->id??0]) }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_minigame', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_minigame', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_minigame', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_minigame', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_minigame', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
                    height:height,
                    startupMode:startupMode,
                } );
            });
            $('.ckeditor-basic').each(function () {
                var elem_id=$(this).prop('id');
                var height=$(this).data('height');
                height=height!=""?height:150;
                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_minigame', [$folder_image,$data->id??0]) }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_minigame', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_minigame', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_minigame', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_minigame', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_minigame', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
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
                    connectorPath: '{{route('admin.ckfinder_connector_minigame', [$folder_image,$data->id??0])}}',
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
                    connectorPath: '{{route('admin.ckfinder_connector_minigame', [$folder_image,$data->id??0])}}',
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
            function checkMax(id, max){
                if($('#'+id).val() > max){
                    $('#'+id).val(max);
                }
            }
        </script>
    @endif
    @if(isset($data_viewed) && $data_viewed!='')
        <script>
            $(document).ready(function(){
                $('.phanphoi').click();
            })
        </script>
    @endif
    {{--    XỬ LÝ AJAX DATATABLE--}}
    <script>
        "use strict";
        var edit_flg = false;
        var datatablechgt;
        var datatable;
        var datatableadd;
        var datatableaddcreated;
        var index = 0;
        var datatablectchgt;
        //CẤU HÌNH GIẢI THƯỞNG.
        $('body').on('click', '.cauhinhgiaithuong', function(e) {
            e.preventDefault();
            if ($('#kt_datatable_chgt').hasClass('test_addclass')){
            }else {
                $('#kt_datatable_chgt').addClass('test_addclass');
                // begin first table
                datatablechgt = $('#kt_datatable_chgt').DataTable({
                    responsive: true,
                    dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                            <'row'<'col-sm-12'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    // dom: "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
                    lengthMenu: [5, 10, 20, 50],
                    pageLength: 20,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    //"order": [[1, "desc"]],
                    ajax: {
                        url: '{{url()->current()}}' + '?ajax=1&chgt=1&phanphoi=0&shop=0&ctchgt=0',
                        type: 'GET',
                        data: function (d) {
                        }
                    },
                    buttons: [
                        {
                            text: ' <i class="fas fa-plus-circle icon-md i_them-giai-thuong"></i> Thêm Giải thưởng ',
                            action : function(e) {
                                e.preventDefault();
                                //  Danh sách các  giải thưởng
                                if ($('button[aria-controls="kt_datatable_chgt"]').hasClass('daduvatpham')){
                                    toastr.error("Bạn đã chọn tối đa số lượng giải thưởng cho phép. Nếu muốn chọn thêm, hãy xóa giải thưởng đã chọn.");
                                    return false;
                                }
                                // $('body').on('change','#kt_datatable_chgt .daduvatpham',function(){
                                //
                                // })
                                var ds_index =  0;
                                $('#kt_datatable_chgt .t_ds-checkbox-item').each(function () {
                                    ds_index = ds_index + 1;
                                });
                                $('.l_count_giaithuong').val(ds_index);
                                $('#dsGiaiThuong .so-giai-thuong').html('');
                                $('#dsGiaiThuong .so-giai-thuong').html(ds_index);
                                $('#dsGiaiThuong').modal('show');
                                $('#dsGiaiThuong').on('show.bs.modal', function (e) {
                                    $('#kt_datatable_ctchgt tbody').html('');
                                    datatablectchgt.table().draw();
                                });
                                if ($('#kt_datatable_ctchgt').hasClass('ctchgt_addclass')){
                                    $('#dsGiaiThuong').on('show.bs.modal', function (e) {
                                        $('#kt_datatable_ctchgt tbody').html('');
                                        datatablectchgt.table().draw();
                                    });
                                    $('#kt_datatable_ctchgt').parent().addClass('');
                                }else {
                                    $('#kt_datatable_ctchgt').addClass('ctchgt_addclass');
                                    // begin first table
                                    datatablectchgt = $('#kt_datatable_ctchgt').DataTable({
                                        responsive: true,
                                        dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                            <'row'<'col-sm-12 scroll-default'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                                        // dom: "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
                                        lengthMenu: [5, 10, 20, 50],
                                        pageLength: 10,
                                        language: {
                                            'lengthMenu': 'Display _MENU_',
                                        },
                                        searchDelay: 500,
                                        processing: true,
                                        serverSide: true,
                                        //"order": [[1, "desc"]],
                                        ajax: {
                                            url: '{{url()->current()}}' + '?ajax=1&chgt=0&phanphoi=0&shop=0&ctchgt=1',
                                            type: 'GET',
                                            data: function (d) {
                                                d.id = $('#id_dsgt').val();
                                                d.title = $('#title_dsgt').val();
                                                d.position = $('#positiongt_dsgt').val();
                                                d.valuefrom = $('#valuefrom_dsgt').val();
                                                d.valueto = $('#valueto_dsgt').val();
                                                d.gametype = $('#gametype_dsgt').val();
                                            }
                                        },
                                        buttons: [
                                            {
                                                class: "btn-cap-nhat-gt",
                                                text: ' <i class="far fa-save icon-md btn-cap-nhat-gt " style="color: #ffffff"></i> Cập nhật ',
                                                action : function(e) {
                                                    e.preventDefault();
                                                    var allSelected = '';
                                                    var total = datatablectchgt.$('.checkbox-item input[type="checkbox"]:checked').length;
                                                    var inputArray = [];
                                                    var r_index = 0;
                                                    datatablectchgt.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {
                                                        if ($(elem).is(':checked') || (!$(elem).is(':checked') && $(elem).parent().next().val()!="")) {
                                                            if($(elem).is(':checked')){
                                                                r_index = r_index + 1;
                                                                var curentRow = $(this).parent().parent().parent();
                                                                var id = $(elem).attr('rel');
                                                                var title = curentRow.find('.title').val();
                                                                var image = curentRow.find('.image').val();
                                                                var iditemset = curentRow.find('.iditemset').val();
                                                            }else{
                                                                var curentRow = $(this).parent().parent().parent();
                                                                var id = '';
                                                                var title = '';
                                                                var image = '';
                                                                var iditemset = curentRow.find('.iditemset').val();
                                                            }
                                                            inputArray.push({
                                                                'id':id,
                                                                'iditemset':iditemset,
                                                                'title':title,
                                                                'image':image
                                                            })
                                                        }
                                                    })
                                                    $('#datasetitem').val(JSON.stringify(inputArray));
                                                    fnSetitemNew($('#datasetitem').val());
                                                    var c_t_position = $('.t_position').val();
                                                    if (parseInt(r_index) == parseInt(c_t_position)){
                                                        $('.flastPosition').val('');
                                                    }
                                                }
                                            }
                                        ],
                                        columns: [
                                            {
                                                data: null,
                                                title: 'Chọn',
                                                orderable: false,
                                                searchable: false,
                                                width: "20px",
                                                class: "ckb_item",
                                                render: function (data, type, row) {
                                                    var flastPosition = $('.flastPosition').val();
                                                    console.log(flastPosition)
                                                    if (flastPosition){
                                                        return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input class="ds-checkbox-item" type="checkbox" rel="' + row.id + '" id="">&nbsp<span></span></label><input type="hidden" class="iditemset" name="iditemset" value="'+(row.children.length>0?row.children[0].id:'')+'"><input type="hidden" class="image" value="'+row.image+'"><input type="hidden" class="title" value="'+row.title+'">';
                                                    }else{
                                                        if (row.children.length>0){
                                                            return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input class="ds-checkbox-item" type="checkbox" rel="' + row.id + '" id="">&nbsp<span></span></label><input type="hidden" class="iditemset" name="iditemset" value="'+(row.children.length>0?row.children[0].id:'')+'"><input type="hidden" class="image" value="'+row.image+'"><input type="hidden" class="title" value="'+row.title+'">';
                                                        }else{
                                                            return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input class="ds-checkbox-item" disabled="disabled" type="checkbox" rel="' + row.id + '" id="">&nbsp<span style="background: rgb(243, 243, 247);"></span></label><input type="hidden" class="iditemset" name="iditemset" value="'+(row.children.length>0?row.children[0].id:'')+'"><input type="hidden" class="image" value="'+row.image+'"><input type="hidden" class="title" value="'+row.title+'">';
                                                        }
                                                    }
                                                    return "";
                                                }
                                            },
                                            {data: 'id', title: 'ID'},
                                                {{--{--}}
                                                {{--    data: 'id', title: '{{__('ID')}}',--}}
                                                {{--    render: function (data, type, row) {--}}
                                                {{--        return row.id;--}}
                                                {{--    }--}}
                                                {{--},--}}
                                            {data: 'title', title: '{{__('Tên giải thưởng')}}'},
                                            {
                                                data: "groups", title: '{{__('Loại giải thưởng')}}', orderable: false,
                                                render: function (data, type, row) {
                                                    var temp = "";
                                                    $.each(row.groups, function (index, value) {
                                                        if (value.name == 'admin') {
                                                            temp += "<span class=\"label label-pill label-inline label-center mr-2  label-primary \">" + value.title + "</span><br />";
                                                        } else {
                                                            temp += "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + value.title + "</span><br />";
                                                        }
                                                    });
                                                    return temp;
                                                }
                                            },
                                            {
                                                data: 'children', title: '{{__('Loại thưởng')}}',
                                                render: function (data, type, row) {
                                                    if(row.params.gift_type == 0){
                                                        return "{{config('module.minigame.gift_type.0')}}";
                                                    }else if(row.params.gift_type == 1){
                                                        return "{{config('module.minigame.gift_type.1')}}";
                                                    }
                                                }
                                            },
                                            {
                                                data: 'params', title: '{{__('Số vật phẩm')}}',
                                                render: function (data, type, row) {
                                                    return row.params.value;
                                                }
                                            },
                                            {
                                                data: 'params', title: '{{__('Loại game')}}',
                                                render: function (data, type, row) {
                                                    if(row.position == 1){
                                                        return "{{config('module.minigame.game_type.1')}}";
                                                    } else if(row.position == 2){
                                                        return "{{config('module.minigame.game_type.2')}}";
                                                    }else if(row.position == 3){
                                                        return "{{config('module.minigame.game_type.3')}}";
                                                    }else if(row.position == 4){
                                                        return "{{config('module.minigame.game_type.4')}}";
                                                    }else if(row.position == 5){
                                                        return "{{config('module.minigame.game_type.5')}}";
                                                    }else if(row.position == 6){
                                                        return "{{config('module.minigame.game_type.6')}}";
                                                    }else if(row.position == 7){
                                                        return "{{config('module.minigame.game_type.7')}}";
                                                    }else if(row.position == 8){
                                                        return "{{config('module.minigame.game_type.8')}}";
                                                    }else if(row.position == 9){
                                                        return "{{config('module.minigame.game_type.9')}}";
                                                    }else if(row.position == 10){
                                                        return "{{config('module.minigame.game_type.10')}}";
                                                    }else if(row.position == 11){
                                                        return "{{config('module.minigame.game_type.11')}}";
                                                    }else if(row.position == 12){
                                                        return "{{config('module.minigame.game_type.12')}}";
                                                    }else if(row.position == 13){
                                                        return "{{config('module.minigame.game_type.13')}}";
                                                    }else if(row.position == 14){
                                                        return "{{config('module.minigame.game_type.14')}}";
                                                    }
                                                }
                                            },
                                            {data: 'image',title:'{{__('Ảnh hiển thị')}}', orderable: false, searchable: false,
                                                render: function ( data, type, row ) {
                                                    if(row.image=="" || row.image==null){
                                                        return  "<img class=\"image-item\" src=\"/assets/backend/themes/images/empty-photo.jpg\" style=\"max-width: 70px\">";
                                                    }
                                                    else{
                                                        return  "<img class=\"image-item\" src=\""+row.image+"\" style=\"max-width: 70px\">";
                                                    }
                                                }
                                            },
                                        ],
                                        "drawCallback": function (settings) {
                                            var v_index = 0
                                            $('#kt_datatable_ctchgt_wrapper .dataTables_length').each(function () {
                                                v_index = v_index + 1
                                            })
                                            if (v_index == 2){
                                                $('#kt_datatable_ctchgt_wrapper .dataTables_length').last().remove();
                                            }
                                            $('#kt_datatable_ctchgt_wrapper #kt_datatable_ctchgt_paginate').remove();
                                        }
                                    });
                                    var filter = function () {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                        datatablectchgt.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                                    };
                                    $('#kt_search_dsgt').on('click', function (e) {
                                        e.preventDefault();
                                        var params = {};
                                        $('.datatable-input_dsgt').each(function () {
                                            var i = $(this).data('col-index');
                                            if (params[i]) {
                                                params[i] += '|' + $(this).val();
                                            } else {
                                                params[i] = $(this).val();
                                            }
                                        });
                                        $.each(params, function (i, val) {
                                            // apply search params to datatable
                                            datatablectchgt.column(i).search(val ? val : '', false, false);
                                        });
                                        datatablectchgt.table().draw();
                                    });
                                    $('#kt_reset_dsgt').on('click', function (e) {
                                        e.preventDefault();
                                        $('.datatable-input_dsgt').each(function () {
                                            $(this).val('');
                                            datatablectchgt.column($(this).data('col-index')).search('', false, false);
                                        });
                                        datatablectchgt.table().draw();
                                        var total_vp = 0;
                                        total_vp = $('.l_count_giaithuong').val();
                                        $('.so-giai-thuong').html('');
                                        $('.so-giai-thuong').html(total_vp);
                                    });
                                    datatablectchgt.on("change", ".ckb_item input[type='checkbox']", function () {
                                        if (this.checked) {
                                            var currTr = $(this).closest("tr");
                                            datatablectchgt.rows(currTr).select();
                                        } else {
                                            var currTr = $(this).closest("tr");
                                            datatablectchgt.rows(currTr).deselect();
                                        }
                                    });
                                    //function update field
                                    datatablectchgt.on("change", ".update_field", function (e) {
                                        e.preventDefault();
                                        edit_flg = true;
                                    });
                                }
                            }
                        },
                    ],
                    columns: [
                        {
                            data: "id", title: 'ID', orderable: false,
                            render: function (data, type, row) {
                                return '<span class="item_datatable item_datatable_'+(row.children.length>0?row.children[0].id:'')+'">' + row.id + '</span><input type="hidden" class="t_ds-checkbox-item">';
                            }
                        },
                        {data: 'title', title: '{{__('Tên giải thưởng')}}'},
                        {
                            data: 'title_custom', title: '{{__('Tên giải thưởng custom')}}',
                            render: function (data, type, row) {
                                return row.title_custom+"<input type='hidden' class='update_field id' value='" + row.id + "'><input type='hidden' class='update_field title' value='" + row.title_custom + "'><input type='hidden' class='update_field image' value='" + row.image_custom + "'>"+'<input type="hidden" class="iditemset" name="iditemset" value="'+(row.children.length>0?row.children[0].id:'')+'">';
                            }
                        },
                        {
                            data: "groups", title: '{{__('Loại giải thưởng')}}', orderable: false,
                            render: function (data, type, row) {
                                var temp = "";
                                $.each(row.groups, function (index, value) {
                                    if (value.name == 'admin') {
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-primary \">" + value.title + "</span><br />";
                                    } else {
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + value.title + "</span><br />";
                                    }
                                });
                                return temp;
                            }
                        },
                            {{--{--}}
                            {{--    data: 'children', title: '{{__('Loại thưởng')}}',--}}
                            {{--    render: function (data, type, row) {--}}
                            {{--        if(row.params.gift_type == 0){--}}
                            {{--            return "{{config('module.minigame.gift_type.0')}}";--}}
                            {{--        }else if(row.params.gift_type == 1){--}}
                            {{--            return "{{config('module.minigame.gift_type.1')}}";--}}
                            {{--        }--}}
                            {{--    }--}}
                            {{--},--}}
                        {
                            data: 'params', title: '{{__('Giá trị vật phẩm')}}',
                            render: function (data, type, row) {
                                return row.params.value+ "" +
                                    "<input class='update_field limit' data-field='limit' data-required='0' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='hidden' min='1' value='" + (row.children.length>0?(row.children[0].params!=null?row.children[0].params.limit:''):'') + "' style='width:60px'>";
                            }
                        },
                        {
                            data: 'children', title: '{{__('Số lượng')}}',visible: false,
                            render: function (data, type, row) {
                                return "<input class='update_field limit' data-field='limit' data-required='0' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' min='1' value='" + (row.children.length>0?(row.children[0].params!=null?row.children[0].params.limit:''):'') + "' style='width:60px'>";
                            }
                        },
                        {
                            data: 'children', title: '{{__('Giá trị bonus từ')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field bonus_from' data-field='bonus_from' data-required='0' data-id='"+(row.children.length>0?row.children[0].id:'')+"' min='1' type='number' value='" + (row.children.length>0?(row.children[0].params!=null?row.children[0].params.bonus_from:''):'') + "' style='width:60px'>";
                            }
                        },
                        {
                            data: 'children', title: '{{__('Giá trị bonus đến')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field bonus_to' data-field='bonus_to' data-required='0' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' min='1' value='" + (row.children.length>0?(row.children[0].params!=null?row.children[0].params.bonus_to:''):'') + "' style='width:60px'>";
                            }
                        },
                        {
                            data: 'children', title: '{{__('Vị trí (bắt đầu từ 0)')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field order' data-field='order' data-required='1' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' min='1' value='" + (row.children.length>0?row.children[0].order:'') + "' style='width:40px'>";
                            }
                        },
                        {
                            data: 'params', title: '{{__('% chơi thật')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field percent' data-field='percent' data-required='1' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' min='1' value='" + (row.children.length>0?(row.children[0].params!=null?row.children[0].params.percent:''):'') + "' style='width:55px'>";
                            }
                        },
                        {
                            data: 'params', title: '{{__('% chơi thử')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field try_percent' data-field='try_percent' data-required='1' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' min='1' value='" + (row.children.length>0?(row.children[0].params!=null?row.children[0].params.try_percent:''):'') + "' style='width:55px'>";
                            }
                        },
                        {
                            data: 'params', title: '{{__('% nổ hũ')}}',
                            render: function (data, type, row) {
                                return "<input class='update_field nohu_percent' data-field='nohu_percent' data-required='0' data-id='"+(row.children.length>0?row.children[0].id:'')+"' type='number' min='1' value='" + (row.children.length>0?(row.children[0].params!=null?row.children[0].params.nohu_percent:''):'') + "' style='width:55px'>";
                            }
                        },
                        {data: 'image_custom',title:'{{__('Ảnh hiển thị')}}', orderable: false, searchable: false,
                            render: function ( data, type, row ) {
                                if(row.image=="" || row.image==null){
                                    return  "<img class=\"image-item\" src=\"/assets/backend/themes/images/empty-photo.jpg\" style=\"max-width: 70px\">";
                                }
                                else{
                                    return  "<img class=\"image-item\" src=\""+row.image_custom+"\" style=\"max-width: 70px\">";
                                }
                            }
                        },
                        { data: 'action',title:'Thao tác', orderable: false, searchable: false}
                    ],
                    "drawCallback": function (settings) {
                        var bt_position = $('.t_position').val();
                        var bt_count_giaithuong = $('.l_count_giaithuong').val();
                        if (parseInt(bt_position) == parseInt(bt_count_giaithuong)){
                            // $('button[aria-controls="kt_datatable_chgt"]').prop('disabled', true);
                            $('button[aria-controls="kt_datatable_chgt"]').addClass('daduvatpham');
                            $('button[aria-controls="kt_datatable_chgt"]').css('background','#e4e6ef');
                            $('button[aria-controls="kt_datatable_chgt"]').css('border-color','#e4e6ef');
                        }
                        $(function ()
                        {
                            $('[data-toggle="tooltip"]').tooltip()
                        });
                        var v_index = 0
                        $('#kt_datatable_chgt_wrapper .dataTables_length').each(function () {
                            v_index = v_index + 1
                        })
                        if (v_index == 2){
                            $('#kt_datatable_chgt_wrapper .dataTables_length').last().remove();
                        }
                        $('#kt_datatable_chgt_wrapper #kt_datatable_chgt_paginate').remove();
                        var api = this.api();
                        var apiJson = api.ajax.json();
                        var rows = api.rows({page: 'current'}).nodes();
                        var t_position = $('.t_position').val();
                        var t_index =  0;
                        $('#kt_datatable_chgt .t_ds-checkbox-item').each(function () {
                            t_index = t_index + 1;
                        });
                        var c_ton = t_position - t_index;
                        var choi_that = 0;
                        $('#kt_datatable_chgt .percent').each(function () {
                            var i_choi_that = $(this).val();
                            if (i_choi_that){
                                choi_that = parseInt(choi_that) + parseInt(i_choi_that);
                            }
                        });
                        var choi_thu = 0;
                        $('#kt_datatable_chgt .try_percent').each(function () {
                            var i_choi_thu = $(this).val();
                            if (i_choi_thu){
                                choi_thu = parseInt(choi_thu) + parseInt(i_choi_thu);
                            }
                        });
                        if (t_index > 0){
                            var val = parseInt(t_index)/(parseInt(t_position));
                            val = Math.round( val*100 );
                            if (c_ton == 0){
                                //total first rows
                                $(rows).eq(0).before(
                                    '<tr class="group total-allpage">' +
                                    '<td colspan="8"><b> </span></b></td>' +
                                    '<td colspan="1"><b class="total_choithat">Tổng: <span class="text_choi_that">' + choi_that + '%</span></b></td>' +
                                    '<td colspan="1"><b class="total_choithu">Tổng: <span class="text_choi_thu">' + choi_thu + '%</span></b></td>' +
                                    '<td colspan="3"></td>' +
                                    '</tr>',
                                );
                            }else{
                                //total first rows
                                $(rows).eq(0).before(
                                    '<tr class="group total-allpage">' +
                                    '<td colspan="8"><b class=""></b></td>' +
                                    '<td colspan="1"><b class="total_choithat">Tổng: <span class="text_choi_that">' + choi_that + '%</span></b></td>' +
                                    '<td colspan="1"><b class="total_choithu">Tổng: <span class="text_choi_thu">' + choi_thu + '%</span></b></td>' +
                                    '<td colspan="3"></td>' +
                                    '</tr>',
                                );
                            }
                        }else{
                            var t_html = '';
                            t_html += '<tr class="group total-allpage">';
                            t_html += '<td colspan="8"><b class=""></b></td>';
                            t_html += '<td colspan="1"><b class="total_choithat">Tổng: <span class="text_choi_that">' + 0 + '%</span></b></td>';
                            t_html += '<td colspan="1"><b class="total_choithu">Tổng: <span class="text_choi_thu">' + 0 + '%</span></b></td>';
                            t_html += '<td colspan="3"></td>';
                            t_html += '</tr>';
                            t_html += '<tr class="odd"><td valign="top" colspan="15" class="dataTables_empty">No data available in table</td></tr>';
                            $('#kt_datatable_chgt tbody').html(t_html);
                        }
                    }
                });
                var filter = function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    datatablechgt.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                };
                $('#kt_search_chgt').on('click', function (e) {
                    e.preventDefault();
                    var params = {};
                    $('.datatable-input_chgt').each(function () {
                        var i = $(this).data('col-index');
                        if (params[i]) {
                            params[i] += '|' + $(this).val();
                        } else {
                            params[i] = $(this).val();
                        }
                    });
                    $.each(params, function (i, val) {
                        // apply search params to datatable
                        datatablechgt.column(i).search(val ? val : '', false, false);
                    });
                    datatablechgt.table().draw();
                });
                $('#kt_reset_chgt').on('click', function (e) {
                    e.preventDefault();
                    $('.datatable-input_chgt').each(function () {
                        $(this).val('');
                        datatablechgt.column($(this).data('col-index')).search('', false, false);
                    });
                    datatablechgt.table().draw();
                });
                datatablechgt.on("click", "#btnCheckAll", function () {
                    $(".ckb_item input[type='checkbox']").prop('checked', this.checked).change();
                })
                datatablechgt.on("change", ".ckb_item input[type='checkbox']", function () {
                    if (this.checked) {
                        var currTr = $(this).closest("tr");
                        datatablechgt.rows(currTr).select();
                    } else {
                        var currTr = $(this).closest("tr");
                        datatablechgt.rows(currTr).deselect();
                    }
                });
                //function update field
                datatablechgt.on("change", ".update_field", function (e) {
                    e.preventDefault();
                    edit_flg = true;
                });
                //function update field
                {{--datatable.on("change", ".update_field", function (e) {--}}
                {{--    e.preventDefault();--}}
                {{--    var field=$(this).data('field');--}}
                {{--    var id=$(this).data('id');--}}
                {{--    if(id==''){--}}
                {{--        return;--}}
                {{--    }--}}
                {{--    var required=$(this).data('required');--}}
                {{--    var value=$(this).val();--}}
                {{--    $.ajax({--}}
                {{--        type: "POST",--}}
                {{--        url: '{{route('admin.minigame-category.updatefieldcat')}}',--}}
                {{--        data: {--}}
                {{--            '_token':'{{csrf_token()}}',--}}
                {{--            'field':field,--}}
                {{--            'id':id,--}}
                {{--            'value':value,--}}
                {{--            'required' :required--}}
                {{--        },--}}
                {{--        beforeSend: function (xhr) {--}}
                {{--        },--}}
                {{--        success: function (data) {--}}
                {{--            if (data.success) {--}}
                {{--                if (data.redirect + "" != "") {--}}
                {{--                    location.href = data.redirect;--}}
                {{--                }--}}
                {{--                toast('{{__('Cập nhật thành công')}}');--}}
                {{--            } else {--}}
                {{--                toast(data.message, 'error');--}}
                {{--            }--}}
                {{--        },--}}
                {{--        error: function (data) {--}}
                {{--            toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');--}}
                {{--        },--}}
                {{--        complete: function (data) {--}}
                {{--        }--}}
                {{--    });--}}
                {{--});--}}
            }
        })
        //PHÂN PHỐI
        $('body').on('click', '.phanphoi', function(e) {
            e.preventDefault();
            // begin first table
            if ($('#kt_datatable').hasClass('test_addclass')){
            }else {
                $('#kt_datatable').addClass('test_addclass');
                datatable = $('#kt_datatable').DataTable({
                    responsive: true,
                    dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                            <'row'<'col-sm-12'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    // dom: "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
                    lengthMenu: [5, 10, 50, 50],
                    pageLength: 5,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "order": [[1, "desc"]],
                    ajax: {
                        url: '{{url()->current()}}' + '?ajax=1&phanphoi=1&chgt=0&shop=0&ctchgt=0',
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
                    "drawCallback": function (settings) {
                        $(function ()
                        {
                            $('[data-toggle="tooltip"]').tooltip()
                        });
                        var v_index = 0
                        $('#kt_datatable_wrapper .dataTables_length').each(function () {
                            v_index = v_index + 1
                        })
                        if (v_index == 2){
                            $('#kt_datatable_wrapper .dataTables_length').last().remove();
                        }
                        $('#kt_datatable_wrapper #kt_datatable_paginate').remove();
                        var api = this.api();
                        var apiJson = api.ajax.json();
                        var rows = api.rows({page: 'current'}).nodes();
                        var last=null;
                        var countColumn= datatable.columns(':visible').count();
                        var c_data_shop = JSON.parse($('.c_data_shop').val());
                        var c_parent = null;
                        var c_childs = null;
                        var c_customs = null;
                        if (c_data_shop){
                            c_parent = c_data_shop.parent;
                            if (c_parent){
                                c_customs = c_parent.customs;
                            }
                            c_childs = c_data_shop.childs;
                        }
                        api.column(1, {page:'current'} ).data().each( function ( group_shop, j ) {
                            let groupshopid = group_shop;
                            api.column(2, {page:'current'} ).data().each( function ( group, i ) {
                                for (let h = 0; h < group.length; h++){
                                    if (group_shop == group[h].group_id){
                                        if (c_customs){
                                            var isCustom = false;
                                            $.each(c_customs, function (key, value) {
                                                if (value.shop_id == group[h].id && value.status == 1){
                                                    isCustom = true;
                                                }
                                            })
                                            if (c_childs && c_childs.length){
                                                $.each(c_childs, function (keyc, valuec) {
                                                    var p_child = valuec.customs;
                                                    if (p_child){
                                                        $.each(p_child, function (keyp, valuep) {
                                                            if (valuep.shop_id == group[h].id && valuep.status == 1){
                                                                isCustom = true;
                                                            }
                                                        })
                                                    }
                                                })
                                            }
                                            if (isCustom){
                                                if (group[h].minigame_module && group[h].minigame_module.length > 0){
                                                    $('#inActiveShop .group-id').val('');
                                                    $('#inActiveShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#activeShop .group-id').val('');
                                                    $('#activeShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#deleteShop .group-id').val('');
                                                    $('#deleteShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#deleteGroupShop .group-id').val('');
                                                    $('#deleteGroupShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    if (group[h].minigame_module[0].status == 1){
                                                        // status_group = 1;
                                                        $(rows).eq( i ).after(
                                                            "<tr class='group' style='background: #ffffff'>" +
                                                            "<td style='background: #ffffff' ><label class='checkbox checkbox-lg checkbox-outline checkbox-item'><input data-shop=" + group[h].id + " class='checkbox-item-shop checkbox-item-shop-" + group[h].group_id + "' type='checkbox'>&nbsp<span></span></label></td>" +
                                                            "<td style='background: #ffffff'>"+group[h].id+"</td>" +
                                                            "<td style='background: #ffffff'>"+group[h].domain+"</td>" +
                                                            "<td style='background: #ffffff'><span class='switch switch-outline switch-icon switch-success switch-success-shop' data-shop='"+group[h].id+"'><label><input checked type='checkbox' data-shop=" + group[h].id + " class='checkbox-itemgroupshop-" + groupshopid + "' name='status'/><span></span></label></span></td>" +
                                                            "<td style='background: #ffffff'><a data-id=" + group[h].id + " class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle deleteShop'><i class=\"la la-trash\"></i></a></td>" +
                                                            "</tr>"
                                                        );
                                                    }else{
                                                        // isStatus = false;
                                                        $(rows).eq( i ).after(
                                                            "<tr class='group' style='background: #ffffff'>" +
                                                            "<td style='background: #ffffff' ><label class='checkbox checkbox-lg checkbox-outline checkbox-item'><input data-name='"+group[h].domain+"' data-shop=" + group[h].id + " class='checkbox-item-shop checkbox-item-shop-" + group[h].group_id + "' type='checkbox'>&nbsp<span></span></label></td>" +
                                                            "<td style='background: #ffffff'>"+group[h].id+"</td>" +
                                                            "<td style='background: #ffffff'>"+group[h].domain+"</td>" +
                                                            "<td style='background: #ffffff'><span class='switch switch-outline switch-icon switch-success switch-success-shop-replication' data-name='"+group[h].domain+"' data-shop='"+group[h].id+"'><label><input data-shop=" + group[h].id + " type='checkbox' class='checkbox-itemgroupshop-" + groupshopid + "' name='status'/><span></span></label></span></td>" +
                                                            "<td style='background: #ffffff'><a data-id=" + group[h].id + " class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle deleteShop'><i class=\"la la-trash\"></i></a></td>" +
                                                            "</tr>"
                                                        );
                                                    }
                                                }
                                            }else{
                                                if (group[h].minigame_module && group[h].minigame_module.length > 0){
                                                    $('#inActiveShop .group-id').val('');
                                                    $('#inActiveShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#activeShop .group-id').val('');
                                                    $('#activeShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#deleteShop .group-id').val('');
                                                    $('#deleteShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#deleteGroupShop .group-id').val('');
                                                    $('#deleteGroupShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    if (group[h].minigame_module[0].status == 1){
                                                        // status_group = 1;
                                                        $(rows).eq( i ).after(
                                                            "<tr class='group' style='background: #ffffff'>" +
                                                            "<td style='background: #ffffff' ><label class='checkbox checkbox-lg checkbox-outline checkbox-item'><input data-shop=" + group[h].id + " class='checkbox-item-shop checkbox-item-shop-" + group[h].group_id + "' type='checkbox'>&nbsp<span></span></label></td>" +
                                                            "<td style='background: #ffffff'>"+group[h].id+"</td>" +
                                                            "<td style='background: #ffffff'>"+group[h].domain+"</td>" +
                                                            "<td style='background: #ffffff'><span class='switch switch-outline switch-icon switch-success switch-success-shop' data-shop='"+group[h].id+"'><label><input checked type='checkbox' data-shop=" + group[h].id + " class='checkbox-itemgroupshop-" + groupshopid + "' name='status'/><span></span></label></span></td>" +
                                                            "<td style='background: #ffffff'><a data-id=" + group[h].id + " class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle deleteShop'><i class=\"la la-trash\"></i></a></td>" +
                                                            "</tr>"
                                                        );
                                                    }else{
                                                        // isStatus = false;
                                                        $(rows).eq( i ).after(
                                                            "<tr class='group' style='background: #ffffff'>" +
                                                            "<td style='background: #ffffff' ><label class='checkbox checkbox-lg checkbox-outline checkbox-item'><input data-shop=" + group[h].id + " class='checkbox-item-shop checkbox-item-shop-" + group[h].group_id + "' type='checkbox'>&nbsp<span></span></label></td>" +
                                                            "<td style='background: #ffffff'>"+group[h].id+"</td>" +
                                                            "<td style='background: #ffffff'>"+group[h].domain+"</td>" +
                                                            "<td style='background: #ffffff'><span class='switch switch-outline switch-icon switch-success switch-success-shop' data-shop='"+group[h].id+"'><label><input type='checkbox' data-shop=" + group[h].id + " class='checkbox-itemgroupshop-" + groupshopid + "' name='status'/><span></span></label></span></td>" +
                                                            "<td style='background: #ffffff'><a data-id=" + group[h].id + " class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle deleteShop'><i class=\"la la-trash\"></i></a></td>" +
                                                            "</tr>"
                                                        );
                                                    }
                                                }
                                            }
                                        }else {
                                            var isCustom = false;
                                            if (c_childs && c_childs.length){
                                                $.each(c_childs, function (keyc, valuec) {
                                                    var p_child = valuec.customs;
                                                    if (p_child){
                                                        $.each(p_child, function (keyp, valuep) {
                                                            if (valuep.shop_id == group[h].id && valuep.status == 1){
                                                                isCustom = true;
                                                            }
                                                        })
                                                    }
                                                })
                                            }
                                            if (isCustom){
                                                if (group[h].minigame_module && group[h].minigame_module.length > 0){
                                                    $('#inActiveShop .group-id').val('');
                                                    $('#inActiveShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#activeShop .group-id').val('');
                                                    $('#activeShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#deleteShop .group-id').val('');
                                                    $('#deleteShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#deleteGroupShop .group-id').val('');
                                                    $('#deleteGroupShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    if (group[h].minigame_module[0].status == 1){
                                                        // status_group = 1;
                                                        $(rows).eq( i ).after(
                                                            "<tr class='group' style='background: #ffffff'>" +
                                                            "<td style='background: #ffffff' ><label class='checkbox checkbox-lg checkbox-outline checkbox-item'><input data-shop=" + group[h].id + " class='checkbox-item-shop checkbox-item-shop-" + group[h].group_id + "' type='checkbox'>&nbsp<span></span></label></td>" +
                                                            "<td style='background: #ffffff'>"+group[h].id+"</td>" +
                                                            "<td style='background: #ffffff'>"+group[h].domain+"</td>" +
                                                            "<td style='background: #ffffff'><span class='switch switch-outline switch-icon switch-success switch-success-shop' data-shop='"+group[h].id+"'><label><input checked type='checkbox' data-shop=" + group[h].id + " class='checkbox-itemgroupshop-" + groupshopid + "' name='status'/><span></span></label></span></td>" +
                                                            "<td style='background: #ffffff'><a data-id=" + group[h].id + " class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle deleteShop'><i class=\"la la-trash\"></i></a></td>" +
                                                            "</tr>"
                                                        );
                                                    }else{
                                                        // isStatus = false;
                                                        $(rows).eq( i ).after(
                                                            "<tr class='group' style='background: #ffffff'>" +
                                                            "<td style='background: #ffffff' ><label class='checkbox checkbox-lg checkbox-outline checkbox-item'><input data-name='"+group[h].domain+"' data-shop=" + group[h].id + " class='checkbox-item-shop checkbox-item-shop-" + group[h].group_id + "' type='checkbox'>&nbsp<span></span></label></td>" +
                                                            "<td style='background: #ffffff'>"+group[h].id+"</td>" +
                                                            "<td style='background: #ffffff'>"+group[h].domain+"</td>" +
                                                            "<td style='background: #ffffff'><span class='switch switch-outline switch-icon switch-success switch-success-shop-replication' data-name='"+group[h].domain+"' data-shop='"+group[h].id+"'><label><input data-shop=" + group[h].id + " type='checkbox' class='checkbox-itemgroupshop-" + groupshopid + "' name='status'/><span></span></label></span></td>" +
                                                            "<td style='background: #ffffff'><a data-id=" + group[h].id + " class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle deleteShop'><i class=\"la la-trash\"></i></a></td>" +
                                                            "</tr>"
                                                        );
                                                    }
                                                }
                                            }else {
                                                if (group[h].minigame_module && group[h].minigame_module.length > 0){
                                                    $('#inActiveShop .group-id').val('');
                                                    $('#inActiveShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#activeShop .group-id').val('');
                                                    $('#activeShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#deleteShop .group-id').val('');
                                                    $('#deleteShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    $('#deleteGroupShop .group-id').val('');
                                                    $('#deleteGroupShop .group-id').val(group[h].minigame_module[0].group_id);
                                                    if (group[h].minigame_module[0].status == 1){
                                                        // status_group = 1;
                                                        $(rows).eq( i ).after(
                                                            "<tr class='group' style='background: #ffffff'>" +
                                                            "<td style='background: #ffffff' ><label class='checkbox checkbox-lg checkbox-outline checkbox-item'><input data-shop=" + group[h].id + " class='checkbox-item-shop checkbox-item-shop-" + group[h].group_id + "' type='checkbox'>&nbsp<span></span></label></td>" +
                                                            "<td style='background: #ffffff'>"+group[h].id+"</td>" +
                                                            "<td style='background: #ffffff'>"+group[h].domain+"</td>" +
                                                            "<td style='background: #ffffff'><span class='switch switch-outline switch-icon switch-success switch-success-shop' data-shop='"+group[h].id+"'><label><input checked data-shop=" + group[h].id + " type='checkbox' class='checkbox-itemgroupshop-" + groupshopid + "' name='status'/><span></span></label></span></td>" +
                                                            "<td style='background: #ffffff'><a data-id=" + group[h].id + " class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle deleteShop'><i class=\"la la-trash\"></i></a></td>" +
                                                            "</tr>"
                                                        );
                                                    }else{
                                                        // isStatus = false;
                                                        $(rows).eq( i ).after(
                                                            "<tr class='group' style='background: #ffffff'>" +
                                                            "<td style='background: #ffffff' ><label class='checkbox checkbox-lg checkbox-outline checkbox-item'><input data-shop=" + group[h].id + " class='checkbox-item-shop checkbox-item-shop-" + group[h].group_id + "' type='checkbox'>&nbsp<span></span></label></td>" +
                                                            "<td style='background: #ffffff'>"+group[h].id+"</td>" +
                                                            "<td style='background: #ffffff'>"+group[h].domain+"</td>" +
                                                            "<td style='background: #ffffff'><span class='switch switch-outline switch-icon switch-success switch-success-shop' data-shop='"+group[h].id+"'><label><input type='checkbox' data-shop=" + group[h].id + " class='checkbox-itemgroupshop-" + groupshopid + "' name='status'/><span></span></label></span></td>" +
                                                            "<td style='background: #ffffff'><a data-id=" + group[h].id + " class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle deleteShop'><i class=\"la la-trash\"></i></a></td>" +
                                                            "</tr>"
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        } );
                    },
                    buttons: [
                    ],
                    columns: [
                        {
                            data: null,
                            title: '<label class="checkbox checkbox-lg checkbox-outline"><input type="checkbox" id="btnCheckAllpp">&nbsp<span></span></label>',
                            orderable: false,
                            searchable: false,
                            width: "20px",
                            class: "ckb_item",
                            render: function (data, type, row) {
                                return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item checkbox-item-shop-group" data-group="' + row.id + '"><input class="checkbox-item-shop-group-input" type="checkbox" rel="' + row.id + '">&nbsp<span></span></label>';
                            }
                        },
                        {
                            data: 'id', title: 'ID',
                            render: function (data, type, row) {
                                return "<span style='font-size: 14px;font-weight: 700'>Nhóm shop</span>";
                            }
                        },
                        {
                            data: 'shop', title: '{{__('Tên điểm bán/nhóm điểm bán')}}',
                            render: function (data, type, row) {
                                return "<span style='font-size: 14px;font-weight: 700'>" + row.title + "</span><small class='count_shop'>" + row.count_shop + "</small>";
                            }
                        },
                        {data: 'status',title:'{{__('Trạng thái minigame')}}', orderable: false, searchable: false,
                            render: function (data, type, row) {
                                let status_group;
                                let isStatus = 0;
                                let groupid = row.id;
                                var p_data_shop = JSON.parse($('.c_data_shop').val());
                                var p_parent = null;
                                var p_customs = null;
                                var s_childs = null;
                                if (p_data_shop){
                                    p_parent = p_data_shop.parent;
                                    if (p_parent){
                                        p_customs = p_parent.customs;
                                    }
                                    s_childs = p_data_shop.childs;
                                }
                                if (p_customs && p_customs.length > 0) {
                                    var p_isCustom = false;
                                    $.each(p_customs, function (key22, value22) {
                                        $.each(row.shop,function(key,value){
                                            if (value22.shop_id == value.id && value22.status == 1) {
                                                if (value.minigame_module && value.minigame_module.length > 0){
                                                    if (value.minigame_module[0].status == 1){
                                                    }else {
                                                        p_isCustom = true;
                                                    }
                                                }else {
                                                    isStatus = 3;
                                                }
                                            }else {
                                                if (value.minigame_module && value.minigame_module.length > 0){
                                                    if (value.minigame_module[0].status == 1){
                                                    }else {
                                                        isStatus = 1;
                                                    }
                                                }else {
                                                    isStatus = 3;
                                                }
                                            }
                                        });
                                    });
                                    if (s_childs && s_childs.length > 0){
                                        $.each(s_childs, function (keyc22, valuec22) {
                                            var t_child = valuec22.customs;
                                            if (t_child){
                                                $.each(t_child, function (keyc33, valuec33) {
                                                    $.each(row.shop,function(keyr,valuer){
                                                        if (valuec33.shop_id == valuer.id && valuec33.status == 1) {
                                                            if (valuer.minigame_module && valuer.minigame_module.length > 0){
                                                                if (valuer.minigame_module[0].status == 1){
                                                                }else {
                                                                    p_isCustom = true;
                                                                }
                                                            }else {
                                                                isStatus = 3;
                                                            }
                                                        }else {
                                                            if (valuer.minigame_module && valuer.minigame_module.length > 0){
                                                                if (valuer.minigame_module[0].status == 1){
                                                                }else {
                                                                    isStatus = 1;
                                                                }
                                                            }else {
                                                                isStatus = 3;
                                                            }
                                                        }
                                                    });
                                                })
                                            }
                                        })
                                    }
                                    if (p_isCustom){
                                        isStatus = 4;
                                    }
                                }else{
                                    var p_isCustom = false;
                                    if (s_childs && s_childs.length > 0){
                                        $.each(s_childs, function (keyc22, valuec22) {
                                            var t_child = valuec22.customs;
                                            if (t_child){
                                                $.each(t_child, function (keyc33, valuec33) {
                                                    $.each(row.shop,function(keyr,valuer){
                                                        if (valuec33.shop_id == valuer.id && valuec33.status == 1) {
                                                            if (valuer.minigame_module && valuer.minigame_module.length > 0){
                                                                if (valuer.minigame_module[0].status == 1){
                                                                }else {
                                                                    p_isCustom = true;
                                                                }
                                                            }else {
                                                                isStatus = 3;
                                                            }
                                                        }else {
                                                            if (valuer.minigame_module && valuer.minigame_module.length > 0){
                                                                if (valuer.minigame_module[0].status == 1){
                                                                }else {
                                                                    isStatus = 1;
                                                                }
                                                            }else {
                                                                isStatus = 3;
                                                            }
                                                        }
                                                    });
                                                })
                                            }
                                        })
                                        if (p_isCustom){
                                            isStatus = 4;
                                        }
                                    }else {
                                        $.each(row.shop,function(key,value){
                                            if (value.minigame_module && value.minigame_module.length > 0){
                                                if (value.minigame_module[0].status == 1){
                                                }else {
                                                    if (p_isCustom){
                                                        isStatus = 4;
                                                    }else {
                                                        isStatus = 1;
                                                    }
                                                }
                                            }else {
                                                isStatus = 3;
                                            }
                                        });
                                    }
                                }
                                if (isStatus == 1){
                                    return "<span class='switch switch-outline switch-icon switch-success switch-success-group-shop' data-shopgroup=" + groupid + "><label><input type='checkbox' name='status'/><span></span></label></span>";
                                }else if (isStatus == 4){
                                    return "<span class='switch switch-outline switch-icon switch-success switch-success-group-shop-replication' data-shopgroup=" + groupid + "><label><input type='checkbox' name='status'/><span></span></label></span>";
                                }
                                else if (isStatus == 3){
                                    return '';
                                }
                                else if (isStatus == 0) {
                                    return "<span class='switch switch-outline switch-icon switch-success switch-success-group-shop' data-shopgroup=" + groupid + "><label><input checked type='checkbox' name='status'/><span></span></label></span>";
                                }
                            }
                        },
                        { data: 'action',title:'Thao tác', orderable: false, searchable: false}
                    ],
                });
                var filter = function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    datatable.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                };
                //function update field
                datatable.on("change", ".update_field", function (e) {
                    e.preventDefault();
                    var field=$(this).data('field');
                    var id=$(this).data('id');
                    if(id==''){
                        return;
                    }
                    var required=$(this).data('required');
                    var value=$(this).val();
                    $.ajax({
                        type: "POST",
                        url: '{{route('admin.minigame-category.updatefieldcat')}}',
                        data: {
                            '_token':'{{csrf_token()}}',
                            'field':field,
                            'id':id,
                            'value':value,
                            'required' :required
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
                                toast(data.message, 'error');
                            }
                        },
                        error: function (data) {
                            toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                        },
                        complete: function (data) {
                        }
                    });
                });
            }
        });
        //THÊM SHOP
        $('body').on('click', '.add-webits', function(e) {
            e.preventDefault();
            let addwebits = $(this).data('shop');
            if (addwebits == 1){
                $("#dsShop").modal('show');
                if ($('#kt_datatable_addwebits').hasClass('test_addclass')){
                }else {
                    $('#kt_datatable_addwebits').addClass('test_addclass');
                    datatableadd = $('#kt_datatable_addwebits').DataTable({
                        responsive: true,
                        dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                            <'row'<'col-sm-12'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                        lengthMenu: [5, 20, 50, 150,400],
                        pageLength: 5,
                        language: {
                            'lengthMenu': 'Display _MENU_',
                        },
                        searchDelay: 500,
                        processing: true,
                        serverSide: true,
                        "order": [[1, "desc"]],
                        ajax: {
                            url: '{{url()->current()}}' + '?ajax=1&phanphoi=0&chgt=0&shop=1&ctchgt=0',
                            type: 'GET',
                            data: function (d) {
                                d.id = $('#id').val();
                                d.domain = $('#domain').val();
                                d.status = $('#status').val();
                                d.group_shop = $('#group_shop').val();
                                d.started_at = $('#started_at').val();
                                d.ended_at = $('#ended_at').val();

                            }
                        },
                        buttons: [
                        ],
                        columns: [
                            {
                                data: null,
                                title: '<label class="checkbox checkbox-lg checkbox-outline labelCheckAllAddShop"><input type="checkbox" id="btnCheckAllAddShop">&nbsp<span></span></label>',
                                orderable: false,
                                searchable: false,
                                width: "20px",
                                class: "ckb_item",
                                render: function (data, type, row) {
                                    return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item checkbox-item_'+ row.id +'"><input type="checkbox" data-id="' + row.id + '" id="" rel="' + row.id + '" id="">&nbsp<span></span></label>';
                                }
                            },
                            {data: 'id', title: 'ID'},
                            {
                                data: 'domain', title: '{{__('Tên shop')}}',
                                render: function (data, type, row) {
                                    return "<a href=\"https://"+ row.domain + "\" target='_blank'    >" + row.domain + "</a>";
                                }
                            },
                            {
                                data: 'status', title: '{{__('Trạng thái')}}',
                                render: function (data, type, row) {
                                    if (row.status == 1){
                                        return '<span class="badge badge-success">Hoạt động</span>';
                                    }else {
                                        return '<span class="badge badge-danger">Không hoạt động</span>';
                                    }
                                }
                            },
                        ],
                        "drawCallback": function (settings) {
                            var v_index = 0
                            $('#kt_datatable_addwebits_wrapper .dataTables_length').each(function () {
                                v_index = v_index + 1
                            })
                            if (v_index == 2){
                                $('#kt_datatable_addwebits_wrapper .dataTables_length').last().remove();
                            }
                            $('#kt_datatable_addwebits_wrapper #kt_datatable_addwebits_paginate').remove();
                        }
                    });
                    var filter = function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        datatable.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                    };
                    $('#kt_search_add-shop').on('click', function (e) {
                        e.preventDefault();
                        var params = {};
                        $('.datatable-input_add-shop').each(function () {
                            var i = $(this).data('col-index');
                            if (params[i]) {
                                params[i] += '|' + $(this).val();
                            } else {
                                params[i] = $(this).val();
                            }
                        });
                        $.each(params, function (i, val) {
                            // apply search params to datatable
                            datatableadd.column(i).search(val ? val : '', false, false);
                        });
                        datatableadd.table().draw();
                    });
                    $('#kt_reset_add-shop').on('click', function (e) {
                        e.preventDefault();
                        $('.datatable-input_add-shop').each(function () {
                            $(this).val('');
                            datatableadd.column($(this).data('col-index')).search('', false, false);
                        });
                        datatableadd.table().draw();
                    });
                    datatableadd.on("click", "#btnCheckAllAddShop", function () {
                        $("#kt_datatable_addwebits .ckb_item input[type='checkbox']").prop('checked', this.checked).change();
                    })
                    datatableadd.on("change", ".ckb_item input[type='checkbox']", function () {
                        if (this.checked) {
                            var currTr = $(this).closest("tr");
                            datatableadd.rows(currTr).select();
                        } else {
                            var currTr = $(this).closest("tr");
                            datatableadd.rows(currTr).deselect();
                        }
                    });
                    //function update field
                    datatableadd.on("change", ".update_field", function (e) {
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
                }
            }else if (addwebits == 2) {
                $("#dsNhomShop").modal('show');
            }else if (addwebits == 3){
                var check = checkValidate();
                if (check){
                }else {
                    return;
                }
                var p_data_shop = JSON.parse($('.c_data_shop').val());
                var p_parent = null;
                var p_customs = null
                if (p_data_shop){
                    p_parent = p_data_shop.parent;
                    if (p_parent){
                        p_customs = p_parent.customs;
                    }
                }
                let checkreplication = false;
                let nameshop
                if (p_customs) {
                    var p_isCustom = false;
                    $.each(p_customs, function (key22, value22) {
                        $(".checkbox-item-shop").each(function () {
                            if (this.checked){
                                nameshop = $(this).data('name');
                                let id = parseInt($(this).data('shop'));
                                if (value22.shop_id == id){
                                    checkreplication = true;
                                }
                            }
                        });
                    })
                }
                if (checkreplication){
                    if (confirm('' + nameshop + ' này đang phân bổ minigame gốc bạn có muốn kích hoạt ' + nameshop + ' này không?')) {}else {
                        let check = $(this).find('input');
                        check.prop("checked", false);
                        return false;
                    }
                }
                let isChecked = false;
                $(".checkbox-item-shop").each(function () {
                    if (this.checked){
                        isChecked = true;
                    }
                });
                if (!isChecked){
                    alert("Vui lòng chọn shop để thực hiện thao tác");
                    return false;
                }
                $("#activeShop").modal('show');
            }else if (addwebits == 4){
                let isChecked = false;
                $(".checkbox-item-shop").each(function () {
                    if (this.checked){
                        isChecked = true;
                    }
                });
                if (!isChecked){
                    alert("Vui lòng chọn shop để thực hiện thao tác");
                    return false;
                }
                $("#inActiveShop").modal('show');
            }
        })
        //Thêm shop khi created
        $('body').on('click', '.add-webits-created', function(e) {
            e.preventDefault();
            let addwebits = $(this).data('shop');
            if (addwebits == 1){
                $("#dsShopCreated").modal('show');
                if ($('#kt_datatable_addwebits_created').hasClass('test_addclass')){
                }else {
                    $('#kt_datatable_addwebits_created').addClass('test_addclass');
                    datatableaddcreated = $('#kt_datatable_addwebits_created').DataTable({
                        responsive: true,
                        dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                            <'row'<'col-sm-12'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                        lengthMenu: [5, 10, 20, 50,100],
                        pageLength: 10,
                        language: {
                            'lengthMenu': 'Display _MENU_',
                        },
                        searchDelay: 500,
                        processing: true,
                        serverSide: true,
                        "order": [[1, "desc"]],
                        ajax: {
                            url: '{{url()->current()}}' + '?ajax=1&phanphoi=0&chgt=0&shop=1&ctchgt=0',
                            type: 'GET',
                            data: function (d) {
                                d.id = $('#id').val();
                                d.domain = $('#domain').val();
                                d.status = $('#status').val();
                                d.position = $('#position').val();
                                d.started_at = $('#started_at').val();
                                d.ended_at = $('#ended_at').val();
                            }
                        },
                        buttons: [
                        ],
                        columns: [
                            {
                                data: null,
                                title: '<label class="checkbox checkbox-lg checkbox-outline labelCheckAllAddShop"><input type="checkbox" id="btnCheckAllAddShop">&nbsp<span></span></label>',
                                orderable: false,
                                searchable: false,
                                width: "20px",
                                class: "ckb_item",
                                render: function (data, type, row) {
                                    return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input type="checkbox" data-id="' + row.id + '" id="" rel="' + row.id + '" id="">&nbsp<span></span></label>';
                                }
                            },
                            {data: 'id', title: 'ID'},
                            {
                                data: 'domain', title: '{{__('Tên shop')}}',
                                render: function (data, type, row) {
                                    return "<a href=\"https://"+ row.domain + "\" target='_blank'    >" + row.domain + "</a>";
                                }
                            },
                            {
                                data: 'status', title: '{{__('Trạng thái')}}',
                                render: function (data, type, row) {
                                    if (row.status == 1){
                                        return '<span class="badge badge-success">Hoạt động</span>';
                                    }else {
                                        return '<span class="badge badge-danger">Không hoạt động</span>';
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
                    $('#kt_search_add-shop-created').on('click', function (e) {
                        e.preventDefault();
                        var params = {};
                        $('.datatable-input_add-shop-created').each(function () {
                            var i = $(this).data('col-index');
                            if (params[i]) {
                                params[i] += '|' + $(this).val();
                            } else {
                                params[i] = $(this).val();
                            }
                        });
                        $.each(params, function (i, val) {
                            // apply search params to datatable
                            datatableaddcreated.column(i).search(val ? val : '', false, false);
                        });
                        datatableaddcreated.table().draw();
                    });
                    $('#kt_reset_add-shop-created').on('click', function (e) {
                        e.preventDefault();
                        $('.datatable-input_add-shop-created').each(function () {
                            $(this).val('');
                            datatableaddcreated.column($(this).data('col-index')).search('', false, false);
                        });
                        datatableaddcreated.table().draw();
                    });
                    datatableaddcreated.on("click", "#btnCheckAllAddShop", function () {
                        $("#kt_datatable_addwebits_created .ckb_item input[type='checkbox']").prop('checked', this.checked).change();
                    })
                    datatableaddcreated.on("change", ".ckb_item input[type='checkbox']", function () {
                        if (this.checked) {
                            var currTr = $(this).closest("tr");
                            datatableaddcreated.rows(currTr).select();
                        } else {
                            var currTr = $(this).closest("tr");
                            datatableaddcreated.rows(currTr).deselect();
                        }
                    });
                    //function update field
                    datatableaddcreated.on("change", ".update_field", function (e) {
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
                }
            }else if (addwebits == 2) {
                $("#dsNhomShopCreated").modal('show');
            }else if (addwebits == 3){
                var isDeleteShopCreated = false;
                $('#kt_datatable_created .item-created-checkbox').each(function(){
                    if (this.checked){
                        isDeleteShopCreated = true
                    }
                });
                if (!isDeleteShopCreated){
                    alert("Vui lòng chọn shop")
                    return false;
                }
                $("#deleteShopCreated").modal('show');
            }
        })
        function updatechgt(e){
            var result = true;
            e.preventDefault();
            var allSelected = '';
            if(datatablechgt!=undefined){
                var total = datatablechgt.$('tbody tr').length;
                if(total>0){
                    var check = false;
                    var item = null;
                    //check so nguyen
                    datatablechgt.$('input[type=number]').each(function(){
                        if($(this).val()!='' && !$(this).val().match(/^\d+$/)){
                            $(this).focus();
                            item = $(this);
                            check = true;
                            return;
                        }
                    })
                    if(check){
                        toastr.options =
                            {
                                "closeButton" : true,
                                "progressBar" : true,
                                'positionClass' : 'toast-top-center',
                            }
                        toastr.error("Vui lòng nhập số nguyên dương");
                        item.focus();
                        result = false;
                    }
                    datatablechgt.$('input[data-required=1]').each(function(){
                        if($(this).val() == ""){
                            $(this).focus();
                            item = $(this);
                            check = true;
                            return;
                        }
                    });
                    if(check){
                        toastr.options =
                            {
                                "closeButton" : true,
                                "progressBar" : true,
                                'positionClass' : 'toast-top-center',
                            }
                        toastr.error("Chưa nhập mục bắt buộc");
                        item.focus();
                        result = false;
                    }
                    //check phan tram
                    var total = 0;
                    datatablechgt.$('input.percent').each(function(){
                        if($(this).val() != ""){
                            total = total + parseInt($(this).val());
                            item = $(this);
                        }
                    });
                    if(total != 100){
                        toastr.options =
                            {
                                "closeButton" : true,
                                "progressBar" : true,
                                'positionClass' : 'toast-top-center',
                            }
                        item.focus();
                        toastr.error("Tổng tỷ lệ chơi thật phải là 100%");
                        result = false;
                    }
                    //check phan tram chơi thử
                    var total = 0;
                    datatablechgt.$('input.try_percent').each(function(){
                        if($(this).val() != ""){
                            total = total + parseInt($(this).val());
                            item = $(this);
                        }
                    });
                    if(total != 100){
                        toastr.options =
                            {
                                "closeButton" : true,
                                "progressBar" : true,
                                'positionClass' : 'toast-top-center',
                            }
                        item.focus();
                        toastr.error("Tổng tỷ lệ chơi thử phải là 100%");
                        result = false;
                    }
                    //check trung vi tri
                    var check = false;
                    var item = null;
                    var seen='';
                    datatablechgt.$('input.order').each(function(){
                        var see=$(this).val();
                        if(seen.match(see)){
                            item = $(this);
                            check = true;
                            result = false;
                        }
                        else{
                            seen=seen+$(this).val();
                        }
                    });
                    if(check){
                        toastr.options =
                            {
                                "closeButton" : true,
                                "progressBar" : true,
                                'positionClass' : 'toast-top-center',
                            }
                        toastr.error("Vị trí không được trùng nhau");
                        item.focus();
                        result = false;
                    }
                    var inputArray = [];
                    datatablechgt.$('tbody tr').each(function (index, elem)  {
                        var curentRow = $(this);
                        var id = curentRow.find('.id').val();
                        var limit = curentRow.find('.limit').val();
                        var bonus_from = curentRow.find('.bonus_from').val();
                        var bonus_to = curentRow.find('.bonus_to').val();
                        var percent = curentRow.find('.percent').val();
                        var try_percent = curentRow.find('.try_percent').val();
                        var nohu_percent = curentRow.find('.nohu_percent').val();
                        var order = curentRow.find('.order').val();
                        var title = curentRow.find('.title').val();
                        var image = curentRow.find('.image').val();
                        var special = curentRow.find('.special').val();
                        var iditemset = curentRow.find('.iditemset').val();
                        inputArray.push({
                            'id':id,
                            'iditemset':iditemset,
                            'params':{
                                'limit':limit,
                                'bonus_from':bonus_from,
                                'bonus_to':bonus_to,
                                'percent':percent,
                                'try_percent':try_percent,
                                'nohu_percent':nohu_percent,
                                'special':special
                            },
                            'order':order,
                            'title':title,
                            'image':image
                        })
                    })
                    $('#datasetitem').val(JSON.stringify(inputArray));
                    if(result){
                        fnSetitemUpdate($('#datasetitem').val());
                    }
                }
            }
            return result;
        }
        //Setitem khi mở popup giải thưởng
        function fnSetitemNew(data){
            $.ajax({
                url: '/admin/minigame-category/{{isset($data)?$data->id:""}}/setitem',
                datatype:'json',
                data:{
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    data: JSON.parse(JSON.stringify(data)),
                    type: "ctchgt"
                },
                type: 'post',
                success: function (data) {
                    if(data.success){
                        datatablechgt.table().draw();
                        var st_position = parseInt($('.t_position').val());
                        var st_count_giaithuong = parseInt($('.l_count_giaithuong').val());
                        if (st_position == st_count_giaithuong){
                            $('button[aria-controls="kt_datatable_chgt"]').addClass('daduvatpham');
                            $('button[aria-controls="kt_datatable_chgt"]').css('background','#e4e6ef');
                            $('button[aria-controls="kt_datatable_chgt"]').css('border-color','#e4e6ef');
                        }
                        {{--$('.datatable-input_chgt').each(function () {--}}
                        {{--    $(this).val('');--}}
                        {{--    datatablechgt.column($(this).data('col-index')).search('', false, false);--}}
                        {{--});--}}
                        {{--$('#kt_datatable_chgt .dataTables_empty').parent().remove();--}}
                        {{--$.each(data.data,function(keyd,row){--}}
                        {{--    var name_vp = '';--}}
                        {{--    $.each(row.groups, function (indexi, valuei) {--}}
                        {{--        if (valuei.name == 'admin') {--}}
                        {{--            name_vp = valuei.title;--}}
                        {{--        } else {--}}
                        {{--            name_vp = valuei.title;--}}
                        {{--        }--}}
                        {{--    });--}}
                        {{--    var name_tvp = '';--}}
                        {{--    if(row.params.gift_type == 0){--}}
                        {{--        name_tvp =  '{{config('module.minigame.gift_type.0')}}';--}}
                        {{--    }else if(row.params.gift_type == 1){--}}
                        {{--        name_tvp =  '{{config('module.minigame.gift_type.1')}}';--}}
                        {{--    }--}}
                        {{--    var image = '';--}}
                        {{--    if(row.image=="" || row.image==null){--}}
                        {{--        image =  "/assets/backend/themes/images/empty-photo.jpg";--}}
                        {{--    }--}}
                        {{--    else{--}}
                        {{--        image =  row.image_custom;--}}
                        {{--    }--}}
                        {{--    var html = '';--}}
                        {{--    html =`<tr class="odd">--}}
                        {{--                    <td><span class="item_datatable item_datatable_${ row.children.length>0?row.children[0].id:'' }">${ row.id }</span><input type="hidden" class="t_ds-checkbox-item"></td>--}}
                        {{--                    <td>${ row.title }</td>--}}
                        {{--                    <td>--}}
                        {{--                        ${ row.title_custom }--}}
                        {{--                        <input type="hidden" class="update_field id" value="${ row.id }">--}}
                        {{--                        <input type="hidden" class="update_field title" value="${ row.title_custom }">--}}
                        {{--                        <input type="hidden" class="update_field image" value="${ row.image_custom }">--}}
                        {{--                        <input type="hidden" class="iditemset" name="iditemset" value="${ row.children.length>0?row.children[0].id:'' }">--}}
                        {{--                    </td>--}}
                        {{--                    <td>--}}
                        {{--                        <span class="label label-pill label-inline label-center mr-2  label-success ">${ name_vp }</span><br>--}}
                        {{--                    </td>--}}
                        {{--                    <td>--}}
                        {{--                        ${ row.params.value }--}}
                        {{--                    </td>--}}
                        {{--                    <td>--}}
                        {{--                        <input class="update_field order" data-field="order" data-required="1" data-id="${ row.children.length>0?row.children[0].id:'' }" type="number" min="1" value="0" style="width:40px">--}}
                        {{--                    </td>--}}
                        {{--                    <td>--}}
                        {{--                        <input class="update_field percent" data-field="percent" data-required="1" data-id="${ row.children.length>0?row.children[0].id:'' }" type="number" min="1" value="0" style="width:55px">--}}
                        {{--                    </td>--}}
                        {{--                    <td>--}}
                        {{--                        <input class="update_field try_percent" data-field="try_percent" data-required="1" data-id="${ row.children.length>0?row.children[0].id:'' }" type="number" min="1" value="0" style="width:55px">--}}
                        {{--                    </td>--}}
                        {{--                    <td>--}}
                        {{--                        <img class="image-item" src="${ image }" style="max-width: 70px">--}}
                        {{--                    </td>--}}
                        {{--                    <td>--}}
                        {{--                        <a rel="${ row.id }" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger" onclick="customShow('${ row.children.length>0?row.children[0].id:'' }','${ row.id_custom }','${ row.title }','${ row.image }')" title="Sửa riêng cho shop">--}}
                        {{--                            <i class="la la-edit"></i>--}}
                        {{--                        </a>--}}
                        {{--                        <a rel="${ row.children.length>0?row.children[0].id:'' }" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle" data-toggle="modal" data-target="#deleteItemModal" title="Xóa">--}}
                        {{--                            <i class="la la-trash"></i>--}}
                        {{--                        </a>--}}
                        {{--                    </td>--}}
                        {{--                </tr>`;--}}
                        {{--    $('#kt_datatable_chgt tbody').append(html);--}}
                        {{--});--}}
                        {{--$.each(data.arr_delete_item_id,function(keyitem,rowitem){--}}
                        {{--    $('.item_datatable_' + rowitem + '').parent().parent().remove();--}}
                        {{--})--}}
                        {{--var c_index = 0;--}}
                        {{--$('#kt_datatable_chgt .t_ds-checkbox-item').each(function(){--}}
                        {{--    c_index = c_index + 1;--}}
                        {{--});--}}
                        {{--var m_position = $('.t_position').val();--}}
                        {{--var m_ton = m_position - c_index;--}}
                        {{--var m_val = parseInt(c_index)/parseInt(m_position);--}}
                        {{--m_val = Math.round( m_val*100 );--}}
                        {{--if (c_index == 0){--}}
                        {{--    var c_ton = $('.t_position').val();--}}
                        {{--    var htmlno = '';--}}
                        {{--    htmlno += '<tr class="group total-allpage">';--}}
                        {{--    htmlno += '<td colspan="10"><b class="total_vp">Tổng: <span style="color: #434657;font-weight: 600">' + 0 + '% (Thiếu ' + c_ton + ' vật phẩm)</span></b></td>';--}}
                        {{--    htmlno += '</tr>';--}}
                        {{--    htmlno += '<tr class="odd"><td valign="top" colspan="15" class="dataTables_empty">No data available in table</td></tr>';--}}
                        {{--    $('#kt_datatable_chgt tbody').html(htmlno);--}}
                        {{--}else{--}}
                        {{--    if (m_ton > 0){--}}
                        {{--        var m_html = 'Tổng: ' + m_val + '% (Thiếu ' + m_ton + ' vật phẩm)';--}}
                        {{--        $('#kt_datatable_chgt .total_vp').html(m_html);--}}
                        {{--    }else{--}}
                        {{--        var m_html = 'Tổng: ' + m_val + '%';--}}
                        {{--        $('#kt_datatable_chgt .total_vp').html(m_html);--}}
                        {{--    }--}}
                        {{--}--}}
                        // datatablechgt.table().draw();
                        $("#dsGiaiThuong").modal('toggle');
                    }
                    toast(data.message);
                }
            })
        }
        //Setitem trong list cấu hình giải thưởng
        function fnSetitemUpdate(data){
            $.ajax({
                url: '/admin/minigame-category/{{isset($data)?$data->id:""}}/setitem',
                datatype:'json',
                data:{
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    data: data,
                    type: "chgt"
                },
                type: 'post',
                success: function (data) {
                    toast(data.message);
                }
            })
        }
        //Funtion web ready state
        jQuery(document).ready(function () {
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
            $('#deleteItemModal').on('show.bs.modal', function(e) {
                //get data-id attribute of the clicked element
                var item_id = $(e.relatedTarget).attr('rel')
                $('#deleteItemModal .item_id').attr('value', item_id);
            });
            $('.deleteItem').click(function(){
                $.ajax({
                    url: '/admin/minigame-category/deleteitem',
                    datatype:'json',
                    data:{
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        data: {
                            id: $('#deleteItemModal .item_id').val(),
                        }
                    },
                    type: 'post',
                    success: function (data) {
                        if(!data.success){
                            toast(data.message, 'error');
                        }
                        $('.flastPosition').val(1);
                        var g_so_giai_thuong = $('.so-giai-thuong').text();
                        g_so_giai_thuong = parseInt($('.so-giai-thuong').text()) - 1;
                        var l_count_giaithuong = parseInt($('.l_count_giaithuong').val());
                        l_count_giaithuong = l_count_giaithuong - 1;
                        $('.l_count_giaithuong').val(l_count_giaithuong);
                        $('.so-giai-thuong').html(g_so_giai_thuong);
                        $('button[aria-controls="kt_datatable_chgt"]').removeClass('daduvatpham');
                        $('button[aria-controls="kt_datatable_chgt"]').css('background','#d7dae7');
                        $('button[aria-controls="kt_datatable_chgt"]').css('border-color','#d7dae7');
                        datatablechgt.table().draw();
                        if(datatablectchgt!=undefined){
                            datatablectchgt.table().draw();
                        }
                        $('#deleteItemModal').modal('hide');
                    }
                })
            })
            $('.btn_custom_update').click(function(){
                //toast('');
                // if($('#customModal #title_custom').val()==''){
                //     $('#customModal #title_custom').focus();
                //     toast('Vui lòng nhập tên giải thưởng', 'error');
                //     return;
                // }
                // if($('#customModal #image_custom').val()==''){
                //     toast('Vui lòng chọn ảnh giải thưởng', 'error');
                //     return;
                // }
                $.ajax({
                    url: '/admin/minigame-category/setcustom',
                    datatype:'json',
                    data:{
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        data: {
                            parent_id: $('#customModal #parent_id').val(),
                            id: $('#customModal #id_custom').val(),
                            title: $('#customModal #title_custom').val(),
                            image: $('#customModal #image_custom').val(),
                            title_minigame: $('#customModal #title_minigame').val(),
                            image_minigame: $('#customModal #image_minigame').val()
                        }
                    },
                    type: 'post',
                    success: function (data) {
                        if(data.success){
                            toast(data.message);
                        }else{
                            toast(data.message, 'error');
                        }
                        datatablechgt.table().draw();
                        $('#customModal').modal('hide');
                    }
                })
            })
            $('body').on('change','#select-client',function(){
                if($('#select-client').val()!='{{session("shop_id")}}'){
                    if($('#datachange').val() == 1){
                        if(confirm('Thông tin đang được chỉnh sửa sẽ được lưu khi chuyển điểm bán. bạn có muốn chuyển không?!')){
                            var id = $(this).val();
                            var route = '{{ $route??'' }}';
                            $.ajax({
                                type: "POST",
                                url: "{{route('admin.shop.switch')}}",
                                data: {
                                    '_token':'{{csrf_token()}}',
                                    'id':id,
                                },
                                beforeSend: function (xhr) {
                                    $(this).prop('disabled', true);
                                },
                                success: function (data) {
                                    if (data.status == 1) {
                                        toast(data.message);
                                        if(data.redirect){
                                            window.location.href = route+ '&shop_id='+id;
                                            // window.location.reload();
                                        }
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
                                    $(this).prop('disabled', false);
                                }
                            });
                        }else{
                            $('#select-client').val('{{session("shop_id")}}').change();
                        }
                    }else{
                        var id = $(this).val();
                        var route = '{{ $route??'' }}';
                        $.ajax({
                            type: "POST",
                            url: "{{route('admin.shop.switch')}}",
                            data: {
                                '_token':'{{csrf_token()}}',
                                'id':id,
                            },
                            beforeSend: function (xhr) {
                                $(this).prop('disabled', true);
                            },
                            success: function (data) {
                                if (data.status == 1) {
                                    toast(data.message);
                                    if(data.redirect){
                                        window.location.href = route+ '&shop_id='+id;
                                        // window.location.reload();
                                    }
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
                                $(this).prop('disabled', false);
                            }
                        });
                    }
                }
            })
        });
        function customShow(parent_id, id, title_old, image_old,title_custom,image_custom){
            $('#customModal #id_custom').val(id);
            $('#customModal #parent_id').val(parent_id);
            $('#customModal #title_minigame').val(title_old);
            $('#customModal #image_minigame').val(image_old);
            $('#customModal .image_minigame').attr('src',image_old);
            $('#customModal #title_custom').val(title_custom);
            $('#customModal #image_custom').val(image_custom);
            $('#customModal .image_custom').attr('src',image_custom);
            $('#customModal').modal('show');
        }
    </script>
    <link href="/assets/backend/assets/css/replication.css?v={{time()}}" rel="stylesheet" type="text/css"/>

    <style>
        #kt_datatable_ctchgt thead {
            position: sticky;
            top: 0px;
            z-index: 11;
            background: #fff;
        }
        #kt_datatable_addwebits thead {
            position: sticky;
            top: -4px;
            z-index: 11;
            background: #fff;
        }
        #kt_datatable_ctchgt .btn-secondary{
            position: sticky;
            top: 40px;
            z-index: 22;
        }
    </style>
@endsection
