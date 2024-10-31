@extends('admin._layouts.master')

@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.'.$module.'.index')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>
        <div class="btn-group">
            <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
                <i class="ki ki-check icon-sm"></i>
                @if(isset($data))
                    {{__('Cập nhật')}}
                @else
                    {{__('Thêm mới')}}
                @endif

            </button>
            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split btn-submit-dropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <ul class="nav nav-hover flex-column">
                    <li class="nav-item">
                        <button  class="nav-link btn-submit-custom" data-form="formMain">
                            <i class="nav-icon flaticon2-reload"></i>
                            <span class="ml-2">
                                 @if(isset($data))
                                    {{__('Cập nhật & tiếp tục')}}
                                @else
                                    {{__('Thêm mới & tiếp tục')}}
                                @endif
                            </span>
                        </button>
                    </li>

                </ul>
            </div>
        </div>
    </div>
@endsection
@section('content')

    @if(isset($data))
        {{Form::open(array('route'=>array('admin.'.$module.'.update',$data->id),'method'=>'PUT','id'=>'formMain','enctype'=>"multipart/form-data" , 'files' => true))}}
    @else
        {{Form::open(array('route'=>array('admin.'.$module.'.store'),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
    @endif
    <input type="hidden" name="submit-close" id="submit-close">

    <div class="row">
        <div class="col-lg-9">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                        </h3>
                    </div>

                </div>

                <div class="card-body">
                    {{-----title------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tiêu đề') }}</label>
                            <input type="text" id="title_gen_slug" name="title" value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus="true"
                                   placeholder="{{ __('Tên chiến dịch') }}" maxlength="120"
                                   class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}">
                            @if ($errors->has('title'))
                                <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                            @endif
                        </div>

                    </div>

                    {{-----code------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Mã nhận thưởng (Hệ thống tự động sinh ra một mã random không trùng, có thể tự tùy chỉnh)') }}</label>
                            <input type="text" id="code" name="code" value="{{ old('code', isset($data) ? $data->code : "PZ".time()) }}" {{ old('code', isset($data) ? 'readonly' : null) }} autofocus="true"
                                   placeholder="{{ __('Mã nhận thưởng') }}" maxlength="120"
                                   class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}">
                            @if ($errors->has('code'))
                                <span class="form-text text-danger">{{ $errors->first('code') }}</span>
                            @endif
                        </div>
                    </div>
                    {{-----max_use------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Tổng số lượt được nhận') }}</label>
                            <input type="text" id="max_uses" name="max_uses" value="{{ old('max_uses', isset($data) ? $data->max_uses : null) }}" autofocus="true"
                                   placeholder="{{ __('Tổng số lượt được nhận') }}" maxlength="120"
                                   class="form-control {{ $errors->has('max_uses') ? ' is-invalid' : '' }}">
                            @if ($errors->has('max_uses'))
                                <span class="form-text text-danger">{{ $errors->first('max_uses') }}</span>
                            @endif
                        </div>
                    </div>
                    {{-----max_uses_user------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Số lần người dùng sử dụng trong 1 giftcode ') }}</label>
                            <input type="text" id="max_uses_user" name="max_uses_user" value="{{ old('max_uses_user', isset($data) ? $data->max_uses_user : null) }}" autofocus="true"
                                   placeholder="{{ __('Tổng số người dùng được nhận') }}" maxlength="120"
                                   class="form-control {{ $errors->has('max_uses_user') ? ' is-invalid' : '' }}">
                            @if ($errors->has('max_uses_user'))
                                <span class="form-text text-danger">{{ $errors->first('max_uses') }}</span>
                            @endif
                        </div>
                    </div>
                    {{-- type --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-6">
                            <label class="form-control-label">{{ __('Loại gift code (bắt buộc)') }}</label>
                            {{Form::select('type',config('module.'.$module.'.type'),old('type', isset($data) ? $data->type : null),array('class'=>'form-control type-gift'))}}
                            @if($errors->has('type'))
                                <div class="form-control-feedback">{{ $errors->first('type') }}</div>
                            @endif
                        </div>
                    </div>
                     {{-- started_at --}}
                     <div class="form-group row">
                        <div class="col-12 col-md-6">
                            <label>{{ __('Thời gian bắt đầu') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                       name="started_at"
                                       value="{{ old('started_at', isset($data) ?  Carbon\Carbon::parse($data->started_at)->format('d/m/Y H:i:s') : null) }}"
                                       placeholder="{{ __('Thời gian bắt đầu') }}" autocomplete="off"
                                       data-toggle="datetimepicker">

                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar"></i></span>
                                </div>
                            </div>
                            @if($errors->has('started_at'))
                                <div class="form-control-feedback">{{ $errors->first('started_at') }}</div>
                            @endif
                        </div>
                    </div>
                     {{-- ended_at --}}
                     <div class="form-group row">
                        <div class="col-12 col-md-6">
                            <label>{{ __('Thời gian kết thúc') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                       name="ended_at"
                                       value="{{ old('ended_at', isset($data) ?  Carbon\Carbon::parse($data->ended_at)->format('d/m/Y H:i:s') : null) }}"
                                       placeholder="{{ __('Thời gian kết thúc') }}" autocomplete="off"
                                       data-toggle="datetimepicker">

                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar"></i></span>
                                </div>
                            </div>
                            @if($errors->has('ended_at'))
                                <div class="form-control-feedback">{{ $errors->first('ended_at') }}</div>
                            @endif
                        </div>
                    </div>

                    {{--  parrams dành riêng cho loại nhận tiền --}}
                    @if (isset($data) && isset($gift) && isset($type) && $type == 1)
                    <div class="form-group row item-type item-type-1">
                        <div class="col-12 col-md-6">
                            <label class="form-control-label">{{ __('Cấu hình nhận thưởng') }}</label>
                            <table id="type-code-1" class="table table-bordered table-list">
                                <thead>
                                <tr>
                                    <th class="text-success">Số đào</th>
                                    <th class="text-danger">% trúng thưởng (tổng số % yêu cầu là 100%)</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gift as $item)
                                    <tr>
                                        <td><input type="text" class="form-control amount-item" name="params_amount[]"  value="{{$item->amount}}"></td>
                                        <td><input type="text"  class="form-control percent-item" maxlength="3" name="params_percent[]"  value="{{$item->percent}}"></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="6">
                                        <button type="button" class="btn btn-primary btn-block add-row">Thêm</button>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @else
                        <div class="form-group row item-type item-type-1">
                            <div class="col-12 col-md-6">
                                <label class="form-control-label">{{ __('Cấu hình nhận thưởng') }}</label>
                                <table id="type-code-1" class="table table-bordered table-list">
                                    <thead>
                                    <tr>
                                        <th class="text-success">Số đào</th>
                                        <th class="text-danger">% trúng thưởng (tổng số % yêu cầu là 100%)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" class="form-control amount-item" name="params_amount[]"  value=""></td>
                                            <td><input type="text"  class="form-control percent-item" maxlength="3" name="params_percent[]"  value=""></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="6">
                                            <button type="button" class="btn btn-primary btn-block add-row">Thêm</button>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    {{-- user created at --}}
                    @if (isset($user_created_at))
                    <div class="form-group row item-type item-type-1">
                        <div class="col-12 col-md-6">
                            <label>{{ __('Tài khoản thành viên đăng kí được nhận quà từ ngày:') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                        name="user_created_at"
                                        value="{{ old('user_created_at', isset($user_created_at) ?  Carbon\Carbon::parse($user_created_at)->format('d/m/Y H:i:s') : null) }}"
                                        placeholder="{{ __('Tài khoản thành viên đăng kí được nhận quà từ ngày:') }}" autocomplete="off"
                                        data-toggle="datetimepicker">

                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar"></i></span>
                                </div>
                            </div>
                            @if($errors->has('user_created_at'))
                                <div class="form-control-feedback">{{ $errors->first('user_created_at') }}</div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="form-group row item-type item-type-1">
                        <div class="col-12 col-md-6">
                            <label>{{ __('Tài khoản thành viên đăng kí được nhận quà từ ngày:') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                        name="user_created_at"
                                        value=""
                                        placeholder="{{ __('Tài khoản thành viên đăng kí được nhận quà từ ngày:') }}" autocomplete="off"
                                        data-toggle="datetimepicker">

                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar"></i></span>
                                </div>
                            </div>
                            @if($errors->has('user_created_at'))
                                <div class="form-control-feedback">{{ $errors->first('user_created_at') }}</div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- params dành riêng cho loại voucher booking --}}
                    @if (isset($data) && isset($gift) && isset($type) && $type == 2)
                    <div class="form-group row item-type item-type-2">
                        <div class="col-12 col-md-6">
                            <label class="form-control-label">{{ __('Cấu hình nhận thưởng') }}</label>
                            <table class="table table-bordered table-list">
                                <thead>
                                <tr>
                                    <th class="text-success">% Giảm giá</th>
                                    <th class="text-danger">Số tiền giảm tối đa cho voucher</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" class="form-control" name="ratio_booking"  value="{{$gift->ratio_booking}}"></td>
                                        <td><input type="text"  class="form-control" name="amount_reduction_max"  value="{{$gift->amount_reduction_max}}"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                        <div class="form-group row item-type item-type-2">
                            <div class="col-12 col-md-6">
                                <label class="form-control-label">{{ __('Cấu hình nhận thưởng') }}</label>
                                <table class="table table-bordered table-list">
                                    <thead>
                                    <tr>
                                        <th class="text-success">% Giảm giá</th>
                                        <th class="text-danger">Số tiền giảm tối đa cho voucher</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" class="form-control" name="ratio_booking"  value=""></td>
                                            <td><input type="text"  class="form-control" name="amount_reduction_max"  value=""></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            Trạng thái <i class="mr-2"></i>
                        </h3>
                    </div>
                </div>

                <div class="card-body">
                    {{-- status --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                            {{Form::select('status',config('module.'.$module.'.status'),old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
                            @if($errors->has('status'))
                                <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                            @endif
                        </div>

                    </div>
                    {{-- created_at --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Ngày tạo') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                       name="created_at"
                                       value="{{ old('created_at', isset($data) ? $data->created_at->format('d/m/Y H:i:s') : date('d/m/Y H:i:s')) }}"
                                       placeholder="{{ __('Ngày tạo') }}" autocomplete="off"
                                       data-toggle="datetimepicker">

                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar"></i></span>
                                </div>
                            </div>
                            @if($errors->has('created_at'))
                                <div class="form-control-feedback">{{ $errors->first('created_at') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ Form::close() }}

@endsection

@section('scripts')


    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>
    <script>


        "use strict";
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


            $('.ckeditor-source').each(function () {
                var elem_id=$(this).prop('id');
                var height=$(this).data('height');
                height=height!=""?height:150;
                var startupMode= $(this).data('startup-mode');
                if(startupMode=="source"){
                    startupMode="source";
                }
                else{
                    startupMode="wysiwyg";
                }

                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                    height:height,
                    startupMode:startupMode,
                } );
            });

            $('.ckeditor-basic').each(function () {
                var elem_id=$(this).prop('id');
                var height=$(this).data('height');
                height=height!=""?height:150;
                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                    height:height,
                    removeButtons: 'Source',
                } );
            });


            // Image choose item
            $(".ck-popup").click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.ck-parent');

                var elemThumb = parent.find('.ck-thumb');
                var elemInput = parent.find('.ck-input');
                var elemBtnRemove = parent.find('.ck-btn-remove');
                CKFinder.modal({
                    connectorPath: '{{route('admin.ckfinder_connector')}}',
                    resourceType: 'Images',
                    chooseFiles: true,

                    width: 900,
                    height: 600,
                    onInit: function (finder) {
                        finder.on('files:choose', function (evt) {
                            var file = evt.data.files.first();
                            var url = file.getUrl();
                            elemThumb.attr("src", MEDIA_URL+url);
                            elemInput.val(url);

                        });
                    }
                });
            });
            $(".ck-btn-remove").click(function (e) {
                e.preventDefault();

                var parent = $(this).closest('.ck-parent');

                var elemThumb = parent.find('.ck-thumb');
                var elemInput = parent.find('.ck-input');
                elemThumb.attr("src", "/assets/backend/themes/images/empty-photo.jpg");
                elemInput.val("");

            });

            // Image extenstion choose item
            $(".ck-popup-multiply").click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.ck-parent');
                var elemBoxSort = parent.find('.sortable');
                var elemInput = parent.find('.image_input_text');
                CKFinder.modal({
                    connectorPath: '{{route('admin.ckfinder_connector')}}',
                    resourceType: 'Images',
                    chooseFiles: true,
                    width: 900,

                    height: 600,
                    onInit: function (finder) {
                        finder.on('files:choose', function (evt) {
                            var allFiles = evt.data.files;

                            var chosenFiles = '';
                            var len = allFiles.length;
                            allFiles.forEach( function( file, i ) {
                                chosenFiles += file.get('url');
                                if (i != len - 1) {
                                    chosenFiles += "|";
                                }
                                elemBoxSort.append(`<div class="image-preview-box">
                                            <img src="${file.get( 'url' )}" alt="">
                                            <a rel="8" class="btn btn-xs  btn-icon btn-danger btn_delete_image" data-toggle="modal" data-target="#deleteModal"><i class="la la-close"></i></a>
                                        </div>`);
                            });
                            var allImageChoose=parent.find(".image-preview-box img");
                            var allPath = "";
                            var len = allImageChoose.length;
                            allImageChoose.each(function (index, obj) {
                                allPath += $(this).attr('src');

                                if (index != len - 1) {
                                    allPath += "|";
                                }
                            });
                            elemInput.val(allPath);

                            //set lại event cho các nút xóa đã được thêm
                            //remove image extension each item
                            $('.btn_delete_image').click(function (e) {

                                var parent = $(this).closest('.ck-parent');
                                var elemInput = parent.find('.image_input_text');
                                $(this).closest('.image-preview-box').remove();
                                var allImageChoose=parent.find(".image-preview-box img");

                                var allPath = "";
                                var len = allImageChoose.length;
                                allImageChoose.each(function (index, obj) {
                                    allPath += $(this).attr('src');

                                    if (index != len - 1) {
                                        allPath += "|";
                                    }
                                });
                                elemInput.val(allPath);
                            });
                            //khoi tao lại sortable sau khi append phần tử mới
                            $('.sortable').sortable().bind('sortupdate', function (e, ui) {

                                var parent = $(this).closest('.ck-parent');
                                var allImageChoose=parent.find(".image-preview-box img");
                                var elemInput = parent.find('.image_input_text');
                                var allPath = "";
                                var len = allImageChoose.length;
                                allImageChoose.each(function (index, obj) {
                                    allPath += $(this).attr('src');

                                    if (index != len - 1) {
                                        allPath += "|";
                                    }
                                });
                                elemInput.val(allPath);
                            });

                        });
                    }
                });
            });

            //remove image extension each item
            $('.btn_delete_image').click(function (e) {

                var parent = $(this).closest('.ck-parent');
                var elemInput = parent.find('.image_input_text');
                $(this).closest('.image-preview-box').remove();
                var allImageChoose=parent.find(".image-preview-box img");

                var allPath = "";
                var len = allImageChoose.length;
                allImageChoose.each(function (index, obj) {
                    allPath += $(this).attr('src');

                    if (index != len - 1) {
                        allPath += "|";
                    }
                });
                elemInput.val(allPath);
            });


            //khoi tao sortable
            $('.sortable').sortable().bind('sortupdate', function (e, ui) {

                var parent = $(this).closest('.ck-parent');
                var allImageChoose=parent.find(".image-preview-box img");
                var elemInput = parent.find('.image_input_text');
                var allPath = "";
                var len = allImageChoose.length;
                allImageChoose.each(function (index, obj) {
                    allPath += $(this).attr('src');

                    if (index != len - 1) {
                        allPath += "|";
                    }
                });
                elemInput.val(allPath);
            });
        });
        $(document).ready(function () {

            $(".add-row").click(function () {
                var markup = '<tr><td><input type="text" class="form-control amount-item" name="params_amount[]"  value=""></td><td><input type="text"  class="form-control percent-item" maxlength="3" name="params_percent[]"  value=""></td></tr>';
                $("#type-code-1 tbody").append(markup);
            });
            checkType();
            function checkType(){
                var type = $('.type-gift').val();
                $('.item-type').css('display','none')
                if(type == 1){
                    $('.item-type-1').css('display','block');
                }
                else if(type == 2){
                    $('.item-type-2').css('display','block');
                }
            }
            $('.type-gift').on('change',function(){
                checkType();
            })
        });
    </script>
@endsection
