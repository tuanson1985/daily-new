{{-- Extends layout --}}
@extends('admin._layouts.master')
@section('action_area')
<div class="d-flex align-items-center text-right">
   <a href="{{route('admin.user-qtv.index')}}"
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

{{--<div class="card card-custom gutter-b">--}}
{{--   <div class="card-header">--}}
{{--      <div class="card-title">--}}
{{--         <h3 class="card-label">--}}
{{--            {{__('Phân quyền tài khoản game')}} <i class="mr-2"></i>--}}
{{--         </h3>--}}
{{--      </div>--}}
{{--   </div>--}}
{{--   <div class="card-body">--}}
{{--      <div class="mb-2 alert alert-info">--}}
{{--         Lưu ý ví dụ chiết khấu 10% tức là khách trả 100k ctv được 90k--}}
{{--      </div>--}}
{{--      <div class="m-form__group form-group">--}}
{{--         @foreach($providers as $provider)--}}
{{--         @if($provider->childs->count())--}}
{{--         <h4 class="bg-light">--}}
{{--            <a class="btn btn-sm btn-outline-primary" data-toggle="collapse" href="#coll_provider_{{ $provider->id }}" role="button" aria-expanded="false">--}}
{{--            <i class="fa fa-caret-down"></i>--}}
{{--            </a>--}}
{{--            {{ $provider->title }}--}}
{{--         </h4>--}}
{{--         @foreach($provider->childs as $item)--}}
{{--         <?php--}}
{{--            $checked = null;--}}
{{--            $ratio = [];--}}
{{--            if (!empty($data->access_categories)) {--}}
{{--                $pivot = $data->access_categories->where('id', $item->id)->first()->pivot??null;--}}
{{--                if (!empty($pivot)) {--}}
{{--                    $checked = $pivot->active == 1? 'checked': '';--}}
{{--                    $ratio = json_decode($pivot->ratio, true);--}}
{{--                }--}}
{{--            }--}}
{{--            ?>--}}
{{--         <div class="form-group discount-group collapse pl-2" id="coll_provider_{{ $provider->id }}">--}}
{{--            <label class="checkbox mb-1">--}}
{{--            <input type="checkbox" name="discount[{{$item->id}}][active]" {{ $checked }} value="1">--}}
{{--            <span></span><b style="margin-left: 15px">{{$item->title}}</b></label>--}}
{{--            </label>--}}
{{--            <div>Chiết khấu CTV thực nhận:</div>--}}
{{--            <div class="row mb-1">--}}
{{--               <div class="col-md-6">--}}
{{--                  <div class="input-group discount-live">--}}
{{--                     <div class="input-group-prepend">--}}
{{--                        <span class="input-group-text">Tất cả các mệnh giá</span>--}}
{{--                     </div>--}}
{{--                     <input type="text" class="form-control ratio-val general-val" name="discount[{{$item->id}}][ratio][default]" value="{{ $ratio['default']??null }}">--}}
{{--                     <div class="input-group-append">--}}
{{--                        <span class="input-group-text">100k thực nhận <span class="ratio-result text-danger px-1"></span> k</span>--}}
{{--                     </div>--}}
{{--                  </div>--}}
{{--               </div>--}}
{{--               <div class="col-md-6">--}}
{{--                  <div class="mb-2">--}}
{{--                     Hoặc chiết khấu theo từng mệnh giá:--}}
{{--                     <a class="btn btn-sm btn-outline-primary" data-toggle="collapse" href="#discount{{$item->id}}" role="button" aria-expanded="false">--}}
{{--                     <i class="fa fa-caret-down"></i>--}}
{{--                     </a>--}}
{{--                  </div>--}}
{{--                  <div class="collapse" id="discount{{$item->id}}">--}}
{{--                     <div class="card card-body">--}}
{{--                        <?php $prev = 0; ?>--}}
{{--                        @foreach(config('etc.discount_step') as $key => $value)--}}
{{--                        <div class="mb-2">--}}
{{--                           <div class="mb-1">Lớn hơn <b>{{ number_format($prev, 0, '.', ',') }}</b> hoặc bằng <b>{{ number_format($value, 0, '.', ',') }}</b></div>--}}
{{--                           <div class="input-group discount-live">--}}
{{--                              <input type="text" name="discount[{{$item->id}}][ratio][{{ $value }}]" class="form-control ratio-val custom-val" value="{{ $ratio[$value]??null }}">--}}
{{--                              <div class="input-group-append">--}}
{{--                                 <span class="input-group-text">100k thực nhận <span class="ratio-result text-danger px-1"></span>k</span>--}}
{{--                              </div>--}}
{{--                           </div>--}}
{{--                        </div>--}}
{{--                        <?php $prev = $value; ?>--}}
{{--                        @endforeach--}}
{{--                        <div class="mb-2">--}}
{{--                           <div class="mb-1">Lớn hơn <b>{{ number_format($value, 0, '.', ',') }}</b></div>--}}
{{--                           <div class="input-group discount-live">--}}
{{--                              <input type="text" name="discount[{{$item->id}}][ratio][over]" class="form-control ratio-val custom-val" value="{{ $ratio['over']??null }}">--}}
{{--                              <div class="input-group-append">--}}
{{--                                 <span class="input-group-text">100k thực nhận <span class="ratio-result text-danger px-1"></span> k</span>--}}
{{--                              </div>--}}
{{--                           </div>--}}
{{--                        </div>--}}
{{--                     </div>--}}
{{--                  </div>--}}
{{--               </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--            </div>--}}
{{--            <hr>--}}
{{--         </div>--}}
{{--         @endforeach--}}
{{--         @endif--}}
{{--         @endforeach--}}
{{--      </div>--}}
{{--   </div>--}}
{{--</div>--}}
{{--<div class="card card-custom gutter-b">--}}
{{--   <div class="card-header">--}}
{{--      <div class="card-title">--}}
{{--         <h3 class="card-label">--}}
{{--            {{__('Phân quyền shop được up acc')}} <i class="mr-2"></i>--}}
{{--         </h3>--}}
{{--      </div>--}}
{{--   </div>--}}
{{--   <div class="card-body">--}}
{{--    @if ($user->type_information == 0)--}}
{{--        <div class="alert alert-custom">--}}
{{--        <b class="text-warning">Lưu ý:</b>--}}
{{--        <ul>--}}
{{--            <li>Chọn phương án 2 All shop ctv sẽ up k giới hạn shop nào</li>--}}
{{--            <li>Qtv có quyền up shop A nếu shop A nằm trong phương án 1 hoặc 3.</li>--}}
{{--        </ul>--}}
{{--        </div>--}}
{{--    @endif--}}
{{--      <div class="row">--}}
{{--        @if ($user->type_information == 0)--}}
{{--            <div class="col-md-6 mb-2">--}}
{{--            <h6>Phương án 1: Chọn theo nhóm</h6>--}}
{{--            <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">--}}
{{--                <thead>--}}
{{--                    <tr>--}}
{{--                        <th style="width: 100px;text-align: center;">--}}
{{--                        <label class="checkbox mb-1">--}}
{{--                        <input type="checkbox" class="cbxAll">--}}
{{--                        <span style="margin-top: -5px;"></span>--}}
{{--                        </label>--}}
{{--                        </th>--}}
{{--                        <th><i>Chọn nhóm</i></th>--}}
{{--                    </tr>--}}
{{--                </thead>--}}
{{--                <tbody>--}}
{{--                    <?php $access_shop_groups = !empty(old('access_shop_groups'))? old('access_shop_groups'): $data->access_shop_groups->pluck('id')->toArray(); ?>--}}
{{--                    @foreach($shop_groups as $key => $item)--}}
{{--                    <tr>--}}
{{--                        <th style="width: 100px;text-align: center;" class="m-datatable__cell--center">--}}
{{--                        <label class="checkbox mb-1">--}}
{{--                        <input value="{{ $item->id }}" {{ in_array($item->id, $access_shop_groups)? 'checked': '' }} type="checkbox" name="access_shop_groups[]">--}}
{{--                        <span style="margin-top: -5px;"></span>--}}
{{--                        </label>--}}
{{--                        </th>--}}
{{--                        <th>{{ $item->title }}</th>--}}
{{--                    </tr>--}}
{{--                    @endforeach--}}
{{--                </tbody>--}}
{{--            </table>--}}
{{--            @if($errors->has('access_shop_groups'))--}}
{{--            <div class="form-control-feedback">{{ $errors->first('access_shop_groups') }}</div>--}}
{{--            @endif--}}
{{--            </div>--}}
{{--        @endif--}}
{{--         <div class="col-md-6 mb-2" id="shop_access_block">--}}
{{--            @if ($user->type_information == 0)--}}
{{--                <div class="mb-2">--}}
{{--                <label class="checkbox mb-1">--}}
{{--                <input type="checkbox" name="shop_access_all" value="1" {{ $data->shop_access == 'all'? 'checked': '' }}>--}}
{{--                <span></span><b class="ml-2">Phương án 2: All shops</b></label>--}}
{{--                </label>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            <div class="mb-2">--}}
{{--               <h6>Phương án 3: Chọn từng shop:</h6>--}}
{{--            </div>--}}
{{--            <?php $access_shops = !empty(old('shop_access'))? old('shop_access'): $data->access_shops->pluck('id')->toArray(); ?>--}}
{{--            <select name="shop_access[]" multiple="multiple" class="form-control select2"  data-placeholder="Chọn shop" style="width: 100%" id="kt_select2_2">--}}
{{--            @foreach($shops as $key => $item)--}}
{{--            <option value="{{ $item->id }}" {{ in_array($item->id, $access_shops)? 'selected': '' }}>{{ $item->domain }}</option>--}}
{{--            @endforeach--}}
{{--            </select>--}}
{{--            @if($errors->has('shop_access'))--}}
{{--            <div class="form-control-feedback">{{ $errors->first('shop_access') }}</div>--}}
{{--            @endif--}}
{{--         </div>--}}
{{--      </div>--}}
{{--   </div>--}}
{{--</div>--}}
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
{{--                                                    @if(isset($item))--}}
{{--                                                        @php--}}
{{--                                                            $server_mode =  \App\Library\Helpers::DecodeJson('server_mode',$item->params);--}}
{{--                                                            $server_id =  \App\Library\Helpers::DecodeJson('server_id',$item->params);--}}
{{--                                                            $server_data =  \App\Library\Helpers::DecodeJson('server_data',$item->params);--}}
{{--                                                        @endphp--}}
{{--                                                        @if($server_mode==1 &&!empty($server_id) && count($server_id)>0)--}}
{{--                                                            <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">--}}
{{--                                                                <thead>--}}
{{--                                                                <tr>--}}
{{--                                                                    <th>Tên máy chủ</th>--}}
{{--                                                                    <th style="width: 100px;text-align: center;">--}}
{{--                                                                        <label class="checkbox mb-1">--}}
{{--                                                                            <input type="checkbox" class="cbxAll">--}}
{{--                                                                            <span style="margin-top: -5px;"></span>--}}
{{--                                                                        </label>--}}
{{--                                                                    </th>--}}
{{--                                                                </tr>--}}
{{--                                                                </thead>--}}
{{--                                                                <tbody>--}}
{{--                                                                @for ($i = 0; $i < count($server_id); $i++)--}}
{{--                                                                    @if($server_data[$i]!="" && $server_data[$i]!='null')--}}
{{--                                                                        <tr>--}}
{{--                                                                            <th>{{$server_data[$i]}}</th>--}}
{{--                                                                            <th style="width: 100px;text-align: center;" class="m-datatable__cell--center">--}}
{{--                                                                                <label class="checkbox mb-1">--}}
{{--                                                                                    @if(isset($param->{'allow_server_'.$item->id}) && in_array($server_id[$i],(array)$param->{'allow_server_'.$item->id}))--}}
{{--                                                                                        <input value="{{$server_id[$i]}}" checked type="checkbox" name="allow_server_{{$item->id}}[]">--}}
{{--                                                                                    @else--}}
{{--                                                                                        <input value="{{$server_id[$i]}}" type="checkbox" name="allow_server_{{$item->id}}[]">--}}
{{--                                                                                    @endif--}}
{{--                                                                                    <span style="margin-top: -5px;"></span>--}}
{{--                                                                                </label>--}}
{{--                                                                            </th>--}}
{{--                                                                        </tr>--}}
{{--                                                                    @endif--}}
{{--                                                                @endfor--}}
{{--                                                                </tbody>--}}
{{--                                                            </table>--}}
{{--                                                        @endif--}}
{{--                                                    @endif--}}
{{--                                                    @if(isset($item))--}}
{{--                                                        @php--}}
{{--                                                            $name =  \App\Library\Helpers::DecodeJson('name',$item->params);--}}
{{--                                                        @endphp--}}
{{--                                                        @if(!empty($name) && count(array($name))>0)--}}
{{--                                                            <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">--}}
{{--                                                                <thead>--}}
{{--                                                                <tr>--}}
{{--                                                                    <th>Thuộc tính</th>--}}
{{--                                                                    <th style="width: 100px;text-align: center;">--}}
{{--                                                                        <label class="checkbox mb-1">--}}
{{--                                                                            <input type="checkbox" class="cbxAll">--}}
{{--                                                                            <span style="margin-top: -5px;"></span>--}}
{{--                                                                        </label>--}}
{{--                                                                    </th>--}}
{{--                                                                    <th>--}}
{{--                                                                        <label class="checkbox mb-1">--}}
{{--                                                                            @if(isset($param->accept_attribute_role) && in_array($item->id,(array)$param->accept_attribute_role))--}}
{{--                                                                                <input value="{{$item->id}}" checked type="checkbox" name="accept_attribute_role[]" >--}}
{{--                                                                            @else--}}
{{--                                                                                <input value="{{$item->id}}" type="checkbox" name="accept_attribute_role[]" >--}}
{{--                                                                            @endif--}}
{{--                                                                            <span style="margin-top: -5px;"></span>--}}
{{--                                                                        </label>--}}
{{--                                                                    </th>--}}
{{--                                                                </tr>--}}
{{--                                                                </thead>--}}
{{--                                                                <tbody>--}}
{{--                                                                @if(isset($param->accept_attribute_role) && in_array($item->id,(array)$param->accept_attribute_role))--}}
{{--                                                                    @for ($i = 0; $i < count($name); $i++)--}}
{{--                                                                        @if($name[$i]!="" && $name[$i]!=null)--}}
{{--                                                                            <tr>--}}
{{--                                                                                <th>{{$name[$i]}}</th>--}}
{{--                                                                                <th style="width: 100px;text-align: center;" class="m-datatable__cell--center">--}}
{{--                                                                                    <label class="checkbox mb-1">--}}
{{--                                                                                        <input value="{{$i}}" checked type="checkbox" name="allow_name_{{$item->id}}[]">--}}
{{--                                                                                        <span style="margin-top: -5px;"></span>--}}
{{--                                                                                    </label>--}}
{{--                                                                                </th>--}}
{{--                                                                                <th></th>--}}
{{--                                                                            </tr>--}}

{{--                                                                        @endif--}}
{{--                                                                    @endfor--}}
{{--                                                                @else--}}
{{--                                                                    @for ($i = 0; $i < count($name); $i++)--}}
{{--                                                                        @if($name[$i]!="" && $name[$i]!=null)--}}
{{--                                                                            <tr>--}}
{{--                                                                                <th>{{$name[$i]}}</th>--}}
{{--                                                                                <th style="width: 100px;text-align: center;" class="m-datatable__cell--center">--}}
{{--                                                                                    <label class="checkbox mb-1">--}}
{{--                                                                                        @if(isset($param->{'allow_name_'.$item->id}) && in_array($i,(array)$param->{'allow_name_'.$item->id}))--}}
{{--                                                                                            <input value="{{$i}}" checked type="checkbox" name="allow_name_{{$item->id}}[]">--}}
{{--                                                                                        @else--}}
{{--                                                                                            <input value="{{$i}}" type="checkbox" name="allow_name_{{$item->id}}[]">--}}
{{--                                                                                        @endif--}}
{{--                                                                                        <span style="margin-top: -5px;"></span>--}}
{{--                                                                                    </label>--}}
{{--                                                                                </th>--}}
{{--                                                                                <th></th>--}}
{{--                                                                            </tr>--}}

{{--                                                                        @endif--}}
{{--                                                                    @endfor--}}
{{--                                                                @endif--}}
{{--                                                                </tbody>--}}
{{--                                                            </table>--}}
{{--                                                        @endif--}}
{{--                                                    @endif--}}
                                                    <div class="row">
{{--                                                        <div class="col-sm-6 col-lg-4">--}}
{{--                                                            <label class="col-form-label">Yêu cầu tối đa được nhận</label>--}}
{{--                                                            <div class="input-group">--}}
{{--                                                                <input type="text" class="form-control m-input" name="limit_{{$item->id}}" value="{{isset($param->{'limit_'.$item->id})?$param->{'limit_'.$item->id}:""}}" placeholder="Số" aria-describedby="basic-addon2">--}}
{{--                                                                <div class="input-group-append"><span class="input-group-text">Lần</span></div>--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
                                                        <div class="col-sm-6 col-lg-4">
                                                            <label class="col-form-label">Phần trăm tiền nhận khi hoàn tất</label>
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
