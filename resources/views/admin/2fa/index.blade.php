{{-- Extends layout --}}
@extends('admin._layouts.master')
@section('action_area')
<div class="d-flex align-items-center text-right">
   @if ($user->google2fa_enable == 1 && empty($user->two_factor_recovery_codes))
      <a href="{{route('admin.security-2fa.recovery-code')}}"
         class="btn btn-light-primary font-weight-bolder mr-2">
         Lấy mã khôi phục
      </a>
   @endif
</div>
@endsection
@section('styles')
<link rel="stylesheet" href="/assets/backend/assets/lib/fancybox/jquery.fancybox.min.css" />

@endsection
{{-- Content --}}
@section('content')
<div class="card card-custom" id="kt_page_sticky_card">
   <div class="card-header">
      <div class="card-title">
         <h3 class="card-label">
            Bảo mật tài khoản với Google Authenticator <i class="mr-2"></i>
         </h3>
      </div>
      <div class="card-toolbar"></div>
   </div>
   <div class="card-body">
        @if ($google2fa_enable == 1)
         <form action="{{route('admin.security-2fa.disable2fa')}}" method="POST" id="form-submit">
            @csrf
            <div class="form-group row">
               <div class="col-12 col-md-3">
                   <label for="status" class="form-control-label">{{ __('Trạng thái bảo mật:') }}</label>
                   {{Form::select('status',['0'=>'Tắt','1'=>'Bật'],old('status', isset($user) ? $user->google2fa_enable : null),array('class'=>'form-control','id'=>'status-google2fa'))}}
                   @if($errors->has('status'))
                     <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                   @endif
               </div>
            </div>
            <div class="form-group row" id="google2fa-code" style="display: none">
               <div class="col-12 col-md-3">
                  <div class="form-group">
                     <label>Nhập mã code nhận được từ Google Authenticator </label>
                     <input type="text" class="form-control" name="code" required placeholder="Vui lòng nhập mã code..." value="">
                 </div>
               </div>
            </div>
            <div class="form-group row google2fa-btn-submit" style="display: none">
               <div class="col-12 col-md-3">
                  <button type="submit" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="form-submit">Xác nhận</button>
               </div>
           </div>
         </form>  
        @else
            @include('admin.2fa.huongdan');
        @endif
   </div>
</div>
@endsection
@section('scripts')
<script src="/assets/backend/themes/plugins/custom/wizard/wizard-1.js" type="text/javascript"></script>
<script src="/assets/backend/assets/lib/fancybox/jquery.fancybox.min.js" type="text/javascript"></script>
<script>
   $(document).ready(function(){
      $('body').on('change','#status-google2fa',function(){
         changeStatus();
      })
      function changeStatus(){
         var val = $('#status-google2fa').val();
         if(val == 0){
            $('#google2fa-code').css('display','block');
            $('.google2fa-btn-submit').css('display','block');
            $('#google2fa-code input').val('');
         }
         else{
            $('#google2fa-code').css('display','none');
         }
      }
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
      Fancybox.bind('[data-fancybox="gallerycoverDetail"]', {
         infinite: true,
         thumbs : {
               autoStart : true,
         },
         dragToClose: true,
         animated: true,
         closeButton: "top",
         openSpeed: 300,
         Image: {
               zoom: false,
               // zoom: 200
         },
         caption: function (fancybox, carousel, slide) {
               return (
                  `${slide.index + 1} / ${carousel.slides.length} <br /> + slide.caption`
               );
         },
         slideshow: true,
         Toolbar: {

               display: [
                  { id: "prev", position: "center" },
                  { id: "counter", position: "center" },
                  { id: "next", position: "center" },
                  "close",
               ],

         },

      });
   });
</script>
@endsection