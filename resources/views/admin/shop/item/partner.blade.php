{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{ url('/admin/shop/'.$data->id.'/edit') }}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>
    </div>
@endsection
{{-- Content --}}
@section('content')
    @php
        $index = 0;
        if (empty($data->ntn_partner_key)){
            $index = $index + 1;
        }
        if (empty($data->ccc_partner_key)){
            $index = $index + 1;
        }
        if (empty($data->ntn_partner_key_card)){
            $index = $index + 1;
        }
        if (empty($data->tichhop_key)){
             $index = $index + 1;
        }
        if (empty($data->daily_partner_key_service)){
             $index = $index + 1;
        }
        if (empty($data->ppp_partner_key)){
             $index = $index + 1;
        }
    @endphp

    @if($index > 0)
        <div class="alert alert-danger" role="alert">
            @if (empty($data->ntn_partner_key))
                *  Chưa có thông tin key nạp thẻ NTN
                <br>
            @endif
            @if (empty($data->ccc_partner_key))
                * Chưa có thông tin key nạp thẻ CCC
                <br>
            @endif
            @if (empty($data->ntn_partner_key_card))
                * Chưa có thông tin key mua thẻ
                <br>
            @endif
            @if (empty($data->tichhop_key))
                * Chưa có thông tin key tích hợp
                <br>
            @endif
            @if (empty($data->daily_partner_key_service))
                * Chưa có thông tin key đại lý
                <br>
            @endif
            @if (empty($data->ppp_partner_key))
                * Chưa có thông tin key PPP
                <br>
            @endif
        </div>
    @else
        <span ><i class="fas fa-check-circle" style="color: #0a90eb;margin-right: 4px"></i> Đã đầy đủ thông tin</span>
    @endif
    <div class="row" style="margin-top: 8px">
        <div class="col-lg-6">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            Nạp thẻ nhanh <i class="mr-2"></i>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    {{Form::open(array('route'=>array('admin.'.$module.'.partner','ntn'),'method'=>'POST','id'=>'formNTN','enctype'=>"multipart/form-data"))}}
                    <input type="hidden" name="shop_id" value="{{$data->id}}">
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tài khoản') }} <span style="color: red">*</span></label>
                            <input type="text" name="ntn_username" value="{{ old('ntn_username', isset($data->ntn_username) ? $data->ntn_username : 'tt_'.\Str::replace('.', '', $data->domain)) }}" autofocus
                                   placeholder="{{ __('ntn_username') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ntn_username') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ntn_username'))
                                <span class="form-text text-danger">{{ $errors->first('ntn_username') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Mật khẩu') }} <span style="color: red">*</span></label>
                            <input type="password" name="ntn_password" value="{{ old('ntn_password', isset($data->ntn_password) ? '**********' : null ) }}" autofocus
                                   placeholder="{{ __('Mật khẩu') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ntn_password') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ntn_password'))
                                <span class="form-text text-danger">{{ $errors->first('ntn_password') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Partner_id NTN') }} <span style="color: red">*</span></label>
                            <input type="text" id="ntn_partner_id"value="{{ old('ntn_partner_id', isset($data->ntn_partner_id) ? '*********' : null ) }}" autofocus
                                   placeholder="{{ __('Partner_id NTN') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ntn_partner_id') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ntn_partner_id'))
                                <span class="form-text text-danger">{{ $errors->first('ntn_partner_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Partner_key NTN') }} <span style="color: red">*</span></label>
                            <input type="text" id="ntn_partner_key"value="{{ old('ntn_partner_key', isset($data->ntn_partner_key) ? '*********' : null ) }}" autofocus
                                   placeholder="{{ __('Partner_key NTN') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ntn_partner_key') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ntn_partner_key'))
                                <span class="form-text text-danger">{{ $errors->first('ntn_partner_key') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <button type="submit" class="btn btn-primary mr-2">Lấy thông tin</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            Cần câu cơm <i class="mr-2"></i>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    {{Form::open(array('route'=>array('admin.'.$module.'.partner','ccc'),'method'=>'POST','id'=>'formCCC','enctype'=>"multipart/form-data"))}}
                    <input type="hidden" name="shop_id" value="{{$data->id}}">
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tài khoản') }} <span style="color: red">*</span></label>
                            <input type="text" name="ccc_username" value="{{ old('ccc_username', isset($data->ccc_username) ? $data->ccc_username : 'tt_'.\Str::replace('.', '', $data->domain)) }}" autofocus
                                   placeholder="{{ __('ccc_username') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ccc_username') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ccc_username'))
                                <span class="form-text text-danger">{{ $errors->first('ccc_username') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Mật khẩu') }} <span style="color: red">*</span></label>
                            <input type="password" name="ccc_password" value="{{ old('ccc_password', isset($data->ccc_password) ? '**********' : null ) }}" autofocus
                                   placeholder="{{ __('Mật khẩu') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ccc_password') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ccc_password'))
                                <span class="form-text text-danger">{{ $errors->first('ccc_password') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Partner_id CCC') }} <span style="color: red">*</span></label>
                            <input type="text" id="ccc_partner_id"value="{{ old('ccc_partner_id', isset($data->ccc_partner_id) ? '*********' : null ) }}" autofocus
                                   placeholder="{{ __('Partner_id CCC') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ccc_partner_id') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ccc_partner_id'))
                                <span class="form-text text-danger">{{ $errors->first('ccc_partner_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Partner_key CCC') }} <span style="color: red">*</span></label>
                            <input type="text" id="ccc_partner_key"value="{{ old('ccc_partner_key', isset($data->ccc_partner_key) ? '*********' : null ) }}" autofocus
                                   placeholder="{{ __('Partner_key CCC') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ccc_partner_key') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ccc_partner_key'))
                                <span class="form-text text-danger">{{ $errors->first('ccc_partner_key') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <button type="submit" class="btn btn-primary mr-2">Lấy thông tin</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            Tích hợp <i class="mr-2"></i>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    {{Form::open(array('route'=>array('admin.'.$module.'.partner','tichhop'),'method'=>'POST','id'=>'formTichHop','enctype'=>"multipart/form-data"))}}
                    <input type="hidden" name="shop_id" value="{{$data->id}}">
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tài khoản') }} <span style="color: red">*</span></label>
                            <input type="text" name="tichhop_username" value="{{ old('tichhop_username', isset($data->tichhop_username) ? $data->tichhop_username : 'tt_'.\Str::replace('.', '', $data->domain)) }}" autofocus
                                   placeholder="{{ __('tichhop_username') }}" maxlength="120"
                                   class="form-control {{ $errors->has('tichhop_username') ? ' is-invalid' : '' }}">
                            @if ($errors->has('tichhop_username'))
                                <span class="form-text text-danger">{{ $errors->first('tichhop_username') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Mật khẩu') }} <span style="color: red">*</span></label>
                            <input type="password" name="tichhop_password" value="{{ old('tichhop_password', isset($data->tichhop_password) ? '**********' : null ) }}" autofocus
                                   placeholder="{{ __('Mật khẩu') }}" maxlength="120"
                                   class="form-control {{ $errors->has('tichhop_password') ? ' is-invalid' : '' }}">
                            @if ($errors->has('tichhop_password'))
                                <span class="form-text text-danger">{{ $errors->first('tichhop_password') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Partner_id NTN') }} <span style="color: red">*</span></label>
                            <input type="text" id="tichhop_key"value="{{ old('tichhop_key', isset($data->tichhop_key) ? '*********' : null ) }}" autofocus
                                   placeholder="{{ __('Key tích hợp') }}" maxlength="120"
                                   class="form-control {{ $errors->has('tichhop_key') ? ' is-invalid' : '' }}">
                            @if ($errors->has('tichhop_key'))
                                <span class="form-text text-danger">{{ $errors->first('tichhop_key') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <button type="submit" class="btn btn-primary mr-2">Lấy thông tin</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            Đại lý <i class="mr-2"></i>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    {{Form::open(array('route'=>array('admin.'.$module.'.partner','daily'),'method'=>'POST','id'=>'formDaiLy','enctype'=>"multipart/form-data"))}}
                    <input type="hidden" name="shop_id" value="{{$data->id}}">
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tài khoản') }} <span style="color: red">*</span></label>
                            <input type="text" name="daily_username" value="{{ old('daily_username', isset($data->daily_username) ? $data->daily_username : 'tt_'.\Str::replace('.', '', $data->domain)) }}" autofocus
                                   placeholder="{{ __('daily_username') }}" maxlength="120"
                                   class="form-control {{ $errors->has('daily_username') ? ' is-invalid' : '' }}">
                            @if ($errors->has('daily_username'))
                                <span class="form-text text-danger">{{ $errors->first('daily_username') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Mật khẩu') }} <span style="color: red">*</span></label>
                            <input type="password" name="daily_password" value="{{ old('daily_password', isset($data->daily_password) ? '**********' : null ) }}" autofocus
                                   placeholder="{{ __('Mật khẩu') }}" maxlength="120"
                                   class="form-control {{ $errors->has('daily_password') ? ' is-invalid' : '' }}">
                            @if ($errors->has('daily_password'))
                                <span class="form-text text-danger">{{ $errors->first('daily_password') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Key DaiLy') }} <span style="color: red">*</span></label>
                            <input type="text" id="daily_key"value="{{ old('daily_key', isset($data->daily_key) ? '*********' : null ) }}" autofocus
                                   placeholder="{{ __('Key đại lý') }}" maxlength="120"
                                   class="form-control {{ $errors->has('daily_key') ? ' is-invalid' : '' }}">
                            @if ($errors->has('daily_key'))
                                <span class="form-text text-danger">{{ $errors->first('daily_key') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <button type="submit" class="btn btn-primary mr-2">Lấy thông tin</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            PAYPAYPAY <i class="mr-2"></i>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    {{Form::open(array('route'=>array('admin.'.$module.'.partner','paypaypay'),'method'=>'POST','id'=>'formPaypaypay','enctype'=>"multipart/form-data"))}}
                    <input type="hidden" name="shop_id" value="{{$data->id}}">
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tài khoản') }} <span style="color: red">*</span></label>
                            <input type="text" name="ppp_username" value="{{ old('ppp_username', isset($data->ppp_username) ? $data->ppp_username : 'tt_'.\Str::replace('.', '', $data->domain)) }}" autofocus
                                   placeholder="{{ __('ppp_username') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ppp_username') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ppp_username'))
                                <span class="form-text text-danger">{{ $errors->first('ppp_username') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Mật khẩu') }} <span style="color: red">*</span></label>
                            <input type="password" name="ppp_password" value="{{ old('ppp_password', isset($data->ppp_password) ? '**********' : null ) }}" autofocus
                                   placeholder="{{ __('Mật khẩu') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ppp_password') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ppp_password'))
                                <span class="form-text text-danger">{{ $errors->first('ppp_password') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Partner_id PPP') }} <span style="color: red">*</span></label>
                            <input type="text" id="ppp_partner_id"value="{{ old('ppp_partner_id', isset($data->ppp_partner_id) ? '*********' : null ) }}" autofocus
                                   placeholder="{{ __('Partner_id PPP') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ppp_partner_id') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ppp_partner_id'))
                                <span class="form-text text-danger">{{ $errors->first('ppp_partner_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Partner_id NTN') }} <span style="color: red">*</span></label>
                            <input type="text" id="ppp_partner_key"value="{{ old('ppp_partner_key', isset($data->ppp_partner_key) ? '*********' : null ) }}" autofocus
                                   placeholder="{{ __('Key PPP') }}" maxlength="120"
                                   class="form-control {{ $errors->has('ppp_partner_key') ? ' is-invalid' : '' }}">
                            @if ($errors->has('ppp_partner_key'))
                                <span class="form-text text-danger">{{ $errors->first('ppp_partner_key') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <button type="submit" class="btn btn-primary mr-2">Lấy thông tin</button>
                        </div>
                    </div>
                    {{ Form::close() }}
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
        jQuery(document).ready(function () {
            $('#formNTN').submit(function (e) {
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
                            toast(data.message);
                            $('#ntn_partner_id').val('***********')
                            $('#ntn_partner_key').val('***********')
                        }
                        else{
                            formSubmit.trigger("reset")
                            toast(data.message, 'error');
                        }
                    },
                    error: function (data) {
                        formSubmit.trigger("reset")
                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function (data) {
                        btnSubmit.text('Đăng nhập');
                        btnSubmit.prop('disabled', false);
                    }
                });
            })
            $('#formCCC').submit(function (e) {
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
                            toast(data.message);
                            $('#ccc_partner_id').val('***********')
                            $('#ccc_partner_key').val('***********')
                        }
                        else{
                            formSubmit.trigger("reset")
                            toast(data.message, 'error');
                        }
                    },
                    error: function (data) {
                        formSubmit.trigger("reset")
                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function (data) {
                        btnSubmit.text('Đăng nhập');
                        btnSubmit.prop('disabled', false);
                    }
                });
            })
            $('#formTichHop').submit(function (e) {
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
                            toast(data.message);
                            $('#tichhop_key').val('***********')
                        }
                        else{
                            formSubmit.trigger("reset")
                            toast(data.message, 'error');
                        }
                    },
                    error: function (data) {
                        formSubmit.trigger("reset")
                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function (data) {
                        btnSubmit.text('Đăng nhập');
                        btnSubmit.prop('disabled', false);
                    }
                });
            })
            $('#formDaiLy').submit(function (e) {
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
                            toast(data.message);
                            $('#daily_key').val('***********')
                        }
                        else{
                            formSubmit.trigger("reset")
                            toast(data.message, 'error');
                        }
                    },
                    error: function (data) {
                        formSubmit.trigger("reset")
                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function (data) {
                        btnSubmit.text('Đăng nhập');
                        btnSubmit.prop('disabled', false);
                    }
                });
            })
            $('#formPaypaypay').submit(function (e) {
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
                            toast(data.message);
                            $('#ppp_partner_id').val('***********')
                            $('#ppp_partner_key').val('***********')
                        }
                        else{
                            formSubmit.trigger("reset")
                            toast(data.message, 'error');
                        }
                    },
                    error: function (data) {
                        formSubmit.trigger("reset")
                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function (data) {
                        btnSubmit.text('Đăng nhập');
                        btnSubmit.prop('disabled', false);
                    }
                });
            })
        })
    </script>
@endsection


