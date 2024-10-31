{{-- Extends layout --}}
@extends('admin._layouts.master')
@section('action_area')
<div class="d-flex align-items-center text-right">
   <a href="{{route('admin.user-qtv.index')}}"
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
{{Form::open(array('route'=>array('admin.user-qtv.update',$data->id),'method'=>'PUT','id'=>'formMain','enctype'=>"multipart/form-data" , 'files' => true))}}
@else
{{Form::open(array('route'=>array('admin.user-qtv.store'),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
@endif
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
            <input type="hidden" name="submit-close" id="submit-close">
            <div class="form-group row">
               <div class="col-12 col-md-6">
                  <label for="username">{{ __('Tên tài khoản')}} <span style="color: red">(*)</span></label>
                  <input type="text" name="username"
                  value="{{ old('username', isset($data) ? $data->username : null) }}"
                  placeholder="{{ __('Tên tài khoản') }}" {{isset($data)?"readonly":""}}  autocomplete="off"
                  class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}">
                  @if ($errors->has('username'))
                  <span class="form-text text-danger">{{ $errors->first('username') }}</span>
                  @endif
               </div>
               {{-----email------}}
               <div class="col-12 col-md-6">
                  <label for="title">{{ __('Email')}} <span style="color: red">(*)</span></label>
                  <input type="text" name="email" value="{{ old('email', isset($data) ? $data->email : null) }}"
                     placeholder="{{ __('Email') }}"
                     class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}">
                  @if ($errors->has('email'))
                  <span class="form-text text-danger">{{ $errors->first('email') }}</span>
                  @endif
               </div>
            </div>
            <div class="form-group row">
               {{-----password------}}
               <div class="col-12 col-md-6">
                  <label for="title">{{ __('Mật khẩu cấp 1')}} <span style="color: red">(*)</span></label>
                  <input type="password" name="password" value=""
                     placeholder="{{ __('Mật khẩu cấp 1') }}" autocomplete="off"
                     class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}">
                  @if ($errors->has('password'))
                  <span class="form-text text-danger">{{ $errors->first('password') }}</span>
                  @endif
               </div>
               {{-----password2------}}
               <div class="col-12 col-md-6">
                  <label for="title">{{ __('Mật khẩu cấp 2')}} <span style="color: red">(*)</span></label>
                  <input type="password" name="password2" value=""
                     placeholder="{{ __('Mật khẩu cấp 2') }}"
                     class="form-control {{ $errors->has('password2') ? ' is-invalid' : '' }}">
                  @if ($errors->has('password2'))
                  <span class="form-text text-danger">{{ $errors->first('password2') }}</span>
                  @endif
               </div>
            </div>
            <div class="form-group row">
               {{-----phone------}}
               <div class="col-12 col-md-6">
                  <label for="title">{{ __('Số điện thoại')}}</label>
                  <input type="text" name="phone" value="{{ old('phone', isset($data) ? $data->phone : null) }}"
                     placeholder="{{ __('Số điện thoại') }}"
                     class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}">
                  @if ($errors->has('phone'))
                  <span class="form-text text-danger">{{ $errors->first('phone') }}</span>
                  @endif
               </div>
               {{-----ip_allow------}}
               <div class="col-12 col-md-6">
                  <label for="title">{{ __('IP truy cập (Nếu nhiều ip ngăn cách với nhau bằng dấu phẩy)')}}</label>
                  <input type="text" name="ip_allow" value="{{ old('ip_allow', isset($data->ip_allow) ? $data->ip_allow : 'all') }}"
                     placeholder="{{ __('IP truy cập') }}"
                     class="form-control {{ $errors->has('ip_allow') ? ' is-invalid' : '' }}">
                  @if ($errors->has('ip_allow'))
                  <span class="form-text text-danger">{{ $errors->first('ip_allow') }}</span>
                  @endif
               </div>
            </div>
            <div class="form-group row">
               {{-----payment_limit------}}
               <div class="col-12 col-md-6">
                  <label for="title">{{ __('Hạn mức thanh toán')}}</label>
                  <input type="text" name="payment_limit" value="{{ old('payment_limit', isset($data) ? $data->payment_limit : config('module.user.payment_limit')) }}"
                     placeholder="{{ __('Hạn mức thanh toán') }}"
                     class="form-control input-price {{ $errors->has('payment_limit') ? ' is-invalid' : '' }}">
                  @if ($errors->has('payment_limit'))
                  <span class="form-text text-danger">{{ $errors->first('payment_limit') }}</span>
                  @endif
               </div>
               {{-----limit_fail_charge------}}
               <div class="col-12 col-md-6">
                  <label for="title">{{ __('Giới hạn block nạp thẻ sai')}}</label>
                  <input type="text" name="limit_fail_charge" value="{{ old('limit_fail_charge', isset($data) ? $data->limit_fail_charge : config('module.user.limit_fail_charge')) }}"
                     placeholder="{{ __('Giới hạn block nạp thẻ sai') }}"
                     class="form-control {{ $errors->has('limit_fail_charge') ? ' is-invalid' : '' }}">
                  @if ($errors->has('limit_fail_charge'))
                  <span class="form-text text-danger">{{ $errors->first('limit_fail_charge') }}</span>
                  @endif
               </div>
            </div>
            <div class="form-group row">
               {{-----account_type------}}
               <div class="col-12 col-md-6">
                  <label for="title">{{ __('Loại tài khoản')}}</label>
{{--                   <input type="text" name="account_type" readonly value="2">--}}
                  {{Form::select('account_type',config('module.user-qtv.account_type'),old('account_type', isset($data) ? $data->account_type : null),array('class'=>'form-control'))}}
                  @if($errors->has('account_type'))
                  <div class="form-control-feedback">{{ $errors->first('account_type') }}</div>
                  @endif
                  @if ($errors->has('account_type'))
                  <span class="form-text text-danger">{{ $errors->first('account_type') }}</span>
                  @endif
               </div>
                {{-----account_type------}}
                <div class="col-12 col-md-6">
                    <label for="title">{{ __('Loại quản trị viên')}}</label>
                    {{--                   <input type="text" name="account_type" readonly value="2">--}}
                    {{Form::select('type_information_ctv',config('module.user-qtv.type_information_ctv'),old('type_information_ctv', isset($data) ? $data->type_information_ctv : null),array('class'=>'form-control'))}}
                    @if($errors->has('type_information_ctv'))
                        <div class="form-control-feedback">{{ $errors->first('type_information_ctv') }}</div>
                    @endif
                    @if ($errors->has('type_information_ctv'))
                        <span class="form-text text-danger">{{ $errors->first('type_information_ctv') }}</span>
                    @endif
                </div>
               {{-----role_id------}}
               @if (isset($data) && $data->type_information == 1 || $user->type_information == 1)
                  <div class="col-12 col-md-6">
                     <label for="title">{{ __('Nhóm vai trò')}}</label>
                     <select name="role_ids[]" multiple="multiple" title="" class="form-control select2 col-md-5"  data-placeholder="{{__('Chọn vai trò')}}" id="kt_select2_2" style="width: 100%">
                     @if( !empty(old('role_id')) )
                     {!!\App\Library\Helpers::buildMenuDropdownListNotIdParent0($roles,old('role_ids')) !!}
                     @else
                     @if(isset($data))
                     {!!\App\Library\Helpers::buildMenuDropdownListNotIdParent0($roles,($data->roles->pluck('id')->toArray())??[]) !!}
                     @else
                     {!!\App\Library\Helpers::buildMenuDropdownListNotIdParent0($roles,null) !!}
                     @endif
                     @endif
                     </select>
                     @if($errors->has('role_ids'))
                     <div class="form-control-feedback text-danger">{{ $errors->first('role_ids') }}</div>
                     @endif
                  </div>
               @else
                  <div class="col-12 col-md-6">
                     <label for="title">{{ __('Nhóm vai trò')}}</label>
                     <select name="role_ids[]" multiple="multiple" title="" class="form-control select2 col-md-5"  data-placeholder="{{__('Chọn vai trò')}}" id="kt_select2_2" style="width: 100%">
                     @if( !empty(old('role_id')) )
                     {!!\App\Library\Helpers::buildMenuDropdownList($roles,old('role_ids')) !!}
                     @else
                     @if(isset($data))
                     {!!\App\Library\Helpers::buildMenuDropdownList($roles,($data->roles->pluck('id')->toArray())??[]) !!}
                     @else
                     {!!\App\Library\Helpers::buildMenuDropdownList($roles,null) !!}
                     @endif
                     @endif
                     </select>
                     @if($errors->has('role_ids'))
                     <div class="form-control-feedback text-danger">{{ $errors->first('role_ids') }}</div>
                     @endif
                  </div>
               @endif

            </div>
{{--            @if (auth()->user()->can('user-qtv-classify'))--}}
{{--            <div class="form-group row">--}}
{{--               --}}{{-----type_information------}}
{{--               <div class="col-12 col-md-6">--}}
{{--                  <label for="title">{{ __('Phân loại:')}}</label>--}}
{{--                  {{Form::select('type_information',[''=>'-- Chưa chọn thông tin --']+config('module.user-qtv.type_information'),old('type_information', isset($data) ? $data->type_information : null),array('class'=>'form-control'))}}--}}
{{--                  @if($errors->has('type_information'))--}}
{{--                  <div class="form-control-feedback">{{ $errors->first('type_information') }}</div>--}}
{{--                  @endif--}}
{{--                  @if ($errors->has('type_information'))--}}
{{--                  <span class="form-text text-danger">{{ $errors->first('type_information') }}</span>--}}
{{--                  @endif--}}
{{--               </div>--}}
{{--            </div>--}}
{{--            @endif--}}
             @if (auth()->user()->can('user-qtv-required-login-gmail'))
                 {{-----required_login_gmail------}}
                 <div class="form-group row">
                     <div class="col-12 col-md-6">
                         <label for="title">{{ __('Yêu cầu đăng nhập với Gmail:')}}</label>
                         {{Form::select('required_login_gmail',[''=>'-- Chưa chọn thông tin --']+config('module.user-qtv.required_login_gmail'),old('required_login_gmail', isset($data) ? $data->required_login_gmail : null),array('class'=>'form-control'))}}
                         @if($errors->has('required_login_gmail'))
                             <div class="form-control-feedback">{{ $errors->first('required_login_gmail') }}</div>
                         @endif
                         @if ($errors->has('required_login_gmail'))
                             <span class="form-text text-danger">{{ $errors->first('required_login_gmail') }}</span>
                         @endif
                     </div>
                 </div>
             @endif
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
                  {{Form::select('status',config('module.user-qtv.status'),old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
                  @if($errors->has('status'))
                  <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                  @endif
               </div>
            </div>
            {{-- status --}}
            <div class="form-group row">
               <div class="col-12 col-md-12">
                  <label for="status" class="form-control-label">{{ __('Kích hoạt ODP') }}</label>
                  {{Form::select('odp_active',[0 =>'Không', 1=>'Có'],old('odp_active', isset($data) ? $data->odp_active : null),array('class'=>'form-control'))}}
                  @if($errors->has('odp_active'))
                  <div class="form-control-feedback">{{ $errors->first('odp_active') }}</div>
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

   jQuery(document).ready(function () {

   });

   $(document).ready(function () {

       // Demo 6
       $('.datetimepicker-default').datetimepicker({
           useCurrent: true,
           autoclose: true,
           format: "DD/MM/YYYY HH:mm:ss"
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


   // function removeAttSelect(ele,ele_rm){

   // }
   $('body').on('change','#kt_select2_4',function(){
       $("#kt_select2_3").val('');
       $("#shop_access .select2-selection__choice").remove();;
   })
   $('body').on('change','#kt_select2_3',function(){
       $("#kt_select2_4").val('');
       $("#shop_expect .select2-selection__choice").remove();;
   })


</script>
@endsection
