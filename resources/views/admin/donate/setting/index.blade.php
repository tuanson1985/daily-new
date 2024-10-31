{{-- Extends layout --}}
@extends('admin._layouts.master')

@section('action_area')
    <div class="d-flex align-items-center text-right">


        <div class="btn-group">
            <div class="btn-group">
                <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain">
                    <i class="ki ki-check icon-sm"></i>
                    {{__('Cập nhật')}}
                </button>


            </div>
        </div>
    </div>
@endsection

{{-- Content --}}
@section('content')
    <form action="" id="formMain" method="post" class="form" enctype="multipart/form-data">
        @csrf
        <div class="card card-custom " id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">

                        {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>

                    </h3>
                </div>
                <div class="card-toolbar"></div>
            </div>

            <div class="card-body">

                {{-----title------}}
                <div class="form-group row">
                    <div class="col-12 col-md-12">
                        <label>{{ __('Chiết khấu') }}</label>
                        <input type="text" id="ratio" name="ratio" value="{{ old('ratio', isset($ratio) ? $ratio : null) }}" autofocus="true"
                               placeholder="{{ __('Chiết khấu') }}"
                               class="form-control {{ $errors->has('ratio') ? ' is-invalid' : '' }}">
                        @if ($errors->has('ratio'))
                            <span class="form-text text-danger">{{ $errors->first('ratio') }}</span>
                        @endif
                    </div>

                </div>
            </div>

        </div>

    </form>

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')
    <script>
        $(document).ready(function () {
            $('.btn-submit-custom').click(function (e) {
                e.preventDefault();
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }

                var btn = this;
                KTUtil.btnWait(btn, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);

                var formSubmit = $('#' + $(btn).data('form'));
                var url = formSubmit.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: formSubmit.serialize(), // serializes the form's elements.
                    beforeSend: function (xhr) {

                    },
                    success: function (data) {
                        if (data.success) {
                            if (data.redirect + "" != "") {
                                location.href = data.redirect;
                            }
                            toast('{{__('Cập nhật thành công')}}');
                        } else {
                            toast(data.message, 'error');
                        }
                    },
                    error: function (data) {
                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function (data) {
                        KTUtil.btnRelease(btn);
                    }
                });

            });
        });
    </script>

@endsection
