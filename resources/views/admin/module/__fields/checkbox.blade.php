

@php
    $nameOld=str_replace(']','',  Arr::get($fields,'name'));
    $nameOld=str_replace('params[','params.',  $nameOld)
@endphp

<div class="checkbox-inline">
    <label class="checkbox checkbox-outline">
        <input type="hidden" value="0" name="{{Arr::get($field,'name')}}">
        <input  type="checkbox" name="{{Arr::get($field,'name') }}" value="1" @if(  old( $nameOld, $params->{ str_replace(['params[',']'],'',  Arr::get($fields,'name'))}?? Arr::get($fields,'value'))   )) checked="checked" @endif >
        <span></span>{{ __(Arr::get($field,'label')) }}
    </label>
</div>


@if ($errors->has(Arr::get($field,'name')))
    <span class="form-text text-danger">{{ $errors->first(Arr::get($field,'name')) }}</span>
@endif




