


<div class="form-group {{ $errors->has($field['name']) ? ' text-danger' : '' }}">
    <label for="{{ $field['name'] }}" >{{ __($field['label']) }}:</label>
    <textarea  id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="form-control {{ Arr::get( $field, 'class') }}" >{{ old($field['name'], setting($field['name'])) }}</textarea>
    @if ($errors->has($field['name']))
        <span class="form-text text-danger">{{ $errors->first($field['name']) }}</span>
    @endif
</div>
