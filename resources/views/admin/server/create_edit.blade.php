<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"> </script>
<div class="loadingServer" style="display: none;position: fixed;top: 0;left: 0;width: 100%;height: 100%;z-index: 999999;background: rgba(0,0,0,0.5);text-align: center">
    <button class="buttonload">
        <i class="fa fa-refresh fa-spin" style="color: #fff"></i>&nbsp;&nbsp;&nbsp;Đang đồng bộ server, Vui lòng chờ...
    </button>

    <style>
        /* Style buttons */
        .buttonload {
            margin-top: 50px;
            background-color: #04AA6D; /* Green background */
            border: none; /* Remove borders */
            color: white; /* White text */
            padding: 12px 16px; /* Some padding */
            font-size: 16px /* Set a font size */
        }
        .datepicker{z-index: 999!important}
    </style>
</div>
@extends('admin._layouts.master')

@section('action_area')
    <style>
        .tooltip {
            position: relative;
            display: inline-block;
            border-bottom: 1px dotted black;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>


    <div class="d-flex align-items-center text-right">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
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
@section('content')

    @if(isset($data))
        {{Form::open(array('route'=>array('admin.'.$module.'.update',$data->id),'method'=>'PUT','id'=>'formMain','enctype'=>"multipart/form-data" , 'files' => true))}}
    @else
        {{Form::open(array('route'=>array('admin.'.$module.'.store'),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
    @endif
    <input type="hidden" name="submit-close" id="submit-close">

    <div class="row">
        @if(isset($data))
            <h3 style="margin-bottom: 20px">Server {{$data->ipaddress}}</h3>
        @else
            <h3 style="margin-bottom: 20px">Thêm mới Server</h3>
        @endif
        <div class="tab">
            <button class="tablinks tabsLink1 active" onclick="openCity(event, 'London')" type="button">Thông tin server</button>
            <button class="tablinks tabsLink2" onclick="openCity(event, 'Paris')" type="button">Danh sách Shop</button>
            <button class="tablinks tabsLink3" onclick="openCity(event, 'Tokyo')" type="button">Lịch sử</button>
        </div>
        <div id="London" class="tabcontent" style="display: block">
            <div class="row">
                <div class="col-lg-9">
                    <div class="card card-custom gutter-b">


                        <div class="card-body">
                            {{-----server_category_id------}}
                            <div class="form-group row">
                                <div class="col-12 col-md-6">
                                    <label>{{ __('Danh mục server') }}</label>
                                    <select name="server_category_id" class="form-control col-md-5" id="kt_select2_1" style="width: 100%">
                                        <option value="0">-- {{__('Không chọn danh mục')}} --</option>
                                        @if( !empty(old('server_category_id')) )
                                            {!!\App\Library\Helpers::buildMenuDropdownList($dataCatalog,old('server_category_id')) !!}
                                        @else
                                            <?php $itSelect = [] ?>
                                            @if(isset($data))
                                                <?php array_push($itSelect, $data->server_category_id)?>
                                            @endif
                                            {!!\App\Library\Helpers::buildMenuDropdownList($dataCatalog,$itSelect) !!}
                                        @endif
                                    </select>
                                    @if($errors->has('server_category_id'))
                                        <div class="form-control-feedback">{{ $errors->first('server_category_id') }}</div>
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" value="{{isset($data) ? $data->type_category_id : 0}}" id="server_arr_selected"/>
                            <div class="formLoadArrNcc"></div>
                            {{-----parrent_id------}}
                            <div class="form-group row">
                                <div class="col-12 col-md-6">
                                    <label>{{ __('Nhà cung cấp') }}</label>
                                    <select name="parrent_id" class="form-control select2 col-md-5" id="kt_select2_2" style="width: 100%">
                                        <option value="0">-- {{__('Không chọn nhà cung cấp')}} --</option>
                                        @foreach ($dataCategory as $item)
                                            @if (isset($data) && $item->id == $data->parrent_id)
                                                <option value='{{$item->id}}' selected>{{$item->title}}</option>
                                            @else
                                                <option value='{{$item->id}}'>{{$item->title}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @if($errors->has('parrent_id'))
                                        <div class="form-control-feedback">{{ $errors->first('parrent_id') }}</div>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label>{{ __('Địa chỉ IP') }}</label>
                                    <input type="text" id="ipaddress" name="ipaddress" value="{{ old('ipaddress', isset($data) ? $data->ipaddress : null) }}" autofocus="true"
                                           placeholder="{{ __('Đia chỉ ip') }}" maxlength="250"
                                           class="form-control {{ $errors->has('key') ? ' is-invalid' : '' }}">
                                    @if ($errors->has('ipaddress'))
                                        <span class="form-text text-danger">{{ $errors->first('ipaddress') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                {{-- price --}}
                                <div class="col-12 col-md-12">
                                    <label  class="form-control-label">{{ __('Giá') }}</label>
                                    <input type="text" id="price"  name="price" value="{{ old('price', isset($data) ? $data->price : null) }}"
                                           placeholder="{{ __('Giá') }}"
                                           class="form-control input-price {{ $errors->has('price') ? ' is-invalid' : '' }}">
                                    @if($errors->has('price'))
                                        <div class="form-control-feedback">{{ $errors->first('price') }}</div>
                                    @endif
                                </div>
                            </div>


                            {{-----description------}}
                            <div class="form-group row" style="display: none">
                                <div class="col-12 col-md-12">
                                    <label for="locale">{{ __('Ghi chú') }}:</label>
                                    <textarea id="content" name="content" class="form-control ckeditor-basic" >{{ old('content', isset($data) ? $data->content : null) }}</textarea>
                                    @if ($errors->has('content'))
                                        <span class="form-text text-danger">{{ $errors->first('content') }}</span>
                                    @endif
                                </div>
                            </div>


                            {{-----gallery block------}}
                            <div class="form-group row hidden" style="display: none!important">
                                {{-----image------}}
                                <div class="col-md-4">
                                    <label for="locale">{{ __('Hình đại diện') }}:</label>
                                    <div class="">
                                        <div class="fileinput ck-parent" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: 150px; height: 150px">

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
                            {{-----end gallery block------}}


                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card card-custom gutter-b">
                        <div class="card-body">
                            {{-- register_date --}}
                            {{-- started_at --}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label>{{ __('Ngày đăng ký') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control date"
                                               name="register_date"
                                               value="{{ old('register_date', (isset($data) && strlen($data->register_date) > 0) ?  Carbon\Carbon::parse($data->register_date)->format('d/m/Y') : date('d/m/Y')) }}"
                                               placeholder="{{ __('Ngày đăng ký') }}" autocomplete="off"
                                              >

                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="la la-calendar"></i></span>
                                        </div>
                                    </div>
                                    @if($errors->has('register_date'))
                                        <div class="form-control-feedback">{{ $errors->first('register_date') }}</div>
                                    @endif
                                </div>
                            </div>

                            {{-- cf_status --}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label for="cf_status" class="form-control-label">{{ __('Trỏ qua Cloudfare') }}</label>
                                        <span class="switch switch-outline switch-icon switch-success btn-update-stt">
                                        <label>
                                            <input type="checkbox" id="cf_status" {{isset($data) && $data->cf_status == 1 ? "checked" : ""}}  name="cf_status"/>
                                        <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>



                            <div class="form-group row">
                                {{-- cf_account --}}
                                <div class="col-12 col-md-12">
                                    <label  class="form-control-label">{{ __('Tài khoản Cloudfare') }}</label>
                                    <input type="text" id="cf_account"  name="cf_account" value="{{ old('cf_account', isset($data) ? $data->cf_account : null) }}"
                                           placeholder="{{ __('Tài khoản CF') }}"
                                           class="form-control input-cf_account {{ $errors->has('cf_account') ? ' is-invalid' : '' }}">
                                    @if($errors->has('cf_account'))
                                        <div class="form-control-feedback">{{ $errors->first('cf_account') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label>{{ __('Ngày hết hạn') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control date"
                                               name="ended_at"
                                               value="{{ old('ended_at', (isset($data) && strlen($data->ended_at) > 0) ?  Carbon\Carbon::parse($data->ended_at)->format('d/m/Y') : date('d/m/Y',strtotime('+1 year'))) }}"
                                               placeholder="{{ __('Ngày hết hạn') }}" autocomplete="off"
                                               >

                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="la la-calendar"></i></span>
                                        </div>
                                    </div>
                                    @if($errors->has('ended_at'))
                                        <div class="form-control-feedback">{{ $errors->first('ended_at') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label>{{ __('Link gia hạn') }}</label>
                                    <input type="text" id="purchase_link_gen_slug" name="purchase_link" value="{{ old('purchase_link', isset($data) ? $data->purchase_link : null) }}" autofocus="true"
                                           placeholder="{{ __('Link gia hạn') }}" maxlength="120"
                                           class="form-control {{ $errors->has('purchase_link') ? ' is-invalid' : '' }}">
                                    @if ($errors->has('purchase_link'))
                                        <span class="form-text text-danger">{{ $errors->first('purchase_link') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- status --}}
                            <div class="form-group row">
                                <div class="col-12 col-md-12">
                                    <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                                    {{Form::select('status',config('module.'.$module.'.status'),old('status', isset($data) ? $data->status : null),array('class'=>'form-control'))}}
                                    @if($errors->has('status'))
                                        <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="Paris" class="tabcontent">
            {{--Thêm thuộc tính yêu cầu khách nhập--}}
            <div class="row">
                <div class="col-lg-12">

                    <div class="card card-custom gutter-b">
                        <div class="card-header">
                            <div class="d-flex align-items-center text-right" style="margin-left: auto;order: 2;">
                                <div class="btn-group">
                                    <a href="javascript:updateServer(0)" type="button"  class="btn btn-success font-weight-bolder">
                                        <i class="la la-refresh"></i>
                                        {{__('Cập nhật Shop')}}
                                    </a>
                                </div>&nbsp;&nbsp;
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <style>
                                    .lstShop {
                                        border-collapse: collapse;
                                        width: 100%;
                                    }

                                    .lstShop td, th {
                                        border: 1px solid #cccccc;
                                        text-align: left;
                                        padding: 8px;
                                        font-size: 13px;
                                    }

                                    .lstShop    tr:nth-child(even) {
                                        background-color: #dddddd;
                                    }
                                </style>
                                @if(isset($dataShopInServer) && count($dataShopInServer) > 0)
                                    <div class="ttlstShop" style="width: 100%">
                                        <div class="container-fulid" style="padding: 15px">
                                            <table class="lstShop">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Shop</th>
                                                    <th>Nhóm Shop</th>
                                                    <th>Tài khoản CloudFare</th>
                                                    <th>Trạng thái</th>
                                                    <th>Ngày cập nhật</th>
                                                    <th>Thao tác</th>
                                                </tr>
                                                @foreach($dataShopInServer as $item)
                                                    <tr>
                                                        <td>{{$item->id}}</td>
                                                        <td>{{$item->title}}</td>
                                                        <td>{{isset($item->group->title) ? $item->group->title : ""}}</td>
                                                        <td>{{$data->cf_account}}</td>
                                                        <td>
                                                            <span class="switch switch-outline switch-icon switch-success btn-update-stt" data-id="{{$item->id}}">
                                                                <label>
                                                                    @if($item->status == 1)
                                                                        <input type="checkbox" checked="checked" name="select" onchange="changeStatus(event,{{$item->id}})">
                                                                    @else
                                                                        <input type="checkbox" name="select" onchange="changeStatus(event,{{$item->id}})">
                                                                    @endif
                                                                    <span></span>
                                                                </label>
                                                            </span>
                                                        </td>
                                                        <td>{{$item->updated_at}}</td>
                                                        <td>
                                                            <a href="javascript://"  rel="{{$item->id}}" data-cf="{{$item->cf_status}}" data-title="{{$item->title}}" data-status="{{$item->status}}" data-group="{{$item->group_id != null ? $item->group_id : 0}}" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary edit_toggle" data-toggle="modal" data-target="#updateShopModal" id="details_{{$item->id}}" title="Sửa"><i class="la la-edit"></i></a>
                                                            <a  rel="{{$item->id}}" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-text-white btn-hover-bg-danger delete_toggle' data-toggle="modal" data-target="#deleteModal" class="delete_toggle" title="Xóa"><i class="la la-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>

                                @endif
                                <div class="col-12 col-md-12">
                                    <div id="field_send_container" class="form-group m-form__group">
                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"> </script>
                                        @if(isset($data))
                                            @php
                                                $shop_name =  \App\Library\Helpers::DecodeJson('shop_name',$data->shop_name);
                                                $shop_status =  \App\Library\Helpers::DecodeJson('shop_status',$data->shop_name);
                                                $register_date =  \App\Library\Helpers::DecodeJson('register_date_dm',$data->shop_name);
                                                $ended_date =  \App\Library\Helpers::DecodeJson('ended_date',$data->shop_name);
                                                $ncc =  \App\Library\Helpers::DecodeJson('NCC',$data->shop_name);
                                                $img_domain =  \App\Library\Helpers::DecodeJson('image_domain',$data->shop_name);
                                                $shop_link =  \App\Library\Helpers::DecodeJson('shop_link',$data->shop_name);
                                                $cf_account =  \App\Library\Helpers::DecodeJson('cf_acc',$data->shop_name);
                                            @endphp
                                            @if(!empty($shop_name))
                                                @for ($i = 0; $i < count($shop_name); $i++)
                                                    @if( $shop_name[$i]!=null)
                                                        <div class="row cat-item" style="margin-top: 5px;">
                                                            <div class="input-group">
                                                                <div class="col-sm-12">
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
                                                                        <input  title="Tên domain" type="text" class="m-input m-input--air" style="width: calc(100% - 900px);color: blue;" name="shop_name[]" placeholder="Tên Shop" value="{{$shop_name != null ? $shop_name[$i] : ""}}">
                                                                        <input  title="Ngày đăng ký"  type="text" class="form-control  date"  style="width: 130px;color: blue;" name="register_date_dm[]" placeholder="Ngày đăng ký" value="{{$register_date != null ? $register_date[$i] : ""}}">
                                                                        <input  title="Ngày hết hạn"  type="text" class="form-control  date"  style="width: 130px;color: blue;" name="ended_date[]" placeholder="Ngày hết hạn" value="{{$ended_date != null ? $ended_date[$i] : ""}}">
                                                                        <input  title="Link gia hạn"  type="text" class="form-control" style="width: 100px" name="shop_link[]" placeholder="Link gia hạn" value="{{$shop_link != null ? $shop_link[$i] : ""}}">
                                                                        <input  title="Nhà cung cấp domain" type="text" class="form-control" style="width: 100px" name="NCC[]" placeholder="Nhà cung cấp" value="{{$ncc != null ? $ncc[$i] : ""}}">
                                                                        <input  title="Tài khoản CF(Nếu trỏ qua CF)" type="text" class="form-control" style="width: 100px" name="cf_acc[]" placeholder="Tài khoản CF(Nếu trỏ qua CF)" value="{{$cf_account != null ? $cf_account[$i] : ""}}">
                                                                        <div  title="Ảnh CMND"  class="form-control" style="width: 140px;padding: 3px">
                                                                            {{-----image------}}
                                                                            <div class="" style="width: 140px">
                                                                                <div class="">
                                                                                    <div class="fileinput ck-parent" data-provides="fileinput">
                                                                                        <div class="fileinput-new thumbnail" style="width: 31px; height: 31px;position: relative">

                                                                                            @if(old('image', $img_domain != null ? $img_domain[$i] : null)!="")
                                                                                                <a target="_blank" href="{{$img_domain != null ? $img_domain[$i] : null}}"><img class="ck-thumb" style="position: absolute;left: 0;top: 0;width: 100%;height: 100%;" src="{{ old('image', $img_domain != null ? $img_domain[$i] : null) }}"></a>
                                                                                            @else
                                                                                                <img class="ck-thumb" style="position: absolute;left: 0;top: 0;;width: 100%;height: 100%;" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                                                            @endif
                                                                                            <input class="ck-input" type="hidden" name="image_domain[]" value="{{ old('image',$img_domain != null ? $img_domain[$i] : null) }}">

                                                                                        </div>

                                                                                        <a href="#" style="padding: 5px;border: 1px solid #ccc;border-radius: 10px;text-align: center;" class=" red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                                                                        <a href="#" style="padding: 5px;border: 1px solid #ccc;border-radius: 10px;text-align: center;" class=" red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>

                                                                                    </div>
                                                                                    @if ($errors->has('image'))
                                                                                        <span class="form-text text-danger">{{ $errors->first('image') }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                            <select  style="width: 80px" title="Trạng thái"  class="form-control m-input m-input--air" name="shop_status[]">
                                                                                <option style="color:blue" value="1" {{$shop_status != null && $shop_status[$i]=="1"?"selected":""}}>Hoạt động</option>
                                                                                <option style="color:red" value="0" {{$shop_status != null && $shop_status[$i]=="0"?"selected":""}}>Ngưng hoạt động</option>
                                                                                <option style="color:yellowgreen" value="2" {{$shop_status != null && $shop_status[$i]=="2"?"selected":""}}>Đang dựng</option>
                                                                            </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endfor
                                            @else
                                                <div class="row cat-item" style="margin-top: 5px;">
                                                    <div class="input-group">
                                                        <div class="col-sm-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
                                                                <input  title="Tên domain" type="text" class="m-input m-input--air" style="width: calc(100% - 900px);color: blue;" name="shop_name[]" placeholder="Tên Shop" value="">
                                                                <input  title="Ngày đăng ký"  type="text" class="form-control  date"  style="width: 130px;color: blue;" name="register_date_dm[]" placeholder="Ngày đăng ký" value="">
                                                                <input  title="Ngày hết hạn"  type="text" class="form-control  date"  style="width: 130px;color: blue;" name="ended_date[]" placeholder="Ngày hết hạn" value="">
                                                                <input  title="Link gia hạn"  type="text" class="form-control" style="width: 100px" name="shop_link[]" placeholder="Link gia hạn" value="">
                                                                <input  title="Nhà cung cấp domain" type="text" class="form-control" style="width: 100px" name="NCC[]" placeholder="Nhà cung cấp" value="">
                                                                <input  title="Tài khoản CF(Nếu trỏ qua CF)" type="text" class="form-control" style="width: 100px" name="cf_acc[]" placeholder="Tài khoản CF(Nếu trỏ qua CF)" value="">
                                                                <div  title="Ảnh CMND"  class="form-control" style="width: 140px;padding: 3px">
                                                                    {{-----image------}}
                                                                    <div class="" style="width: 140px">
                                                                        <div class="">
                                                                            <div class="fileinput ck-parent" data-provides="fileinput">
                                                                                <div class="fileinput-new thumbnail" style="width: 31px; height: 31px;position: relative">
                                                                                    <img class="ck-thumb" style="position: absolute;left: 0;top: 0;;width: 100%;height: 100%;" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                                                    <input class="ck-input" type="hidden" name="image_domain[]" value="">

                                                                                </div>

                                                                                <a href="#" style="padding: 5px;border: 1px solid #ccc;border-radius: 10px;text-align: center;" class=" red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                                                                <a href="#" style="padding: 5px;border: 1px solid #ccc;border-radius: 10px;text-align: center;" class=" red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>

                                                                            </div>
                                                                            @if ($errors->has('image'))
                                                                                <span class="form-text text-danger">{{ $errors->first('image') }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if(!isset($data))
                                                                    <select  style="width: 80px" title="Trạng thái"  class="form-control m-input m-input--air" name="shop_status[]">
                                                                        <option style="color:blue" value="1" selected>Hoạt động</option>
                                                                        <option style="color:red" value="0" >Ngưng hoạt động</option>
                                                                        <option style="color:yellowgreen" value="2" >Đang dựng</option>
                                                                    </select>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                        @else
                                            <div class="row cat-item" style="margin-top: 5px;">
                                                <div class="input-group">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
                                                            <input  title="Tên domain" type="text" class="m-input m-input--air" style="width: calc(100% - 900px);color: blue;" name="shop_name[]" placeholder="Tên Shop" value="">
                                                            <input  title="Ngày đăng ký"  type="text" class="form-control  date"  style="width: 130px;color: blue;" name="register_date_dm[]" placeholder="Ngày đăng ký" value="">
                                                            <input  title="Ngày hết hạn"  type="text" class="form-control  date"  style="width: 130px;color: blue;" name="ended_date[]" placeholder="Ngày hết hạn" value="">
                                                            <input  title="Link gia hạn"  type="text" class="form-control" style="width: 100px" name="shop_link[]" placeholder="Link gia hạn" value="">
                                                            <input  title="Nhà cung cấp domain" type="text" class="form-control" style="width: 100px" name="NCC[]" placeholder="Nhà cung cấp" value="">
                                                            <input  title="Tài khoản CF(Nếu trỏ qua CF)" type="text" class="form-control" style="width: 100px" name="cf_acc[]" placeholder="Tài khoản CF(Nếu trỏ qua CF)" value="">
                                                            <div  title="Ảnh CMND"  class="form-control" style="width: 140px;padding: 3px">
                                                                {{-----image------}}
                                                                <div class="" style="width: 140px">
                                                                    <div class="">
                                                                        <div class="fileinput ck-parent" data-provides="fileinput">
                                                                            <div class="fileinput-new thumbnail" style="width: 31px; height: 31px;position: relative">
                                                                                <img class="ck-thumb" style="position: absolute;left: 0;top: 0;;width: 100%;height: 100%;" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                                                <input class="ck-input" type="hidden" name="image_domain[]" value="">

                                                                            </div>

                                                                            <a href="#" style="padding: 5px;border: 1px solid #ccc;border-radius: 10px;text-align: center;" class=" red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                                                            <a href="#" style="padding: 5px;border: 1px solid #ccc;border-radius: 10px;text-align: center;" class=" red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>

                                                                        </div>
                                                                        @if ($errors->has('image'))
                                                                            <span class="form-text text-danger">{{ $errors->first('image') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <select  style="width: 80px" title="Trạng thái"  class="form-control m-input m-input--air" name="shop_status[]">
                                                                <option style="color:blue" value="1" selected>Hoạt động</option>
                                                                <option style="color:red" value="0" >Ngưng hoạt động</option>
                                                                <option style="color:yellowgreen" value="2" >Đang dựng</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-12 col-md-12">
                                <div class="text-right" style="display: block;width: 100%;">
                                    <button id="btnAddSendmore" type="button" class="btn btn-primary m-btn m-btn--air">
                                        + Thêm shop cho server
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="Tokyo" class="tabcontent">
            <div class="row">
                <div class="col-lg-12">
                        <div class="ttlstShop" style="width: 100%;background: #fff;border-radius: 10px;">
                            <div class="container-fulid" style="padding: 15px">
                                <table class="lstShop">
                                    <tr>
                                        <th>ID</th>
                                        <th>Server</th>
                                        <th>Ngày cập nhật</th>
                                        <th>Nội dung cập nhật</th>
                                        <th>Danh sách Shop</th>
                                        <th>Giá Server(/Tháng)</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                    @if(isset($dataLogServer) && count($dataLogServer) > 0)
                                        @foreach($dataLogServer as $item)
                                            <tr>
                                                <td>{{$item->id}}</td>
                                                <td>{{$item->ipaddress}}</td>
                                                <td>{{$item->updated_at}}</td>
                                                <td>{{$item->content}}</td>
                                                <td>{{$item->shop_list}}</td>
                                                <td>{{number_format($item->new_price)}}đ</td>
                                                <td>{{$item->status == 1 ? "Hoạt động" : "Ngưng hoạt động"}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        <style>
           /* Style the tab */
            .tab {
                overflow: hidden;
                border: 1px solid #ccc;
                background-color: #ffffff;
                display: block;width: 100%;
            }

            /* Style the buttons inside the tab */
            .tab button {
                background-color: inherit;
                float: left;
                border: none;
                outline: none;
                cursor: pointer;
                padding: 14px 16px;
                transition: 0.3s;
                font-size: 14px;
                font-weight: bold;
            }

            /* Change background color of buttons on hover */
            .tab button:hover {
                background-color: #ddd;
            }

            /* Create an active/current tablink class */
            .tab button.active {
                background-color: #ccc;
            }

            /* Style the tab content */
            .tabcontent {
                display: none;
                padding: 6px 12px;
                border: 1px solid #ccc;
                border-top: none;
                width: 100%;
            }
        </style>



    </div>





    {{ Form::close() }}

    <!-- delete item Modal -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.shop.destroy',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'DELETE'))}}
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
    @if(isset($data))
    <!-- delete item Modal -->
    <div class="modal fade" id="updateShopModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Chỉnh sửa thông tin Shop')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        {{-- shop_name --}}
                        <div class="col-12 col-md-12">
                            <input type="text" id="shop_name_popup" disabled  value="" placeholder="{{ __('Shop') }}" class="form-control input-cf_account">
                        </div>
                    </div>
                    {{-- group_id --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <select class="form-control col-md-5" id="group_id_popup" style="width: 100%;max-width: 100%">
                                <option value="0">-- {{__('--Nhóm Shop---')}} --</option>
                                <?php $itSelect = [] ?>
                                {!!\App\Library\Helpers::buildMenuDropdownList($dataGroupShop,$itSelect) !!}
                            </select>
                        </div>
                    </div>
                    {{-- cf_status --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="cf_status" class="form-control-label" style="padding: 10px 0">{{ __('Trỏ qua tài khoản Cloudfare') }}</label>
                            <span class="switch switch-outline switch-icon switch-success btn-update-stt" style="display: inline-block;float: right">
                                <label>
                                    <input type="checkbox" id="cf_status_popup"/>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>

                    {{-- status --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                            <select class="form-control col-md-5" id="status_popup" style="width: 100%;max-width: 100%">
                                <option value="">--Trạng thái--</option>
                                @foreach(config('module.'.$module.'.status') as $key=>$value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" id="id_popup" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Bỏ qua')}}</button>
                    <button type="button" class="btn btn-danger m-btn m-btn--custom  btn-sumit-detais" >{{__('Cập nhật')}}</button>
                </div>
            </div>
        </div>
    </div>
    @endif

@endsection

@section('scripts')


    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>
    <script>


        "use strict";
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        $(".btn-sumit-detais").on("click",function (){
            const id = $("#updateShopModal #id_popup").val();
            const group_id = $("#updateShopModal #group_id_popup").val();
            const cf_status = $("#updateShopModal #cf_status_popup").is(":checked") == true ? 1 : 0;
            const status = $("#updateShopModal #status_popup").val();
            console.log("id:"+id+"group_id:"+group_id+"cf_status:"+cf_status+"status:"+status);

            $.ajax({
                type: "POST",
                url: "{{route('admin.server.update-shop')}}",
                data: {
                    '_token':'{{csrf_token()}}',
                    'id':id,
                    'group_id':group_id,
                    'cf_status':cf_status,
                    'status':status
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    $('#updateShopModal').hide();
                    if (data.status == 1) {

                        toast(data.message);
                        $("#details_"+id).attr("data_group",group_id);
                        $("#details_"+id).attr("data-cf",cf_status);
                        $("#details_"+id).attr("data-status",status);
                        setTimeout(function (){
                            location.reload();
                        },2000)
                    } else {
                        toast(data.message, 'error');
                    }
                },
                error: function (data) {
                    $('#updateShopModal').hide();
                    toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                },
                complete: function (data) {

                }
            });
        })

        function updateServer(id){
            $(".loadingServer").show();
            $.ajax({
                type: "POST",
                url: "{{route('admin.server.update-server')}}",
                data: {
                    '_token':'{{csrf_token()}}',
                    'id':id
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    $(".loadingServer").hide();
                    if (data.status == 1) {
                        $(".loadingServer").hide();
                        toast('{{__('Cập nhật thành công, Tải lại trang để xem cập nhật')}}');
                        //KTDatatablesDataSourceAjaxServer.init();
                    } else {

                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    }
                },
                error: function (data) {
                    $(".loadingServer").hide();
                    toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                },
                complete: function (data) {

                }
            });
        }

        function changeStatus(e,id) {
            const checked = e.target.checked;
            $.ajax({
                type: "POST",
                url: "{{route('admin.shop.update-stt')}}",
                data: {
                    '_token':'{{csrf_token()}}',
                    'field':'status',
                    'id':id,
                    'value':checked
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {

                    if (data.status == 1) {

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
        }


        $('#deleteModal').on('show.bs.modal', function(e) {
            //get data-id attribute of the clicked element
            var id = $(e.relatedTarget).attr('rel')
            $('#deleteModal .id').attr('value', id);
        });
        $('#updateShopModal').on('show.bs.modal', function(e) {
            //get data-id attribute of the clicked element
            const id = $(e.relatedTarget).attr('rel');
            $("#group_id_popup").val($(e.relatedTarget).attr('data-group'));
            $("#shop_name_popup").val($(e.relatedTarget).attr('data-title'));
            $("#status_popup").val($(e.relatedTarget).attr('data-status'));
            if($(e.relatedTarget).attr('data-status'))
            $("#cf_status_popup").prop("checked", $(e.relatedTarget).attr('data-cf') == 1 ? true : false);
            $('#updateShopModal .id').attr('value', id);
        });

        function openCity(evt, cityName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        function SendEvents1(selector) {

            $('.btnRemoveOpt', selector).click(function () {
                var container = $(this).closest('.cat-container');
                var count = container.find('.data-item').length;
                if (count == 1)
                    return;
                $(this).closest('.data-item').remove();
            });

            $('select[name="send_type[]"]', selector).change(function () {
                if ([6].indexOf(parseInt($(this).val())) == -1) {
                    $(this).closest('.cat-item').find('.cat-container').hide();
                } else {
                    $(this).closest('.cat-item').find('.cat-container').show();
                }
            });
            $('.btnRemove', selector).click(function () {
                var fcount = $(this).closest('.m-form__group').find('>.cat-item').length;
                if (fcount == 1)
                    return;
                $(this).closest('.cat-item').remove();
            });
        }


        $(document).ready(function () {
            $('.date').datepicker({
                format: 'dd/mm/yyyy'
            });
            $('#btnAddSendmore').click(function(){

                var fcount = $('#field_send_container>.row').length;
                if (fcount >= 1005)
                    return;
                var adpl = $('#field_send_container>.row').first().clone();
                adpl.addClass('hide-data');
                $('input[type="text"]', adpl).val('');
                $('[name="send_type[]"]', adpl).val(1);
                adpl.appendTo($('#field_send_container'));
               // $('input[type="text"]', adpl).focus();
                SendEvents1(adpl);
                $('select[name="send_type[]"]', adpl).change();
                setTimeout(function (){
                    $('.date').datepicker({
                        format: 'dd/mm/yyyy'
                    });
                },100)

            });

            $(".btnRemove").click(function (){
                $(this).closest(".row").remove();
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

            $("#kt_select2_1").on("change",function (){
                loadSubCateServer();
            })

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
        $(document).ready(function () {
            loadSubCateServer();
            $(".add-row").click(function () {
                var markup = '<tr><td><input type="text" class="form-control amount-item" name="params_amount[]"  value=""></td><td><input type="text"  class="form-control percent-item" maxlength="3" name="params_percent[]"  value=""></td></tr>';
                $("#type-code-1 tbody").append(markup);
            });
            checkType();
            function checkType(){
                var type = $('.type-gift').val();
                $('.item-type').css('display','none')
                if(type == 1){
                    $('.item-type-1').css('display','block');
                }
                else if(type == 2){
                    $('.item-type-2').css('display','block');
                }
            }
            $('.type-gift').on('change',function(){
                checkType();
            })
        });


        //func loadAttribute for Theme
        function loadSubCateServer(){
            let server_category_id = 0;
            let server_arr_selected = 0;
            server_category_id = $("#kt_select2_1").val();
            server_arr_selected = $("#server_arr_selected").val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/server/loadSubCateServer',
                data: {
                    "server_category_id":server_category_id,
                    "server_selected":server_arr_selected
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if (data.status == "SUCCESS") {
                        $(".formLoadArrNcc").html(data.htmlAttribute);
                    } else {
                        toast(data.msg, 'error');
                        $(".formLoadArrNcc").html("");
                    }
                },
                error: function (data) {
                    toast('{{__('Không thể load mảng')}}', 'error');
                },
                complete: function (data) {
                    //KTUtil.btnRelease(btn);
                }
            });
        }

    </script>
@endsection
