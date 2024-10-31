
@php
    $nameOld=str_replace(']','',  Arr::get($fields,'name'));
    $nameOld=str_replace('params[','params.',  $nameOld)
@endphp
<label class="form-control-label">{{ __(Arr::get($fields,'label')) }}</label>
<textarea  id="{{ Arr::get($fields,'name') }}"
           name="{{ Arr::get($fields,'name') }}"
           class="form-control ckeditor-basic {{ Arr::get( $fields, 'class') }} {{ $errors->has(Arr::get($fields,'name')) ? ' is-invalid' : '' }}"
           data-height="{{Arr::get($fields,'height')!=""?Arr::get($fields,'height'):"400" }}px"  data-startup-mode="" >{{ old( $nameOld, $params->{ str_replace(['params[',']'],'',  Arr::get($fields,'name'))}?? Arr::get($fields,'value') )}}
</textarea>
{{--check thông báo lỗi--}}
@if($errors->has(Arr::get($fields,'name')))
    <div class="form-control-feedback">{{ $errors->first(  Arr::get($fields,'name')) }}</div>
@endif

