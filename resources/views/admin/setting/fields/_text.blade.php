

<div class="form-group {{ $errors->has($field['name']) ? ' text-danger' : '' }} ">
    <label for="{{ $field['name'] }}" >{{ __($field['label']) }}:</label>
    <input autocomplete="off" type="{{ $field['type'] }}"
           name="{{ $field['name'] }}"
           value="{{ old($field['name'], setting($field['name'])) }}"
           id="{{ $field['name'] }}"
           placeholder="{{ __($field['label']) }}"
           class="form-control {{ Arr::get( $field, 'class') }} {{ $errors->has($field['name']) ? ' is-invalid' : '' }}">

    @if ($errors->has($field['name']))
        <span class="form-text text-danger">{{ $errors->first($field['name']) }}</span>
    @endif
</div>
