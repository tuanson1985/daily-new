@foreach($data as $key => $item)
    <optgroup label="{{ $item->title }}">
        @foreach($item->childs as $value)
            <option value="{{ $value->id }}" {{ (in_array($value->id, $selected))? 'selected': '' }}>{{ $value->title }}</option>
        @endforeach
    </optgroup>
@endforeach