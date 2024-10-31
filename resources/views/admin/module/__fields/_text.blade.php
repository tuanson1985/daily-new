

{{--//giải thích phần value--}}
{{--1. nếu old không có sẽ lấy dữ liệu từ params--}}
{{--2. Nếu param không có sẽ lấy giá trị mặc của params trong config--}}

@php
    $nameOld=str_replace(']','',  Arr::get($fields,'name'));
    $nameOld=str_replace('params[','params.',  $nameOld)
@endphp
<label class="form-control-label">{{ __(Arr::get($fields,'label')) }}</label>
<input type="{{ Arr::get($fields,'type') }}"
       name="{{ Arr::get($fields,'name') }}"
       value="{{ old($nameOld, $params->{ str_replace(['params[',']'],'',  Arr::get($fields,'name'))}?? Arr::get($fields,'value') )}}"
       id="{{ Arr::get($fields,'name') }}"
       placeholder="{{ __(Arr::get($fields,'label')) }}"
       class="form-control {{ Arr::get( $fields, 'class') }} {{ $errors->has(Arr::get($fields,'name')) ? ' is-invalid' : '' }}">

{{--check thông báo lỗi--}}
@if($errors->has(Arr::get($fields,'name')))
    <div class="form-control-feedback">{{ $errors->first(  Arr::get($fields,'name')) }}</div>
@endif
