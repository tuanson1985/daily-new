{{-- Extends layout --}}
@extends('admin._layouts.master')
@section('action_area')
<div class="d-flex align-items-center text-right">
   <div class="btn-group">
      <a href="{{route('admin.plusmoney-report-qtv.index')}}" type="button" class="btn btn-danger font-weight-bolder mr-2">
      <i class="flaticon-list-3"></i>
      {{__('Lịch sử cộng/trừ tiền QTV(CTV)')}}
      </a>
   </div>
   <button type="button" class="btn btn-success font-weight-bolder" data-toggle="modal"
      data-target="#confirmModal">
   <i class="ki ki-check icon-sm"></i>
   {{__('Xác nhận')}}
   </button>
</div>
@endsection
{{-- Content --}}
@section('content')
@if ($message = session()->get('success'))
<div class="m-subheader ">
   <div class="alert alert-success alert-dismissible fade show" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      </button>
      <i class="glyphicon glyphicon-ok"></i> {{$message}}
   </div>
</div>
@endif
@if($messages=$errors->all())
<div class="m-subheader ">
   <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      </button>
      <strong>Lỗi !</strong> {{$messages[0]}}
   </div>
</div>
@endif
{{Form::open(array('route'=>array('admin.post_money_qtv'),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
<div class="card card-custom" id="kt_page_sticky_card">
   <div class="card-header">
      <div class="card-title">
         <h3 class="card-label">
            {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
         </h3>
      </div>
      <div class="card-toolbar"></div>
   </div>
   <div class="card-body">
      <div class="row">
         <div class="col-md-6">
            <div class="form-group row u_checker">
               <div class="col-12 col-md-12">
                  <div class="input-group">
                     <div class="input-group-prepend">
                        {{Form::select('field',['username'=>'Tài khoản','id'=>"ID"],old('field', request('field')),array('id'=>'field','class'=>'form-control input-group-text'))}}
                     </div>
                     <input type="text" name="username" id="user_input"
                        value="{{ old('value', request('value') ? request('value') : null) }}"
                        class="form-control" placeholder="Tìm kiếm..." autocomplete="off">
                     <div class="input-group-append">
                        <button type="button" id="btnChecker" class="btn btn-success">Kiểm tra</button>
                     </div>
                  </div>
                  <span class="form-text"></span>
               </div>
            </div>
            {{--form-group --}}
            <div class="form-group row">
               {{-- mode --}}
               <label for="mode" class="col-12 col-md-4 col-form-labe text-md-right">{{ __('Loại giao dịch') }} <span  style="color: red">(*)</span></label>
               <div class="col-12 col-md-8">
                  {{Form::select('mode',[1=>'Cộng tiền',0=>'Trừ tiền'],old('mode', request('mode')),array('class'=>'form-control'))}}
                  @if($errors->has('mode'))
                  <div class="form-control-feedback">{{ $errors->first('mode') }}</div>
                  @endif
               </div>
            </div>
            {{-----form-group------}}
            <div class="form-group row">
               {{-----amount------}}
               <label for="title" class="col-form-label text-md-right col-12 col-md-4">{{ __('Số tiền')}} <span style="color: red">(*)</span> </label>
               <div class="col-12 col-md-8">
                  <input name="amount" value="{{ old('amount', isset($data) ? $data->amount : null) }}"
                     placeholder="{{ __('Số tiền') }}"
                     class="form-control  input-price  {{ $errors->has('amount') ? ' is-invalid' : '' }}"
                     autocomplete="off">
                  @if ($errors->has('amount'))
                  <span class="form-text text-danger">{{ $errors->first('amount') }}</span>
                  @endif
               </div>
            </div>
            {{-----form-group------}}
            <div class="form-group row">
               {{-- source_type --}}
               <label for="source_type" class="col-form-label text-md-right col-12 col-md-4">{{ __('Nguồn cộng tiền từ') }} <span   style="color: red">(*)</span></label>
               <div class="col-12 col-md-8">
                  <select name="source_type" id="source_type" class="form-control {{ $errors->has('amount') ? ' is-invalid' : '' }}" required>
                     <option value="">-- Nguồn tiền cộng --</option>
                     <option value="1" {{old('source_type')=="1"?"selected":""}}>
                     ATM
                     </option>
                     <option value="2" {{old('source_type')=="2"?"selected":""}}>Ví
                     điện tử
                     </option>
                     <option value="4" {{old('source_type')=="4"?"selected":""}}>
                     MOMO
                     </option>
                     <option value="5" {{old('source_type')=="5"?"selected":""}}>
                     Tiền PR
                     </option>
                     <option value="6" {{old('source_type')=="6"?"selected":""}}>
                     Tiền test
                     </option>
                     <option value="7" {{old('source_type')=="7"?"selected":""}}>
                     Tiền thẻ lỗi
                     </option>
                     <option value="3" {{old('source_type')=="3"?"selected":""}}>
                     Khác
                     </option>
                  </select>
                  @if($errors->has('source_type'))
                  <div
                     class="form-control-feedback text-danger ">{{ $errors->first('source_type') }}</div>
                  @endif
               </div>
            </div>
            {{-----form-group------}}
            <div class="form-group row">
               {{-- source_bank --}}
               <label for="source_bank" class="col-form-label text-md-right col-12 col-md-4">{{ __('Ngân hàng/ví') }}</label>
               <div class="col-12 col-md-8">
                  <select name="source_bank" id="source_bank"
                     class="form-control m-input m-input--air {{ $errors->has('amount') ? ' is-invalid' : '' }}"
                     required>
                     <option value="">-- Ngân hàng/ví --</option>
                     <option class="c1" value="VIETCOMBANK" {{old('source_bank')=="1"?"selected":""}}>
                     Vietcombank
                     </option>
                     <option class="c1" value="VIETTINBANK" {{old('source_bank')=="2"?"selected":""}}>
                     Viettinbank
                     </option>
                     <option class="c1" value="AGRIBANK" {{old('source_bank')=="4"?"selected":""}}>
                     Agribank
                     </option>
                     <option class="c1" value="TECHCOMBANK" {{old('source_bank')=="5"?"selected":""}}>
                     Techcombank
                     </option>
                     <option class="c1" value="MBBANK" {{old('source_bank')=="6"?"selected":""}}>
                     Mbbank
                     </option>
                     <option class="c1" value="BIDV" {{old('source_bank')=="7"?"selected":""}}>
                     BIDV
                     </option>
                     {{-------}}
                     <option class="c2" value="TCSR" {{old('source_bank')=="TCSR"?"selected":""}}>
                     TCSR (Thecaosieure.com)
                     </option>
                     <option class="c2" value="TSR" {{old('source_bank')=="TSR"?"selected":""}}>
                     Tsr(thesieure.com)
                     </option>
                     <option class="c2" value="TKCR" {{old('source_bank')=="TKCR"?"selected":""}}>
                     Tkcr(tkcr.vn)
                     </option>
                     <option class="c2" value="AZPRO" {{old('source_bank')=="AZPRO"?"selected":""}}>
                     AZPRO
                     </option>
                     {{----MOMO---}}
                     <option class="c4" value="MOMO2869" {{old('source_bank')=="MOMO2869"?"selected":""}}>
                     MOMO2869
                     </option>
                     <option class="c4" value="MOMO2442" {{old('source_bank')=="MOMO2442"?"selected":""}}>
                     MOMO2442
                     </option>
                     <option class="c4" value="MOMO3323" {{old('source_bank')=="MOMO3323"?"selected":""}}>
                     MOMO3323
                     </option>
                     <option class="c4" value="MOMO2928" {{old('source_bank')=="MOMO2928"?"selected":""}}>
                     MOMO2928
                     </option>
                     <option class="c4" value="MOMO4666" {{old('source_bank')=="MOMO4666"?"selected":""}}>
                     MOMO4666
                     </option>
                     <option class="c4" value="MOMO0556" {{old('source_bank')=="MOMO0556"?"selected":""}}>
                     MOMO0556
                     </option>
                     <option class="c4" value="MOMO9872" {{old('source_bank')=="MOMO9872"?"selected":""}}>
                     MOMO9872
                     </option>
                     <option class="c4" value="MOMO4555" {{old('source_bank')=="MOMO4555"?"selected":""}}>
                     MOMO4555
                     </option>
                  </select>
                  @if($errors->has('source_bank'))
                  <div
                     class="form-control-feedback text-danger ">{{ $errors->first('source_bank') }}</div>
                  @endif
               </div>
            </div>
            {{-- description --}}
            <div class="form-group row">
               <label for="mode" class="col-form-label text-md-right col-12 col-md-4">{{ __('Nội dung') }}</label>
               <div class="col-12 col-md-8">
                  <textarea style="min-height:100px;" type="text"
                     class="form-control"
                     name="description" placeholder="Nội dung">{{old('description')}}</textarea>
               </div>
            </div>
            {{-----form-group------}}
            <div class="form-group row">
               {{-----password2------}}
               <label for="password2" class="col-form-label text-md-right col-12 col-md-4">{{ __('Mật khẩu cấp 2')}} <span style="color: red">(*)</span> </label>
               <div class="col-12 col-md-8">
                  <input type="password" name="password2" value=""
                     placeholder="{{ __('Mật khẩu cấp 2') }}"
                     class="form-control {{ $errors->has('password2') ? ' is-invalid' : '' }}"
                     autocomplete="off">
                  @if ($errors->has('password2'))
                  <span class="form-text text-danger">{{ $errors->first('password2') }}</span>
                  @endif
               </div>
            </div>
             @if (Auth::user()->google2fa_enable == 1)
                 {{-----form-group------}}
                 <div class="form-group row">
                     {{-----google2fa-code------}}
                     <label for="google2fa-code" class="col-form-label text-md-right col-12 col-md-4">{{ __('Mã bảo mật google 2FA')}} <span style="color: red">(*)</span> </label>
                     <div class="col-12 col-md-8">
                         <input type="text" name="google2fa-code" value=""
                                placeholder="{{ __('Mã bảo mật google 2FA') }}"
                                class="form-control {{ $errors->has('google2fa-code') ? ' is-invalid' : '' }}"
                                autocomplete="off">
                         @if ($errors->has('google2fa-code'))
                             <span class="form-text text-danger">{{ $errors->first('google2fa-code') }}</span>
                         @endif
                     </div>
                 </div>
             @endif
         </div>
         <div class="col-md-6 info_user"></div>
      </div>
   </div>
</div>
{{ Form::close() }}
<!-- confirmModal -->
<div class="modal fade" id="confirmModal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i aria-hidden="true" class="ki ki-close"></i>
            </button>
         </div>
         <div class="modal-body">
            {{__('Bạn thực sự muốn thực hiện tao tác này?')}}
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
            <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom"
               data-form="formMain" data-submit-close="1">
            <i class="ki ki-check icon-sm"></i>
            {{__('Xác nhận')}}
            </button>
         </div>
      </div>
   </div>
</div>
@endsection
{{-- Styles Section --}}
@section('styles')
@endsection
{{-- Scripts Section --}}
@section('scripts')
<script>
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
       //btn source bank
       $("#source_bank option").hide();
       $("#source_type").change(function () {
           $("#source_bank option").hide();
           $("#source_bank").val('')
           var parrent = this.value;
           if (parrent == 1) {
               $(".c1").show();
           } else if (parrent == 2) {
               $(".c2").show();

           } else if (parrent == 4) {
               $(".c4").show();
           } else {
               $("#source_bank option").hide();
           }
       });
       //end btn source bank
       $("#btnChecker").click(function () {
           var ur = $('#user_input').val();
           var field = $('#field').val();
           var shop_id = $('#shop_id').val();
           $(this).addClass('spinner spinner-track spinner-primary spinner-right pr-15 disabled');
           $.get('{{route('admin.get_user_to_money_qtv')}}' + '?username=' + ur + '&field=' + field,
               function (data) {
                   $('.u_checker').removeClass('has-danger').addClass('has-success')
                   $('.u_checker').find('.form-text').html('Đã tìm thấy thông tin tài khoản');
                   $('.info_user').html('');
                   $('.info_user').html(data);

               })
               .fail(function () {
                   $('#balance').val('').trigger('input')
                   $('.u_checker').removeClass('has-success').addClass('has-danger')
                   $('.u_checker').find('.form-text').html('Không tìm thấy thông tin tài khoản');
                   $('.info_user').html('');
               })
               .always(function () {
                   $('#btnChecker').removeClass('spinner spinner-track spinner-primary spinner-right pr-15 disabled');
               });
       });
       if ($('#user_input').val() != "") {
           $("#btnChecker").click();
       }
       $('.btn-confirm').click(function (e) {
           e.preventDefault();
           var btnThis = $(this);
           btnThis.attr('disabled', true)
           var form = $(this).closest('form');
           bootbox.confirm({
               message: "Bạn thực sự muốn thực hiện tao tác này",
               buttons: {
                   confirm: {
                       label: 'Xác nhận',
                       className: 'btn-success'
                   },
                   cancel: {
                       label: 'Đóng',
                   }
               },
               callback: function (result) {
                   if (result == true) {
                       form.submit();
                   } else {
                       btnThis.attr('disabled', false)
                   }
               }
           })
       });
   });
</script>
@endsection
