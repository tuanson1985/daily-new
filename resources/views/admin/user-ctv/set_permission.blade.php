{{-- Extends layout --}}
@extends('admin._layouts.master')
@section('action_area')
<div class="d-flex align-items-center text-right">
   <a href="{{route('admin.user-ctv.index')}}"
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
{{Form::open(array('route'=>array('admin.user-qtv.post_set_permission',$data->id),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
<div class="row">
   <div class="col-lg-12">
      <div class="card card-custom gutter-b">
         <div class="card-header">
            <div class="card-title">
               <h3 class="card-label">
                  {{__($page_breadcrumbs[1]['title']??"")}} <i class="mr-2"></i>
               </h3>
            </div>
         </div>
         <div class="card-body">
            @if(isset($data))
            <div class="text-center">
               <h3 class="bold">ID: {{$data->id}}</h3>
               <h3 class="bold text-danger">Tài khoản: {{$data->username}}</h3>
               <h3 class="bold text-danger">Email: {{$data->email}}</h3>
            </div>
            @endif
         </div>
      </div>
   </div>
</div>

<div class="card card-custom gutter-b">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">
                {{__('Phân quyền dịch vụ ( Vui lòng chọn shop trước khi phân quyền dịch vụ)')}} <i class="mr-2"></i>
            </h3>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-10">
                @php
                    $param=json_decode(isset($service_access->params)?$service_access->params:"");
                @endphp
                @if(isset($datatableService))
                    @forelse($datatableService as $index=> $item)
                        <div style="margin-bottom: 15px;" class="col-sm-12 service">
                            <div class="row m-form__group">
                                <label class="col-sm-4 col-lg-2 col-form-label">#{{$item->id}}: <b>[{{$item->title}}]</b></label>
                                <div class="col-sm-8 col-lg-10">
                                    <div style="padding-top: 10px;" class="row">
                                        <div class="col-sm-4">
                                            <label class="checkbox mb-1">
                                                @if(isset($param->display_info_role) && in_array($item->id,(array)$param->display_info_role))
                                                    <input value="{{$item->id}}" type="checkbox" checked name="display_info_role[]">
                                                @else
                                                    <input value="{{$item->id}}" type="checkbox" name="display_info_role[]">
                                                @endif
                                                <span></span>&nbsp Xem trước thông tin
                                            </label>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="checkbox mb-1">
                                                @if(isset($param->view_role) && in_array($item->id,(array)$param->view_role))
                                                    <input value="{{$item->id}}" checked type="checkbox" name="view_role[]">
                                                @else
                                                    <input value="{{$item->id}}" type="checkbox" name="view_role[]">
                                                @endif
                                                <span></span>&nbsp Xem
                                            </label>
                                        </div>
                                        <div class="col-sm-4 service_accept">
                                            <label class="checkbox mb-1">
                                                @if(isset($param->accept_role) && in_array($item->id,(array)$param->accept_role))
                                                    <input value="{{$item->id}}" checked type="checkbox" name="accept_role[]">
                                                @else
                                                    <input value="{{$item->id}}" type="checkbox" name="accept_role[]">
                                                @endif
                                                <span></span>&nbsp  Nhận
                                            </label>
                                        </div>

                                    </div>
                                    <div class="service_settings" style="display: none;">
                                        <div class="card card-custom kt_card_custom" id="kt_card_{{$index}}">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h3 class="card-label">Phân quyền nhận</h3>
                                                </div>
                                                <div class="card-toolbar">
                                                    <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary mr-1" data-card-tool="toggle" data-toggle="tooltip" data-placement="top" title="Thu gọn">
                                                        <i class="ki ki-arrow-down icon-nm"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div style="" class="m-portlet__body">
                                                    <div class="row">

                                                        <div class="col-sm-6 col-lg-6">
                                                            <label class="col-form-label">Phần trăm tiền nhận khi hoàn tất <span class="policy__text__error" style="color: #f64e60">(Điền dấu chấm để thể hiện số thập phân)</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control m-input" name="ratio_{{$item->id}}" value="{{isset($param->{'ratio_'.$item->id})?$param->{'ratio_'.$item->id}:""}}" placeholder="Số" aria-describedby="basic-addon2">
                                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
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
                    @empty
                    @endforelse
                @endif
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}
@endsection
{{-- Styles Section --}}
@section('styles')
@endsection
{{-- Scripts Section --}}
@section('scripts')
<script>
   "use strict";

   function init_discount(){
       $('.discount-live').each(function(){
           var el = $(this);
           el.find('.ratio-result').text(100 - (el.find('.ratio-val').val() > 0? el.find('.ratio-val').val(): 0))
       });
   }

   jQuery(document).ready(function () {

       $('#shop_access_custom input').change(function() {
           $('input[name="shop_access_all"]').prop("checked", false);
       });

       init_discount();
       $(".ratio-val").inputmask({
           groupSeparator: ",",
           radixPoint: ".",
           alias: "numeric",
           placeholder: "0",
           autoGroup: true,
           min:0,
           max:100
       });
       $('.ratio-val').keyup(function(){
           var el = $(this);
           var resetClass = el.hasClass('custom-val')? '.general-val': '.custom-val';
           if (el.hasClass('custom-val')) {
               el.parents('.discount-group').find(resetClass).val('').parents('.discount-live').find('.ratio-result').text(100);
           }else{
               el.parents('.discount-group').find(resetClass).val(el.val()).parents('.discount-live').find('.ratio-result').text(100 - (el.val() > 0? el.val(): 0));
           }
           el.parents('.discount-live').find('.ratio-result').text(100 - (el.val() > 0? el.val(): 0))
       });


       //btn submit form
       $('.btn-submit-custom').click(function (e) {
           e.preventDefault();
           var btn = this;
           if (confirm('Vui lòng kiểm tra phần đã cấu hình 1 lần nữa để tránh xảy ra lỗi setup nhầm. Cảm ơn!!')) {
               $(".btn-submit-custom").each(function (index, value) {
                   KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
               });
               $('.btn-submit-dropdown').prop('disabled', true);
               //gắn thêm hành động close khi submit
               $('#submit-close').val($(btn).data('submit-close'));
               var formSubmit = $('#' + $(btn).data('form'));
               formSubmit.submit();
           }
       });
       // var jsondata = [
       //
       //
       //     {"id":"15","parent":"#","text": "Child 1"},
       //     { "id": "ajson3", "parent": "ajson3", "text": "Child 1"},
       //     { "id": "ajson4", "parent": "ajson2", "text": "Child 2"},
       // ];

       // var jsondata=[{"id":"15","parent":"13","text":"Ch\u1ec9nh s\u1eeda ng\u00f4n ng\u1eef"},{"id":"19","parent":"0","text":"Qu\u1ea3n l\u00fd t\u00e0i kho\u1ea3n"},{"id":"13","parent":"18","text":"Ng\u00f4n ng\u1eef"},{"id":"16","parent":"13","text":"X\u00f3a ng\u00f4n ng\u1eef"},{"id":"18","parent":"0","text":"H\u1ec7 th\u1ed1ng"}];

    //    $('#kt_tree_3').jstree({
    //        "plugins": ["wholerow", "checkbox", "types","search"],
    //        "core": {
    //            "dblclick_toggle" : false,
    //            "themes": {
    //                "responsive": false,
    //                "icons":false,
    //                "dots": true,
    //            },
    //            "data": jsondata
    //        },

    //        "types": {
    //            "default": {
    //                "icon": "fa fa-folder text-warning"
    //            },
    //            "file": {
    //                "icon": "fa fa-file  text-warning"
    //            }
    //        },

    //    }).bind("loaded.jstree", function (e, data) {
    //        var perSelected=$('#permission_ids').val();

    //        var arrPer = perSelected.split(",");
    //        $.each(arrPer, function( index, value ) {

    //            $('#kt_tree_3').jstree("select_node", value, true);
    //        });


    //    })
    //        .on('changed.jstree', function (e, data) {

    //            var i, j, r = [];
    //            for(i = 0, j = data.selected.length; i < j; i++) {

    //                r.push(data.instance.get_node(data.selected[i]).id);
    //            }
    //            $('#permission_ids').val(r.join(','));
    //        });

    //    $( "#btnDeselectAll").click(function(e) {
    //        e.preventDefault();
    //        $("#kt_tree_3").jstree().deselect_all(true);
    //        $("#permission_ids").val('');
    //    });

    //    $( "#btnDeselectAll").click(function(e) {
    //        e.preventDefault();
    //        $("#kt_tree_3").jstree().uncheck_all(true);
    //    });

    //    $( "#btnSelectAll").click(function(e) {
    //        e.preventDefault();
    //        $("#kt_tree_3").jstree().check_all(true);
    //    });
    //    $( "#btnToggleTree").click(function(e) {
    //        var isOpen=$(this).data('open');

    //        if(isOpen){

    //            $("#kt_tree_3").jstree('close_all');
    //            $(this).data('open',0);
    //            $(this).text('{{__('Mở rộng')}}');
    //        }
    //        else{
    //            $("#kt_tree_3").jstree('open_all');
    //            $(this).data('open',1);
    //            $(this).text('{{__('Thu gọn')}}');
    //        }


    //    });

   });



</script>
<script>
   $('.service_accept input,[value="admin_plus_money"],[value="user_plus_money"],[value="admin_minus_money"],[value="user_minus_money"]').change(function () {
       UpdateView();
   });
   $('[name="input_img"]').change(function () {
       $('#img_preview').hide();
   });

   function UpdateView() {
       $('.service_accept input').each(function (idx, elm) {
           if ($(elm).is(':checked')) {
               $(elm).closest('.service').find('.service_settings').slideDown();
           } else {
               $(elm).closest('.service').find('.service_settings').slideUp();
           }
       });

       if ($('[value="user_plus_money"]:checked').length != 0 || $('[value="admin_plus_money"]:checked').length != 0) {
           $('.max_plus').show();
       } else {
           $('.max_plus').hide();
       }
       if ($('[value="user_minus_money"]:checked').length != 0 || $('[value="admin_minus_money"]:checked').length != 0) {
           $('.max_minus').show();
       } else {
           $('.max_minus').hide();
       }
   }

   UpdateView();
   $('.cbxAll').change(function () {
       var container = $(this).closest('table');
       if ($(this).is(':checked')) {
           $('[type="checkbox"]', container).prop('checked', true);
       } else {
           $('[type="checkbox"]', container).prop('checked', false);
       }
   })

</script>
<script>
   // jQuery(document).ready(function () {
   //     var vl=$('#kt_card_0')
   //
   // })

   $('.kt_card_custom').each(function (idx, elm) {
       var card= new KTCard($(elm).attr('id'));
   });

</script>
@endsection
