

@php
    $nameOld=str_replace(']','',  Arr::get($fields,'name'));
    $nameOld=str_replace('params[','params.',  $nameOld)
@endphp
<label class="form-control-label">{{ __(Arr::get($fields,'label')) }}</label>

<select name="{{ Arr::get($fields,'name') }}" class="form-control {{ \Arr::get( $field, 'class') }}"
        id="{{ Arr::get($fields,'name') }}">
    @foreach(\Arr::get($field, 'options', []) as $val => $label)
        <option
            @if(  old( $nameOld, $params->{ str_replace(['params[',']'],'',  Arr::get($fields,'name'))}?? Arr::get($fields,'value') ) == $val ) selected
            @endif value="{{ $val }}">{{ __($label) }}</option>
    @endforeach
</select>

{{--check thông báo lỗi--}}
@if($errors->has(Arr::get($fields,'name')))
    <div class="form-control-feedback">{{ $errors->first(  Arr::get($fields,'name')) }}</div>
@endif

