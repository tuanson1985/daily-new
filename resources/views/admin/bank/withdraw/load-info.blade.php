@if($data->bank->bank_type==0)
    <div class="form-group m-form__group row">
        <label for="example-text-input" class="col-sm-3 col-form-label">
            Ngân hàng
        </label>
        <div class="col-sm-7">
            <input name="bank_title" value="{{$data->bank->title}}" class="form-control m-input" type="text" readonly>
        </div>
    </div>

    <div class="form-group m-form__group row">
        <label for="example-text-input" class="col-sm-3 col-form-label">
            Chủ tài khoản
        </label>
        <div class="col-sm-7">
            <input name="holder_name" value="{{$data->holder_name}}" class="form-control m-input" type="text" readonly>
        </div>
    </div>
    <div class="form-group m-form__group row">
        <label for="example-text-input" class="col-sm-3 col-form-label">
            Số tài khoản
        </label>
        <div class="col-sm-7">
            <input name="account_number" value="{{$data->account_number}}" class="form-control m-input" type="text" readonly>
        </div>
    </div>

    <div class="form-group m-form__group row">
        <label for="example-text-input" class="col-sm-3 col-form-label">
            Chi nhánh
        </label>
        <div class="col-sm-7">
            <input name="" value="{{$data->brand}}" class="form-control m-input" type="text" readonly>
        </div>
    </div>
@elseif($data->bank->bank_type==1)

    <div class="form-group m-form__group row">
        <label for="example-text-input" class="col-sm-3 col-form-label">
            Ví điện tử
        </label>
        <div class="col-sm-7">
            <input name="bank_title" value="{{$data->bank->title}}" class="form-control m-input" type="text" readonly>
        </div>
    </div>

    <div class="form-group m-form__group row">
        <label for="example-text-input" class="col-sm-3 col-form-label">
            Tài khoản ví
        </label>
        <div class="col-sm-7">
            <input name="account_vi" value="{{$data->account_vi}}" class="form-control m-input" type="text" readonly>
        </div>
    </div>

@endif

<div class="form-group m-form__group row">
    <label for="example-text-input" class="col-sm-3 col-form-label ">
        Số tiền cần rút <span class="required" aria-required="true"> * </span>
    </label>
    <div class="col-sm-7">
        <input name="amount" value="" class="form-control m-input input-price" type="text" placeholder="Số tiền cần rút">
        <p class="m-form__help" style="font-size: 14px">Số tiền rút từ 100.000đ đến 10.000.000đ</p>
        @if($data->bank->fee_type==0)

            <p class="m-form__help" style="font-size: 14px">Phí rút tiền: {{number_format($data->bank->fee)}}đ (Không trừ vào số tiền rút)</p>
        @else
            <p class="m-form__help" style="font-size: 14px">Phí rút tiền: {{$data->bank->fee}}% (Không trừ vào số tiền rút)</p>
        @endif
    </div>
</div>

<div class="form-group m-form__group row">
    <label for="example-text-input" class="col-sm-3 col-form-label">
        Nội dung rút tiền
    </label>
    <div class="col-sm-7">
        <input name="description" value="" class="form-control m-input" type="text" placeholder="Nhập nội dung rút tiền nếu cần thiết">
    </div>
</div>

<div class="form-group m-form__group row">
    <label for="example-text-input" class="col-sm-3 col-form-label">
        Mật khẩu cấp 2 (*)
    </label>
    <div class="col-sm-7">
        <input name="password2" type="password" value="" class="form-control m-input" type="text" placeholder="Mật khẩu cấp 2">
    </div>
</div>



<script>
    jQuery(document).ready(function () {

        $('.input-price').mask('000.000.000.000.000', {reverse: true});
        $('.input-price').focus();
    });

</script>
