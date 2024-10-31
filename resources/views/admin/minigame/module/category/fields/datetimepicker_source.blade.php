<div class="form-group {{ $errors->has($field['name']) ? ' text-danger' : '' }}">
    <label for="{{ $field['name'] }}" >{{ __($field['label']) }}:</label>
    <div class="input-group">
        <input id="datetimepicker_{{ $field['name'] }}" type="text" class="form-control  datetimepicker-input datetimepicker-default {{ Arr::get( $field, 'class') }}"
               name="{{ $field['name'] }}"
               value="{{old($field['name'],setting($field['name']) ? setting($field['name']) : null)}}"
               placeholder="{{ __(Arr::get( $field, 'placeholder')) }}" autocomplete="off"
               data-toggle="datetimepicker">
        @if ($errors->has($field['name']))
            <span class="form-text text-danger">{{ $errors->first($field['name']) }}</span>
        @endif
    </div>
</div>
