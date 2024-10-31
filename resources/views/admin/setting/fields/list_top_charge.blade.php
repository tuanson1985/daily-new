



<div class="form-group {{ $errors->has($field['name']) ? ' text-danger' : '' }} ">
    <label for="{{ $field['name'] }}" >{{ __($field['label']) }}:</label>
    <div class="top-charger">
        @if (isset($field['row']))
            @if ((setting($field['name'])))
                @php
                    $top_charge = json_decode(setting($field['name']));
                @endphp
                @for ($i = 0; $i <= count($top_charge) - 1; $i++)
                    <div class="row list-row-data row-list-{{$i}}">
                        <div class="col-md-6 col-6">
                            <input type="{{ $field['type'] }}"
                            name="{{ $field['name']}}[user][]"
                            value="{{isset($top_charge[$i]->user) ? $top_charge[$i]->user : null}}"
                            id="{{ $field['name'] }}"
                            placeholder="{{ __($field['label']) }} tên thành viên"
                            class="form-control {{ Arr::get( $field, 'class') }} {{ $errors->has($field['name']) ? ' is-invalid' : '' }}">
                        </div>
                        <div class="col-md-5 col-5">
                            <input type="{{ $field['type'] }}"
                            name="{{ $field['name'] }}[amount][]"
                            value="{{ isset($top_charge[$i]->amount) ? $top_charge[$i]->amount : null }}"
                            id="{{ $field['name'] }}"
                            placeholder="{{ __($field['label']) }} số tiền"
                            class="form-control {{ Arr::get( $field, 'class') }} {{ $errors->has($field['name']) ? ' is-invalid' : '' }}">
                        </div>
                        <div class="col-md-1 col-1">
                            <i class="far fa-trash-alt btn-delete" style="margin-top: 10px;cursor:pointer" data-id="{{$i}}"></i>
                        </div>
                    </div>
                    <br class="row-list-{{$i}}">
                @endfor
            @else    
                @for ($i = 0; $i <= $field['row'] - 1; $i++)
                    <div class="row list-row-data row-list-{{$i}}">
                        <div class="col-md-6 col-6">
                            <input type="{{ $field['type'] }}"
                            name="{{ $field['name']}}[user][]"
                            value=""
                            id="{{ $field['name'] }}"
                            placeholder="{{ __($field['label']) }} tên thành viên"
                            class="form-control {{ Arr::get( $field, 'class') }} {{ $errors->has($field['name']) ? ' is-invalid' : '' }}">
                        </div>
                        <div class="col-md-5 col-5">
                            <input type="{{ $field['type'] }}"
                            name="{{ $field['name'] }}[amount][]"
                            value=""
                            id="{{ $field['name'] }}"
                            placeholder="{{ __($field['label']) }} số tiền"
                            class="form-control {{ Arr::get( $field, 'class') }} {{ $errors->has($field['name']) ? ' is-invalid' : '' }}">
                        </div>
                        <div class="col-md-1 col-1">
                            <i class="far fa-trash-alt btn-delete" style="margin-top: 10px;cursor:pointer" data-id="{{$i}}"></i>
                        </div>
                    </div>
                    <br class="row-list-{{$i}}">
                @endfor
            @endif
        @endif
        @if ($errors->has($field['name']))
            <span class="form-text text-danger">{{ $errors->first($field['name']) }}</span>
        @endif
    </div>
    <div class="row">
        <button type="button" id="add-btn-fake-top" class="btn btn-secondary col-md-12 col-12">Thêm</button>
    </div>
</div>
@section('scripts')
    @parent
    <script>

        $(document).ready(function(){
            $('body').on('click','.btn-delete',function(){
                id = $(this).data('id');
               
                $('.row-list-'+id).remove();
            })
            $('body').on('click','#add-btn-fake-top',function(){
                id_rand = Math.floor(Math.random() * (9999 - 1000)) + 1000;
                var ele = $('.list-row-data').length
                if(ele > 15){
                    alert('Vượt số lượng data cho phép')
                    return false;
                }
                let html = '';
                html += '<div class="row list-row-data row-list-'+id_rand+'">';
                html += '<div class="col-md-6 col-6">';
                html += '<input type="list_top_charge" name="sys_top_charge[user][]" value="" id="sys_top_charge" placeholder="Cấu hình top nạp thẻ tên thành viên" class="form-control">';
                html += '</div>';
                html += '<div class="col-md-5 col-5">';
                html += '<input type="list_top_charge" name="sys_top_charge[amount][]" value="" id="sys_top_charge" placeholder="Cấu hình top nạp thẻ số tiền" class="form-control">';
                html += '</div>';
                html += '<div class="col-md-1 col-1">';
                html += '<i class="far fa-trash-alt btn-delete" style="margin-top: 10px;cursor:pointer" data-id="'+id_rand+'"></i>';
                html += '</div>';
                html += '</div>';
                html += '<br class="row-list-'+id_rand+'">';
                $('.top-charger').append(html);
            })
        });
    </script>
@stop

