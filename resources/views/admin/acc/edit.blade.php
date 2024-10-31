@extends('admin._layouts.master')
@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.acc_type_'.($data->category->display_type??($_GET['type']??1)))}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>
        <div class="btn-group">
            <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
                <i class="ki ki-check icon-sm"></i>
                {{__('Cập nhật')}}
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
                                {{__('Cập nhật & tiếp tục')}}
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

    {{Form::open(array('route'=>array('admin.acc.edit',[$type, $data->id??0]),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data" , 'files' => true))}}
    <input type="hidden" name="submit-close" id="submit-close">
    <input type="hidden" name="target" value="{{ $data->target??($_GET['target']??null) }}">
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
                    <div>
                        <property-select
                            :time="{{ time() }}"
                            :groups="{{ json_encode($groups) }}" 
                            :config="{{ json_encode(config('etc.acc_property')) }}" 
                            :ids="{{ json_encode(empty($data->groups)? []: $data->groups->pluck('id')->toArray()) }}"
                            :extend="{{ json_encode($data->params['ext_info']??[]) }}"
                            :catonly="{{ ($data->target??($_GET['target']??null)) == 1? 1: 0 }}"
                        />
                    </div>
                    @if( ($_GET['target']??null) == 1)
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">Up mới hàng loạt bằng file excel:</label>
                                    <input type="file" name="excel">
                                </div>
                            </div>
                        </div>
                        <p><hr></p>
                        <h3>Hoặc Up nick lẻ:</h3>
                    @endif
                    <div class="form-group row">
                        <div class="col-md-4 mb-2">
                            <label>{{ __('Tên đăng nhập') }}</label>
                            <input type="text" name="title" value="{{ $data->title??null }}" autofocus="true"
                                   placeholder="{{ __('Tên đăng nhập') }}" maxlength="120"
                                   class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}" required autocomplete="off">
                            <span class="form-text text-danger input-error error-title"></span>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>{{ __('Mật khẩu đăng nhập') }}</label>
                            <div class="input-group">
                                <input type="password" name="slug" value="{{ empty($data)? null: \App\Library\Helpers::Decrypt($data->slug, config('etc.encrypt_key')) }}"
                                       placeholder="{{ __('Mật khẩu đăng nhập') }}" maxlength="120"
                                       class="form-control {{ $errors->has('slug') ? ' is-invalid' : '' }}" autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary view-password" type="button"><i class="fa fa-eye"></i></button>
                                </div>
                            </div>
                           <span class="form-text text-danger input-error error-slug"></span>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>{{ __('Code/ID tài khoản ingame') }}</label>
                            <input type="text" name="idkey" value="{{ $data->idkey??null }}"
                                   placeholder="{{ __('ID, code, email ...') }}" maxlength="120"
                                   class="form-control {{ $errors->has('idkey') ? ' is-invalid' : '' }}" autocomplete="off">
                            <span class="form-text text-danger input-error error-idkey"></span>
                        </div>
                        @if( ($data->target??($_GET['target']??null)) == 1 )
                        <div class="col-md-4 mb-2">
                            <label>Server (nếu có)</label>
                            <input type="number" name="params[server]" value="{{ $data->params['server']??null }}" class="form-control" autocomplete="off">
                        </div>
                        @endif
                    </div>
                    @if($type == 2 && empty($data->id))
                        <div class="form-group row">
                            <div class="col-md-6">
                                Chưa có file mẫu? 
                                <a href="/assets/backend/files/acc-random.xlsx?t={{ time() }}" class="btn btn-sm btn-success">Tải file mẫu</a>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="">Up mới hàng loạt bằng file excel:</label>
                                    <input type="file" name="excel">
                                </div>
                                <div class="py-5">
                                    <hr>
                                </div>
                                <div class="mb-2">
                                    <label for="">Đổi mật khẩu hàng loạt bằng file excel. Chọn đúng danh mục đã up trước đây, hệ thống sẽ tìm đổi mk theo nick trong danh sách mới:</label>
                                    <input type="file" name="excel_password">
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($type != 2)
                        {{-----description------}}
                        <div class="form-group row">
                            <div class="col-12 col-md-12">
                                <label for="locale">{{ __('Mô tả') }}:</label>
                                <textarea id="description" name="description" class="form-control" rows="4"  data-startup-mode="" >{!! old('description', isset($data) ? $data->description : null) !!}</textarea>
                                <span class="form-text text-danger input-error error-description"></span>
                            </div>
                        </div>
                        {{-----content------}}
                        {{-- <div class="form-group row">
                            <div class="col-12 col-md-12">
                                <label for="locale">{{ __('Nội dung') }}</label>
                                <textarea id="content" name="content" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ old('content', isset($data) ? $data->content : null) }}</textarea>
                                @if ($errors->has('content'))
                                    <span class="form-text text-danger">{{ $errors->first('content') }}</span>
                                @endif
                            </div>
                        </div> --}}
                        {{-----gallery block------}}
                        <div class="form-group row">
                            {{-----image------}}
                            <div class="col-md-4"> 
                                <label for="locale">{{ __('Ảnh đại diện') }}:</label>
                                <div class="">
                                    <div class="image-preview-box before-upload"></div>
                                    <input type="file" name="image_file" class="upload-preview">
                                    <input type="hidden" name="image" value="{{ $data->image??null }}">
                                    {{-- <div class="fileinput ck-parent" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">
                                            @if(old('image', $data->image??null)!="")
                                                <img class="ck-thumb" src="{{ old('image', isset($data) ? \App\Library\MediaHelpers::media($data->image) : null) }}">
                                            @else
                                                <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                            @endif
                                            <input class="ck-input" type="hidden" name="image" value="{{ old('image', $data->image??null) }}">
                                        </div>
                                        <div>
                                            <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                            <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                        </div>
                                    </div> --}}
                                    <span class="form-text text-danger input-error error-image_file"></span>
                                    @if(!empty($data->image))
                                    <div class="image-preview-box">
                                        <img src="{{\App\Library\MediaHelpers::media($data->image)}}" alt="Lỗi hiển thị ảnh" class="img-fluid">
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label for="locale">{{ __('Ảnh chi tiết') }}:</label>
                                <div class="card" >
                                    <div class="card-body p-3" style="min-height: 148px">
                                        <div class="image-preview-box before-upload row"></div>
                                        <input type="file" class="upload-preview" name="image_extension_file[]" multiple="multiple">
                                        <input type="hidden" name="image_extension" value="{{ $data->image_extension??null }}" type="text">
                                        <span class="form-text text-danger input-error error-image_extension_file"></span>
                                        <div class="text-warning mb-2">
                                            Click vào biểu tượng xoá để chọn ảnh để xoá. Sau đó ấn cập nhật để hoàn thành
                                        </div>
                                        <div class="image-preview-box row">
                                        @foreach(explode('|', old('image_extension',$data->image_extension??null)) as $key => $img)
                                            <div class="col-md-3 col-sm-4 col-6 mb-2">
                                                <div style="position: relative; min-height: 60px;" class="border image-item">
                                                    <img src="{{\App\Library\MediaHelpers::media($img)}}" class="img-fluid">
                                                    <div class="form-check" style="position: absolute; top: 10px; right: 10px;">
                                                        <input type="checkbox" class="form-check-input d-none" name="delete_image_extension[]" id="delete_ext_{{ $key }}" value="{{ $key }}">
                                                        <label class="form-check-label" style="cursor: pointer;" for="delete_ext_{{ $key }}"><i class="fa fa-trash text-danger"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-custom gutter-b">
                @if($type != 2)
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__('Cấu hình giá bán')}} <i class="mr-2"></i>
                            <span class="d-block text-muted pt-2 font-size-sm">{{__("Thiết lập giá bán và % giảm giá")}}</span>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        {{-- price_old --}}
                        <div class="">
                            <label  class="form-control-label">{{ __('Giá ảo (đ)') }}</label>
                            <input type="text" name="price_old" value="{{ $data->price_old??null }}" placeholder="Giá ảo" class="form-control m-input input-price {{ $errors->has('url') ? ' is-invalid' : '' }}">
                            <span class="form-text text-danger input-error error-price_old"></span>
                        </div>
                    </div> 
                    <div class="form-group">
                        {{-- price --}}
                        <div class="">
                            <label  class="form-control-label">{{ __('Giá gốc (đ)') }}</label>
                            <input type="text" name="price" value="{{ $data->price??null }}" placeholder="Giá gốc" class="form-control m-input input-price {{ $errors->has('price') ? ' is-invalid' : '' }}">
                            <span class="form-text text-danger input-error error-price"></span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="text-center d-md-none">
        <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
            <i class="ki ki-check icon-sm"></i>
            {{__('Cập nhật')}}
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
                            {{__('Cập nhật & tiếp tục')}}
                        </span>
                    </button>
                </li>

            </ul>
        </div>
    </div>
    {{ Form::close() }}

@endsection

{{-- Styles Section --}}
@section('styles')
    <style>
        .sortable div {
            border: 1px solid #CCC;
            font-family: "Tahoma";
            margin: 5px 7px;
            padding: 5px;
        }

        div.sortable-placeholder {
            border: 1px dashed #CCC;
            background: none;
        }

        .sortable .image-preview-box{
            position: relative;

        }
        .sortable .image-preview-box .btn_delete_image{
            position: absolute;
            top:-5px;
            right:-5px;
            display: none;
        }
        .sortable .image-preview-box:hover .btn_delete_image{
            display: block;
        }
        .sortable.grid div {
            float: left;
            width: 84px;
            height: 84px;
        }

        .sortable.grid div img {
            float: left;
            max-width: 100%;
            max-height: 100%;
        }
        .sortable.grid {
            overflow: hidden;
        }
    </style>
@endsection
{{-- Scripts Section --}}
@section('scripts')
    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>
    <script>
        "use strict";
        $(document).ready(function () {

            $('.image-item input[type="checkbox"]').change(function() {
                var checked = this.checked;
                $(this).parents('.image-item').css('opacity', checked? '0.3': 1);     
            });
            //btn submit form
            var submiting = false;
            $('.btn-submit-custom').click(function (e) {
                e.preventDefault();
                if (!submiting) {
                    if ($('#has-required').length) {
                        alert('Vui lòng hoàn thành nhập liệu');
                    }else{
                        $('#submit-close').val($(this).data('submit-close'));
                        submiting = true;
                        var formData = new FormData(document.getElementById('formMain'))
                        axios.post('', formData).then(function(resp){
                            $('.input-error').text('');
                            if (resp.data.error) {
                                alert('Vui lòng kiểm tra lại các ô nhập liệu cảnh báo màu đỏ');
                                for (var key in resp.data.error) {
                                    if ($('.error-'+key).length) {
                                        $('.error-'+key).text(resp.data.error[key][0]);
                                    }
                                }
                                submiting = false;
                            }else if(resp.data.redirect){
                                if (resp.data.alert) {
                                    if (window.confirm(resp.data.alert)) {
                                        window.location.href = resp.data.redirect;
                                    }
                                }else{
                                    window.location.href = resp.data.redirect;
                                }
                            }else{
                                alert('Vui lòng tải lại trang và thử lại!');
                            }
                        }).catch(function (error) {
                            alert('Vui lòng tải lại trang và thử lại!');
                            submiting = false;
                        }).then(function(){
                        });
                    }
                }else{
                    alert('Đang xử lý. Vui lòng đợi!');
                }
            });

             // Multiple images preview in browser
            function imagesPreview(input, placeToInsertImagePreview) {

                if (input.files) {
                    var filesAmount = input.files.length;
                    var wrap = '';
                    if (placeToInsertImagePreview.hasClass('row')) {
                        wrap = 'col-md-3 col-sm-4 col-6 mb-2';
                    }
                    for (var i = 0; i < filesAmount; i++) {
                        var reader = new FileReader();
                        reader.onload = function(event) {
                            placeToInsertImagePreview.append($($.parseHTML('<div>')).attr('class', wrap).append(
                                '<img src="'+event.target.result+'" class="img-fluid">'
                            ));
                            // $($.parseHTML('<img>')).attr('src', event.target.result).attr('class', 'img-fluid')
                            // .appendTo($($.parseHTML('<div>')).attr('class', 'col-md-3 col-sm-4 col-6 mb-2').appendTo(placeToInsertImagePreview));
                        }

                        reader.readAsDataURL(input.files[i]);
                    }
                }

            };

            $('.upload-preview').on('change', function() {
                if ($(this).attr('multiple') != 'multiple') {
                    $(this).parent().find('.before-upload').html('');
                }
                imagesPreview(this, $(this).parent().find('.before-upload'));
            });

            // $(document).on('change', 'input[type="file"][multiple="multiple"]', function(){
            //     var e = $(this);
            //     if(e.val()){
            //         e.clone().insertAfter(e).css('display', 'none');
            //         e.val('');
            //     }
            // });

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
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Flash", 
                    height:height,
                    startupMode:startupMode,
                } );
            });
            $('.ckeditor-basic').each(function () {
                var elem_id=$(this).prop('id');
                var height=$(this).data('height');
                height=height!=""?height:150;
                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Flash",
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
                    connectorPath: '{{route('admin.ckfinder_connector_acc', $data->id??0)}}',
                    resourceType: 'Images',
                    chooseFiles: true,
                    // connectorInfo: '', /*params*/
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
                    connectorPath: '{{route('admin.ckfinder_connector_acc', $data->id??0)}}', 
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
                                            <img src="${MEDIA_URL+file.get('url')}" alt="" data-input="${file.get( 'url' )}">
                                            <a rel="8" class="btn btn-xs  btn-icon btn-danger btn_delete_image" data-toggle="modal" data-target="#deleteModal"><i class="la la-close"></i></a>
                                        </div>`);
                            });
                            var allImageChoose=parent.find(".image-preview-box img");
                            var allPath = "";
                            var len = allImageChoose.length;
                            allImageChoose.each(function (index, obj) {
                                allPath += $(this).attr('data-input');

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

            //ckfinder for upload file
            $(".ck-popup-file").click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.ck-parent');
                var elemInput = parent.find('.ck-input');
                var elemBtnRemove = parent.find('.ck-btn-remove');
                CKFinder.modal({
                    connectorPath: '{{route('admin.ckfinder_connector_acc', $data->id??0)}}',
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
                filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}?type=Flash&token=123",
                filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Flash",
                height:height,
                startupMode:startupMode,
            } );
            CKEDITOR.on('instanceReady', function(ev) {
                var editor = ev.editor;
                editor.dataProcessor.htmlFilter.addRules({
                    elements : {
                        a : function( element ) {
                            if ( !element.attributes.rel ){
                                //gets content's a href values
                                var url = element.attributes.href;

                                //extract host names from URLs (IE safe)
                                var parser = document.createElement('a');
                                parser.href = url;

                                var hostname = parser.hostname;
                                if ( hostname !== window.location.host) {
                                    element.attributes.rel = 'nofollow';
                                    element.attributes.target = '_blank';
                                }
                            }
                        }
                    }
                });
            })
        });


        $('.ckeditor-basic').each(function () {
            var elem_id=$(this).prop('id');
            var height=$(this).data('height');
            height=height!=""?height:150;
            CKEDITOR.replace(elem_id, {
                filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_acc', $data->id??0) }}?type=Flash&token=123",
                filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_acc', $data->id??0) }}?command=QuickUpload&type=Flash",
                height:height,
                removeButtons: 'Source',
            } );

            CKEDITOR.on('instanceReady', function(ev) {
                var editor = ev.editor;
                editor.dataProcessor.htmlFilter.addRules({
                    elements : {
                        a : function( element ) {
                            if ( !element.attributes.rel ){
                                //gets content's a href values
                                var url = element.attributes.href;

                                //extract host names from URLs (IE safe)
                                var parser = document.createElement('a');
                                parser.href = url;

                                var hostname = parser.hostname;
                                if ( hostname !== window.location.host) {
                                    element.attributes.rel = 'nofollow';
                                    element.attributes.target = '_blank';
                                }
                            }
                        }
                    }
                });
            })
        });


        // Image choose item
        $(".ck-popup").click(function (e) {
            e.preventDefault();
            var parent = $(this).closest('.ck-parent');

            var elemThumb = parent.find('.ck-thumb');
            var elemInput = parent.find('.ck-input');
            var elemBtnRemove = parent.find('.ck-btn-remove');
            CKFinder.modal({
                connectorPath: '{{route('admin.ckfinder_connector_acc', $data->id??0)}}',
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
                connectorPath: '{{route('admin.ckfinder_connector_acc', $data->id??0)}}',
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

        $('body').on('click', '.view-password', function(){
            var input = $(this).parents('.input-group').find('input');
            input.attr('type', input.attr('type') == 'password'? 'text': 'password');
        })
    </script>
@endsection