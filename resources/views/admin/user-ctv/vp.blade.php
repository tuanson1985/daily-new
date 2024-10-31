{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
{{--
        <div class="btn-group">
            <a href="{{route('admin.plusmoney-report.index')}}" type="button" class="btn btn-danger font-weight-bolder mr-2">
                <i class="flaticon-list-3"></i>
                {{__('Lịch sử cộng/trừ tiền')}}
            </a>
        </div> --}}

        <button type="button" class="btn btn-success font-weight-bolder" data-toggle="modal"
                data-target="#confirmModal">
            <i class="ki ki-check icon-sm"></i>
            {{__('Xác nhận')}}
        </button>

    </div>
@endsection

{{-- Content --}}
@section('content')

    @if ($message = session()->get('success'))
        <div class="m-subheader ">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                </button>
                <i class="glyphicon glyphicon-ok"></i> {{$message}}
            </div>
        </div>


    @endif

    @if($messages=$errors->all())

        <div class="m-subheader ">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                </button>
                <strong>Lỗi !</strong> {{$messages[0]}}
            </div>
        </div>

    @endif

    {{Form::open(array('route'=>array('admin.post_vp'),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
    <div class="card card-custom" id="kt_page_sticky_card">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">
                    {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                </h3>
            </div>
            <div class="card-toolbar"></div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row u_checker">
                        <div class="col-12 col-md-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    {{Form::select('field',['username'=>'Tài khoản','id'=>"ID"],old('field', request('field')),array('id'=>'field','class'=>'form-control input-group-text'))}}
                                </div>
                                <input type="text" name="username" id="user_input"
                                       value="{{ old('value', request('value') ? request('value') : null) }}"
                                       class="form-control" placeholder="Tìm kiếm...">
                                <div class="input-group-append">
                                    <button type="button" id="btnChecker" class="btn btn-success">Kiểm tra</button>
                                </div>
                            </div>
                            <span class="form-text"></span>
                        </div>
                    </div>
                    {{--form-group --}}
                    <div class="form-group row">
                        {{-- mode --}}
                        <label for="mode" class="col-12 col-md-4 col-form-labe text-md-right">{{ __('Loại giao dịch') }} <span  style="color: red">(*)</span></label>
                        <div class="col-12 col-md-8">
                            {{Form::select('mode',[1=>'Cộng vật phẩm',0=>'Trừ vật phẩm'],old('mode', request('mode')),array('class'=>'form-control'))}}
                            @if($errors->has('mode'))
                                <div class="form-control-feedback">{{ $errors->first('mode') }}</div>
                            @endif
                        </div>
                    </div>
                     {{--form-group --}}
                     <div class="form-group row">
                        {{-- mode --}}
                        <label for="mode" class="col-12 col-md-4 col-form-labe text-md-right">{{ __('Loại vật phẩm') }} <span  style="color: red">(*)</span></label>
                        <div class="col-12 col-md-8">
                            <select name="type_vp" class="form-control">
                                @foreach($typeVp as $key => $type)
                                    @if($type->parent_id == 11 || $type->parent_id == 12 || $type->parent_id == 13 || $type->parent_id == 14)
                                    @else
                                        <option value="{{ $type->parent_id }}">{{ $type->image  }}</option>
                                    @endif
                                @endforeach
                                <option value="gem_num">Ngọc</option>
                                <option value="coin_num">Coin</option>
                                <option value="xu_num">Xu</option>
                                <option value="robux_num">Robux</option>
                            </select>

                        </div>
                    </div>

                    {{-----form-group------}}
                    <div class="form-group row">
                        {{-----amount------}}
                        <label for="title" class="col-form-label text-md-right col-12 col-md-4">{{ __('Số vật phẩm')}} <span style="color: red">(*)</span> </label>
                        <div class="col-12 col-md-8">
                            <input name="amount" value="{{ old('amount', isset($data) ? $data->amount : null) }}"
                                   placeholder="{{ __('Số vật phẩm') }}"
                                   class="form-control  input-price  {{ $errors->has('amount') ? ' is-invalid' : '' }}"
                                   autocomplete="off">
                            @if ($errors->has('amount'))
                                <span class="form-text text-danger">{{ $errors->first('amount') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- description --}}
                    <div class="form-group row">

                        <label for="mode" class="col-form-label text-md-right col-12 col-md-4">{{ __('Nội dung') }}</label>
                        <div class="col-12 col-md-8">
                               <textarea style="min-height:100px;" type="text"
                                         class="form-control"
                                         name="description" placeholder="Nội dung">{{old('description')}}</textarea>
                        </div>
                    </div>
                    {{-----form-group------}}
                    <div class="form-group row">
                        {{-----password2------}}
                        <label for="password2" class="col-form-label text-md-right col-12 col-md-4">{{ __('Mật khẩu cấp 2')}} <span style="color: red">(*)</span> </label>
                        <div class="col-12 col-md-8">
                            <input type="password" name="password2" value=""
                                   placeholder="{{ __('Mật khẩu cấp 2') }}"
                                   class="form-control {{ $errors->has('password2') ? ' is-invalid' : '' }}"
                                   autocomplete="off">
                            @if ($errors->has('password2'))
                                <span class="form-text text-danger">{{ $errors->first('password2') }}</span>
                            @endif
                        </div>
                    </div>


                </div>
                <div class="col-md-6 info_user"></div>
            </div>


        </div>
    </div>
    {{ Form::close() }}

    <!-- confirmModal -->
    <div class="modal fade" id="confirmModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn thực hiện tao tác này?')}}
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom"
                            data-form="formMain" data-submit-close="1">
                        <i class="ki ki-check icon-sm"></i>
                        {{__('Xác nhận')}}
                    </button>

                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')

    <script>


        $(document).ready(function () {

            //btn submit form
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


            //btn source bank
            $("#source_bank option").hide();
            $("#source_type").change(function () {

                $("#source_bank option").hide();
                $("#source_bank").val('')
                var parrent = this.value;
                if (parrent == 1) {
                    $(".c1").show();
                } else if (parrent == 2) {
                    $(".c2").show();

                } else if (parrent == 4) {
                    $(".c4").show();
                } else {
                    $("#source_bank option").hide();
                }

            });
            //end btn source bank


            $("#btnChecker").click(function () {

                var ur = $('#user_input').val();
                var field = $('#field').val();
                $(this).addClass('spinner spinner-track spinner-primary spinner-right pr-15 disabled');
                $.get('{{route('admin.get_user_to_vp')}}' + '?username=' + ur + '&field=' + field,
                    function (data) {

                        $('.u_checker').removeClass('has-danger').addClass('has-success')
                        $('.u_checker').find('.form-text').html('Đã tìm thấy thông tin tài khoản');
                        $('.info_user').html('');
                        $('.info_user').html(data);

                    })
                    .fail(function () {

                        $('#balance').val('').trigger('input')
                        $('.u_checker').removeClass('has-success').addClass('has-danger')
                        $('.u_checker').find('.form-text').html('Không tìm thấy thông tin tài khoản');
                        $('.info_user').html('');
                    })
                    .always(function () {
                        $('#btnChecker').removeClass('spinner spinner-track spinner-primary spinner-right pr-15 disabled');
                    });

            });
            if ($('#user_input').val() != "") {
                $("#btnChecker").click();
            }

            $('.btn-confirm').click(function (e) {
                e.preventDefault();
                var btnThis = $(this);
                btnThis.attr('disabled', true)
                var form = $(this).closest('form');
                bootbox.confirm({
                    message: "Bạn thực sự muốn thực hiện tao tác này",
                    buttons: {
                        confirm: {
                            label: 'Xác nhận',
                            className: 'btn-success'
                        },
                        cancel: {
                            label: 'Đóng',
                        }
                    },
                    callback: function (result) {
                        if (result == true) {
                            form.submit();
                        } else {
                            btnThis.attr('disabled', false)
                        }
                    }
                })
            });


        });

    </script>


@endsection
