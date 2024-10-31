{{-- Extends layout --}}
@extends('admin._layouts.master')
@section('action_area')
@endsection
{{-- Content --}}
@section('content')
<style>
    .copyData{
        cursor: pointer;
    }
</style>
<div class="card card-custom" id="kt_page_sticky_card">
   <div class="card-header">
      <div class="card-title">
         <h3 class="card-label">
            Cài đặt bảo mật Google Authenticator <i class="mr-2"></i>
         </h3>
      </div>
      <div class="card-toolbar"></div>
   </div>
   <div class="card-body">
    <div class="text-center">
        <div class="row" id="body-code">
            <div class="col-md-4 m-auto col-sm-12">
                <h4>Mở App Google Authenticator và quét mã phía dưới:</h4>
                <img src="{{$google2fa_url}}" alt="">
                <form method="POST" id="form-setup" action="{{ route('admin.security-2fa.setup') }}">
                    @csrf
                    <div class="form-group text-left">
                        <label>Nhập mã code nhận được từ Google Authenticator </label>
                        <input type="text" class="form-control" name="code" required placeholder="Vui lòng nhập mã code..." value="">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success font-weight-bolder">Xác nhận</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
   </div>
</div>
@endsection
@section('scripts')
<script src="/assets/backend/themes/plugins/custom/wizard/wizard-1.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $('#form-setup').submit(function (e) {
            e.preventDefault();
            var formSubmit = $(this);
            var url = formSubmit.attr('action');
            var btnSubmit = formSubmit.find(':submit');
            btnSubmit.text('Đang xử lý...');
            btnSubmit.prop('disabled', true);
            $.ajax({
                type: "POST",
                url: url,
                cache:false,
                data: formSubmit.serialize(), // serializes the form's elements.
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if(data.status == 1){
                        $('#body-code').remove();
                        swal()
                            function swal(){
                                Swal.fire({
                                    title: 'Thông báo',
                                    html: '<p>Bạn đã cấu hình thành công. Mã code khôi phục của bạn là: </p><h2><b>'+data.two_factor_recovery_codes+'</b> <span class="copyData" data-copy="'+data.two_factor_recovery_codes+'"><i class="flaticon2-copy"></i></span></h2>. <p>Vui lòng lưu trữ mã này để khôi phục ứng dụng google 2FA khi cần thiết.</p>',
                                    icon: 'success',
                                    confirmButtonText: 'Xác nhận',
                                    allowOutsideClick:false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = data.redirect;
                                    }
                                });
                            }
                    }
                    else{
                        toast(data.message, 'error');
                        btnSubmit.text('Xác nhận');
                        btnSubmit.prop('disabled', false);
                    }
                },
                error: function (data) {
                    toast('{{__('Lỗi hệ thống, vui lòng liên hệ QTV để xử lý')}}', 'error');
                    btnSubmit.text('Xác nhận');
                    btnSubmit.prop('disabled', false);
                },
                complete: function (data) {
                
                }
            });
        });
        $('body').on('click','.copyData',function(){
                data = $(this).data('copy');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val($.trim(data)).select();
                document.execCommand("copy");
                $temp.remove();
                toastr.success('Đã sao chép: '+ data);
            })
    });
</script>
@endsection