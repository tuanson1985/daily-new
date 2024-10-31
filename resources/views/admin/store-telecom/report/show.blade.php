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
            <span> {{__('Chi tiết đơn hàng')}} #{{$data->id}}</span> <i class="mr-2"></i>
            <br/>
            @if ($data->status == 1)
            <span class="label label-lg label-pill label-inline label-success mr-2">{{config('module.store-card.status.1')}}</span>
            @elseif($data->status == 2)    
            <span class="label label-lg label-pill label-inline label-warning mr-2">{{config('module.store-card.status.2')}}</span>
            @elseif($data->status == 3)
            <span class="label label-lg label-pill label-inline label-info mr-2">{{config('module.store-card.status.3')}}</span>
            @elseif($data->status == 4)
            <span class="label label-lg label-pill label-inline label-dark mr-2">{{config('module.store-card.status.4')}}</span>
            @elseif($data->status == 5)
            <span class="label label-lg label-pill label-inline label-primary mr-2">{{config('module.store-card.status.5')}}</span>
            @elseif($data->status == 0)
            <span class="label label-lg label-pill label-inline label-danger mr-2">{{config('module.store-card.status.0')}}</span>
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
                  Khách hàng:
               </th>
               <th class="th-value">
                  <span style="font-weight:bold;color:#000">
                    {{$data->author->id}} 
                    - {{$data->author->username}} 
                    @if (isset($data->author->fullname))
                    - {{$data->author->fullname}} 
                    @endif
                </span>
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Nhà cung cấp dịch vụ
               </th>
               <th class="th-value">
                  <span style="font-weight:bold;color:#000">{{config('module.store-card.gate_id.'.$data->gate_id)}}</span> - {{$data->request_id}}
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Nhà mạng
               </th>
               <th class="th-value">
                  {{$params->telecom}}
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Mệnh giá
               </th>
               <th class="th-value">
                    {{number_format($params->amount)}}
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Số lượng
               </th>
               <th class="th-value">
                  {{$params->quantity}}
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Trị giá
               </th>
               <th class="th-value">
                  {{number_format($data->price)}} VNĐ
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Chiết khấu
               </th>
               <th class="th-value">
                  {{$data->ratio}} %
               </th>
            </tr>
            <tr>
               <th class="th-index">
                  #
               </th>
               <th class="th-name">
                  Nội dung - Mã lỗi
               </th>
               <th class="th-value">
                  {{$data->description}}
               </th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td>
                 <b> #</b>
               </td>
               <td>
                  <b>Tổng tiền </b>
               </td>
               <td>
                  <b>{{number_format($data->real_received_price)}} VNĐ</b>
               </td>
            </tr>
            @if ( $data->status == 4 || $data->status == 2 && Auth::user()->can(['store-card-recheck']))
            <tr>
               <td>
                 <b> </b>
               </td>
               <td>
                 <b> </b>
               </td>
               <td>
                  <button type="button" class="btn btn-sm btn-danger"  data-toggle="modal" data-target="#reCheck">
                     Kiểm tra đơn hàng
                  </button>
               </td>
            </tr>
            @endif
         </tbody>
      </table>
   </div>
</div>
@if ( $data->status == 4 || $data->status == 2 && Auth::user()->can(['store-card-recheck']))
   <div class="modal fade" id="reCheck" tabindex="-1" role="basic" aria-hidden="true">
      <div style="text-align:initial;" class="modal-dialog">
         <div class="modal-content">
               {{Form::open(array('route'=>array('admin.store-card.recheck',$data->id),'class'=>'m-form','id'=>'formReCheck','method'=>'POST'))}}
               <div class="modal-header">
                  <h4 class="modal-title">Xác nhận thao tác</h4>
                  <div style="display: none" class="select-status"></div>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
               </div>
               <div class="modal-body">
                  Bạn muốn kiểm tra đơn hàng này trên nhà cung cấp? Nếu trạng thái thất bại, khách hàng sẽ được hoàn lại giao dịch này. Vui lòng giữ thao tác đến khi tiến trình chạy xong.
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal">Đóng</button>
                  <button type="submit" class="btn btn-primary m-btn m-btn--air btn-danger btn-submit-recheck">Xác nhận</button>
               </div>
               {{ Form::close() }}
         </div>
      </div>
   </div>
@endif
<script>
   
</script>
@endsection
@section('scripts')
  <script>
      jQuery(document).ready(function () {
         $('#formReCheck').submit(function(e){
            e.preventDefault();
            var formSubmit = $(this);
            var url = formSubmit.attr('action');
            var btnSubmit = formSubmit.find(':submit');
            btnSubmit.text('Đang xử lý...');
            btnSubmit.prop('disabled', true);
            $.ajax({
               type: "POST",
               url: url,
               cache:false,
               data: formSubmit.serialize(), // serializes the form's elements.
               beforeSend: function (xhr) {
                  
               },
               success: function (data) {
                  if(data.status == 1){
                        toast(data.message + "Vui lòng f5 lại trang để hiển thị dữ liệu vừa cập nhật.");
                  }
                  else if(data.status == 2){
                     toast(data.message + "Vui lòng f5 lại trang để hiển thị dữ liệu vừa cập nhật.",'warning');
                  }
                  else{
                     toast(data.message + "Vui lòng f5 lại trang để hiển thị dữ liệu vừa cập nhật.", 'error');
                  }
                  $('#reCheck').modal('hide');
                  btnSubmit.text('Xác nhận');
                  btnSubmit.prop('disabled', false);
               },
               error: function (data) {
                  toast('{{__('Có lỗi phát sinh vui lòng thử lại')}}', 'error');
                  $('#reCheck').modal('hide');
                  btnSubmit.text('Xác nhận');
                  btnSubmit.prop('disabled', false);
               },
               complete: function (data) {
               
               }
            });
         });
      })
  </script>
@endsection