{{-- Extends layout --}}
@extends('admin._layouts.master')
@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.'.$module.'.index')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>
        @if (isset($data) && isset($data->title) && auth()->user()->can('client-get-partner'))
            <a href="{{route('admin.'.$module.'.partner',$data->id)}}"
               class="btn btn-danger font-weight-bolder mr-2">
                <i class="fab fa-avianex"></i>
                Tạo TT NCC
            </a>
        @endif
        <div class="btn-group">
            <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain"
                    data-submit-close="1">
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
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab-content-general">
                <span class="nav-icon">
                    <i class="flaticon-menu-2"></i>
                </span>
                <span class="nav-text">Thông tin chung</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-content-notify">
                <span class="nav-icon">
                    <i class="flaticon-alarm"></i>
                </span>
                <span class="nav-text">Thông báo</span>
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active show" id="tab-content-general" role="tabpanel">
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
                                <label class="col-12">{{ __('Nhóm shop') }} <span
                                        style="color: #FF4747">*</span></label>
                                <div class="col-6">
                                    <select class="form-control select2" name="group_id">
                                        <option value="">-- Không chọn --</option>
                                        @foreach ($dataCategory as $item)
                                            <option
                                                value="{{$item->id}}" {{isset($data->group_id) && $data->group_id == $item->id ? 'selected' : null}}>{{$item->title}}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('group_id'))
                                        <div class="form-text text-danger">{{ $errors->first('group_id') }}</div>
                                    @endif
                                </div>
                            </div>
                            {{-----title------}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label>{{ __('Tiêu đề') }} <span style="color: #FF4747">*</span></label>
                                    <input type="text" name="title"
                                           value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus
                                           placeholder="{{ __('Tiêu đề') }}" maxlength="120"
                                           class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}">
                                    @if ($errors->has('title'))
                                        <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-----domain------}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label>{{ __('Tên shop') }} <span style="color: #FF4747">*</span></label>
                                    <input type="text" name="domain"
                                           value="{{ old('domain', isset($data) ? $data->domain : null) }}" autofocus
                                           placeholder="{{ __('domain') }}" maxlength="120"
                                           class="form-control {{ $errors->has('domain') ? ' is-invalid' : '' }}">
                                    @if ($errors->has('domain'))
                                        <span class="form-text text-danger">{{ $errors->first('domain') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                {{-----type_information------}}
                                <div class="col-12 col-md-6">
                                    <label for="title">{{ __('Phân loại:')}}</label>
                                    {{Form::select('type_information',[''=>'-- Chưa chọn thông tin --']+config('module.shop.type_information'),old('type_information', isset($data) ? $data->type_information : null),array('class'=>'form-control'))}}
                                    @if($errors->has('type_information'))
                                        <div class="form-control-feedback">{{ $errors->first('type_information') }}</div>
                                    @endif
                                    @if ($errors->has('type_information'))
                                        <span class="form-text text-danger">{{ $errors->first('type_information') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-----id_transfer------}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label>{{ __('ID Nạp Ví ATM tự động') }} <span
                                            style="color: #FF4747">*</span></label>
                                    <input type="text" name="id_transfer"
                                           value="{{ old('id_transfer', isset($data) ? $data->id_transfer : null) }}"
                                           autofocus
                                           placeholder="{{ __('id_transfer') }}" maxlength="120"
                                           class="form-control {{ $errors->has('id_transfer') ? ' is-invalid' : '' }}">
                                    @if ($errors->has('id_transfer'))
                                        <span class="form-text text-danger">{{ $errors->first('id_transfer') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-----key_transfer------}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label>{{ __('Key Nạp Ví ATM tự động') }} <span
                                            style="color: #FF4747">*</span></label>
                                    <input type="text" name="key_transfer"
                                           value="{{ old('key_transfer', isset($data) ? $data->key_transfer : null) }}"
                                           autofocus
                                           placeholder="{{ __('key_transfer') }}" maxlength="120"
                                           class="form-control {{ $errors->has('key_transfer') ? ' is-invalid' : '' }}">
                                    @if ($errors->has('key_transfer'))
                                        <span class="form-text text-danger">{{ $errors->first('key_transfer') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-----ratio_atm------}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label>{{ __('Chiết khấu nạp ATM tự động') }} <span style="color: #FF4747">*</span></label>
                                    <input type="text" name="ratio_atm"
                                           value="{{ old('ratio_atm', isset($data) ? $data->ratio_atm : null) }}"
                                           autofocus
                                           placeholder="{{ __('ratio_atm') }}" maxlength="120"
                                           class="form-control {{ $errors->has('ratio_atm') ? ' is-invalid' : '' }}">
                                    @if ($errors->has('ratio_atm'))
                                        <span class="form-text text-danger">{{ $errors->first('ratio_atm') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                {{-----is_get_data------}}
                                <div class="col-12 col-md-6">
                                    <label for="title">{{ __('Truy xuất thông tin người dùng từ hệ thống cũ:')}}</label>
                                    {{Form::select('is_get_data',config('module.shop.is_get_data'),old('is_get_data', isset($data) ? $data->is_get_data : null),array('class'=>'form-control'))}}
                                    @if($errors->has('is_get_data'))
                                        <div class="form-control-feedback">{{ $errors->first('is_get_data') }}</div>
                                    @endif
                                    @if ($errors->has('is_get_data'))
                                        <span class="form-text text-danger">{{ $errors->first('is_get_data') }}</span>
                                    @endif
                                </div>
                            </div>
                               {{-----EndPoint------}}
                               <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label>{{ __('End point truy xuất dữ liệu người dùng') }} <span style="color: #FF4747">*</span></label>
                                    <input type="text" name="url_get_data"
                                           value="{{ old('url_get_data', isset($data) ? $data->url_get_data : null) }}" autofocus
                                           placeholder="{{ __('End point truy xuất dữ liệu người dùng') }}" maxlength="120"
                                           class="form-control {{ $errors->has('url_get_data') ? ' is-invalid' : '' }}">
                                    @if ($errors->has('url_get_data'))
                                        <span class="form-text text-danger">{{ $errors->first('url_get_data') }}</span>
                                    @endif
                                </div>
                            </div>
                            @if (isset($data))
                                {{-----id_hash------}}
                                <div class="form-group row">
                                    <div class="col-12 col-md-12">
                                        <label>{{ __('ID Hash') }}</label>
                                        <input type="text" value="{{ old('id', isset($data) ? md5($data->id) : null) }}"
                                               class="form-control" readonly>
                                    </div>
                                </div>
                                {{-----secret_key------}}
                                <div class="form-group row">
                                    <div class="col-12 col-md-12">
                                        <label>{{ __('Secret Key') }}</label>
                                        <div class="input-group">
                                            <input type="text" id="value_secret_key"
                                                   value="{{ old('secret_key', isset($data) ? $data->secret_key : null) }}"
                                                   autofocus
                                                   placeholder="{{ __('secret_key') }}" readonly
                                                   class="form-control {{ $errors->has('secret_key') ? ' is-invalid' : '' }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" id="add_secret_key"
                                                        data-id="{{$data->id}}">Tạo mã
                                                </button>
                                            </div>
                                        </div>
                                        @if ($errors->has('secret_key'))
                                            <span
                                                class="form-text text-danger">{{ $errors->first('secret_key') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            {{-----role_ids------}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label for="locale">{{ __('Chọn nhóm vai trò dành cho shop') }}:</label>
                                    <select name="role_ids[]" multiple="multiple" title=""
                                            class="form-control select2 col-md-5"
                                            data-placeholder="{{__('Chọn vai trò')}}" id="kt_select2_2"
                                            style="width: 100%">
                                        @if( !empty(old('role_id')) )
                                            {!!\App\Library\Helpers::buildMenuDropdownList($roles,old('role_ids')) !!}
                                        @else
                                            @if(isset($data))
                                                {!!\App\Library\Helpers::buildMenuDropdownList($roles,$shop_access??[]) !!}
                                            @else
                                                {!!\App\Library\Helpers::buildMenuDropdownList($roles,null) !!}
                                            @endif
                                        @endif
                                    </select>
                                    @if($errors->has('role_ids'))
                                        <div
                                            class="form-control-feedback text-danger">{{ $errors->first('role_ids') }}</div>
                                    @endif
                                </div>
                            </div>

                            {{--             Chọn theme     --}}
                            @if(isset($theme))
                                <div class="form-group m-form__group ">
                                    <label for="sys_theme_config_theme">Chọn theme:</label>
                                    <div class="row" style="width: 100%;margin: 0 auto">
                                        <div class="col-md-6 pl-0 pr-0">
                                            <select name="theme_id" class="form-control col-md-12" id="theme_id">
                                                <option value=""> === Chọn theme cho shop ===</option>
                                                @foreach($theme as $index=>$item)
                                                    <option value="{{$item->id}}">{{$item->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if(isset($key_theme))
                                            <div class="col-auto" style="padding-left: 16px;margin-top: 8px">
                                                <a class="onclickshowclone" href="http://review.nick.vn/"
                                                   target="_blank" data-title="Theme"
                                                   data-theme="{{ isset($themeclient) ? $themeclient->theme_id : '' }}">Xem
                                                    thử</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-----note------}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label for="locale">{{ __('Mô tả') }}:</label>
                                    <textarea id="note" name="note" class="form-control" data-height="150"
                                              data-startup-mode="">{{ old('note', isset($data) ? $data->note : null) }}</textarea>
                                    @if ($errors->has('note'))
                                        <span class="form-text text-danger">{{ $errors->first('note') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <div class="checkbox-list">
                                        <label class="checkbox">
                                            <input type="checkbox" id="btn-clone" name="is_clone" value="1">
                                            <span></span>Lấy cấu hình từ shop khác</label>
                                    </div>
                                </div>
                            </div>
                            <div class="body-shop-clone" style="display: none">
                                <div class="form-group row">
                                    <label class="col-12">{{ __('Chọn shop cấu hình') }} :</label>
                                    <div class="col-6">
                                        <select class="form-control" id="shop_clone" name="shop_id_clone">
                                            <option value="">Chưa chọn shop cấu hình</option>
                                            @if (isset($shop) && count($shop) > 0)
                                                @foreach ($shop as $item)
                                                    <option value="{{$item->id}}">{{$item->title}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Các module cần lấy cấu hình:</label><br>
                                    <div class="attribute-box">
                                        <ul id="auto-checkboxes" class="list-unstyled checktree">
                                            <div class="checkbox-list">
                                                @if (config('module.shop.clone_module') && count(config('module.shop.clone_module')) > 0)
                                                    @php
                                                        $clone_module = config('module.shop.clone_module');
                                                    @endphp
                                                    @foreach ($clone_module as $key => $item)
                                                        <label class="checkbox">
                                                            <input type="checkbox" name="clone_module[]"
                                                                   class="moule_shop" value="{{$item['key']}}">
                                                            <span></span>
                                                            {{$item['title']}}
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </ul>
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
                            @if(isset($data))
                                <div class="form-group row">
                                    <div class="col-12 col-md-12">
                                        <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                                        {{Form::select('status',(config('module.'.$module.'.status')??[]) ,old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
                                        @if($errors->has('status'))
                                            <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="form-group row">
                                    <div class="col-12 col-md-12">
                                        <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                                        <input type="text" class="form-control" disabled value="Ngừng hoạt động">
                                    </div>
                                </div>
                            @endif
                            {{-- order --}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label for="order">{{ __('Thứ tự') }}</label>
                                    <input type="text" name="order"
                                           value="{{ old('order', isset($data) ? $data->order : null) }}"
                                           placeholder="{{ __('Thứ tự') }}"
                                           class="form-control {{ $errors->has('order') ? ' is-invalid' : '' }}">
                                    @if ($errors->has('order'))
                                        <span class="form-text text-danger">{{ $errors->first('order') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- expired_time --}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label>{{ __('Thời gian hết hạn') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                            name="expired_time"
                                            value="{{ old('expired_time', isset($data->expired_time) ?  Carbon\Carbon::parse($data->expired_time)->format('d/m/Y H:i:s') : null) }}"
                                            placeholder="{{ __('Thời gian hết hạn') }}" autocomplete="off"
                                            data-toggle="datetimepicker">

                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="la la-calendar"></i></span>
                                        </div>
                                    </div>
                                    @if($errors->has('expired_time'))
                                        <div class="form-control-feedback">{{ $errors->first('expired_time') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-custom gutter-b">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">
                                    Tracking <i class="mr-2"></i>
                                </h3>
                            </div>
                        </div>
                        <div class="card-body">

                            {{-- status --}}
                            @if(isset($data))
                                @php

                                    $track = null;
                                    if (isset($data->param_tracking)){
                                        $params = json_decode($data->param_tracking);
                                        if (isset($params->tracking)){
                                            $track = json_decode($params->tracking);
                                        }
                                    }

                                @endphp
                                <div class="form-group row">
                                    <div class="col-12 col-md-12">
                                        <label for="status" class="form-control-label">{{ __('Chọn group') }}</label>
                                        <select class="form-control select2" name="group_tracking">
                                            <option value="">-- Không chọn --</option>
                                            @foreach ($trackings as $key => $tracking)
                                                @if(isset($track))
                                                    @if($track->group == $tracking)
                                                        <option selected value="{{ $tracking }}">{{ $tracking }}</option>
                                                    @else
                                                        <option value="{{ $tracking }}">{{ $tracking }}</option>
                                                    @endif
                                                @else
                                                    <option value="{{ $tracking }}">{{ $tracking }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-12 col-md-12">
                                        <label for="order">{{ __('Trạng thái tracking') }}</label>
                                        <select class="form-control select2" name="status_tracking">
                                            @if(isset($track))
                                                @if($track->status == 1)
                                                    <option value="">-- Không chọn --</option>
                                                    <option selected value="1">Theo dõi</option>
                                                    <option value="2">Không theo dõi</option>
                                                @elseif($track->status == 2)
                                                    <option value="">-- Không chọn --</option>
                                                    <option value="1">Theo dõi</option>
                                                    <option selected value="2">Không theo dõi</option>
                                                @else
                                                    <option value="">-- Không chọn --</option>
                                                    <option value="1">Theo dõi</option>
                                                    <option value="2">Không theo dõi</option>
                                                @endif
                                            @else
                                                <option value="">-- Không chọn --</option>
                                                <option value="1">Theo dõi</option>
                                                <option value="2">Không theo dõi</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
        <div class="tab-pane fade" id="tab-content-notify" role="tabpanel">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            Danh sách thông báo
                        </h3>
                    </div>
                    <div class="card-toolbar"></div>
                </div>
                @if(isset($data))
                    <div class="card-body" id="table-group-notify">
                        <div class="h5 font-weight-boldest mb-3">Báo cáo hàng ngày</div>
                        <table class="table table-bordered table-hover table-checkable dataTable no-footer dtr-inline">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên nhóm</th>
                                <th>Thao tác</th>
                            </tr>
                            </thead>
                            @php
                                $telegram_groups = json_decode($data->telegram_config);
                            @endphp
                            <tbody>
                            @if($telegram_groups)
                                @forelse($telegram_groups as $key => $group)
                                    <tr data-index="{{ $group->order }}">
                                        <td>{{ $group->group_id }}</td>
                                        <td>{{ $group->group_name }}</td>
                                        <td nowrap="nowrap">
                                            <a href="#collapse-{{ $group->order }}" class="btn btn-sm btn-clean btn-icon" data-toggle="collapse">
                                                <i class="la la-edit"></i>
                                            </a>
                                            <a href="#modal-confirm-delete" class="btn btn-sm btn-clean btn-icon delete-group" data-toggle="modal" title="Delete" data-index="{{ $group->order }}">
                                                <i class="la la-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr data-index="{{ $group->order }}">
                                        <td colspan="3" class="py-0 border-0">
                                            <div class="collapse p-3" id="collapse-{{ $group->order }}" data-parent="#table-group-notify">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-12 col-lg-6">
                                                            <div class="form-group row">
                                                                <label class="col-2 col-form-label">ID Group <span class="text-danger">(*)</span>:</label>
                                                                <div class="col-10">
                                                                    <input class="form-control icon-error" type="text" placeholder="ID Group" name="group_id" value="{{ $group->group_id }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div class="form-group row">
                                                                <label class="col-2 col-form-label">Tên Group <span class="text-danger">(*)</span>:</label>
                                                                <div class="col-10">
                                                                    <input class="form-control icon-error" type="text" placeholder="Tên Group" name="group_name" value="{{ $group->group_name }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div class="form-group row">
                                                                <label class="col-2 col-form-label">Nội dung:</label>
                                                                <div class="col-10">
                                                                    <a href="#modal-detail-info-group" type="button" class="btn btn-success btn-shadow-hover font-weight-bold w-100 " data-toggle="modal" data-index="{{ $group->order }}">Custom nội dung thông báo</a>
                                                                    <input type="hidden" value="{{ $group->config }}" id="config-group-{{ $group->order }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div class="form-group row">
                                                                <label class="col-2 col-form-label">Kiếm tra:</label>
                                                                <div class="col-10">
                                                                    <button type="button" class="btn btn-success btn-shadow-hover font-weight-bold w-100 send-msg-demo"
                                                                            data-order="{{ $group->order }}" {{ !$data->status || !$group->status ? 'disabled' : '' }}>
                                                                        Gửi tin nhắn mẫu
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div class="form-group row">
                                                                <label class="col-2 col-form-label">Nhận thông báo:</label>
                                                                <div class="col-10">
                                                        <span class="switch switch-outline switch-icon switch-success">
                                                            <label>
                                                                <input type="checkbox" name="status" {{ $group->status ? 'checked' : '' }}>
                                                                <span></span>
                                                            </label>
                                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <div class="d-flex justify-content-end">
                                                                <a href="#collapse-{{ $group->order }}" type="button" class="btn btn-outline-danger mr-2" data-toggle="collapse">Huỷ</a>
                                                                <button type="button" class="btn btn-success update-info-group" role="button" data-index="{{ $group->order }}">Lưu thông tin</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            @else
                            @endif

                            </tbody>
                        </table>
                        <a href="#collapse-add-new-group" data-toggle="collapse" class="d-flex align-items-center"><i class="flaticon-plus mr-3" style="color:unset"></i>Thêm group nhận thông báo</a>
                        <div class="collapse p-3" id="collapse-add-new-group" data-parent="#table-group-notify">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">ID Group <span class="text-danger">(*)</span>:</label>
                                            <div class="col-10">
                                                <input class="form-control icon-error" type="text" placeholder="ID Group" name="group_id">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Tên Group <span class="text-danger">(*)</span>:</label>
                                            <div class="col-10">
                                                <input class="form-control icon-error" type="text" placeholder="Tên Group" name="group_name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Nhận thông báo:</label>
                                            <div class="col-10">
                                                <label class="switch switch-outline switch-icon switch-success">
                                                    <label>
                                                        <input type="checkbox" name="status">
                                                        <span></span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <div class="d-flex justify-content-end">
                                            <a href="#collapse-add-new-group" type="button" class="btn btn-outline-danger mr-2" data-toggle="collapse">Huỷ</a>
                                            <button type="button" class="btn btn-success mr-2" id="add-row-group-notify">Lưu thông tin</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-detail-info-group" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nội dung thông báo chi tiết</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên thông báo</label>
                        <input type="text" class="form-control" value="Cáo báo ngày {{ \Carbon\Carbon::now()->format('d-m-Y') }}" disabled>
                    </div>
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">
                                    Nội dung chi tiết
                                </h3>
                            </div>
                            <div class="card-toolbar">
                                <button type="button" class="btn btn-primary btn-sm btn-shadow font-weight-bold" id="set-config-default"><i class="flaticon-refresh"></i>Nội dung mặc định</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-end" id="nestable-menu-action">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success" data-action="collapse-all"><i class="flaticon2-shrink mr-2"></i>Thu gọn</button>
                                    <button type="button" class="btn btn-success" data-action="expand-all"><i class="flaticon2-resize mr-2"></i>Mở rộng</button>
                                </div>
                            </div>
                            <div class="separator separator-solid my-7"></div>
                            <div data-scroll="true" data-height="400" id="content-body-scroll">
                                <div class="dd" id="nestable">
                                    @include('admin.shop.widget.notify-list')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Huỷ</button>
                    <button type="button" class="btn btn-primary font-weight-bold" id="save-config-telegram" data-index="">Lưu thông tin</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xoá</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    Xác nhận xoá group:
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-outline-danger btn-sm mr-3" data-dismiss="modal">Huỷ</a>
                    <button type="button" class="btn btn-danger btn-sm confirm-delete-group" data-index="">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="check_create" class="check_create" value="{{ isset($data) ? 1 : 0 }}">
    <input type="hidden" name="check_shop_id" class="check_shop_id" value="{{ isset($data) ? $data->id : '' }}">

@endsection
{{-- Styles Section --}}
@section('styles')
    <style>
        .attribute-box {
            border: 1px solid #ccc;
            padding: 16px;
            width: 50%;
            height: 400px;
            overflow-y: scroll;
        }
        .nested-list-item > button {
            margin-left: unset;
        }
        .scroll.ps>.ps__rail-y>.ps__thumb-y {
            width: 8px;
        }
        .scroll.ps>.ps__rail-y>.ps__thumb-y, .scroll.ps>.ps__rail-y>.ps__thumb-y:focus, .scroll.ps>.ps__rail-y>.ps__thumb-y:hover {
            background-color: #aaa;
            width: 8px;
        }

        .checkbox.checkbox-outline>input.checked2:not(:checked)~span:after {
            width: 10px;
            height: 1px;
            border-color:#f64e60;
            transform: rotate(0);
        }
        .checkbox.checkbox-outline>input.checked2:not(:checked)~span {
            border-color: #f64e60;
        }
    </style>
@endsection
{{-- Scripts Section --}}
@section('scripts')
    @if(isset($data))
        <script>
            $(document).ready(function () {
                @if(session()->has('notify'))
                toastr.success("{{ session('notify') }}");
                @endif
                //giữ lại tab active khi reload trang
                $("ul.nav > li > a").on("shown.bs.tab", function(e) {
                    window.location.hash = $(e.target).attr("href").substr(1);
                });

                let tab_hash = window.location.hash;
                $(`ul.nav a[href="${tab_hash}"]`).tab('show');
                //end hold tab

                $("#nestable input.is-module[type='checkbox']").change(function () {
                    //click children
                    $(this).closest('.dd-item').find("input[type='checkbox']").prop('checked', this.checked);
                });

                let button_set_default = $('#set-config-default');

                button_set_default.on('click',function (e) {
                    e.preventDefault();
                    $('#nestable input[type="checkbox"]').prop('checked',0)
                    $('#nestable input[type="checkbox"][data-default="1"]').prop('checked',1);
                });

                $('#add-row-group-notify').on('click',function (e) {
                    e.preventDefault();
                    $(this).prop('disabled', true);
                    $(this).addClass('spinner spinner-darker-white spinner-left');

                    let data = Array.from($('#collapse-add-new-group [name]')).reduce(function(obj, item) {
                        if(item.name === 'group_id' || item.name === 'group_name') {
                            $(item).toggleClass('is-invalid',item.value.trim() === '')
                        }
                        if (item.name === 'status') {
                            obj[item.name] = $(item).is(':checked') ? 1 : 0;
                        }else {
                            obj[item.name] = item.value;
                        }
                        return obj;
                    }, {});

                    let last_order = $('#table-group-notify table tr').last().data('index') || 0;
                    data.order = ++last_order;
                    $.ajax({
                        url:"{{ route('admin.telegram-group.store') }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            data:data,
                            shop_id: {{$data->id}},
                        },
                        success:function (res) {
                            if(res.status === 2) {
                                toastr.error(res.message);
                            } else if(res.status === 1){
                                window.location.reload();
                            }else  {
                                toastr.error(res.message);
                            }
                            $('#add-row-group-notify').prop('disabled', false).removeClass('spinner spinner-darker-white spinner-left');
                        }
                    })
                });

                $('a[href="#modal-detail-info-group"]').on('click',function () {

                    let index = $(this).data('index');

                    $('#save-config-telegram').attr('data-index',index);

                    let group_config = JSON.parse($(`#config-group-${index}`).val());
                    group_config.forEach(report => {
                        report.modules.forEach(module => {
                            let list_module = $(`.is-module[data-key="${module.module}"]`).closest('.dd-item');
                            module.index.forEach(index => {
                                let key_index = Object.keys(index)[0];
                                let is_checked = index[key_index] *1;
                                $(list_module).find(`.is-index[data-key="${key_index}"]`).prop('checked',is_checked)
                            })
                        })
                    })
                })

                $('.update-info-group').on('click',function (e) {
                    e.preventDefault();
                    $(this).prop('disabled', true);
                    $(this).addClass('spinner spinner-darker-white spinner-left');

                    let index = $(this).data('index');
                    let collapse = $(`#collapse-${index}`);
                    let data_send = {};
                    data_send.group_id = collapse.find('[name="group_id"]').val();
                    data_send.group_name = collapse.find('[name="group_name"]').val();
                    data_send.group_status = collapse.find('[name="status"]').is(':checked') ? 1 : 0;
                    data_send.shop_id = {{ $data->id }};
                    data_send.order = index;
                    data_send._token = $('meta[name="csrf-token"]').attr('content');

                    collapse.find('[name="group_id"]').toggleClass('is-invalid',!data_send.group_id)
                    collapse.find('[name="group_name"]').toggleClass('is-invalid',!data_send.group_name)

                    $.ajax({
                        url:"{{ route('admin.telegram-group.update') }}",
                        type:'PUT',
                        data: data_send,
                        success:function (res) {
                            if(res.status === 2){
                                toastr.error(res.message);
                            }else if(res.status === 1){
                                window.location.reload();
                            }else  {
                                toastr.error(res.message);
                            }
                            $('.update-info-group').prop('disabled', false).removeClass('spinner spinner-darker-white spinner-left');
                        }
                    })
                });

                $('.delete-group').on('click',function (e) {
                    e.preventDefault();
                    let index = $(this).data('index');
                    let group_name = $(this).parent().prev().text().trim();
                    $('#modal-confirm-delete .confirm-delete-group').attr('data-index',index);
                    $('#modal-confirm-delete .modal-body').text(`Xác nhận xoá group: ${group_name}`);
                })

                $('.confirm-delete-group').on('click',function (e) {
                    e.preventDefault();
                    $(this).prop('disabled', true);
                    $(this).addClass('spinner spinner-darker-white spinner-left');


                    let order = $(this).data('index');
                    $.ajax({
                        url: "{{ route('admin.telegram-group.delete') }}",
                        type:'DELETE',
                        data: {
                            _token:$('meta[name="csrf-token"]').attr('content'),
                            shop_id: {{ $data->id }},
                            order:order,
                        },
                        success:function (res) {
                            if(res.status){
                                window.location.reload();
                            }else  {
                                toastr.error(res.message);
                                $('.confirm-delete-group').prop('disabled', false).removeClass('spinner spinner-darker-white spinner-left');
                            }
                        }
                    })
                })

                //nestable action
                $('#nestable-menu-action').on('click','button', function(e)
                {
                    let action = $(this).attr('data-action');
                    if (action === 'expand-all') {
                        $('.dd').nestable('expandAll');
                    }
                    else{
                        $('.dd').nestable('collapseAll');
                    }

                });

                $('#save-config-telegram').on('click',function (e) {
                    e.preventDefault();
                    let checkbox_index = $('input.is-index[type="checkbox"]:checked');
                    if(!checkbox_index.length) {
                        toastr.error('Cần chọn ít nhất một chỉ số !');
                        return;
                    }
                    $(this).prop('disabled', true).addClass('spinner spinner-darker-white spinner-left');
                    let config = [
                        {
                            report:'total-quantity-config',
                            modules: [],
                        },
                        {
                            report: 'user-config',
                            modules: [],
                        }
                    ];

                    let module_quantity = $('#config-total-quantity .is-module');
                    let index_quantity = $('#config-total-quantity .is-index');

                    Array.from(module_quantity).forEach(elm => {
                        let module_key = $(elm).data('key');
                        config[0].modules.push({module:module_key,index : []});
                    });
                    Array.from(index_quantity).forEach(elm => {
                        let index_key = $(elm).data('key');
                        let parent_key = $(elm).data('parent');
                        let is_checked = $(elm).is(':checked') ? 1 : 0;

                        config[0].modules.forEach((module,index) => {
                            if(module.module === parent_key) {
                                config[0].modules[index].index.push({[index_key] :is_checked});
                            }
                        })
                    });

                    let module_user = $('#config-user .is-module');
                    let index_user = $('#config-user .is-index');

                    Array.from(module_user).forEach(elm => {
                        let module_key = $(elm).data('key');
                        config[1].modules.push({module:module_key,index : []});
                    });
                    Array.from(index_user).forEach(elm => {
                        let index_key = $(elm).data('key');
                        let parent_key = $(elm).data('parent');
                        let is_checked = $(elm).is(':checked') ? 1 : 0;

                        config[1].modules.forEach((module,index) => {
                            if(module.module === parent_key) {
                                config[1].modules[index].index.push({[index_key] :is_checked});
                            }
                        });
                    });
                    config = populateFromArray(config);
                    let order = $(this).data('index');
                    handleDataGroup(config,order);
                })

                function populateFromArray(array) {
                    let output = {};
                    array.forEach(function(item, index) {
                        if (!item) return;
                        if (Array.isArray(item)) {
                            output[index] = populateFromArray(item);
                        } else {
                            output[index] = item;
                        }
                    });
                    return output;
                }

                function handleDataGroup(config,order) {
                    $.ajax({
                        url:'{{ route('admin.telegram-config.update') }}',
                        type:'POST',
                        data:{
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            config:config,
                            order:order,
                            shop_id:{{$data->id}},
                        },
                        success:function (res) {
                            if(res.status){
                                window.location.reload();
                            }else  {
                                toastr.error(res.message);
                            }
                        }
                    })
                }

                function checkStatus(){
                    Array.from($('#nestable>.dd-list>.dd-item')).forEach(elm => {
                        let checked_index = $(elm).find('input.is-index:checked');
                        $(elm).find('input.is-module').prop('checked',checked_index.length === $(elm).find('input.is-index').length)
                        let status = checked_index.length < $(elm).find('input.is-index').length && checked_index.length > 0;
                        $(elm).find('input.is-module').toggleClass('checked2',status)
                    })
                }
                $( "#modal-detail-info-group").on('shown.bs.modal', function () {
                    checkStatus();
                    $('#nestable-menu-action [data-action="collapse-all"]').trigger('click');
                });
                $('input.is-index').on('change',checkStatus);

                $('.send-msg-demo').on('click',function (e) {
                    e.preventDefault();
                    let group_order = $(this).data('order');
                    let elm = $(this);
                    $(elm).prop('disabled', true).addClass('spinner spinner-darker-white spinner-left');
                    $.ajax({
                        url: '/admin/telegram-send-msg-demo/{{ $data->id }}/'+group_order,
                        method:"GET",
                        success:function (res) {
                            $(elm).prop('disabled', false).removeClass('spinner spinner-darker-white spinner-left');

                            if(res.status) {
                                toastr.success(res.message);
                            } else {
                                toastr.error(res.message);
                            }
                        }
                    })
                });
            })
        </script>
    @else
    @endif

    <script>
        "use strict";
        $(document).ready(function () {
            $('#kt_select2_1').select2({
                placeholder: "Chưa chọn nhóm shop"
            });
            $('.select2').select2({
                placeholder: "Select a state"
            });
            $('#shop_clone').select2({
                placeholder: "Chọn shop cần clone"
            });
            $('body').on('click', '#btn-clone', function () {
                var val = $(this).is(":checked");
                if (val === true) {
                    $('.body-shop-clone').css('display', 'block');
                } else {
                    $('.body-shop-clone').css('display', 'none');
                }
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

            $('body').on('click', '#add_secret_key', function () {
                var id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    url: "{{route('admin.shop.secret_key')}}",
                    data: {
                        '_token': '{{csrf_token()}}',
                        'id': id,
                    },
                    beforeSend: function (xhr) {
                        $(this).prop('disabled', true);
                    },
                    success: function (data) {
                        if (data.status == 1) {
                            toast(data.message);
                            $('#value_secret_key').val(data.secret_key)
                        } else {
                            toast('{{__('Có lỗi xảy ra vui lòng thử lại.')}}', 'error');
                        }
                    },
                    error: function (data) {
                        toast('{{__('Lỗi hệ thống, vui lòng liên hệ QTV để xử lý.')}}', 'error');

                    },
                    complete: function (data) {
                        $(this).prop('disabled', false);
                    }
                });
            })
        });

    </script>
@endsection
