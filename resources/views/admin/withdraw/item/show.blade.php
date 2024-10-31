{{-- Extends layout --}}
@extends('admin._layouts.master')
@section('action_area')
@endsection
{{-- Content --}}
@section('content')
@php
$params = json_decode($data->params);
@endphp
<div class="card card-custom" id="kt_page_sticky_card">
   <div class="card-header">
      <div class="card-title">
         <h3 class="card-label">
            <span> {{__('Chi tiết lệnh rút tiền')}} #{{$data->id}}</span> <i class="mr-2"></i>
            <br/>
            @if ($data->status == 1)
            <span class="label label-lg label-pill label-inline label-success mr-2">{{config('module.withdraw.status.1')}}</span>
            @elseif($data->status == 2)    
            <span class="label label-lg label-pill label-inline label-warning mr-2">{{config('module.withdraw.status.2')}}</span>
            @elseif($data->status == 0)
            <span class="label label-lg label-pill label-inline label-danger mr-2">{{config('module.withdraw.status.0')}}</span>
            @else
            ""
            @endif
         </h3>
      </div>
      <div class="card-toolbar"></div>
   </div>
   <div class="card-body">
      <table class="table">
         <thead class="thead-default">
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Tài khoản:
               </th>
               <th class="th-value">
                  <span style="font-weight:bold;color:#000">
                    {{$data->user->id}} 
                    - {{$data->user->fullname_display}} 
                    @if (isset($data->user->email))
                    - {{$data->user->email}} 
                    @endif
                </span>
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Ngân hàng:
               </th>
               <th class="th-value">
                  <span style="font-weight:bold;color:#000">
                     {{$data->bank->title}}
                  </span>
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Mã ngân hàng:
               </th>
               <th class="th-value">
                  <span style="font-weight:bold;color:#000">
                     {{$data->bank->key}} <b data-copy="{{$data->bank->key}}" class="ml-3 btn-copy" style="cursor: pointer"><i class="flaticon2-copy"></i></b>
                  </span>
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Số tài khoản:
               </th>
               <th class="th-value">
                  <span style="font-weight:bold;color:#000">
                     {{$data->account_number}} <b data-copy="{{$data->account_number}}" class="ml-3 btn-copy" style="cursor: pointer"><i class="flaticon2-copy"></i></b>
                  </span>
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Chủ tài khoản:
               </th>
               <th class="th-value">
                  <span style="font-weight:bold;color:#000">
                     {{$data->holder_name}} <b data-copy="{{$data->holder_name}}" class="ml-3 btn-copy" style="cursor: pointer"><i class="flaticon2-copy"></i></b>
                  </span>
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Số tiền rút:
               </th>
               <th class="th-value">
                  <span style="font-weight:bold;color:#000">
                     {{number_format($data->amount)}} VNĐ  <b data-copy="{{$data->amount}}" class="ml-3 btn-copy" style="cursor: pointer"><i class="flaticon2-copy"></i></b>
                  </span>
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Phí rút tiền:
               </th>
               <th class="th-value">
                  <span style="font-weight:bold;color:#000">
                     {{$data->fee}} %
                  </span>
               </th>
            </tr>
            <tr>
               <th class="th-index">
                 <b> #</b>
               </th>
               <th class="th-name">
                  <b>Tổng tiền trừ khách hàng: </b>
               </th>
               <th class="th-value">
                  <b>{{number_format($data->amount_passed)}} VNĐ</b>
               </th>
            </tr>
            @if (isset($data->processor_id) && $data->processor_id != "")
            <tr>
               <th class="th-index"td>
               <b> #</b>
               </th>
               <th class="th-name">
                  <b>Người duyệt tiền: </b>
               </th>
               <th class="th-value">
                  <b>{{$data->processor->username}}</b>
               </th>
            </tr>
            @endif
            @if (isset($data->description) && $data->description != "")
            <tr>
               <th class="th-index"td>
               <b> #</b>
               </th>
               <th class="th-name">
                  <b>Nội dung: </b>
               </th>
               <th class="th-value">
                  <b>{{$data->description}}</b>
               </th>
            </tr>
            @endif
            @if (isset($data->admin_note) && $data->admin_note != "")
            <tr>
               <th class="th-index"td>
               <b> #</b>
               </th>
               <th class="th-name">
                  <b>Ảnh xác thực giao dịch: </b>
               </th>
               <th class="th-value">
                  <a style="border:1px solid:#000" href="{{ config('module.media.url').json_decode($data->admin_note)->image}}" target="_blank"><img width="40px" height="40px" src="{{ config('module.media.url').json_decode($data->admin_note)->image}}" alt=""></a>
               </th>
            </tr>
            @endif
         </thead>
      </table>
      @if ($data->status == 2)
         {{Form::open(array('route'=>array('admin.withdraw.update-item',$data->id),'id'=>'formMain','method'=>'POST','enctype'=>"multipart/form-data"))}}
         <div class="form-group row">
               <div class="col-12 col-md-6">
                  <label class="form-control-label">Trạng thái</label>
                  {{Form::select('status',config('module.withdraw.status'),old('withdraw', isset($data) ? $data->status : null),array('class'=>'form-control select-option'))}}
               </div>
         </div>
         <div class="row">
            <div class="col-12 col-md-6">
               <label class="form-control-label">Nội dung giao dịch hoặc lý do từ chối: </label>
               <div class="form-group">
               <textarea class="form-control" name="description" rows="3" required style="margin-top: 0px; margin-bottom: 0px; height: 38px;"></textarea>
            </div>
            </div>
         </div>
         <div class="form-group row">
            <div class="col-12 col-md-6">
               <label class="form-control-label">Mật khẩu cấp 2: </label>
                  <input type="password" name="password2" value="" required placeholder="{{ __('Mật khẩu cấp 2') }}" class="form-control {{ $errors->has('password2') ? ' is-invalid' : '' }}" autocomplete="off">
                  @if ($errors->has('password2'))
                     <span class="form-text text-danger">{{ $errors->first('password2') }}</span>
                  @endif
            </div>
         </div>
         <div class="form-group row">
            <div class="col-md-4">
                <label for="locale">{{ __('Ảnh xác thực giao dịch') }}:</label>
                <div class="">
                    <div class="fileinput ck-parent" data-provides="fileinput">
                        <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                            @if(old('image', isset($data) ? $data->image : null)!="")
                                <img class="ck-thumb" src="{{ old('image', isset($data) ? $data->image : null) }}">
                            @else
                                <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                            @endif
                            <input class="ck-input" type="hidden" name="image" value="{{ old('image', isset($data) ? $data->image : null) }}">

                        </div>
                        <div>
                            <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                            <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                        </div>
                    </div>
                    @if ($errors->has('image'))
                        <span class="form-text text-danger">{{ $errors->first('image') }}</span>
                    @endif
                </div>
            </div>
        </div>

         <button type="button" class="btn btn-success font-weight-bolder button-status"  style="display: none" data-toggle="modal" data-target="#confirmModal">
            Cập nhật
         </button>
      {{ Form::close() }}
      @endif
   </div>
</div>
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
@section('scripts')
   <script>
       "use strict";
      $(document).ready(function () {
      $('.select-option').on('change',function(){
         $('.button-status').css('display','block');
      })
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
      $('body').on('click','.btn-copy',function(){
            data = $(this).data('copy');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($.trim(data)).select();
            document.execCommand("copy");
            $temp.remove();
      })
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
   </script>
@endsection