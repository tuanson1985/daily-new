


<div class="form-group m-form__group {{ $errors->has($field['name']) ? ' text-danger' : '' }}">
    <label for="{{ $field['name'] }}" >{{ __($field['label']) }}:</label>

    <select name="{{ $field['name'] }}" class="form-control {{ \Arr::get( $field, 'class') }}" id="{{ $field['name'] }}">

        @foreach(\Arr::get($field, 'options', []) as $val => $label)
            <option @if( old($field['name'], setting($field['name'])) == $val ) selected  @endif value="{{ $val }}">{{ __($label) }}</option>
        @endforeach
    </select>

    @if ($errors->has($field['name']))
        <span class="form-text text-danger">{{ $errors->first($field['name']) }}</span>
    @endif
</div>
