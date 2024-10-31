{{-- Extends layout --}}
@extends('admin._layouts.master')
@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.user-ctv.index')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>
        <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split btn-submit-dropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
        </div>
    </div>
@endsection
{{-- Content --}}
@section('content')

    <div class="row">
        <div class="col-md-12 left__right" style="padding-bottom: 16px">
            <div class="card card-custom" style="background: none;box-shadow: none">
                <div class="card-header card-header-tabs-line nav-tabs-line-3x" style="border-bottom: none;padding: 16px">
                    <div class="card-toolbar">
                        <ul class="nav nav-tabs nav-bold nav-tabs-line nav-tabs-line-3x">
                            <!--begin::Item-->
                            <li class="nav-item mr-3">
                                <a class="nav-link btn-show-tab-3 active" data-toggle="tab" href="#kt_user_edit_tab_3">
                                    <span class="nav-text font-size-lg">{{ __('Phân quyền nhận đơn dịch vụ theo thuộc tính') }}</span>
                                </a>
                            </li>
                            <!--begin::Item-->
                            <li class="nav-item mr-3">
                                <a class="nav-link btn-show-tab-1" data-toggle="tab" href="#kt_user_edit_tab_1">
                                    <span class="nav-text font-size-lg" style="font-weight: bold">{{ __('Phân quyền giới hạn nhận đơn') }}</span>
                                </a>
                            </li>
                            <!--begin::Item-->

                        <!--end::Item-->
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0"  >
                    <div class="tab-content">

                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" style="width: 100%">
            <div class="tab-pane show active" id="kt_user_edit_tab_3" role="tabpanel">
                <div class="col-lg-12">
                    {{Form::open(array('route'=>array('admin.service.post_set_permission_detail_user',$data->id),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card card-custom gutter-b">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-label">
                                            {{__('Phân quyền nhận đơn dịch vụ theo thuộc tính')}} <i class="mr-2"></i>
                                        </h3>
                                    </div>
                                    <button type="submit" style="height: 48px;margin-top: 12px" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
                                        <i class="ki ki-check icon-sm"></i>
                                        @if(isset($data))
                                            {{__('Cập nhật')}}
                                        @else
                                            {{__('Thêm mới')}}
                                        @endif
                                    </button>
                                </div>
                                <div class="card-body" style="padding-bottom: 0">
                                    <div class="form-group row">
                                        <div class="col-12 col-sm-6 col-lg-6">
                                            <label style="font-weight: 600" >Chọn ctv áp dụng</label>
                                            <div class="input-group">
                                                <select  name="service_ctv_access[]" multiple="multiple" title="Chọn ctv cần tìm" class="form-control select2 col-md-12 datatable-input"  data-placeholder="{{__('Chọn ctv cần tìm')}}" id="kt_select2_2" >
                                                    <option value="">{{__('Chọn ctv cần tìm')}}</option>
                                                    @foreach($ctvs as $key => $ctv)
                                                        <option
                                                            @if(in_array($ctv->id,$service_accept_allow_users))
                                                            selected
                                                            @endif
                                                            value="{{ $ctv->id }}">{{ $ctv->username }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">

                                            <label style="font-weight: 600" >Chọn cấu hình phân quyền nhận đơn (theo loại tài khoản)</label>
                                            <div class="input-group">
                                                <select class="form-control" required name="type_information_ctv_access">
                                                    <option
                                                        @if($type_information_ctv_access == 0)
                                                            selected
                                                        @endif
                                                        value="0">Không cấu hình</option>
                                                    <option
                                                        @if($type_information_ctv_access == 1)
                                                        selected
                                                        @endif
                                                        value="1">CTV nhà</option>
                                                    <option
                                                        @if($type_information_ctv_access == 2)
                                                        selected
                                                        @endif
                                                        value="2">CTV Khách</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12 col-md-12 data__add__ctv__db">
                                            <div class="row marginauto">
                                                <div class="col-auto policy__elementor__left left__right">
                                                    <label style="font-weight: 600">{{ __('Thuộc tính áp dụng') }} <span class="policy__text__error" style="color: #f64e60">*</span></label>
                                                    <div class="row marginauto">
                                                        <div class="col-md-12 left__right data__shop__service data__shop__ctv">
                                                                <span class="select2 select2-container select2-container--default select2-container--below select2-container--focus"  style="width: 100%;">
                                                                    <span class="selection">
                                                                        <span class="select2-selection select2-selection--multiple select2-selection__danhmuc">
                                                                            <div class="row marginauto" style="padding-left: 8px;padding-top: 4px;padding-bottom: 4px">
                                                                                <div class="col-md-12 left__right scroll-default_shop">
                                                                                    <ul class="select2-selection__rendered" style="padding: 0" id="data_shop_service">
                                                                                        @if(count($service_accept_allow_attributes) && count($names))
                                                                                            @foreach($names??[] as $i => $name)
                                                                                                @php
                                                                                                    $index = $i + 1;
                                                                                                @endphp
                                                                                                @if(in_array($name,$service_accept_allow_attributes))
                                                                                                    <li class="select2-selection__choice remove_shop_service" data-id="{{ $index }}" style="cursor: pointer">
                                                                                                    <span class="select2-selection__choice__remove">×</span>
                                                                                                    <input type="hidden" name="service_attribute[]" value="{{ $name }}" data-id="{{ $index }}" class="d_user_service">
                                                                                                  {{ $name }}
                                                                                                    </li>
                                                                                                @endif
                                                                                            @endforeach
                                                                                        @else
                                                                                            <li style="padding: 4px">{{ __('Chưa có thông tin Cộng tác viên') }}</li>
                                                                                        @endif
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </span>
                                                                    </span>
                                                                    <span class="dropdown-wrapper" aria-hidden="true"></span>
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto policy__elementor__right">
                                                    <i class="fab fa-elementor btn-show-attribute"></i>
                                                </div>
                                            </div>
                                            <span class="text-danger error_khothanhvien" style="line-height: 24px;font-size: 12px"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
                <!--end::Row-->
            </div>

            <div class="tab-pane show" id="kt_user_edit_tab_1" role="tabpanel">
                <div class="col-lg-12">
                    {{Form::open(array('route'=>array('admin.service.post_set_permission',$data->id),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card card-custom gutter-b">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-label">
                                            {{__('Phân quyền nhận đơn dịch vụ (Áp dụng cho toàn bộ thuộc tính)')}} <i class="mr-2"></i>
                                        </h3>
                                    </div>
                                    <button type="submit" style="height: 48px;margin-top: 12px" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
                                        <i class="ki ki-check icon-sm"></i>
                                        @if(isset($data))
                                            {{__('Cập nhật')}}
                                        @else
                                            {{__('Thêm mới')}}
                                        @endif
                                    </button>
                                </div>
                                <div class="card-body" style="padding-bottom: 0">
                                    <div class="form-group row">
                                        <div class="col-12 col-md-4">
                                            <label style="font-weight: 600" >Giới hạn số đơn nhận</label>
                                            <div class="input-group">
                                                <select class="form-control" required name="is_display">
                                                    @if(isset($data))
                                                        @if(isset($data->is_display))
                                                            <option
                                                                @if($data->is_display == 0)
                                                                selected
                                                                @endif
                                                                value="0">Không cấu hình</option>
                                                            <option
                                                                @if($data->is_display == 1)
                                                                selected
                                                                @endif
                                                                value="1">Cấu hình theo dịch vụ</option>
                                                            <option
                                                                @if($data->is_display == 2)
                                                                selected
                                                                @endif
                                                                value="2">Cấu hình theo ctv</option>
                                                        @else
                                                            <option selected value="0">Không cấu hình</option>
                                                            <option value="1">Cấu hình theo dịch vụ</option>
                                                            <option value="2">Cấu hình theo ctv</option>
                                                        @endif
                                                    @else
                                                        <option selected value="0">Không cấu hình</option>
                                                        <option value="1">Cấu hình theo dịch vụ</option>
                                                        <option value="2">Cấu hình theo ctv</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                        <div class="col-12 col-md-4">
                                            <label style="font-weight: 600" >Yêu cầu tối đa được nhận</label>
                                            <div class="input-group">
                                                <input type="text" required class="form-control m-input" name="display_type" value="{{ isset($data->display_type) ? $data->display_type : '' }}" placeholder="Số" aria-describedby="basic-addon2" fdprocessedid="28vlqq">
                                                <div class="input-group-append"><span class="input-group-text">Lần</span></div>
                                            </div>
                                        </div>

                                        @if(isset($data))

                                            <div class="col-12 col-md-8 data__add__ctv__db">
                                                <div class="row marginauto">
                                                    <div class="col-auto policy__elementor__left left__right">
                                                        <label style="font-weight: 600">{{ __('Cộng tác viên áp dụng') }} <span class="policy__text__error" style="color: #f64e60">*</span></label>
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left__right data__shop data__shop__ctv">
                                                                <span class="select2 select2-container select2-container--default select2-container--below select2-container--focus"  style="width: 100%;">
                                                                    <span class="selection">
                                                                        <span class="select2-selection select2-selection--multiple select2-selection__danhmuc">
                                                                            <div class="row marginauto" style="padding-left: 8px;padding-top: 4px;padding-bottom: 4px">
                                                                                <div class="col-md-12 left__right scroll-default_shop">
                                                                                    <ul class="select2-selection__rendered" style="padding: 0" id="data_shop">
                                                                                        @if(!empty($access_limit_users) && count($access_limit_users) > 0)
                                                                                            @foreach($access_limit_users as $access_limit_user)
                                                                                                <li class="select2-selection__choice" data-type="user_id" data-id="{{ $access_limit_user->id }}" style="cursor: pointer">
                                                                                                    <span class="select2-selection__choice__remove " >×</span>
                                                                                                    <input type="hidden" name="user_id[]" value="{{ $access_limit_user->id }}" class="d_user">
                                                                                                    {{ $access_limit_user->username }}
                                                                                                </li>
                                                                                            @endforeach

                                                                                        @else
                                                                                            <li style="padding: 4px">{{ __('Chưa có thông tin cộng tác viên') }}</li>
                                                                                        @endif

                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </span>
                                                                    </span>
                                                                    <span class="dropdown-wrapper" aria-hidden="true"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if(Auth::user()->can('update-providers-in-consignment-fee-policies'))
                                                        <div class="col-auto policy__elementor__right">
                                                            <i class="fab fa-elementor btn-show-ctv"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="text-danger error_khothanhvien" style="line-height: 24px;font-size: 12px"></span>
                                            </div>
                                        @else
                                            <div class="col-12 col-md-8 data__add__ctv__db">
                                                <div class="row marginauto">
                                                    <div class="col-auto policy__elementor__left left__right">
                                                        <label style="font-weight: 600">{{ __('Cộng tác viên áp dụng') }} <span class="policy__text__error" style="color: #f64e60">*</span></label>
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left__right data__shop data__shop__ctv">
                                                                <span class="select2 select2-container select2-container--default select2-container--below select2-container--focus"  style="width: 100%;">
                                                                    <span class="selection">
                                                                        <span class="select2-selection select2-selection--multiple select2-selection__danhmuc">
                                                                            <div class="row marginauto" style="padding-left: 8px;padding-top: 4px;padding-bottom: 4px">
                                                                                <div class="col-md-12 left__right scroll-default_shop">
                                                                                    <ul class="select2-selection__rendered" style="padding: 0" id="data_shop">
                                                                                        <li style="padding: 4px">{{ __('Chưa có thông tin Cộng tác viên') }}</li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </span>
                                                                    </span>
                                                                    <span class="dropdown-wrapper" aria-hidden="true"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto policy__elementor__right">
                                                        <i class="fab fa-elementor btn-show-ctv"></i>
                                                    </div>
                                                </div>
                                                <span class="text-danger error_khothanhvien" style="line-height: 24px;font-size: 12px"></span>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>

                <div class="col-lg-12">
                    {{Form::open(array('route'=>array('admin.service.post_set_permission_user',$data->id),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card card-custom gutter-b">
                                <div class="card-header">
                                    <div class="card-title pt-5" style="display: block">
                                        <h3 class="card-label">
                                            {{__('Phân quyền nhận đơn dịch vụ theo thành viên')}} <i class="mr-2"></i>
                                        </h3>
                                        <p style="color: red">Lưu ý: Những CTV/Loại tài khoản ctv được cấu hình sẽ không được nhận đơn của các thành viên cấu hình</p>
                                    </div>
                                    <button type="submit" style="height: 48px;margin-top: 12px" class="btn btn-success font-weight-bolder" data-submit-close="1">
                                        <i class="ki ki-check icon-sm"></i>
                                        @if(isset($data))
                                            {{__('Cập nhật')}}
                                        @else
                                            {{__('Thêm mới')}}
                                        @endif
                                    </button>
                                </div>
                                <div class="card-body" style="padding-bottom: 0">
                                    <div class="form-group row">
                                        <div class="col-12 col-sm-6 col-lg-6">
                                            <label style="font-weight: 600" >Chọn ctv áp dụng</label>
                                            <div class="input-group">
                                                <select  name="ctv_access[]" multiple="multiple" title="Chọn ctv cần tìm" class="form-control select2 col-md-12 datatable-input"  data-placeholder="{{__('Chọn ctv cần tìm')}}" id="kt_select2_3" >
                                                    <option value="">{{__('Chọn ctv cần tìm')}}</option>
                                                    @foreach($ctvs as $key => $ctv)
                                                        <option
                                                            @if(in_array($ctv->id,$access_limit_ctvs))
                                                            selected
                                                            @endif
                                                            value="{{ $ctv->id }}">{{ $ctv->username }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-6">
                                            <label style="font-weight: 600" >Chọn thành viên áp dụng</label>
                                            <div class="input-group">
                                                <select  name="member_access[]" multiple="multiple" title="Chọn thành viên cần tìm" class="form-control select2 col-md-12 datatable-input"  data-placeholder="{{__('Chọn thành viên cần tìm')}}" id="kt_select2_1" >
                                                    <option value="">{{__('Chọn thành viên cần tìm')}}</option>
                                                    @foreach($members as $key => $member)
                                                        <option
                                                            @if(in_array($member->id,$access_limit_members))
                                                            selected
                                                            @endif
                                                            value="{{ $member->id }}">{{ $member->username }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-6 col-md-3">
                                            <label style="font-weight: 600" >Chọn cấu hình phân quyền nhận đơn của thành viên (theo ctv)</label>
                                            <div class="input-group">
                                                <select class="form-control" required name="sticky">
                                                    @if(isset($data))
                                                        @if(isset($data->sticky))
                                                            <option
                                                                @if($data->sticky == 0)
                                                                selected
                                                                @endif
                                                                value="0">Không cấu hình</option>
                                                            <option
                                                                @if($data->sticky == 1)
                                                                selected
                                                                @endif
                                                                value="1">Cấu hình</option>
                                                        @else
                                                            <option selected value="0">Không cấu hình</option>
                                                            <option value="1">Cấu hình</option>
                                                        @endif
                                                    @else
                                                        <option selected value="0">Không cấu hình</option>
                                                        <option value="1">Cấu hình</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <div class="col-6 col-md-3">
                                            <label style="font-weight: 600" >Chọn cấu hình phân quyền nhận đơn của thành viên (theo loại tài khoản)</label>
                                            <div class="input-group">
                                                <select class="form-control" required name="position">
                                                    @if(isset($data))
                                                        @if(isset($data->position))
                                                            <option
                                                                @if($data->position == 0)
                                                                selected
                                                                @endif
                                                                value="0">Không cấu hình</option>
                                                            <option
                                                                @if($data->position == 1)
                                                                selected
                                                                @endif
                                                                value="1">CTV nhà</option>
                                                            <option
                                                                @if($data->position == 2)
                                                                selected
                                                                @endif
                                                                value="2">CTV Khách</option>
                                                        @else
                                                            <option selected value="0">Không cấu hình</option>
                                                            <option value="1">CTV nhà</option>
                                                            <option value="2">CTV Khách</option>
                                                        @endif
                                                    @else
                                                        <option selected value="0">Không cấu hình</option>
                                                        <option value="1">CTV nhà</option>
                                                        <option value="2">CTV Khách</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="tab-pane show " id="kt_user_edit_tab_2" role="tabpanel">

                <!--end::Row-->
            </div>

        </div>

    </div>

    {{-- Modal new service--}}
    <div class="modal fade" id="serviceModal">
        <div class="modal-dialog  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="text-center modal-title label-service-selected" > {{__("Chọn dịch vụ áp dụng")}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">

                    <!--begin: Datatable-->
                    <table class="table table-bordered table-hover table-checkable " id="kt_datatable_service_modal">

                    </table>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                    <button type="button" class="btn btn-primary  submit_service" data-type="shop">{{__('Xác nhận')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="listUserModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="card card-custom">
                    <!--begin::Card header-->
                    <div class="card-header card-header-tabs-line nav-tabs-line-3x" style="border-bottom: none">
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">
                            <ul class="nav nav-tabs nav-bold nav-tabs-line nav-tabs-line-3x">
                                <!--begin::Item-->
                                <li class="nav-item mr-3">
                                    <a class="active" data-toggle="tab" href="#kt_user_edit_tab_3">
                                        <span class="font-size-lg" style="color: #3f4254;font-size: 18px;font-weight: bold">{{ __('Thêm cộng tác viên áp dụng') }}</span>
                                    </a>
                                </li>

                                <!--end::Item-->
                            </ul>
                        </div>
                        <!--end::Toolbar-->
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane show active" id="kt_user_edit_tab_3" role="tabpanel">
                                <!--begin::Row-->

                                <div class="modal-body" style="padding: 0">
                                    <form class="mb-10">
                                        <div class="row">
                                            {{--title--}}
                                            <div class="form-group col-12 col-sm-6 col-lg-6 mb-0">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="la la-search glyphicon-th"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control datatable-input_ctv"
                                                           placeholder="{{__('Tìm kiếm theo id tên cộng tác viên')}}" id="id_ctv">
                                                </div>
                                            </div>
                                            <div class="form-group col-12 col-sm-6 col-lg-6 mb-0">
                                                <button class="btn btn-primary btn-primary--icon" id="kt_search_ctv">
                                                        <span>
                                                            <i class="la la-search"></i>
                                                            <span>{{ __('Tìm kiếm') }}</span>
                                                        </span>
                                                </button>&#160;&#160;
                                            </div>
                                        </div>
                                        <div class="row">

                                        </div>
                                    </form>
                                    <table class="table table-bordered table-hover table-checkable" id="showUser_datatable">

                                    </table>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Đóng') }}</button>
                                    <button type="button" class="btn btn-success m-btn m-btn--custom add_ctv">{{ __('Xác nhận') }}</button>
                                </div>
                                <!--end::Row-->
                            </div>
                            <!--begin::Tab-->
                        </div>
                    </div>
                    <!--begin::Card body-->
                </div>

            </div>
        </div>
    </div>
    <input type="hidden" class="total_add_shop" value="">
@endsection
{{-- Styles Section --}}
@section('styles')
@endsection
{{-- Scripts Section --}}
@section('scripts')
    <script>
        "use strict";

        $(document).ready(function () {

            // $('body').on('change','.btn-show-tab-1',function(e){
            //     $('#kt_select2_2').select2();
            //     $('#kt_select2_1').select2();
            // })
            // $('body').on('change','.btn-show-tab-2',function(e){
            //     $('#kt_select2_2').select2();
            //     $('#kt_select2_1').select2();
            // })
            // $('body').on('change','.btn-show-tab-3',function(e){
            //     $('#kt_select2_2').select2();
            //     $('#kt_select2_1').select2();
            // })

        })

        function init_discount(){
            $('.discount-live').each(function(){
                var el = $(this);
                el.find('.ratio-result').text(100 - (el.find('.ratio-val').val() > 0? el.find('.ratio-val').val(): 0))
            });
        }
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // Khởi tạo lại Select2 cho các phần tử trong tab hiện tại
            $('#kt_select2_2').select2();
            $('#kt_select2_1').select2();
            $('#kt_select2_3').select2();

        });

        jQuery(document).ready(function () {
            $('#kt_select2_2').select2();
            $('#kt_select2_1').select2();
            $('#kt_select2_3').select2();
            $('#shop_access_custom input').change(function() {
                $('input[name="shop_access_all"]').prop("checked", false);
            });

            init_discount();
            $(".ratio-val").inputmask({
                groupSeparator: ",",
                radixPoint: ".",
                alias: "numeric",
                placeholder: "0",
                autoGroup: true,
                min:0,
                max:100
            });
            $('.ratio-val').keyup(function(){
                var el = $(this);
                var resetClass = el.hasClass('custom-val')? '.general-val': '.custom-val';
                if (el.hasClass('custom-val')) {
                    el.parents('.discount-group').find(resetClass).val('').parents('.discount-live').find('.ratio-result').text(100);
                }else{
                    el.parents('.discount-group').find(resetClass).val(el.val()).parents('.discount-live').find('.ratio-result').text(100 - (el.val() > 0? el.val(): 0));
                }
                el.parents('.discount-live').find('.ratio-result').text(100 - (el.val() > 0? el.val(): 0))
            });


            //btn submit form
            $('.btn-submit-custom').click(function (e) {
                e.preventDefault();
                var btn = this;
                if (confirm('Vui lòng kiểm tra phần đã cấu hình 1 lần nữa để tránh xảy ra lỗi setup nhầm. Cảm ơn!!')) {
                    $(".btn-submit-custom").each(function (index, value) {
                        KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                    });
                    $('.btn-submit-dropdown').prop('disabled', true);
                    //gắn thêm hành động close khi submit
                    $('#submit-close').val($(btn).data('submit-close'));
                    var formSubmit = $('#' + $(btn).data('form'));
                    formSubmit.submit();
                }
            });

        });



    </script>
    <script>
        $('.service_accept input,[value="admin_plus_money"],[value="user_plus_money"],[value="admin_minus_money"],[value="user_minus_money"]').change(function () {
            UpdateView();
        });
        $('[name="input_img"]').change(function () {
            $('#img_preview').hide();
        });

        function UpdateView() {
            $('.service_accept input').each(function (idx, elm) {
                if ($(elm).is(':checked')) {
                    $(elm).closest('.service').find('.service_settings').slideDown();
                } else {
                    $(elm).closest('.service').find('.service_settings').slideUp();
                }
            });

            if ($('[value="user_plus_money"]:checked').length != 0 || $('[value="admin_plus_money"]:checked').length != 0) {
                $('.max_plus').show();
            } else {
                $('.max_plus').hide();
            }
            if ($('[value="user_minus_money"]:checked').length != 0 || $('[value="admin_minus_money"]:checked').length != 0) {
                $('.max_minus').show();
            } else {
                $('.max_minus').hide();
            }
        }

        UpdateView();
        $('.cbxAll').change(function () {
            var container = $(this).closest('table');
            if ($(this).is(':checked')) {
                $('[type="checkbox"]', container).prop('checked', true);
            } else {
                $('[type="checkbox"]', container).prop('checked', false);
            }
        })

        var datatable_user_show;
        var datatable3;

        $('body').on('click','.btn-show-ctv',function(e){
            e.preventDefault();
            showCTV(67);
        });

        $('body').on('click','.btn-show-attribute',function(e){
            e.preventDefault();
            showAttribute(67);
        });

        function showCTV(id){
            KTDatatablesDataSourceAjaxServer2.init(id);
            // KTDatatablesDataSourceAjaxServer3.init(id);
            $('#listUserModal').modal('show');
        }

        function showAttribute(id){
            KTDatatablesDataSourceAjaxServerService.init(id);
            // KTDatatablesDataSourceAjaxServer3.init(id);
            $('#serviceModal').modal('show');
        }

        var KTDatatablesDataSourceAjaxServerService = function () {
            var initTable3 = function () {
                // begin first table
                datatable3 = $('#kt_datatable_service_modal').DataTable({
                    responsive: true,
                    destroy: true,
                    dom: `
                            <'row'<'col-sm-12'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,

                    lengthMenu: [20, 50, 100, 200,500,1000],
                    pageLength: 500,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "order": [[1, "desc"]],
                    ajax: {
                        url: '{{url()->current()}}' + '?flash_sale_service=1&ajax=1',
                        type: 'GET',
                        data: function (d) {
                            d.id_service = $('#id_service').val();
                            d.id_category = $('#id_category_service').val();
                        }
                    },
                    buttons: [
                    ],
                    columns: [
                        {
                            data: null,
                            title: '<label class="checkbox checkbox-lg checkbox-outline"><input type="checkbox" id="btnCheckService">&nbsp<span></span></label>',
                            orderable: false,
                            searchable: false,
                            width: "20px",
                            class: "ckb_item",
                            render: function (data, type, row) {
                                let title = '';
                                if (row.name){
                                    name = row.name;
                                }
                                return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item label_checkbox_service checkbox-item__add__shop__service checkbox-ctv__service__' + row.id + '">' +
                                    '<input type="checkbox" value="' + row.id + '" data-title="' + name + '"  rel="' + row.id + '" id="">&nbsp<span></span></label>';
                            }
                        },
                        {data: 'id', title: '{{__('ID')}}'},
                        {data: 'keyword', title: '{{__('Keyword')}}'},
                        {data: 'name', title: '{{__('Thuộc tính')}}'},
                        {data: 'service_idkey', title: '{{__('Key')}}'},
                        {data: 'price', title: '{{__('Giá bán')}}'},

                    ],
                    "drawCallback": function (settings) {
                        //callback ctv
                        $("#data_shop_service .d_user_service").each(function (index, elem) {

                            let id_user = $(this).data('id');

                            let checkbox_ctv = $('.checkbox-ctv__service__' + id_user + '');

                            let parrent = checkbox_ctv.parent().parent();
                            parrent.addClass('selected');
                            let child = checkbox_ctv.find('input');
                            // child.
                            child.prop('checked', true);
                            // console.log(id_user);
                        });
                    }

                });
                var filter = function () {
                    var val = $.fn.datatable3.util.escapeRegex($(this).val());
                    datatable3.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                };
                $('#kt_search_service_modal').on('click', function (e) {
                    e.preventDefault();
                    var params = {};
                    $('.datatable-input-service-modal').each(function () {
                        var i = $(this).attr('col-index');
                        if (params[i]) {
                            params[i] += '|' + $(this).val();
                        } else {
                            params[i] = $(this).val();
                        }
                    });

                    $.each(params, function (i, val) {
                        // apply search params to datatable
                        datatable3.column(i).search(val ? val : '', false, false);
                    });
                    datatable3.table().draw();
                });
                $('#kt_reset_service_modal').on('click', function (e) {
                    e.preventDefault();
                    $('.datatable-input-service-modal').each(function () {
                        $(this).val('');
                        datatable3.column($(this).data('col-index')).search('', false, false);
                    });
                    datatable3.table().draw();
                });
                datatable3.on("click", "#btnCheckService", function () {
                    $(".ckb_item .label_checkbox_service input[type='checkbox']").prop('checked', this.checked).change();
                })
                datatable3.on("change", ".ckb_item .label_checkbox_product input[type='checkbox']", function () {
                    if (this.checked) {
                        var currTr = $(this).closest("tr");
                        datatable3.rows(currTr).select();
                    } else {
                        var currTr = $(this).closest("tr");
                        datatable3.rows(currTr).deselect();
                    }
                });
                //function update field
                datatable3.on("change", ".update_field", function (e) {
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
                    initTable3();
                },

            };
        }();

        var KTDatatablesDataSourceAjaxServer2 = function (id) {

            var initTable3 = function (id) {
                datatable_user_show = $('#showUser_datatable').DataTable({
                    paging: true,
                    destroy: true,
                    responsive: true,
                    dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                             <'row'<'col-sm-12 scroll-default'tr>>
                       <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    lengthMenu: [5, 10, 20, 50,100,500,1000],
                    pageLength: 20,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "order": [[1, "desc"]],
                    ajax: {
                        url: window.location.href + '?shop_id=' + id + '&show_shop=2&ajax=1',
                        type: 'GET',
                        data: function (d) {
                            d.id = $('#id_ctv').val();
                            d.title = $('#title_ctv').val();
                        }
                    },
                    buttons: [
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

                                let currency = '';
                                let key = '';
                                let symbol = '';
                                if (row.currency){
                                    currency = row.currency.id;
                                    key = row.currency.key;
                                    symbol = row.currency.symbol;
                                }
                                return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item checkbox-item__add__shop checkbox-ctv__' + row.id + '">' +
                                    '<input data-symbol="' + symbol + '" data-currency="' + currency + '" data-key="' + key + '" type="checkbox" data-id="' + row.id + '" data-title="' + row.username + '"  rel="' + row.id + '" id="">&nbsp<span></span></label>';

                            }
                        },
                        {data: 'id', title: 'ID'},
                        {
                            data: 'username', title: '{{__('Tên CTV')}}',
                            render: function (data, type, row) {
                                if( row.username+"" !="null"){
                                    return row.username;
                                }
                                else{
                                    return "";
                                }

                            }
                        },
                        {
                            data: 'email', title: '{{__('Nhóm CTV')}}',
                            render: function (data, type, row) {
                                if( row.email+"" !="null"){
                                    return row.email;
                                }
                                else{
                                    return "";
                                }

                            }
                        },
                        {
                            data: 'status', title: '{{__('Trạng thái')}}',
                            render: function (data, type, row) {
                                if (row.status == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.user.status.1')}}" + "</span>";
                                } else if (row.status == 2) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.user.status.2')}}" + "</span>";
                                } else {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.user.status.0')}}" + "</span>";
                                }

                            }
                        },
                    ],
                    "drawCallback": function (settings) {
                        //callback ctv
                        $("#data_shop .d_user").each(function (index, elem) {
                            let id_user = $(this).val();

                            let checkbox_ctv = $('.checkbox-ctv__' + id_user + '');

                            let parrent = checkbox_ctv.parent().parent();
                            parrent.addClass('selected');
                            let child = checkbox_ctv.find('input');
                            // child.
                            child.prop('checked', true);
                            // console.log(id_user);
                        });
                    }

                });
                var filter = function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    datatable_user_show.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                };
                $('#kt_search_ctv').on('click', function (e) {
                    e.preventDefault();
                    var params = {};
                    $('.datatable-input_ctv').each(function () {
                        var i = $(this).data('col-index');
                        if (params[i]) {
                            params[i] += '|' + $(this).val();
                        } else {
                            params[i] = $(this).val();
                        }
                    });

                    $.each(params, function (i, val) {
                        // apply search params to datatable
                        datatable_user_show.column(i).search(val ? val : '', false, false);
                    });
                    datatable_user_show.table().draw();
                });

                $('#kt_reset_ctv').on('click', function (e) {
                    e.preventDefault();
                    $('.datatable-input_ctv').each(function () {
                        $(this).val('');
                        datatable_user_show.column($(this).data('col-index')).search('', false, false);
                    });
                    datatable_user_show.table().draw();
                });

                datatable_user_show.on("click", "#btnCheckAll", function () {
                    $(".ckb_item input[type='checkbox']").not(":disabled").prop('checked', this.checked).change();
                })
                datatable_user_show.on("change", ".ckb_item input[type='checkbox']", function () {
                    if (this.checked) {
                        var currTr = $(this).closest("tr");
                        datatable_user_show.rows(currTr).select();
                    } else {
                        var currTr = $(this).closest("tr");
                        datatable_user_show.rows(currTr).deselect();
                    }
                });
                //function update field
                datatable_user_show.on("change", ".update_field", function (e) {
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
                init: function (id) {
                    initTable3(id);
                },
            };
        }();

        $(document).ready(function () {
            $('body').on('click', '.add_ctv', function(e) {
                e.preventDefault();
                let id_delete = '';
                let title_delete = '';

                var total = $("#listUserModal .checkbox-item__add__shop input[type=checkbox]:checked").length;
                if(total>0){
                    var t_index = 0;

                    $('#data_shop').html('');

                    $("#listUserModal .checkbox-item__add__shop input[type=checkbox]").each(function (index, elem) {

                        if ($(elem).is(':checked')) {
                            let r_id = $(elem).attr('rel');
                            let r_title = $(elem).data('title');
                            //
                            // arr_currency_key.push(user_key);

                            if (id_delete != '') {
                                id_delete += '|';
                            }

                            id_delete += r_id;

                            if (title_delete != '') {
                                title_delete += '|';
                            }

                            title_delete += r_title;

                            var rhtml_sp = '';
                            t_index = t_index + 1;
                            rhtml_sp += '<li class="select2-selection__choice remove_shop" data-id="' + r_id + '" style="cursor: pointer">';
                            rhtml_sp += '<span class="select2-selection__choice__remove">×</span>';
                            rhtml_sp += '<input type="hidden" name="user_id[]" value="' + r_id + '" class="d_user">';
                            rhtml_sp += r_title;
                            rhtml_sp += '</li>';

                            $('.data__shop .select2-selection__danhmuc').css('border-color','#69b3ff');
                            $('#data_shop').append(rhtml_sp);

                        }

                    });

                    $('.total_add_shop').val(total);

                    $('#listUserModal').modal('hide');

                }
                else{
                    toast('{{ __("Vui lòng chọn dữ liệu cần thêm") }}', 'error');
                }

            });

            $('body').on('click', '.submit_service', function(e) {
                e.preventDefault();
                let id_delete = '';
                let title_delete = '';

                var total = $("#serviceModal .checkbox-item__add__shop__service input[type=checkbox]:checked").length;
                if(total>0){
                    var t_index = 0;

                    $('#data_shop_service').html('');

                    $("#serviceModal .checkbox-item__add__shop__service input[type=checkbox]").each(function (index, elem) {

                        if ($(elem).is(':checked')) {
                            let r_id = $(elem).attr('rel');
                            let r_title = $(elem).data('title');
                            //
                            // arr_currency_key.push(user_key);

                            if (id_delete != '') {
                                id_delete += '|';
                            }

                            id_delete += r_id;

                            if (title_delete != '') {
                                title_delete += '|';
                            }

                            title_delete += r_title;

                            var rhtml_sp = '';
                            t_index = t_index + 1;
                            rhtml_sp += '<li class="select2-selection__choice remove_shop_service" data-id="' + r_id + '" style="cursor: pointer">';
                            rhtml_sp += '<span class="select2-selection__choice__remove">×</span>';
                            rhtml_sp += '<input type="hidden" name="service_attribute[]" data-id="' + r_id + '" value="' + r_title + '" class="d_user_service">';
                            rhtml_sp += r_title;
                            rhtml_sp += '</li>';

                            $('.data__shop__service .select2-selection__danhmuc').css('border-color','#69b3ff');
                            $('#data_shop_service').append(rhtml_sp);

                        }

                    });

                    $('.total_add_shop_service').val(total);

                    $('#serviceModal').modal('hide');

                }
                else{
                    toast('{{ __("Vui lòng chọn dữ liệu cần thêm") }}', 'error');
                }

            });
        })
    </script>
    <script>
        // jQuery(document).ready(function () {
        //     var vl=$('#kt_card_0')
        //
        // })

        $('.kt_card_custom').each(function (idx, elm) {
            var card= new KTCard($(elm).attr('id'));
        });
        $('#kt_select2_2').select2();
        $('#kt_select2_1').select2();
    </script>

    <style>
        .children_count_attribute{
            cursor: pointer;
        }
        .hide{
            display: none;
        }
        #kt_demo_panel.offcanvas{
            width: 410px;
        }
        #kt_demo_panel{
            z-index: 999999;
        }
        .td-attribute-value-title{
            cursor: pointer;
        }
        .group-flex-frames>label:first-child,.group-flex-frames>span:first-child{
            flex: 1;
        }
        .group-flex-frames>input,.group-flex-frames>div{
            flex: 5;
        }
        .cash-limit__elementor__right{
            background: #1bc5bd;
            border-radius: 8px;
            margin: auto auto auto 8px !important;
        }
        .cash-limit__elementor__right i {
            font-size: 26px;
            cursor: pointer;
            color: white;
        }

        .currency-group .group-flex-frames input{
            flex: 2;
        }
        .currency-group .c-unit{
            flex: 2;
            margin: auto;
            padding: 0 12px;
            font-weight: 600;

        }
        .key_name{
            font-weight: 600;
        }
        .variant-log-item.log-active{
            background-color: #f6f8fa;
        }
        .variant-log-item a:first-child{
            font-weight: 600 !important;
        }
        .color-note>div{
            width: 24px;
            height: 24px;
            background-color: pink;
            border-radius: 4px  ;
            border: 0 solid rgba(0,0,0,.5);
            /*box-shadow: 0 0 50px 0 rgb(82 63 105 / 15%)*/
            /*box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;*/
        }
        .color-note>span{
            font-weight: 500;
        }
        .color-note .empty {
            background-color: #ffebe9;
        }
        .color-note .replace {
            background-color: #ddf4ff;
        }
        .color-note .delete {
            background-color: #fff8c5;

        }
        .color-note .skip {
            background-color:#EFEFEF;
            border:1px solid #AAA;
            border-right:1px solid #BBC;
        }
        .color-note .insert {
            /*background-color:#9E9*/
            background-color:#e6ffec

        }
        @media (max-width: 1400px) {

            .currency-group .c-unit {
                flex: 1;

            }
        }
    </style>
    <link href="/assets/backend/assets/css/policy.css?v={{time()}}" rel="stylesheet" type="text/css"/>

@endsection

@section('styles')
    <link rel="stylesheet" type="text/css" href="/assets/backend/assets/css/diffviewv2.css?v={{time()}}"/>
@endsection
