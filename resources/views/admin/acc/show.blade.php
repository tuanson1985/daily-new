@extends('admin._layouts.master')
@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.acc.history')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>
    </div>
@endsection

{{-- Content --}}
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                        </h3>
                    </div>

                </div>

                <div class="card-body">

                    <div class="m-portlet m-portlet--tabs">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-tools">
                                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger"
                                    role="tablist">
                                    <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link active" data-toggle="tab" href="#t_info"
                                           role="tab">
                                            <i class="la la-comment-o"></i>Yêu cầu hoàn tiền
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="t_info" role="tabpanel">
                                    <div class="m-section card-body">

                                        <div class="form-group row">
                                            @if(isset($order_refund))
                                            <div class="col-md-12">
                                                <label for="locale">
                                                    <span style="font-weight: bold">Yêu cầu hoàn tiền:</span>
                                                    @if($order_refund->status == 0)
                                                        <span class="badge badge-danger" style="margin-left: 12px">Đã hủy</span>
                                                    @elseif($order_refund->status == 1)
                                                        <span class="badge badge-success" style="margin-left: 12px">Thành công</span>
                                                    @elseif($order_refund->status == 3)
                                                        <span class="badge badge-dark" style="margin-left: 12px">Từ chối</span>
                                                    @elseif($order_refund->status == 2)
                                                        <span class="badge badge-warning" style="margin-left: 12px">Đang chờ xử lý</span>
                                                    @endif
                                                </label>

                                                @if($order_refund->status == 3)
                                                    <br>

                                                    <label for="locale">
                                                        <span style="font-weight: bold">Lý do từ chối:</span> {{ $order_refund->title }}
                                                    </label>
                                                    <br>
                                                @endif
                                                <div class="card">
                                                    @php
                                                        $image_customer = null;
                                                        $account = null;
                                                        $password = null;
                                                        if (isset($order_refund->content)){
                                                            $params = json_decode($order_refund->content);
                                                            if ($params->image_customer){
                                                                $image_customer = $params->image_customer;
                                                            }
                                                            if ($params->account){
                                                                $account = $params->account;
                                                            }
                                                            if ($params->password){
                                                                $password = $params->password;
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="card-body p-3" style="min-height: 148px;">
                                                        <span class="form-text text-dark">
                                                            Nội dung:
                                                        </span>
                                                        <div class="text-warning mb-5 mt-2">
                                                            {{ $order_refund->description }}
                                                        </div>
                                                        <span class="form-text text-dark">
                                                            Tài khoản:
                                                        </span>
                                                        <div class="text-info mb-5 mt-2">
                                                            {{ $account??'' }}
                                                        </div>
                                                        <span class="form-text text-dark">
                                                            Password:
                                                        </span>
                                                        <div class="text-info mb-5 mt-2">
                                                            {{ $password??'' }}
                                                        </div>
                                                        <span class="form-text text-dark mb-5">
                                                            Ảnh đính kèm:
                                                        </span>
                                                        <div class="image-preview-box row">
                                                            @if(isset($image_customer))
                                                                @foreach($image_customer as $image)
                                                                    <div class="col-md-3 col-sm-4 col-6 mb-2">
                                                                        <div class="border image-item" style="position: relative; min-height: 60px;">
                                                                            <img src="{{ \App\Library\MediaHelpers::media($image) }}" class="img-fluid">
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                        <div class="row mt-5">
                                                            <div class="col-md-12">
                                                                @if($order_refund->status == 2)
                                                                    @if(Auth::user()->can('nick-delete-order-refund'))
                                                                    <button class="btn btn-danger" data-toggle="modal"
                                                                            href="#rejectRefundModal">Từ chối</button>
                                                                    @endif
                                                                    @if(Auth::user()->can('nick-complete-order-refund'))
                                                                    <button class="btn btn-primary ml-5" data-toggle="modal"
                                                                            href="#completedRefundModal">Đồng ý</button>
                                                                    @endif
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                                <div class="col-md-12">
                                                    <label for="locale">
                                                        Không có dữ liệu
                                                    </label>
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectRefundModal" tabindex="-1"
         role="basic" aria-hidden="true">
        <div style="text-align:initial;" class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.acc.history.reject-refund',$data->id),'class'=>'m-form','method'=>'POST'))}}
                <div class="modal-header">
                    <h4 class="modal-title">Từ chối yêu cầu hoàn tiền</h4>
                    <button type="button" class="close"
                            data-dismiss="modal"
                            aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            Vui lòng nhập lý do từ chối yêu cầu hoàn tiền này.
                        </div>
                        <div class="col-md-12 mt-5">
                            <textarea class="form-control" name="note_refund" required>

                            </textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary btn-outline"
                            data-dismiss="modal">Đóng
                    </button>
                    <button type="submit" class="btn btn-primary m-btn m-btn--air">Xác
                        nhận
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="completedRefundModal" tabindex="-1"
         role="basic" aria-hidden="true">
        <div style="text-align:initial;" class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.acc.history.completed-refund',$data->id),'class'=>'m-form','method'=>'POST'))}}
                <div class="modal-header">
                    <h4 class="modal-title">Hoàn thành yêu cầu hoàn tiền
                        vụ</h4>
                    <button type="button" class="close"
                            data-dismiss="modal"
                            aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    Xác nhận hoàn thành yêu cầu hoàn tiền này?
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary btn-outline"
                            data-dismiss="modal">Đóng
                    </button>
                    <button type="submit"
                            class="btn btn-primary m-btn m-btn--air">Xác
                        nhận
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

@endsection

{{-- Styles Section --}}
@section('styles')
    <style>
        .th-index {
            width: 60px;
        }

        .th-name {
            width: 300px;
        }

        .edu-history-sec {
            float: left;
            width: 100%;
        }

        .edu-history {
            float: left;
            width: 100%;
            display: table;
            margin-bottom: 20px;
            position: relative;
        }

        .edu-history > i {
            display: table-cell;
            vertical-align: top;
            width: 70px;
            font-size: 50px;
            color: #fb236a;
            line-height: 60px;
        }

        .edu-hisinfo {
            display: table-cell;
            vertical-align: top;
        }

        .edu-hisinfo > h3 {
            float: left;
            width: 100%;
            font-family: Open Sans;
            font-size: 16px;
            color: #8b91dd;
            margin: 0;
            margin-top: 0px;
            margin-top: 10px;
        }

        .edu-hisinfo > i {
            float: left;
            width: 100%;
            font-style: normal;
            font-size: 14px;
            color: #888888;
            margin-top: 7px;
        }

        .edu-hisinfo > span {
            float: left;
            width: 100%;
            font-family: Open Sans;
            font-size: 16px;
            color: #202020;
            margin-top: 8px;
        }

        .edu-hisinfo > span i {
            font-size: 14px;
            color: #888888;
            font-style: normal;
            margin-left: 12px;
        }

        .edu-hisinfo > p {
            float: left;
            width: 100%;
            margin: 0;
            font-size: 14px;
            color: #888888;
            font-style: normal;
            line-height: 24px;
            margin-top: 10px;
        }

        .edu-history.style2 {
            margin: 0;
            padding-bottom: 20px;
            position: relative;
            padding-left: 40px;
            margin-bottom: 24px;
            padding-bottom: 0;
        }

        .edu-history.style2 > i {
            position: absolute;
            left: 0;
            top: 0;
            width: 16px;
            height: 16px;
            border: 2px solid #8b91dd;
            content: "";

            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            -ms-border-radius: 50%;
            -o-border-radius: 50%;
            border-radius: 50%;

        }

        .edu-history.style2 .edu-hisinfo > h3 {
            margin: 0;
        }

        .edu-history.style2::before {
            position: absolute;
            left: 7px;
            top: 20px;
            width: 2px;
            height: 100%;
            content: "";
            background: #e8ecec;
        }

        .edu-history.style2:last-child::before {
            display: none;
        }

        .edu-history.style2 .edu-hisinfo > h3 span {
            /*        color: #202020;*/
            margin-left: 10px;
        }

        .highligh-row {
            background-color: #ebedf2;
            font-weight: bold;
        }
    </style>
@endsection
{{-- Scripts Section --}}
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
                var elem_id = $(this).prop('id');
                var height = $(this).data('height');
                height = height != "" ? height : 150;
                var startupMode = $(this).data('startup-mode');
                if (startupMode == "source") {
                    startupMode = "source";
                } else {
                    startupMode = "wysiwyg";
                }

                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl: "{{ route('admin.ckfinder_browser') }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                    filebrowserUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                    height: height,
                    startupMode: startupMode,
                });
            });
            $('.ckeditor-basic').each(function () {
                var elem_id = $(this).prop('id');
                var height = $(this).data('height');
                height = height != "" ? height : 150;
                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl: "{{ route('admin.ckfinder_browser') }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                    filebrowserUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                    height: height,
                    removeButtons: 'Source',
                });
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
                            elemThumb.attr("src", MEDIA_URL + url);
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
                            allFiles.forEach(function (file, i) {
                                chosenFiles += file.get('url');
                                if (i != len - 1) {
                                    chosenFiles += "|";
                                }

                                elemBoxSort.append(`<div class="image-preview-box">
                                            <img src="${MEDIA_URL + file.get('url')}" alt="" data-input="${file.get('url')}">
                                            <a rel="8" class="btn btn-xs  btn-icon btn-danger btn_delete_image" data-toggle="modal" data-target="#deleteModal"><i class="la la-close"></i></a>
                                        </div>`);
                            });
                            var allImageChoose = parent.find(".image-preview-box img");
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
                                var allImageChoose = parent.find(".image-preview-box img");

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
                                var allImageChoose = parent.find(".image-preview-box img");
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
                var allImageChoose = parent.find(".image-preview-box img");

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
                var allImageChoose = parent.find(".image-preview-box img");
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
    <script>


        $('.ckeditor-source').each(function () {
            var elem_id = $(this).prop('id');
            var height = $(this).data('height');
            height = height != "" ? height : 150;
            var startupMode = $(this).data('startup-mode');
            if (startupMode == "source") {
                startupMode = "source";
            } else {
                startupMode = "wysiwyg";
            }

            CKEDITOR.replace(elem_id, {
                filebrowserBrowseUrl: "{{ route('admin.ckfinder_browser') }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                filebrowserUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                height: height,
                startupMode: startupMode,
            });
            CKEDITOR.on('instanceReady', function (ev) {
                var editor = ev.editor;
                editor.dataProcessor.htmlFilter.addRules({
                    elements: {
                        a: function (element) {
                            if (!element.attributes.rel) {
                                //gets content's a href values
                                var url = element.attributes.href;

                                //extract host names from URLs (IE safe)
                                var parser = document.createElement('a');
                                parser.href = url;

                                var hostname = parser.hostname;
                                if (hostname !== window.location.host) {
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
            var elem_id = $(this).prop('id');
            var height = $(this).data('height');
            height = height != "" ? height : 150;
            CKEDITOR.replace(elem_id, {
                filebrowserBrowseUrl: "{{ route('admin.ckfinder_browser') }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                filebrowserUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                height: height,
                removeButtons: 'Source',
            });

            CKEDITOR.on('instanceReady', function (ev) {
                var editor = ev.editor;
                editor.dataProcessor.htmlFilter.addRules({
                    elements: {
                        a: function (element) {
                            if (!element.attributes.rel) {
                                //gets content's a href values
                                var url = element.attributes.href;

                                //extract host names from URLs (IE safe)
                                var parser = document.createElement('a');
                                parser.href = url;

                                var hostname = parser.hostname;
                                if (hostname !== window.location.host) {
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


