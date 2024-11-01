{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a
           class="btn btn-light-primary font-weight-bolder mr-2 btnback">
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

{{-- Content --}}
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
                            <input type="text" id="title_gen_slug" name="title" value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus
                                   placeholder="{{ __('Tiêu đề') }}" maxlength="120"
                                   class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}">
                            @if ($errors->has('title'))
                                <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                            @endif
                        </div>

                    </div>

                    {{-----description------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="locale">{{ __('Mô tả') }}:</label>
                            <textarea id="description" name="description" class="form-control" data-height="150"  data-startup-mode="" >{{ old('description', isset($data) ? $data->description : null) }}</textarea>
                            @if ($errors->has('description'))
                                <span class="form-text text-danger">{{ $errors->first('description') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 col-md-3">
                            <label>{{ __('Số user top') }}</label>
                            <input type="number" id="params[acc_show_num]" name="params[acc_show_num]" value="{{ old('params[acc_show_num]', isset($data->params->acc_show_num) ? $data->params->acc_show_num : null) }}" autofocus
                                   placeholder="{{ __('Số user top') }}" maxlength="120"
                                   class="form-control {{ $errors->has('params[acc_show_num]') ? ' is-invalid' : '' }}">
                            @if ($errors->has('params[acc_show_num]'))
                                <span class="form-text text-danger">{{ $errors->first('params[acc_show_num]') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 col-md-3">
                            <label>{{ __('Số lượt chơi') }}</label>
                            <input type="number" id="params[play_num_from]" name="params[play_num_from]" value="{{ old('params[play_num_from]', isset($data->params->play_num_from) ? $data->params->play_num_from : null) }}" autofocus
                                   placeholder="{{ __('từ') }}" maxlength="120"
                                   class="form-control {{ $errors->has('params[play_num_from]') ? ' is-invalid' : '' }}">
                            @if ($errors->has('params[play_num_from]'))
                                <span class="form-text text-danger">{{ $errors->first('params[play_num_from]') }}</span>
                            @endif
                        </div>
                        <div class="col-3 col-md-3">
                            <label>&nbsp;</label>
                            <input type="number" id="params[play_num_to]" name="params[play_num_to]" value="{{ old('params[play_num_to]', isset($data->params->play_num_to) ? $data->params->play_num_to : null) }}" autofocus
                                   placeholder="{{ __('đến') }}" maxlength="120"
                                   class="form-control {{ $errors->has('params[play_num_to]') ? ' is-invalid' : '' }}">
                            @if ($errors->has('params[play_num_to]'))
                                <span class="form-text text-danger">{{ $errors->first('params[play_num_to]') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 col-md-3">
                            <label>{{ __('Số người đang chơi') }}</label>
                            <input type="number" id="params[user_num_from]" name="params[user_num_from]" value="{{ old('params[user_num_from]', isset($data->params->user_num_from) ? $data->params->user_num_from : null) }}" autofocus
                                   placeholder="{{ __('từ') }}" maxlength="120"
                                   class="form-control {{ $errors->has('params[user_num_from]') ? ' is-invalid' : '' }}">
                            @if ($errors->has('params[user_num_from]'))
                                <span class="form-text text-danger">{{ $errors->first('params[user_num_from]') }}</span>
                            @endif
                        </div>
                        <div class="col-3 col-md-3">
                            <label>&nbsp;</label>
                            <input type="number" id="params[user_num_to]" name="params[user_num_to]" value="{{ old('params[user_num_to]', isset($data->params->user_num_to) ? $data->params->user_num_to : null) }}" autofocus
                                   placeholder="{{ __('đến') }}" maxlength="120"
                                   class="form-control {{ $errors->has('params[user_num_to]') ? ' is-invalid' : '' }}">
                            @if ($errors->has('params[user_num_to]'))
                                <span class="form-text text-danger">{{ $errors->first('params[user_num_to]') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 col-md-3">
                            <label>{{ __('Số lượt chơi gần đây') }}</label>
                            <input type="number" id="params[play_num_near]" name="params[play_num_near]" value="{{ old('params[play_num_near]', isset($data->params->play_num_near) ? $data->params->play_num_near : null) }}" autofocus
                                   placeholder="{{ __('Số lượt chơi gần đây') }}" maxlength="120"
                                   class="form-control {{ $errors->has('params[play_num_near]') ? ' is-invalid' : '' }}">
                            @if ($errors->has('params[play_num_near]'))
                                <span class="form-text text-danger">{{ $errors->first('params[play_num_near]') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 col-md-3">
                            <label>{{ __('Số trúng giải đặc biệt') }}</label>
                            <input type="number" id="params[special_num_from]" name="params[special_num_from]" value="{{ old('params[special_num_from]', isset($data->params->special_num_from) ? $data->params->special_num_from : null) }}" autofocus
                                   placeholder="{{ __('từ') }}" maxlength="120"
                                   class="form-control {{ $errors->has('params[special_num_from]') ? ' is-invalid' : '' }}">
                            @if ($errors->has('params[special_num_from]'))
                                <span class="form-text text-danger">{{ $errors->first('params[special_num_from]') }}</span>
                            @endif
                        </div>
                        <div class="col-3 col-md-3">
                            <label>&nbsp;</label>
                            <input type="number" id="params[special_num_to]" name="params[special_num_to]" value="{{ old('params[special_num_to]', isset($data->params->special_num_to) ? $data->params->special_num_to : null) }}" autofocus
                                   placeholder="{{ __('đến') }}" maxlength="120"
                                   class="form-control {{ $errors->has('params[special_num_to]') ? ' is-invalid' : '' }}">
                            @if ($errors->has('params[special_num_to]'))
                                <span class="form-text text-danger">{{ $errors->first('params[special_num_to]') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3 col-md-3">
                            <label>{{ __('Số giải thưởng còn lại') }}</label>
                            <input type="number" id="params[gift_num_exist]" name="params[gift_num_exist]" value="{{ old('params[gift_num_exist]', isset($data->params->gift_num_exist) ? $data->params->gift_num_exist : null) }}" autofocus
                                   placeholder="{{ __('Số giải thưởng còn lại') }}" maxlength="120"
                                   class="form-control {{ $errors->has('params[gift_num_exist]') ? ' is-invalid' : '' }}">
                            @if ($errors->has('params[gift_num_exist]'))
                                <span class="form-text text-danger">{{ $errors->first('params[gift_num_exist]') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- started_at --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-3">
                            <label>{{ __('Đếm ngược') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                       name="started_at"
                                       @if( isset($data->started_at) && $data->started_at!="0000-00-00 00:00:00" )
                                       value="{{ old('started_at', isset($data->started_at) ? date('d/m/Y H:i:s', strtotime($data->started_at)) : "") }}"
                                       @else
                                       value="{{ old('started_at', "") }}"
                                       @endif
                                       placeholder="{{ __('Đếm ngược') }}" autocomplete="off"
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
                            {{Form::select('status',(config('module.minigame.status')??[]) ,old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
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


                    {{-- ended_at --}}
                    <!-- <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Ngày hết hạn') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                       name="ended_at"
                                       @if( isset($data->ended_at) && $data->ended_at!="0000-00-00 00:00:00" )
                                       value="{{ old('expired_at', isset($data->ended_at) ? date('d/m/Y H:i:s', strtotime($data->ended_at)) : "") }}"
                                       @else
                                       value="{{ old('expired_at', "") }}"
                                       @endif
                                       placeholder="{{ __('Ngày hết hạn') }}" autocomplete="off"
                                       data-toggle="datetimepicker">

                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar"></i></span>
                                </div>
                            </div>
                            @if($errors->has('created_at'))
                                <div class="form-control-feedback">{{ $errors->first('created_at') }}</div>
                            @endif
                        </div>
                    </div> -->

                    {{-- order --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">

                            <label for="order">{{ __('Thứ tự') }}</label>
                            <input type="text" name="order" value="{{ old('order', isset($data) ? $data->order : null) }}"
                                   placeholder="{{ __('Thứ tự') }}"
                                   class="form-control {{ $errors->has('order') ? ' is-invalid' : '' }}">
                            @if ($errors->has('order'))
                                <span class="form-text text-danger">{{ $errors->first('order') }}</span>
                            @endif
                        </div>

                    </div>



                </div>
            </div>


        </div>
    </div>



    {{ Form::close() }}
<input type="hidden" value="{{url()->current()}}" name="urlcurrent">
@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')
    <script>
        "use strict";
        $(document).ready(function () {

            $('.btnback').click(function(){
                if(confirm("Thông tin chưa được lưu. Bạn chắc chắn muốn quay lại ?")){
                    location.href = '{{route('admin.'.$module.'.index')}}'
                }
            })
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
            $("select[name=position]").change(function(){
                window.location.href = $('input[name=urlcurrent]').val()+'?position='+$( "select[name=position]" ).val();
            });
        });

    </script>
    <script>



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
                        elemThumb.attr("src", url);
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


        //ckfinder for upload file
        $(".ck-popup-file").click(function (e) {
            e.preventDefault();
            var parent = $(this).closest('.ck-parent');


            var elemInput = parent.find('.ck-input');
            var elemBtnRemove = parent.find('.ck-btn-remove');
            CKFinder.modal({
                connectorPath: '{{route('admin.ckfinder_connector')}}',
                resourceType: 'Files',
                chooseFiles: true,

                width: 900,
                height: 600,
                onInit: function (finder) {
                    finder.on('files:choose', function (evt) {
                        var file = evt.data.files.first();
                        var url = file.getUrl();
                        elemInput.val(url);

                    });
                }
            });
        });


    </script>
@endsection


