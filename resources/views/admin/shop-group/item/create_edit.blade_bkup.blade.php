{{-- Extends layout --}}
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
                  <input type="text" name="title" value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus
                     placeholder="{{ __('Tên nhóm shop') }}" maxlength="120"
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
         </div>
      </div>
      <div class="card card-custom gutter-b">
         <div class="card-header">
            <div class="card-title">
               <h3 class="card-label">
                  {{__('Cấu hình tỷ giá (Tỷ giá phải trong khoảng từ 70-130%. Nếu không có thì bỏ trống)')}} <i class="mr-2"></i>
               </h3>
            </div>
         </div>
         <div class="card-body">
            @if (config('module.shop-group.rate') && count(config('module.shop-group.rate')) > 0)
            @foreach (config('module.shop-group.rate') as $key => $item)
            <div class="m-form__group form-group {{$key != 0 ? 'rate-item hide' : null}}">
               <h4 class="bg-light">{{$item['title']}}</h4>
            </div>
            @if (isset($data))
            @if (isset($item['params']))
            @foreach ($item['params'] as $key_params => $item_params)
            @foreach ($data->params as $key_params_db => $item_params_db)
            @if ($item['key'] === $key_params_db)
            <div class="input-group discount-live ">
               <div class="input-group-prepend">
                  <span class="input-group-text">{{$item_params}}</span>
               </div>
               <input type="text" class="form-control ratio-val general-val" name="params[{{$item['key']}}][{{$key_params}}]" value="{{$item_params_db->$key_params}}" >
            </div>
            <br>
            @endif
            @endforeach
            @endforeach
            @endif
            @else 
            @if (isset($item['params']))
            @foreach ($item['params'] as $key_params => $item_params)
            <div class="input-group discount-live ">
               <div class="input-group-prepend">
                  <span class="input-group-text">{{$item_params}}</span>
               </div>
               <input type="text" class="form-control ratio-val general-val" name="params[{{$item['key']}}][{{$key_params}}]" value="" >
            </div>
            <br>
            @endforeach
            @endif   
            @endif
            @endforeach
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
                  {{Form::select('status',(config('module.'.$module.'.status')??[]) ,old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
                  @if($errors->has('status'))
                  <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                  @endif
               </div>
            </div>
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
<!-- delete item Modal -->
<div class="modal fade" id="deleteModal">
   <div class="modal-dialog">
      <div class="modal-content">
         {{Form::open(array('route'=>array('admin.'.$module.'.destroy',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'DELETE'))}}
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i aria-hidden="true" class="ki ki-close"></i>
            </button>
         </div>
         <div class="modal-body">
            {{__('Bạn thực sự muốn xóa?')}}
         </div>
         <div class="modal-footer">
            <input type="hidden" name="id" class="id" value=""/>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
            <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-custom" data-form="form-delete">{{__('Xóa')}}</button>
         </div>
         {{ Form::close() }}
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
   });
      //Funtion web ready state
      jQuery(document).ready(function () {
            $('.datetimepicker-default').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:00',
                useCurrent: true,
                autoclose: true
   
            });
            $('#deleteModal').on('show.bs.modal', function(e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#deleteModal .id').attr('value', id);
            });
            $('.btn-submit-custom').click(function (e) {
                e.preventDefault();
                $(".btn-submit-custom").each(function (index, value) {
                    KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                });
                var btn = this;
                //gắn thêm hành động close khi submit
                $('#submit-close').val($(btn).data('submit-close'));
                var formSubmit = $('#' + $(btn).data('form'));
                formSubmit.submit();
            });
            $('body').on('change','.btn-update-stt',function(e){
                e.preventDefault();
                var id = $(this).data('id');
                UpdateStatusClient(id);
            })
        });
   
</script>
@endsection