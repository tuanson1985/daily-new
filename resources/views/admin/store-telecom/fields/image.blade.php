<label for="locale">{{ $field['label'] }}:</label>
<div class="form-group {{ $errors->has($field['name']) ? ' text-danger' : '' }}">
    <div class="fileinput ck-parent" data-provides="fileinput">
        <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">
            @if(old($field['name'], setting($field['name']) ?? null)!="")
                <img class="ck-thumb {{$field['name']}}" src="{{ old($field['name'], setting($field['name']) ? \App\Library\MediaHelpers::media(setting($field['name'])) : null) }}">
            @else
                <img class="ck-thumb {{$field['name']}}" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
            @endif
            <input class="ck-input {{$field['name']}}" type="hidden" name="{{$field['name']}}" value="{{ old($field['name'], setting($field['name']) ? setting($field['name']) : null) }}">
        </div>
    </div>
    <div>
        <a href="#" class="btn red fileinput-exists ck-popup" data-field="{{$field['name']}}"> {{__("Thay đổi")}} </a>
        <a href="#" class="btn red fileinput-exists ck-btn-remove" data-field="{{$field['name']}}"> {{__("Xóa")}} </a>
    </div>
    @if ($errors->has($field['name']))
        <span class="form-text text-danger">{{ $errors->first($field['name']) }}</span>
    @endif
</div>


{{-- <div class="form-group {{ $errors->has($field['name']) ? ' text-danger' : '' }} ">
    <div class="fileinput {{ old($field['name'], setting($field['name']) ?? null)!=""?"fileinput-exists":"fileinput-new" }}" data-provides="fileinput">
        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px">
            <img src="/assets/backend/themes/images/empty-photo.jpg" alt="">
        </div>
        <div class="fileinput-preview fileinput-exists thumbnail" style="width: 150px; height: 150px; line-height: 150px;">
            @if(old($field['name'], setting($field['name']) ?? null)!="")
                <img src="{{ old($field['name'], setting($field['name'])!=""?asset("/storage".setting($field['name'])):"/assets/backend/themes/images/empty-photo.jpg") }}">
            @endif
        </div>
        <div>
            <span class="btn default btn-file">
                <span class="fileinput-new"> Chọn {{mb_strtolower(__($field['label'])) }} </span>
                <span class="fileinput-exists"> {{__('Thay đổi')}} </span>
                <input type="file" name="{{$field['name']}}">
            </span>
            <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> {{__("Xóa")}} </a>
        </div>
    </div>
    @if ($errors->has($field['name']))
        <span class="form-text text-danger">{{ $errors->first($field['name']) }}</span>
    @endif
</div> --}}


