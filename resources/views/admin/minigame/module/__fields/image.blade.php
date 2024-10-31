

{{--<label class="form-control-label">{{ __(Arr::get($fields,'label')) }}</label>--}}
{{--<input type="{{ Arr::get($fields,'type') }}"--}}
{{--       name="{{ Arr::get($fields,'name') }}"--}}
{{--       value="{{ old( Arr::get($fields,'name')??'',Arr::get($fields,'value') ) }}"--}}
{{--       id="{{ Arr::get($fields,'name') }}"--}}
{{--       placeholder="{{ __(Arr::get($fields,'label')) }}"--}}
{{--       class="form-control {{ Arr::get( $fields, 'class') }} {{ $errors->has(Arr::get($fields,'name')) ? ' is-invalid' : '' }}">--}}

{{--check thông báo lỗi--}}
{{--@if($errors->has(Arr::get($fields,'name')))--}}
{{--    <div class="form-control-feedback">{{ $errors->first(  Arr::get($fields,'name')) }}</div>--}}
{{--@endif--}}


@php
    $nameOld=str_replace(']','',  Arr::get($fields,'name'));
    $nameOld=str_replace('params[','params.',  $nameOld)
@endphp




<label class="form-control-label">{{ __(Arr::get($fields,'label')) }}</label>
<div class="">
    <div class="fileinput ck-parent" data-provides="fileinput">
        <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

            @if( old( $nameOld, $params->{ str_replace(['params[',']'],'',  Arr::get($fields,'name'))}?? Arr::get($fields,'value') ))
                <img class="ck-thumb" src="{{ old($nameOld, $params->{ str_replace(['params[',']'],'',  Arr::get($fields,'name'))}?? Arr::get($fields,'value') )}}">
            @else
                <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
            @endif
            <input class="ck-input" type="hidden" name="{{Arr::get($fields,'name')}}"
                   value="{{ old( $nameOld, $params->{ str_replace(['params[',']'],'',  Arr::get($fields,'name'))}?? Arr::get($fields,'value') )}}">
        </div>
        <div>
            <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
            <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
        </div>
    </div>
    @if ($errors->has(Arr::get($fields,'name')))
        <span class="form-text text-danger">{{ $errors->first(Arr::get($fields,'name')) }}</span>
    @endif
</div>
