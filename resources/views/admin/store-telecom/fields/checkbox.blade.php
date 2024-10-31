

<div class="form-group {{ $errors->has($field['name']) ? ' text-danger' : '' }} ">


    <div class="checkbox-list">
        <label class="checkbox checkbox-outline">
            <input type="hidden" value="0" name="{{$field['name'] }}">
            <input  type="checkbox" name="{{$field['name'] }}" value="1" @if(old($field['name'], setting($field['name']))) checked="checked" @endif >
            <span></span>{{ __($field['label']) }}
        </label>
    </div>
    @if ($errors->has($field['name']))
        <span class="form-text text-danger">{{ $errors->first($field['name']) }}</span>
    @endif
</div>



