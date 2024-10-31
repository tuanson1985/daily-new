@extends('admin._layouts.master')

@section('action_area')
    <div class="d-flex align-items-center text-right">
        <input type="hidden" value="{{$data->id}}" id="hdID"/>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <a href="{{route('admin.feedback-list')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Trở về
        </a>
        <div class="btn-group">
            <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
                <i class="ki ki-check icon-sm"></i>
                @if(isset($data))
                    {{__('Cập nhật')}}
                @else
                    {{__('Gửi phản hồi')}}
                @endif

            </button>
            <button     type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split btn-submit-dropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <ul class="nav nav-hover flex-column">
                    <li class="nav-item">
                        <button  class="nav-link btn-submit-custom" data-form="formMain">
                            <i class="nav-icon flaticon2-reload"></i>
                            <span class="ml-2">
                                 @if(isset($data))
                                    {{__('Cập nhật')}}
                                @else
                                    {{__('Gửi phản hồi')}}
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

    {{Form::open(array('route'=>array('admin.'.$module.'.update',$data->id),'method'=>'PUT','id'=>'formMain','enctype'=>"multipart/form-data" , 'files' => true))}}
    <input type="hidden" name="submit-close" id="submit-close">
    <div class="row">
        <div class="col-lg-6 col-sm-12">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            Thông tin ý kiến
                        </h3>
                    </div>

                </div>

                <div class="card-body">
                    {{-- status --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="status" class="form-control-label">{{ __('Góp ý về') }}</label>
                            @if($data->author_id == auth()->user()->id && $data->status == 1)
                                <select name="type" id="type" class="form-control">
                                    <option value="">---Lựa chọn mục góp ý---</option>
                                    @if(isset($dataCate) && count($dataCate) > 0)
                                        @foreach($dataCate as $item)
                                            <option {{isset($data) && $data->type == $item->id ? 'selected' : ''}} value="{{$item->id}}">{{$item->title}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            @else
                                <select name="type" id="type" class="form-control" disabled>
                                    <option value="">---Lựa chọn mục góp ý---</option>
                                    @if(isset($dataCate) && count($dataCate) > 0)
                                        @foreach($dataCate as $item)
                                            <option {{isset($data) && $data->type == $item->id ? 'selected' : ''}} value="{{$item->id}}">{{$item->title}}</option>
                                        @endforeach
                                    @endif
                                </select>

                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label>{{ __('Thông tin bổ sung') }}</label>
                            <input type="text" id="title_gen_slug" name="title" {{($data->author_id != auth()->user()->id || $data->status != 1) ? "disabled" : ""}} value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus="true"
                                   placeholder="{{ __('Email, SĐT ...') }}"
                                   class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}">
                        </div>
                    </div>


                    {{-----description------}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="locale">{{ __('Nội dung góp ý') }}:</label>
                            @if($data->author_id == auth()->user()->id && $data->status == 1)
                                <textarea id="contents" name="contents" {{($data->author_id != auth()->user()->id || $data->status != 1) ? "disabled" : ""}} class="form-control ckeditor-basic" >{{ old('contents', isset($data) ? $data->contents : null) }}</textarea>
                            @else
                                <div {{$data->author_id != auth()->user()->id ? "disabled" : ""}} class="form-control" style="height: auto;background-color: #f3f6f9">{!! isset($data) ? $data->contents : "" !!}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                        <label for="locale">{{ __('Ảnh đính kèm') }}:</label>
                        <div class="card" >
                            <div class="card-body p-3 ck-parent" style="min-height: 148px">
                                <input class="image_input_text" type="hidden"  name="files" value="{{ old('files', $data->files??null) }}" type="text">

                                <div class="sortable grid">


                                    @if (old('files',$data->files??null) != "")
                                        @foreach(explode('|', old('files',$data->files??null)) as $img)

                                            <div class="image-preview-box">
                                                <img src="{{\App\Library\MediaHelpers::media($img)}}" alt="">
                                                @if((isset($data) && $data->author_id == auth()->user()->id) && $data->status == 1)
                                                <a rel="8" class="btn btn-xs  btn-icon btn-danger btn_delete_image" data-toggle="modal" data-target="#deleteModal"><i class="la la-close"></i></a>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                                @if((isset($data) && $data->author_id == auth()->user()->id) && $data->status == 1)
                                    <a class="btn btn-success ck-popup-multiply" style="margin-top: 15px;">
                                        <i class="la la-cloud-upload-alt"></i> Chọn hình
                                    </a>
                                @endif
                            </div>
                        </div>
                        </div></div>

                    {{-- status --}}
                    @if(isset($data) && $data->author_id != auth()->user()->id)
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                            {{Form::select('status',config('module.'.$module.'.status'),old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
                            @if($errors->has('status'))
                                <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                            @endif
                        </div>
                    </div>
                    @else
                        @if(isset($data) && $data->status == 1)
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                                    {{Form::select('status',config('module.'.$module.'.status1'),old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
                                    @if($errors->has('status'))
                                        <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-12">
            <div class="card card-custom gutter-b">
                <div class="card-body">

                    <div class="form-group row" style="margin-bottom: 0">
                        <div class="col-12 col-md-12">
                           <i class="fa fa-comment"></i> <label for="status" class="form-control-label">{{ __('Comment') }}</label>
                        </div>

                    </div>
                    <div id="lstComment" style="margin-bottom: 15px"></div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <textarea name="answer" id="answer" class="form-control" placeholder="Content"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <div class="btn-group">
                                <button type="button" class="btn btn-success font-weight-bolder" id="answerformMain">
                                    <i class="ki ki-check icon-sm"></i>
                                    Trả lời
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ Form::close() }}
    <style>
        .image-preview-box{width: 200px;
            height: 200px;float: left}
        .image-preview-box img{width: 170px;
            height: 170px;}
    </style>
<style>
    @media(min-width:568px) {
        .end {
            margin-left: auto
        }
    }

    @media(max-width:768px) {
        #post {
            width: 100%
        }
    }

    #clicked {
        padding-top: 1px;
        padding-bottom: 1px;
        text-align: center;
        width: 100%;
        background-color: #ecb21f;
        border-color: #a88734 #9c7e31 #846a29;
        color: black;
        border-width: 1px;
        border-style: solid;
        border-radius: 13px
    }

    #profile {
        background-color: unset
    }

    #post {
        margin: 10px;
        padding: 6px;
        padding-top: 2px;
        padding-bottom: 2px;
        text-align: center;
        background-color: #ecb21f;
        border-color: #a88734 #9c7e31 #846a29;
        color: black;
        border-width: 1px;
        border-style: solid;
        border-radius: 13px;
        width: 50%
    }



    #nav-items li a,
    #profile {
        text-decoration: none;
        color: rgb(224, 219, 219);
        background-color: black
    }

    .comments {
        margin-top: 5%;
        margin-left: 20px
    }

    .darker {
        border: 1px solid #ecb21f;
        background-color: #0a2cb5;
        border-radius: 5px;
        padding-left: 10px;
        padding-right: 10px;
        padding-top: 10px
    }

    .comment {
        border: 1px solid rgba(16, 46, 46, 1);
        background-color: rgba(16, 46, 46, 0.973);
        border-radius: 5px;
        padding-left: 10px;
        padding-right: 10px;
        padding-top: 10px
    }

    .comment h4,
    .comment span,
    .darker h4,
    .darker span {
        display: inline
    }

    .comment p,
    .comment span,
    .darker p,
    .darker span {
        color: rgb(184, 183, 183)
    }


    label {
        color: rgb(212, 208, 208)
    }

    #align-form {
        margin-top: 20px
    }

    .form-group p a {
        color: white
    }

    #checkbx {
        background-color: black
    }

    #darker img {
        margin-right: 15px;
        position: static
    }
    #lstComment{max-height: 500px;
        overflow: auto;}


</style>
@endsection

@section('scripts')


    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>
    <script>

        //func loadAttribute for Theme
        function loadComment(idComment){
            $("#lstComment").html("Đang tải comment. Vui lòng chờ....");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/feedback/getComment',
                data: {
                    "idComment":idComment
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if (data.status == "SUCCESS") {
                        console.log(data.data.length)
                        if(data.data.length > 0){
                            let htmlEx = "";
                            for(let i=0; i< data.data.length; i++){
                                if(data.data[i].author_id != null){
                                    htmlEx +='<div class="darker mb-4 text-justify"><i class="fa fa-user" aria-hidden="true" class="rounded-circle" width="40" height="40"></i>'+
                                        '<h4 style="color:#fff;padding:0 10px;font-size: 13px;">'+data.data[i].author+'</h4> <span>- '+moment(data.data[i].created_at).format('DD/MM/YYYY h:mm a')+'</span> <br>'+
                                        '<p>'+data.data[i].contents+'</p>'+
                                        '</div>';
                                }
                                else{
                                htmlEx +='<div class="comment mb-4 text-justify"><i class="fa fa-user" aria-hidden="true" class="rounded-circle" width="40" height="40"></i>'+
                                    '<h4 style="color:#fff;padding:0 10px;font-size: 13px;">'+data.data[i].author_comment+'</h4> <span>- '+moment(data.data[i].created_at).format('DD/MM/YYYY h:mm a')+'</span> <br>'+
                                    '<p>'+data.data[i].contents+'</p>'+
                                '</div>';}
                            }
                            $("#lstComment").html(htmlEx);
                        }
                        else {
                                $("#lstComment").html("Không có comment!");
                            }
                    } else {
                        toast(data.msg, 'error');
                        $("#lstComment").html("");
                    }
                },
                error: function (data) {
                    toast('{{__('Không thể load Comment')}}', 'error');
                    $("#lstComment").html("");
                },
                complete: function (data) {
                    //KTUtil.btnRelease(KTUtil.getById("btn"));
                }
            });
        }

        //func loadAttribute for Theme
        function postComment(idComment){
            var comment = $("#answer").val();
            if(comment.length < 1 ){
                toast('{{__('Vui lòng nhập nội dung comment')}}', 'error');
                KTUtil.btnRelease(KTUtil.getById("answerformMain"));
                return;
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/feedback/postComment',
                data: {
                    "idComment":idComment,
                    "content" : $("#answer").val()
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if (data.status == "SUCCESS") {
                        toast(data.msg, 'success');
                        $("#answer").val("");
                        loadComment($("#hdID").val());
                    } else {
                        toast('{{__('Không thể trả lời comment lúc này')}}', 'error');
                    }
                },
                error: function (data) {
                    toast('{{__('Không thể trả lời comment lúc này')}}', 'error');
                },
                complete: function (data) {
                    //KTUtil.btnRelease(KTUtil.getById("answerformMain"));
                }
            });
        }


        "use strict";
        $(document).ready(function () {
            loadComment($("#hdID").val());
            $("#answerformMain").on("click",function (){
                postComment($("#hdID").val());
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
        });

    </script>
@endsection
