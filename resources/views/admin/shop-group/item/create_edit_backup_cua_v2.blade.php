{{-- Extends layout --}}
@extends('admin._layouts.master')
@section('action_area')
<div class="d-flex align-items-center text-right">
   <a  id="backList"
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
                  Thông tin chung <i class="mr-2"></i>
               </h3>
            </div>
         </div>
         <div class="card-body">
            {{-----title------}}
            <div class="form-group row">
               <div class="col-12 col-md-8">
                  <label>{{ __('Tên nhóm điểm bán') }}</label>
                  <input type="text" name="title" value="{{ old('title', isset($data) ? $data->title : null) }}" autofocus
                     placeholder="{{ __('Tên nhóm điểm bán') }}" maxlength="120"
                     class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}">
                  @if ($errors->has('title'))
                  <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                  @endif
               </div>
                <div class="col-12 col-md-4">
                    <label>{{ __('Mã nhóm điểm bán') }}</label>
                    <input type="text" name="key" value="{{ old('key', isset($data) ? $data->key : null) }}" autofocus
                           placeholder="{{ __('Mã nhóm điểm bán') }}" maxlength="120"
                           class="form-control {{ $errors->has('key') ? ' is-invalid' : '' }}">
                    @if ($errors->has('key'))
                        <span class="form-text text-danger">{{ $errors->first('key') }}</span>
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
                 <div class="col-12 col-md-6">
                     <label for="timezone" class="form-control-label">{{ __('Múi giờ') }}</label>
                     {{Form::select('timezone',(config('module.'.$module.'.timezone')??[]) ,old('timezone', isset($data) ? $data->timezone : null),array('class'=>'form-control','id'=>'timezonelist'))}}
                     @if($errors->has('timezone'))
                         <div class="form-text text-danger">{{ $errors->first('timezone') }}</div>
                     @endif
                 </div>
                 <div class="col-12 col-md-6">
                     <label for="timezone" class="form-control-label">{{ __('Đơn vị tiền tệ') }}</label>
                     {{Form::select('currency',(config('module.'.$module.'.currency')??[]) ,old('currency', isset($data) ? $data->currency : null),array('class'=>'form-control'))}}
                     @if($errors->has('currency'))
                         <div class="form-text text-danger">{{ $errors->first('currency') }}</div>
                     @endif
                 </div>
             </div>

             {{-----description------}}
             <div class="form-group row">
                 <div class="col-12 col-md-12">
                     <label for="locale">{{ __('Ngôn ngữ') }}:</label>
                     <input type="hidden" name="language" id="language" value="{{isset($data) ? $data->language : ''}}"/>
                     <div>
                     <select id="kt_select2_1" name="language_sl"
                             class="form-control select2 col-md-5 datatable-input" style="width: 100%"
                            multiple="multiple"  data-actions-box="true" title="-- {{__('Ngôn ngữ')}} --">
                         @if(isset($lang))
                             @foreach($lang as $item)
                                 <?php
                                 $checked = "";
                                 if(isset($data))
                                     {
                                        $ck =  str_contains($data->language,$item->locale);
                                        if($ck == 1){
                                            $checked = 'selected';
                                        }
                                        else{
                                            $checked = "";
                                        }
                                     }
                                 ?>
                                 <option value="{{$item->locale}}" {{$checked}}>{{$item->title}}</option>
                             @endforeach
                         @endif
                     </select>
                         @if($errors->has('language'))
                             <div class="form-text text-danger">{{ $errors->first('language') }}</div>
                         @endif
                     </div>
                 </div>
             </div>




         </div>
      </div>
      <div class="card card-custom gutter-b">
         <div class="card-header">
            <div class="card-title">
               <h3 class="card-label">
                  {{__('Cấu hình tỷ giá')}} <i class="mr-2"></i>
               </h3>
            </div>
         </div>
         <div class="card-body">
             <div class="m-form__group form-group">
                 <h4 class="bg-light">{{__('Tỷ giá chung')}}</h4>
             </div>
             @if(isset($data))
                 <div class="input-group discount-live">
                     <div class="input-group-prepend">
                         <span class="input-group-text">{{__('Số tiền cộng thêm')}}</span>
                     </div>
                     <input type="text" class="form-control ratio-val general-val input-decimal" name="rate_money" value="{{isset($data) ? $data->rate_money : 0}}" >
                 </div>
                 <br>
                 <div class="input-group discount-live">
                     <div class="input-group-prepend">
                         <span class="input-group-text">{{__('Tỷ lệ %')}}</span>
                     </div>
                     <input type="text" class="form-control ratio-val general-val input-decimal" name="rate" value="{{isset($data) ? $data->rate : 0}}" >
                 </div>
                 <br>
             @else
                 <div class="input-group discount-live">
                     <div class="input-group-prepend">
                         <span class="input-group-text">{{__('Số tiền cộng thêm')}}</span>
                     </div>
                     <input type="text" class="form-control ratio-val general-val input-decimal" name="rate_money" value="0" >
                 </div>
                 <br>
                 <div class="input-group discount-live">
                     <div class="input-group-prepend">
                         <span class="input-group-text">{{__('Tỷ lệ %')}}</span>
                     </div>
                     <input type="text" class="form-control ratio-val general-val input-decimal" name="rate" value="100" >
                 </div>
                 <br>
             @endif
            @if (config('module.shop-group.rate') && count(config('module.shop-group.rate')) > 0)
                @foreach (config('module.shop-group.rate') as $key => $item)
                     <div class="m-form__group form-group {{$key != 0 ? 'rate-item hide' : null}}">
                         <h4 class="bg-light">{{$item['title']}}</h4>
                     </div>
                 @if (isset($data) && isset($data->params))
                    @if (isset($item['params']))
                        @foreach ($item['params'] as $key_params => $item_params)
                            @foreach ($data->params as $key_params_db => $item_params_db)
                                @if ($item['key'] === $key_params_db)
                                    <div class="input-group discount-live">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{$item_params['title']}}</span>
                                        </div>
                                        <input type="text" class="form-control ratio-val general-val {{isset($item_params['type']) && $item_params['type'] == "int" ? "input-price" : "input-decimal"}}" name="params[{{$item['key']}}][{{$key_params}}]" value="{{$item_params_db->$key_params}}" >
                                    </div>
                                    <br>
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                 @else
                    @foreach ($item['params'] as $key_params => $item_params)
                        <div class="input-group discount-live">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{$item_params['title']}}</span>
                            </div>
                            <input type="text" class="form-control ratio-val general-val {{isset($item_params['type']) && $item_params['type'] == "int" ? "input-price" : "input-decimal"}}" name="params[{{$item['key']}}][{{$key_params}}]" value="" >
                        </div>
                        <br>
                    @endforeach
                 @endif
                @endforeach
            @endif
         </div>
      </div>
      @if (isset($data))
         <div class="card card-custom gutter-b">
            <div class="card-header">
               <div class="card-title">
                  <h3 class="card-label">
                     {{__('Danh sách shop trong nhóm')}} <i class="mr-2"></i>
                  </h3>
               </div>
            </div>
            <div class="card-body">
               <div class="form-group row">
                  <div class="col-12 col-md-12">
                      <label>{{ __('Tìm kiếm') }}</label>
                      <div class="input-icon">
                        <input type="text" value="{{$data->id}}" id="id-group" style="display: none">
                          <input type="text" class="form-control" id="txtSearch" placeholder="Search...">
                          <span>
                                              <i class="flaticon2-search-1 icon-md"></i>
                                          </span>
                      </div>

                      <div class="nav-search-in-value" style="display: none;">
                          <div id="result-search"></div>
                          <style>
                              #result-search{
                                  background-color: #ffffff;
                                  background-clip: padding-box;
                                  border: 1px solid #E4E6EF;
                                  padding: 10px;
                              }
                              #result-search .rs-item{
                                  margin-bottom: 10px;
                              }
                              #result-search .rs-item:hover{
                                  background-color: #f7f8fa !important;
                              }

                              #result-search .rs-item a .info{
                                  margin-left: 10px;
                              }
                              #result-search .rs-item a .info p{
                                  margin-bottom: 0.2rem;
                              }
                          </style>
                      </div>
                      @if ($errors->has('title'))
                          <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                      @endif
                  </div>
              </div>
               <table class="table table-bordered table-hover table-checkable" id="showItem_datatable"></table>
            </div>
         </div>
      @endif
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
            <div class="form-group row" style="display: none">
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

       // let select = document.getElementById('timezonelist');
       // if (!Intl.supportedValuesOf) {
       //     let opt = new Option('Your browser does not support Intl.supportedValuesOf().', null, true, true);
       //     opt.disabled = true;
       //     select.options.add(opt);
       // } else {
       //     for (const timeZone of Intl.supportedValuesOf('timeZone')) {
       //         var option = document.createElement("option");
       //         option.text = timeZone;
       //         option.value = timeZone;
       //         select.options.add(option);
       //     }
       // }

       $("#backList").on("click",function (){

           if(confirm("Thông tin nhóm điểm bán chưa được lưu. Bạn có muốn quay lại không?") ){
               window.location.href = '{{route('admin.'.$module.'.index')}}';
           }
           else{
               //alert("Cancel clicked");
           }
       });
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
            $('.input-decimal').keypress(function(event) {
            if(event.which == 46
                && $(this).val().indexOf('.') != -1) {
                    event.preventDefault();
                } // prevent if already decimal point

                if(event.which != 46 && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                } // prevent if not number/dot
            });
        });
</script>
@if (isset($data))
    <script>
        $(document).ready(function () {
            setTimeout(function (){
                $("#language").val($("#kt_select2_1").val());
            },500);


            $("#kt_select2_1").on("change",function (){
                $("#language").val($(this).val());
            });
            KTDatatablesDataSourceAjaxServer1.init();
            $('#txtSearch').donetyping(function() {
                var find=$(this).val()
                var module = $('input#module_group').val();
                var url ='{{route("admin.shop-group.get-search")}}';
                $('#result-search .rs-item').remove();
                if(find == null || find == "" || find == undefined){
                    $('.nav-search-in-value').css('display','none');
                    return false
                }
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        find:find
                    }, // serializes the form's elements.
                    beforeSend: function (xhr) {

                    },
                    success: function (data) {
                        $('.nav-search-in-value').css('display','block');
                        $('#result-search').html(data);
                    },
                    error: function (data) {
                        toast('{{__('Không kết nối được với máy chủ')}}', 'error');

                    },
                    complete: function (data) {
                    }
                });
            }, 400);
            $(document).on("click",".btnAppend",function(e) {
                e.preventDefault();
                var module = $('input#module_group').val();
                var url = '{{route("admin.shop-group.update-shop-in-group")}}';
                var group_id =  $('#id-group').val();
                var id= $(this).data('id');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        _token:"{{csrf_token()}}",
                        group_shop:group_id,
                        id:id
                    },
                    beforeSend: function (xhr) {

                    },

                    success: function (data) {
                    if(data.status == 1){
                        $('.item-shop-'+id+' .btnAppend .info').append('<p style="color:#000000"><span><b>Nhóm:</b><span class="label label-pill label-inline label-center mr-2  label-info">'+data.title+'</span></span></p>');
                        $('#showItem_datatable').DataTable().ajax.reload();
                        toast('{{__('Thành công')}}');
                    }
                    else{
                        toast(data.message, 'error');
                    }
                    },
                    error: function (data) {
                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function (data) {
                    }
                });
            });
            $('body').on('click','.btn-delete-shop',function(e){
                e.preventDefault();
                var check = confirmData();
                if(!check){
                    return;
                }
                var group_id = $(this).data('group')
                var shop_id = $(this).data('shop')
                var url = '{{route("admin.shop-group.delete-shop-in-group")}}';
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        _token:"{{csrf_token()}}",
                        group_shop:group_id,
                        shop_id:shop_id
                    },
                    beforeSend: function (xhr) {

                    },

                    success: function (data) {
                    if(data.status == 1){
                            $('#showItem_datatable').DataTable().ajax.reload();
                            toast('{{__('Thành công')}}');
                    }
                    else{
                        toast(data.message, 'error');
                    }
                    },
                    error: function (data) {
                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function (data) {
                    }
                });

            })
            function confirmData() {
                var doc;
                var result = confirm("Bạn có muốn xóa shop khỏi nhóm!");
                if (result == true) {
                    return true;
                } else {
                    return false;
                }
            }
        });
        var datatable;
            var KTDatatablesDataSourceAjaxServer1 = function () {
               var initTable2 = function () {
                     datatable = $('#showItem_datatable').DataTable({
                        paging: false,
                        destroy: true,
                        responsive: true,
                        dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                                 <'row'<'col-sm-12'tr>>
                           <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                        lengthMenu: [20, 50, 100, 200,500,1000],
                        pageLength: 20,
                        language: {
                           'lengthMenu': 'Display _MENU_',
                        },
                        searchDelay: 500,
                        processing: true,
                        serverSide: true,
                        "order": [[1, "desc"]],
                        ajax: {
                           url: '{{route("admin.shop-group.get-shop-in-group")}}' + '?shop_group={{$data->id}}&ajax=1',
                           type: 'GET',
                           data: function (d) {
                                 d.id = $('#id').val();
                           }
                        },
                        columns: [
                           {
                                 data: null,
                                 title: '<label class="checkbox checkbox-lg checkbox-outline"><input type="checkbox" id="btnCheckAll">&nbsp<span></span></label>',
                                 orderable: false,
                                 searchable: false,
                                 width: "20px",
                                 class: "ckb_item",
                                 render: function (data, type, row) {
                                    return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input type="checkbox" rel="' + row.id + '" id="">&nbsp<span></span></label>';
                                 }
                           },
                           {data: 'id', title: 'ID'},
                           {data: 'title', title: 'Tên shop'},
                           {
                                 data: 'status', title: '{{__('Trạng thái')}}',
                                 render: function (data, type, row) {

                                    if (row.status == 1) {
                                       return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.shop.status.1')}}" + "</span>";
                                    } else if (row.status == 0) {
                                       return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.shop.status.0')}}" + "</span>";
                                    } else {
                                       return '';
                                    }

                                 }
                           },
                           { data: 'action',title:'Thao tác', orderable: false, searchable: false}

                        ],
                        "drawCallback": function (settings) {
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
                        var action=$(this).data('action');
                        var field=$(this).data('field');
                        var id=$(this).data('id');
                        var value=$(this).data('value');
                        if(field=='status'){

                           if(value==1){
                                 value=0;
                                 $(this).data('value',1);
                           }
                           else{
                                 value=1;
                                 $(this).data('value',0);
                           }
                        }
                        $.ajax({
                           type: "POST",
                           url: action,
                           data: {
                                 '_token':'{{csrf_token()}}',
                                 'field':field,
                                 'id':id,
                                 'value':value
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
               };
               return {
                     //main function to initiate the module
                     init: function () {
                        initTable2();
                     },
               };
            }();

    </script>
@endif
@endsection
