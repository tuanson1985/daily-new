{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.acc.property')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>
        <div class="btn-group">
            <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
                <i class="ki ki-check icon-sm"></i>
                Cập nhật
            </button>
            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split btn-submit-dropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <ul class="nav nav-hover flex-column">
                    <li class="nav-item">
                        <button  class="nav-link btn-submit-custom" data-form="formMain">
                            <i class="nav-icon flaticon2-reload"></i>
                            <span class="ml-2">Cập nhật</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

{{-- Content --}}
@section('content')

    {{Form::open(array('url' => url()->current(),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
    @php($i = 1)
    @foreach($data['data'] as $key => $item)
    <div class="card card-custom gutter-b">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">
                    Thuộc tính {{ $key }} <i class="mr-2"></i>
                </h3>
            </div>
        </div>
        <div class="card-body">
            <select name="data[{{ $key }}][]" multiple="multiple" title="" class="form-control select2"  data-placeholder="Chọn" id="kt_select2_{{ $i }}" style="width: 100%">
                @foreach($item as $k => $value)
                    <option value="{{ $value['id'] }}">{{ $value['name']??($value['title']??null) }}{{ !empty($value['level'])? " lv {$value['level']}": '' }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @php($i == 5? $i+=2: $i++)
    @endforeach
    {{ Form::close() }}
@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')

    <script type="text/javascript">
        $('.btn-submit-custom').click(function (e) {
            e.preventDefault();
            var btn = this;
            $(".btn-submit-custom").each(function (index, value) {
                KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
            });
            $('.btn-submit-dropdown').prop('disabled', true);
            //gắn thêm hành động close khi submit
            $('#submit-close').val($(btn).data('submit-close'));
            var formSubmit = $('#' + $(btn).data('form'));
            formSubmit.submit();
        });
    </script>
@endsection
