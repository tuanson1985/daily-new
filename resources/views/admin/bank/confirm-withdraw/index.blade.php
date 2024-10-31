{{-- Extends layout --}}
@extends('admin._layouts.master')
@section('action_area')
<div class="d-flex align-items-center text-right">
</div>
@endsection
{{-- Content --}}
@section('content')

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
      <!--begin: Search Form-->
      @if($count_confirm>0)
      <div class="row mb-10">
         <div class="col-lg-3 m--margin-bottom-10-tablet-and-mobile">
            <button type="button" class="btn btn-danger m-btn m-btn--icon open_more" id="wait_confirm">
            <span>
            <i class="la la-eye"></i>
            <span>{{$count_confirm}} yêu cầu cần phê duyệt</span>
            </span>
            </button>
         </div>
      </div>
      @endif
       <form class="mb-10" action="{{route('admin.confirm-withdraw.export-excel')}}" method="POST">
           @csrf
         <div class="row">
            {{--ID--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text"><i
                        class="la la-calendar-check-o glyphicon-th"></i></span>
                  </div>
                  <input type="text" class="form-control datatable-input" id="id" placeholder="{{__('ID')}}">
               </div>
            </div>
            {{--username--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text"> <i class="la  la-user "></i></span>
                  </div>
                  <input type="text" class="form-control datatable-input"  id="username" name="username" value="{{request('username')}}"
                     placeholder="{{__('Tài khoản người rút')}}">
               </div>
            </div>

             <div class="form-group col-12 col-sm-6 col-lg-3">
                 <div class="input-group">
                     <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                     </div>
                     {{Form::select('type_information_ctv',[''=>'-- Tất cả loại ctv --']+config('module.user-qtv.type_information_ctv'),old('type_information_ctv', isset($data) ? $data->type_information_ctv : null),array('id'=>'type_information_ctv','class'=>'form-control datatable-input',))}}
                 </div>
             </div>

            {{--account_number--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text"><i
                        class="la  la-user "></i></span>
                  </div>
                  <input type="text" class="form-control datatable-input"  id="account_number" name="account_number" value="{{request('account_number')}}"
                     placeholder="{{__('STK Rút')}}">
               </div>
            </div>
            {{--status--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text"><i
                        class="la la-calendar-check-o glyphicon-th"></i></span>
                  </div>
                  {{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.withdraw.status'),old('status', isset($data) ? $data->status : null),array('id'=>'status','class'=>'form-control datatable-input',))}}
               </div>
            </div>
            {{--bank_type--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text"><i
                        class="la la-calendar-check-o glyphicon-th"></i></span>
                  </div>
                  {{Form::select('bank_type',[''=>'-- Tất cả loại ví --']+config('module.bank.bank_type'),Request::get('bank_type'),array('id'=>'bank_type','class'=>'form-control datatable-input')) }}
               </div>
            </div>
            {{--bank_title--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text"><i
                        class="la la-calendar-check-o glyphicon-th"></i></span>
                  </div>
                  {{Form::select('bank_title',[''=>'-- Tất cả tên ngân hàng/ví --']+$bank_type_0+$bank_type_1,Request::get('bank_title'),array('id'=>'bank_title','class'=>'form-control datatable-input'))}}
               </div>
            </div>
            {{--source_money--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text"><i
                        class="la la-calendar-check-o glyphicon-th"></i></span>
                  </div>
                  {{Form::select('source_money',[''=>'-- Tất cả nguồn chuyển --']+config('module.bank.bank_type'),Request::get('source_money'),array('id'=>'source_money_filter','class'=>'form-control datatable-input'))}}
               </div>
            </div>
            {{--source_bank--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text"><i
                        class="la la-calendar-check-o glyphicon-th"></i></span>
                  </div>
                  <select name="source_bank" id="source_bank_filter"
                     class="form-control m-input m-input--air">
                     <option value="">-- Tất cả ngân hàng/ví chuyển --</option>
                     <option class="c0" value="VIETCOMBANK" {{request('source_bank')=="VIETCOMBANK"?"selected":""}}>
                     Vietcombank
                     </option>
                     <option class="c0" value="VIETCOMBANK1" {{request('source_bank')=="VIETCOMBANK1"?"selected":""}}>
                     Vietcombank1
                     </option>
                     <option class="c0" value="VIETCOMBANK2" {{request('source_bank')=="VIETCOMBANK2"?"selected":""}}>
                     Vietcombank2
                     </option>
                     <option class="c0" value="VIETTINBANK" {{request('source_bank')=="VIETTINBANK"?"selected":""}}>
                     Viettinbank
                     </option>
                     <option class="c0" value="AGRIBANK" {{request('source_bank')=="AGRIBANK"?"selected":""}}>
                     Agribank
                     </option>
                     <option class="c0" value="AGRIBANK1" {{request('source_bank')=="AGRIBANK1"?"selected":""}}>
                     Agribank1
                     </option>
                     <option class="c0" value="AGRIBANK2" {{request('source_bank')=="AGRIBANK2"?"selected":""}}>
                     Agribank2
                     </option>
                     <option class="c0" value="TECHCOMBANK" {{request('source_bank')=="TECHCOMBANK"?"selected":""}}>
                     Techcombank
                     </option>
                     <option class="c0" value="TECHCOMBANK1" {{request('source_bank')=="TECHCOMBANK1"?"selected":""}}>
                     Techcombank1
                     </option>
                     <option class="c0" value="TECHCOMBANK2" {{request('source_bank')=="TECHCOMBANK2"?"selected":""}}>
                     Techcombank2
                     </option>
                     <option class="c0" value="MBBANK" {{request('source_bank')=="MBBANK"?"selected":""}}>
                     Mbbank
                     </option>
                     <option class="c0" value="BIDV" {{request('source_bank')=="BIDV"?"selected":""}}>
                     BIDV
                     </option>
                     <option class="c0" value="BANK_KHAC" {{request('source_bank')=="KHAC"?"selected":""}}>
                     Bank khác
                     </option>
                     {{-------}}
                     <option class="c1" value="TCSR" {{request('source_bank')=="TCSR"?"selected":""}}>
                     TCSR
                     </option>
                     <option class="c1" value="TSR" {{request('source_bank')=="TSR"?"selected":""}}>
                     TSR
                     </option>
                     <option class="c1" value="TKCR" {{request('source_bank')=="TKCR"?"selected":""}}>
                     TKCR
                     </option>
                     <option class="c1" value="AZPRO" {{request('source_bank')=="AZPRO"?"selected":""}}>
                     AZPRO
                     </option>
                     <option class="c1" value="NICK.VN TV" {{request('source_bank')=="NICK.VN TV"?"selected":""}}>
                     NICK.VN TV
                     </option>
                     <option class="c1" value="TICHHOP.NET" {{request('source_bank')=="TICHHOP.NET"?"selected":""}}>
                     TICHHOP.NET
                     </option>
                     <option class="c1" value="DAILY" {{request('source_bank')=="DAILY"?"selected":""}}>
                     DAILY
                     </option>
                     {{----MOMO---}}
                     <option class="c2" value="MOMO2869" {{request('source_bank')=="MOMO2869"?"selected":""}}>
                     MOMO2869
                     </option>
                     <option class="c2" value="MOMO2442" {{request('source_bank')=="MOMO2442"?"selected":""}}>
                     MOMO2442
                     </option>
                     <option class="c2" value="MOMO3323" {{request('source_bank')=="MOMO3323"?"selected":""}}>
                     MOMO3323
                     </option>
                     <option class="c2" value="MOMO2928" {{request('source_bank')=="MOMO2928"?"selected":""}}>
                     MOMO2928
                     </option>
                     <option class="c2" value="MOMO4666" {{request('source_bank')=="MOMO4666"?"selected":""}}>
                     MOMO4666
                     </option>
                     <option class="c2" value="MOMO0556" {{request('source_bank')=="MOMO0556"?"selected":""}}>
                     MOMO0556
                     </option>
                     <option class="c2" value="MOMO9872" {{request('source_bank')=="MOMO9872"?"selected":""}}>
                     MOMO9872
                     </option>
                     <option class="c2" value="MOMO4555" {{request('source_bank')=="MOMO4555"?"selected":""}}>
                     MOMO4555
                     </option>
                  </select>
               </div>
            </div>
            {{--started_at--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text">Từ</span>
                  </div>
                  <input type="text" name="started_at" id="started_at" autocomplete="off"
                     class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                     placeholder="{{__('Thời gian bắt đầu')}}" data-toggle="datetimepicker">
               </div>
            </div>
            {{--ended_at--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text">Đến</span>
                  </div>
                  <input type="text" name="ended_at" id="ended_at" autocomplete="off"
                     class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                     placeholder="{{__('Thời gian kết thúc')}}" data-toggle="datetimepicker">
               </div>
            </div>
            {{--started_at--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text">Từ</span>
                  </div>
                  <input type="text" name="started_process_at" id="started_process_at" autocomplete="off"
                     class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                     placeholder="{{__('Thời gian hoàn tất bắt đầu')}}" data-toggle="datetimepicker">
               </div>
            </div>
            {{--ended_at--}}
            <div class="form-group col-12 col-sm-6 col-lg-3">
               <div class="input-group">
                  <div class="input-group-prepend">
                     <span class="input-group-text">Đến</span>
                  </div>
                  <input type="text" name="ended_process_at" id="ended_process_at" autocomplete="off"
                     class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                     placeholder="{{__('Thời gian hoàn tất kết thúc')}}" data-toggle="datetimepicker">
               </div>
            </div>
         </div>
          <div class="row mb-5">
              <div class="col-auto">
                  <div class="btn-group m-btn-group" role="group" aria-label="...">
                      <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date" >Hôm nay</a>
                      <a href="#" data-started-at="{{\Carbon\Carbon::yesterday()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::yesterday()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Hôm qua</a>
                      <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng này</a>
                      <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->startOfMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->endOfMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng trước</a>
                  </div>
              </div>
          </div>
         <div class="row">
            <div class="col-lg-12">
               <button class="btn btn-primary btn-primary--icon" id="kt_search">
               <span>
               <i class="la la-search"></i>
               <span>Tìm kiếm</span>
               </span>
               </button>&#160;&#160;
               <button class="btn btn-secondary btn-secondary--icon" id="kt_reset">
               <span>
               <i class="la la-close"></i>
               <span>Reset</span>
               </span>
               </button>&#160;&#160;
                <button class="btn btn-danger btn-secondary--icon" type="submit">
                                <span>
                                    <i class="flaticon-folder-2"></i>
                                    <span>Xuất Excel</span>
                                </span>
                </button>
            </div>
         </div>

          <div class="row mt-5">
              <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                  Số lệnh rút: <b id="total_record">0</b> - Tổng tiền: <b id="total_price">0</b>
              </div>
          </div>
      </form>
      <!--begin: Search Form-->
      <div class="row mb-5">
         <div class="col-lg-12 " style="font-size: 14px ">
            <div class="checkbox-inline">
               <label class="checkbox">
               <input type="checkbox" checked="checked" name="" class="hs-column" data-column="7">
               <span></span>Rút về
               </label>
               <label class="checkbox">
               <input type="checkbox" checked="checked" name="" class="hs-column" data-column="8">
               <span></span>Tên ngân hàng/ví
               </label>
               <label class="checkbox">
               <input type="checkbox" checked="checked" name="" class="hs-column" data-column="9">
               <span></span>STK/TK ví
               </label>
               <label class="checkbox">
               <input type="checkbox"  name="" class="hs-column" data-column="10">
               <span></span> Nguồn chuyển
               </label>
               <label class="checkbox">
               <input type="checkbox"  name="" class="hs-column" data-column="11">
               <span></span> Tên ngân hàng/ví chuyển
               </label>
               <label class="checkbox">
               <input type="checkbox"  name="" class="hs-column" data-column="12">
               <span></span> Ghi chú
               </label>
            </div>
         </div>
      </div>
      <table class="table table-bordered table-hover table-checkable " id="kt_datatable">
      </table>
      <!--end: Datatable-->
   </div>
</div>
<!-- delete item Modal -->
<div class="modal fade" id="deleteModal">
   <div class="modal-dialog">
      <div class="modal-content">
         {{Form::open(array('route'=>array('admin.confirm-withdraw.post-deny',0),'class'=>'form-horizontal','method'=>'POST'))}}
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i aria-hidden="true" class="ki ki-close"></i>
            </button>
         </div>
         <div class="modal-body">
            <div class="form-group">
               <label class="col-md-3 control-label">Lý do hủy:</label>
               <div class="col-md-12">
                  <textarea cols="30" rows="5" name="description" class="form-control" maxlength="100"
                     required></textarea>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <input type="hidden" name="id" class="id" value=""/>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-danger m-btn m-btn--custom">Đồng ý</button>
         </div>
         {{ Form::close() }}
      </div>
   </div>
</div>
<!-- confirm item Modal -->
<div class="modal fade" id="confirmModal">
   <div class="modal-dialog">
      <div class="modal-content">
         {{Form::open(array('route'=>array('admin.confirm-withdraw.post-confirm',0),'class'=>'form-horizontal','method'=>'POST'))}}
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i aria-hidden="true" class="ki ki-close"></i>
            </button>
         </div>
         <div class="modal-body">
            <div class="form-group">
               <label for="recipient-name" class="form-control-label">
               Nguồn chuyển:
               </label>
               <select name="source_money" class="form-control" id="source_money" required>
               <option value="0" {{old('source_money')=="0"?"selected":""}}>
               ATM
               </option>
               <option value="1" {{old('source_money')=="1"?"selected":""}}>Ví
               điện tử
               </option>
               <option value="2" {{old('source_money')=="2"?"selected":""}}>Ví
               momo
               </option>
               </select>
            </div>
            <div class="form-group">
               <label for="recipient-name" class="form-control-label">
               Ngân hàng/ví chuyển:
               </label>
               <select name="source_bank" id="source_bank"
                  class="form-control" required>
                  <option value="">-- Ngân hàng/ví --</option>
                  <option class="c0" value="VIETCOMBANK" {{old('source_bank')=="1"?"selected":""}}>
                  Vietcombank
                  </option>
                  <option class="c0" value="VIETTINBANK" {{old('source_bank')=="2"?"selected":""}}>
                  Viettinbank
                  </option>
                  <option class="c0" value="AGRIBANK" {{old('source_bank')=="4"?"selected":""}}>
                  Agribank
                  </option>
                  <option class="c0" value="TECHCOMBANK" {{old('source_bank')=="5"?"selected":""}}>
                  Techcombank
                  </option>
                  <option class="c0" value="MBBANK" {{old('source_bank')=="6"?"selected":""}}>
                  Mbbank
                  </option>
                  <option class="c0" value="BIDV" {{old('source_bank')=="7"?"selected":""}}>
                  BIDV
                  </option>
                  {{-------}}
                  <option class="c1" value="TCSR" {{old('source_bank')=="TCSR"?"selected":""}}>
                  TCSR
                  </option>
                  <option class="c1" value="TSR" {{old('source_bank')=="TSR"?"selected":""}}>
                  TSR
                  </option>
                  <option class="c1" value="TKCR" {{old('source_bank')=="TKCR"?"selected":""}}>
                  TKCR
                  </option>
                  <option class="c1" value="AZPRO" {{old('source_bank')=="AZPRO"?"selected":""}}>
                  AZPRO
                  </option>
                  <option class="c1"
                  value="NICK.VN TV" {{request('source_bank')=="NICK.VN TV"?"selected":""}}>
                  NICK.VN TV
                  </option>
                  <option class="c1"
                  value="TICHHOP.NET" {{request('source_bank')=="TICHHOP.NET"?"selected":""}}>
                  TICHHOP.NET
                  </option>
                  {{----MOMO---}}
                  <option class="c2" value="MOMO2869" {{old('source_bank')=="MOMO2869"?"selected":""}}>
                  MOMO2869
                  </option>
                  <option class="c2" value="MOMO2442" {{old('source_bank')=="MOMO2442"?"selected":""}}>
                  MOMO2442
                  </option>
                  <option class="c2" value="MOMO3000" {{old('source_bank')=="MOMO3000"?"selected":""}}>
                  MOMO3000
                  </option>
                  <option class="c2" value="MOMO3323" {{old('source_bank')=="MOMO3323"?"selected":""}}>
                  MOMO3323
                  </option>
                  <option class="c2" value="MOMO2928" {{old('source_bank')=="MOMO2928"?"selected":""}}>
                  MOMO2928
                  </option>
                  <option class="c2" value="MOMO4666" {{old('source_bank')=="MOMO4666"?"selected":""}}>
                  MOMO4666
                  </option>
                  <option class="c2" value="MOMO0556" {{old('source_bank')=="MOMO0556"?"selected":""}}>
                  MOMO0556
                  </option>
                  <option class="c2" value="MOMO9872" {{old('source_bank')=="MOMO9872"?"selected":""}}>
                  MOMO9872
                  </option>
                  <option class="c2" value="MOMO4555" {{old('source_bank')=="MOMO4555"?"selected":""}}>
                  MOMO4555
                  </option>
               </select>
            </div>
         </div>
         <div class="modal-footer">
            <input type="hidden" name="id" class="id" value=""/>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-success m-btn m-btn--custom">Xác nhận</button>
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
   var datatable;
   var KTDatatablesDataSourceAjaxServer = function () {
       var initTable1 = function () {


           // begin first table
           datatable = $('#kt_datatable').DataTable({
               responsive: true,
               dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                       <'row'<'col-sm-12'tr>>
                   <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
               lengthMenu: [20, 50, 100, 200, 500, 1000],
               pageLength: 20,
               language: {
                   'lengthMenu': 'Display _MENU_',
               },
               searchDelay: 500,
               processing: true,
               serverSide: true,
               "order": [[0, "desc"]],
               ajax: {
                   url: '{{url()->current()}}' + '?ajax=1',
                   type: 'GET',
                   data: function (d) {
                       d.id = $('#id_filter').val();
                       d.account_type = $('#account_type').val();
                       d.username = $('#username').val();
                       d.bank_type = $('#bank_type').val();
                       d.bank_title = $('#bank_title').val();
                       d.source_money = $('#source_money_filter').val();
                       d.source_bank = $('#source_bank_filter').val();
                       d.started_at = $('#started_at').val();
                       d.ended_at = $('#ended_at').val();
                       d.status = $('#status').val();
                       d.started_process_at = $('#started_process_at').val();
                       d.ended_process_at = $('#ended_process_at').val();
                       d.type_information_ctv = $('#type_information_ctv').val();
                   }
               },
               buttons: [
                   {
                       "extend": 'excelHtml5',
                       "text": ' <i class="far fa-file-excel icon-md"></i> {{__('Xuất excel')}} ',
                       "action": newexportaction,
                       "exportOptions": {
                           "columns": [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]
                       }
                   },
               ],
               columns: [
                   {
                       data: 'id', title: 'ID'
                   },//1
                   {
                       data: 'request_id', title: 'Request ID'
                   },//2
                   {
                       data: 'user.account_type', title: 'Loại thành viên', orderable: false, searchable: false,
                       render: function (data, type, row) {

                           if (row.user.account_type == 1) {
                               return "Quản trị viên";
                           } else if (row.user.account_type == 2) {
                               return "Thành viên";
                           } else if (row.user.account_type == 3) {
                               return "Cộng tác viên";
                           } else {
                               return "";
                           }
                       }
                   },//3
                   {
                       data: 'user.type_information_ctv', title: 'Loại tài khoản', orderable: false, searchable: false,
                       render: function (data, type, row) {
                           if (row.user.type_information_ctv == 1) {
                               return "<span class=\"label label-pill label-inline label-center mr-2 label-primary \">" + "{{config('module.user-qtv.type_information_ctv.1')}}" + "</span>";
                           }
                           else if(row.user.type_information_ctv==2) {
                               return "<span class=\"label label-pill label-inline label-center  mr-2 label-success \">" + "{{config('module.user-qtv.type_information_ctv.2')}}" + "</span>";
                           }
                           else{
                               return "";
                           }
                       }
                   },//3
                   {
                       data: 'created_at', title: 'Thời gian tạo',
                       render: function (row) {
                           return moment(row).format('DD/MM/YYYY HH:mm:ss');
                       }
                   },//4


                   {
                       data: 'status', title: 'Trạng thái',
                       render: function (data, type, row) {

                           if (row.status == 0) {
                               return "<span class=\"label label-pill label-inline label-center  label-danger\">" + "{{config('module.withdraw.status.0')}}" + "</span>";
                           } else if (row.status == 1) {
                               return "<span class=\"label label-pill label-inline label-center  label-success\">" + "{{config('module.withdraw.status.1')}}" + "</span>";
                           } else if (row.status == 2) {
                               return "<span class=\"label label-pill label-inline label-center  label-warning\">" + "{{config('module.withdraw.status.2')}}" + "</span>";
                           } else {
                               return "Lỗi";
                           }


                       }
                   },//5
                   {
                       data: 'user.username', title: 'Người rút',
                       render: function (data, type, row) {
                           return "<a target='_blank' class=\"\" href=\"/admin/report-tran?username=" + row.user.username + "\" style=\"color: #575962 !important;\">" + row.user.username + "</a>";
                       }
                   },//6
                   {
                       data: 'amount', title: 'Số tiền',
                       render: function (data, type, row) {
                           return row.amount;
                       }
                   },
                   {
                       data: 'amount', title: 'Số tiền',
                       render: function (data, type, row) {
                           return row.amount.split(".").join("");
                       }
                   },//7
                   {
                       data: 'bank_type', title: 'Rút về'
                   },
                   {
                       data: 'bank_title', title: 'Tên ngân hàng/ví'
                   },
                   {
                       data: 'account_number', title: 'STK/TK ví',
                       render: function (data, type, row) {
                           if(row.account_number!=""){
                               return row.account_number;
                           }
                           else{
                               return row.account_vi;
                           }
                       }
                   },
                   {
                       data: 'account_number', title: 'Chủ tài khoản',
                       render: function (data, type, row) {
                           if(row.holder_name!=""){
                               return row.holder_name;
                           }
                           else{
                               return '';
                           }
                       }
                   },
                   {
                       data: 'source_money', title: 'Nguồn chuyển', visible: false
                   },

                   {
                       data: 'source_bank', title: 'Tên ngân hàng/ví chuyển', visible: false
                   },
                   {
                       data: 'admin_note', title: 'Ghi chú/Lý do',visible: false
                   },
                   {
                       data: 'processor.username', title: 'Người xử lý'
                   },
                   {data: 'process_at', title: '{{__('Thời gian duyệt')}}'},
                   {data: 'action', title: 'Thao tác', orderable: false, searchable: true}
               ],
               "drawCallback": function (settings) {

                   // hs-column
                   $('.hs-column').change(function () {

                       var column = datatable.column($(this).attr('data-column'));
                       if ($(this).is(":checked")) {
                           column.visible(true);
                       } else {
                           column.visible(false);
                       }
                   });

                   var api = this.api();
                   var apiJson = api.ajax.json();
                   var rows = api.rows({page: 'current'}).nodes();

                   $('#total_record').text(number_format(apiJson.recordsFiltered,'.'));
                   $('#total_price').text(number_format(apiJson.totalSumary.total_amount,'.'));

               }

           });

           var filter = function () {
               var val = $.fn.dataTable.util.escapeRegex($(this).val());
               datatable.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
           };
           $('#kt_search').on('click', function (e) {
               e.preventDefault();
               var params = {};
               $('.datatable-input').each(function () {
                   var i = $(this).data('col-index');
                   if (params[i]) {
                       params[i] += '|' + $(this).val();
                   } else {
                       params[i] = $(this).val();
                   }
               });

               $.each(params, function (i, val) {
                   // apply search params to datatable
                   datatable.column(i).search(val ? val : '', false, false);
               });
               datatable.table().draw();
           });

           $('#kt_reset').on('click', function (e) {
               e.preventDefault();
               $('.datatable-input').each(function () {
                   $(this).val('');
                   datatable.column($(this).data('col-index')).search('', false, false);
               });
               datatable.table().draw();
           });

           datatable.on("click", "#btnCheckAll", function () {
               $(".ckb_item input[type='checkbox']").prop('checked', this.checked).change();
           })

           datatable.on("change", ".ckb_item input[type='checkbox']", function () {
               if (this.checked) {
                   var currTr = $(this).closest("tr");
                   datatable.rows(currTr).select();
               } else {
                   var currTr = $(this).closest("tr");
                   datatable.rows(currTr).deselect();
               }
           });

           //function update field
           datatable.on("change", ".update_field", function (e) {


               e.preventDefault();
               var action = $(this).data('action');
               var field = $(this).data('field');
               var id = $(this).data('id');
               var value = $(this).data('value');
               if (field == 'status') {

                   if (value == 1) {
                       value = 0;
                       $(this).data('value', 1);
                   } else {
                       value = 1;
                       $(this).data('value', 0);
                   }
               }


               $.ajax({
                   type: "POST",
                   url: action,
                   data: {
                       '_token': '{{csrf_token()}}',
                       'field': field,
                       'id': id,
                       'value': value
                   },
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

                   }
               });

           });

           $("#wait_confirm").on("click", function () {
               $("#status").val(2);
               datatable.draw();
           });

       };
       return {

           //main function to initiate the module
           init: function () {
               initTable1();
           },

       };
   }();

   function newexportaction(e, dt, button, config) {


       $(button).text("Đang tải...");
       $(button).prop('disabled', true);
       var self = this;
       var oldStart = dt.settings()[0]._iDisplayStart;
       dt.one('preXhr', function (e, s, data) {
           // Just this once, load all data from the server...
           data.start = 0;
           data.length = 2147483647;
           dt.one('preDraw', function (e, settings) {

               // Call the original action function
               if (button[0].className.indexOf('buttons-copy') >= 0) {
                   $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
               } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                   $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                       $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                       $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
               } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                   $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                       $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                       $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
               } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                   $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                       $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                       $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
               } else if (button[0].className.indexOf('buttons-print') >= 0) {
                   $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
               }
               // dt.one('preXhr', function (e, s, data) {
               //     // DataTables thinks the first item displayed is index 0, but we're not drawing that.
               //     // Set the property to what it was before exporting.
               //     settings._iDisplayStart = oldStart;
               //     data.start = oldStart;
               // });
               // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
               // setTimeout(dt.ajax.reload, 0);
               // Prevent rendering of the full data to the DOM
               $(button).text("Xuất excel");
               $(button).prop('disabled', false);
               return false;
           });
       });
       // Requery the server with the new one-time export settings

       dt.ajax.reload();
   };


   //Funtion web ready state
   jQuery(document).ready(function () {
       KTDatatablesDataSourceAjaxServer.init();

       $('.datetimepicker-default').datetimepicker({
           format: 'DD/MM/YYYY HH:mm:00',
           useCurrent: true,
           autoclose: true

       });

       $('.btn-filter-date').click(function (e) {
           e.preventDefault();
           var startedAt=$(this).data('started-at');
           var endeddAt=$(this).data('ended-at');

           $('#started_process_at').val(startedAt);
           $('#ended_process_at').val(endeddAt);
           datatable.draw();
       });

       $('#deleteModal').on('show.bs.modal', function (e) {
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


   });


</script>
<script>
   //delete button
   //triggered when modal is about to be shown
   $('#deleteModal').on('show.bs.modal', function (e) {
       //get data-id attribute of the clicked element
       var id = $(e.relatedTarget).attr('rel')
       $('#deleteModal .id').attr('value', id);
   });


   $('#confirmModal').on('show.bs.modal', function (e) {
       //get data-id attribute of the clicked element
       var id = $(e.relatedTarget).attr('rel');
       var bank_type = $(e.relatedTarget).attr('rel-bank-type');
       $('#source_money').val(bank_type).change();
       $('#confirmModal .id').attr('value', id);
   });

   jQuery(document).ready(function () {
       $("#source_bank option").hide();
       $("#source_money").change(function () {

           $("#source_bank option").hide();
           $("#source_bank").val('')
           var parrent = this.value;
           if (parrent == 0) {
               $(".c0").show();
           } else if (parrent == 1) {
               $(".c1").show();

           } else if (parrent == 2) {
               $(".c2").show();

           } else {
               $("#source_bank option").hide();
           }

       });


   });

</script>
<script>
   //$("#source_bank option").hide();
   $("#source_money_filter").change(function () {

       if($("#source_money_filter").val()==""){
           $("#source_bank_filter option").show();
           return;
       }

       $("#source_bank_filter option").hide();
       $("#source_bank_filter").val('')
       var parrent = this.value;
       if (parrent == 0) {
           $(".c0").show();
       }
       else if (parrent == 1) {
           $(".c1").show();

       }

       else if (parrent == 2) {
           $(".c2").show();

       }

       else {
           $("#source_bank_filter option").hide();
       }

   });
</script>
@endsection
