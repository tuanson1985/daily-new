{{-- Extends layout --}}
@extends('admin._layouts.master')

@section('action_area')
    @if(session('shop_id'))
        <div class="d-flex align-items-center text-right">
            <div class="dropdown dropdown-inline">
                <a href="#" class="btn btn-secondary btn-icon mr-2" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    <i class="flaticon-more-v2"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-md dropdown-menu-right p-0 m-0">
                    <!--begin::Navigation-->
                    <ul class="navi navi-hover">

                        <li class="navi-separator mb-3 opacity-70"></li>
                        <li class="navi-item">
                            <a href="#" class="navi-link">
                                <i class="fas fa-upload mr-3"></i><span class="menu-text">Nhập từ file Excel</span>

                            </a>
                        </li>
                        <li class="navi-item">
                            <a href="#" class="navi-link">
                            <span class="navi-text">
                                <span
                                    class="label label-xl label-inline label-light-danger">Partner</span>
                            </span>
                            </a>
                        </li>
                        <li class="navi-item">
                            <a href="#" class="navi-link">
                            <span class="navi-text">
                                <span
                                    class="label label-xl label-inline label-light-warning">Suplier</span>
                            </span>
                            </a>
                        </li>
                        <li class="navi-item">
                            <a href="#" class="navi-link">
                            <span class="navi-text">
                                <span
                                    class="label label-xl label-inline label-light-primary">Member</span>
                            </span>
                            </a>
                        </li>


                    </ul>
                    <!--end::Navigation-->
                </div>
            </div>

            <div class="btn-group">
                <div class="btn-group">
                    <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain">
                        <i class="ki ki-check icon-sm"></i>
                        {{__('Cập nhật')}}
                    </button>


                </div>
            </div>
        </div>
    @endif
@endsection

{{-- Content --}}
@section('content')
    @if(session('shop_id'))
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

                    <ul class="nav nav-tabs " role="tablist">

                        @if(!empty(config('setting_fields', [])) )
                            @foreach(config('setting_fields') as $section => $fields)
                                @if($section == 'bonus')
                                    @if(Auth::user()->can('setting-bonus-login'))
                                        <li class="nav-item">
                                            <a class="nav-link {{Arr::get($fields, 'class')}}" data-toggle="tab"
                                               href="#{{$section}}" role="tab" aria-selected="true">
                                            <span class="nav-icon">
                                                <i class="{{ Arr::get($fields, 'icon', 'glyphicon glyphicon-flash') }}"></i>
                                            </span>
                                                <span class="nav-text">{{ $fields['title'] }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @else
                                    <li class="nav-item">
                                        <a class="nav-link {{Arr::get($fields, 'class')}}" data-toggle="tab"
                                           href="#{{$section}}" role="tab" aria-selected="true">
                                        <span class="nav-icon">
                                            <i class="{{ Arr::get($fields, 'icon', 'glyphicon glyphicon-flash') }}"></i>
                                        </span>
                                            <span class="nav-text">{{ $fields['title'] }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>

                    <div class="tab-content" style="margin-top: 25px;">
                        @if(!empty(config('setting_fields', [])) )

                            @foreach(config('setting_fields') as $section => $fields)
                                <div class="tab-pane {{Arr::get($fields, 'class')}}" id="{{$section}}" role="tabpanel">
                                    @foreach($fields['elements'] as $field)
                                        @includeIf('admin.setting.fields.' . $field['type'] )
                                    @endforeach
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>

        </form>
    @else
        <div class="card card-custom" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">
                        Vui lòng chọn shop để cấu hình.
                    </h3>
                </div>

            </div>
        </div>
    @endif
@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}


@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    @if(auth()->user()->hasRole('admin') || auth()->user()->can('setting-folder-image-show'))
        <script>
        $(document).ready(function () {
            if ($(".sys_footer_kitio>input").val() == ''){
                $("#sys_footer_kitio").val($("#sys_footer_kitio option:first").val());
            }
            $("#detail_theme").attr("href","#"+$("#sys_theme_config_theme option:selected").val());
            $("#detail_theme").html("Click để xem chi tiết về theme <b>" + $("#sys_theme_config_theme option:selected").text() + "</b>");
            $("#sys_theme_config_theme").on("change",function(){
                $("#detail_theme").attr("href","#"+$("#sys_theme_config_theme option:selected").val());
                $("#detail_theme").html("Click để xem chi tiết về theme <b>" + $("#sys_theme_config_theme option:selected").text() + "</b>");
            })


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
                            toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
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
                var field = $(this).data('field');
                var parent = $(this).closest('.ck-parent');
                var elemThumb = parent.find('.ck-thumb.'+field);
                var elemInput = parent.find('.ck-input.'+field);
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
                            $('.ck-thumb.'+field).attr("src",url);
                            $('.ck-input.'+field).val(url);
                        });
                    }
                });
            });
            $(".ck-btn-remove").click(function (e) {
                e.preventDefault();
                var field = $(this).data('field');
                var parent = $(this).closest('.ck-parent');

                var elemThumb = parent.find('.ck-thumb');
                var elemInput = parent.find('.ck-input');
                $('.ck-thumb.'+field).attr("src", "/assets/backend/themes/images/empty-photo.jpg");
                $('.ck-input.'+field).val("");

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
        });
    </script>
    @else
        <script>


            $(document).ready(function () {
                if ($(".sys_footer_kitio>input").val() == ''){
                    $("#sys_footer_kitio").val($("#sys_footer_kitio option:first").val());
                }
                $("#detail_theme").attr("href","#"+$("#sys_theme_config_theme option:selected").val());
                $("#detail_theme").html("Click để xem chi tiết về theme <b>" + $("#sys_theme_config_theme option:selected").text() + "</b>");
                $("#sys_theme_config_theme").on("change",function(){
                    $("#detail_theme").attr("href","#"+$("#sys_theme_config_theme option:selected").val());
                    $("#detail_theme").html("Click để xem chi tiết về theme <b>" + $("#sys_theme_config_theme option:selected").text() + "</b>");
                })


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
                                toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
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
                        filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_setting', [$folder_image,$data->id??0]) }}",
                        filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_setting', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                        filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_setting', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                        filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_setting', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                        filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_setting', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                        filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_setting', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
                        height:height,
                        startupMode:startupMode,
                    } );
                });
                $('.ckeditor-basic').each(function () {
                    var elem_id=$(this).prop('id');
                    var height=$(this).data('height');
                    height=height!=""?height:150;
                    CKEDITOR.replace(elem_id, {
                        filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser_setting', [$folder_image,$data->id??0]) }}",
                        filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser_setting', [$folder_image,$data->id??0]) }}?type=Images&token=123",
                        filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser_setting', [$folder_image,$data->id??0]) }}?type=Flash&token=123",
                        filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector_setting', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Files",
                        filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector_setting', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Images",
                        filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector_setting', [$folder_image,$data->id??0]) }}?command=QuickUpload&type=Flash",
                        height:height,
                        removeButtons: 'Sourc',
                    } );
                });

                // Image choose item
                $(".ck-popup").click(function (e) {
                    e.preventDefault();
                    var field = $(this).data('field');
                    var parent = $(this).closest('.ck-parent');
                    var elemThumb = parent.find('.ck-thumb.'+field);
                    var elemInput = parent.find('.ck-input.'+field);
                    var elemBtnRemove = parent.find('.ck-btn-remove');
                    CKFinder.modal({
                        connectorPath: '{{route('admin.ckfinder_connector_setting', [$folder_image,$data->id??0])}}',
                        resourceType: 'Images',
                        chooseFiles: true,

                        width: 900,
                        height: 600,
                        onInit: function (finder) {
                            finder.on('files:choose', function (evt) {
                                var file = evt.data.files.first();
                                var url = file.getUrl();

                                $('.ck-thumb.'+field).attr("src",url);
                                $('.ck-input.'+field).val(url);
                            });
                        }
                    });
                });
                $(".ck-btn-remove").click(function (e) {
                    e.preventDefault();
                    var field = $(this).data('field');
                    var parent = $(this).closest('.ck-parent');

                    var elemThumb = parent.find('.ck-thumb');
                    var elemInput = parent.find('.ck-input');
                    $('.ck-thumb.'+field).attr("src", "/assets/backend/themes/images/empty-photo.jpg");
                    $('.ck-input.'+field).val("");

                });

                // Image extenstion choose item
                $(".ck-popup-multiply").click(function (e) {
                    e.preventDefault();
                    var parent = $(this).closest('.ck-parent');
                    var elemBoxSort = parent.find('.sortable');
                    var elemInput = parent.find('.image_input_text');
                    CKFinder.modal({
                        connectorPath: '{{route('admin.ckfinder_connector_setting', [$folder_image,$data->id??0])}}',
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
                        connectorPath: '{{route('admin.ckfinder_connector_setting', [$folder_image,$data->id??0])}}',
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
            });
        </script>
    @endif
@endsection
