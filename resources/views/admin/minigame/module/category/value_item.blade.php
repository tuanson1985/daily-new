@if(isset($data) && count($data))
    <div class="input-group">
        <select name="value_item[]" multiple="multiple" title="Chọn giá trị giải thưởng" class="form-control select2 col-md-5 datatable-input kt_select2_service" id="kt_select2_service" data-placeholder="{{__('Chọn giá trị giải thưởng')}}" style="width: 100%">
            <option value="">Chọn giá trị giải thưởng</option>
            @foreach($data as $item)
                @if(isset($value_item))
                    @if(in_array($value_item,$item->id))
                        <option selected value="{{ $item->id }}">{{ $item->title }}</option>
                    @else
                        <option value="{{ $item->id }}">{{ $item->title }}</option>
                    @endif
                @else
                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                @endif
            @endforeach
        </select>
    </div>
@endif
