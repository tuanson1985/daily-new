

<div class="input-group">
    <select name="status[]" multiple="multiple" data-placeholder="{{__('Tất cả trạng thái')}}" title="Tất cả trạng thái" id="status" class="form-control datatable-input">
        <option value="">-- Tất cả trạng thái --</option>
        @foreach($status_datas as $key => $status)
            <option value="{{ $key }}"> {{ $status }}</option>
        @endforeach
    </select>
</div>
