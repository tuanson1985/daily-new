@extends('admin._layouts.master')
@section('head')
@endsection
@section('content')
    <!-- BEGIN: Subheader -->
    <div class="m-subheader ">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <ul class="bread_custom m-subheader__breadcrumbs m-nav m-nav--inline">
                    <li class="m-nav__item m-nav__item--home">
                        <a href="/admin" class="m-nav__link m-nav__link--icon">
                            <i class="m-nav__link-icon la la-home" style="width:28px"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="m-nav__separator">
                        /
                    </li>
                    <li class="m-nav__item">
                        <a href="{{route('admin.service-item.index')}}" class="m-nav__link">
						<span class="m-nav__link-text">
							Cấu hình dịch vụ
						</span>
                        </a>
                    </li>
                    <li class="m-nav__separator">
                        /
                    </li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link active">
						<span class="m-nav__link-text">
							@if(isset($data))
                                Chỉnh sửa
                            @else
                                Thêm mới
                            @endif
						</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- END: Subheader -->
    <div class="m-content">

        @if(isset($data))
            {{ Form::open(array('route'=>array('admin.service-item.update',$data->id),'class'=>'m-form m-form--state','method'=>'PUT','enctype'=>"multipart/form-data")) }}
        @else
            {{ Form::open(array('route'=>array('admin.service-item.store'),'class'=>'m-form m-form--state','method'=>'POST','enctype'=>"multipart/form-data" )) }}
        @endif
            <div style="padding: 0;" class="m-content">
                <div class="m-portlet m-form">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">
                                    @if(isset($data))
                                        Chỉnh sửa dịch vụ #{{$data->id}}
                                    @else
                                        Thêm mới
                                    @endif</h3>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="m-form__section m-form__section--first m-form m-form--label-align-right">
                            <div class="form-group m-form__group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="row m-form__group">
                                            <label class="col-md-4 col-form-label">Tên dịch vụ</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="text" name="title"
                                                           value="{{ old('title', isset($data) ? $data->title : null) }}"
                                                           class="form-control m-input m-input--air"
                                                           placeholder="Tên dịch vụ">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row m-form__group">
                                            <label class="col-md-4 col-form-label">Danh mục</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <select id="group_id" name="group_id" class="form-control" title="">
                                                        @if( !empty(old('group_id')) )
                                                            {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,old('group_id')) !!}
                                                        @elseif(!empty(Request::get('group_id')))
                                                            {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,Request::get('group_id')) !!}
                                                        @else
                                                            <?php $itSelect = [] ?>
                                                            @if(isset($data))
                                                                @foreach($data->groups as $gr)
                                                                    {{$gr->id}}
                                                                    <?php array_push($itSelect, $gr->id)?>
                                                                @endforeach
                                                            @endif
                                                            {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,$itSelect) !!}
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-sm-6">
                                    <div class="row m-form__group">
                                        <label class="col-md-4 col-form-label">Thứ tự</label>
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <input type="text" name="order"
                                                       value="{{ old('order', isset($data) ? $data->order : null) }}"
                                                       class="form-control m-input m-input--air"
                                                       placeholder="Số thứ tự">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row m-form__group">
                                        <label class="col-sm-4 col-lg-4 col-form-label">Tự động giao dịch</label>
                                        <div class="col-sm-8 col-lg-8">
                                            <div class="input-group">
                                                <div class="input-group">
                                                    {{Form::select('input_auto',['0'=>'Không','1'=>'Có'],old('input_auto', isset($data) ? \App\Library\Helpers::DecodeJson('input_auto',$data->params) : null),array('class'=>'form-control m-input m-input--air'))}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="row m-form__group">
                                            <label class="col-sm-4 col-form-label">Trạng thái</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    {{Form::select('status',config('constants.module.game.status'),old('status', isset($data) ? $data->status : null),array('class'=>'form-control m-input m-input--air'))}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group">
                                <div class="row m-form__group">
                                    <label class="col-sm-4 col-lg-2 col-form-label">Hình ảnh</label>
                                    <div class="col-sm-8 col-lg-10">
                                        <div class="fileinput  {{ old('image', isset($data) ? $data->image : null)!=""?"fileinput-exists":"fileinput-new" }}  " data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                                                <img src="/assets/backend/images/empty-photo.jpg" data-src="/assets/backend/images/empty-photo.jpg" alt="">
                                            </div>

                                            <div class="fileinput-preview fileinput-exists thumbnail" style="width: 150px; height: 150px;">
                                                @if(old('image', isset($data) ? $data->image : null)!="")
                                                    <img src="{{ old('image', isset($data) ? $data->image : null) }}" >
                                                    <input type="hidden" name="image_oldest" value="1">
                                                @endif
                                            </div>
                                            <div>
                                        <span class="btn btn-default btn-file">
                                            <span class="fileinput-new">Chọn ảnh đại diện</span>
                                            <span class="fileinput-exists">Đổi ảnh đại diện</span>
                                                <input type="file" name="image">
                                        </span>
                                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Xóa</a>
                                            </div>
                                        </div>
                                        @if($errors->has('image'))
                                            <div class="form-control-feedback">{{ $errors->first('image') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group">
                                <div class="row m-form__group">
                                    <label class="col-sm-4 col-lg-2 col-form-label">Mô tả</label>
                                    <div class="col-sm-8 col-lg-10">
                                        <textarea id="content{{\App\Library\Helpers::rand_string(5)}}"
                                                  class="form-control ckeditor_post"
                                                  name="description">{{ old('description', isset($data) ? $data->description : null) }}</textarea>
                                        @if($errors->has('description'))
                                            <div class="form-control-feedback">{{ $errors->first('description') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row">
                                <label class="col-sm-4 col-lg-2 col-form-label">Nội dung</label>
                                <div class="col-sm-8 col-lg-10">
                                    <textarea id="content{{\App\Library\Helpers::rand_string(5)}}"
                                              class="form-control ckeditor_post"
                                              name="content">{{ old('content', isset($data) ? $data->content : null) }}</textarea>
                                    @if($errors->has('content'))
                                        <div class="form-control-feedback">{{ $errors->first('content') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="padding: 0;" class="m-content" id="price_wrapper">
                <div class="m-portlet m-form m-form--label-align-right">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">Cài đặt bảng giá</h3>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div id="price_container" class="form-group m-form__group row">
                            <div class="col-sm-6">
                                <div class="row m-form__group">
                                    <label class="col-md-4 col-form-label">Máy chủ</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <select class="form-control m-input m-input--air" name="server_mode">
                                                <option value="0" {{old('server_mode', isset($data) ? \App\Library\Helpers::DecodeJson('server_mode',$data->params) : null)==0?"selected":"" }}>Không dùng</option>
                                                <option value="1" {{old('server_mode', isset($data) ? \App\Library\Helpers::DecodeJson('server_mode',$data->params) : null)==1?"selected":"" }}>Dùng</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div  style="display:{{old('server_price', isset($data) ? \App\Library\Helpers::DecodeJson('server_mode',$data->params) : null)==1?"block":"none" }}"  class="col-sm-6 server-price">
                                <div class="row m-form__group">
                                    <label class="col-md-4 col-form-label">Tính giá</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <select class="form-control  m-input m-input--air" name="server_price">
                                                <option value="0" {{old('server_price', isset($data) ? \App\Library\Helpers::DecodeJson('server_price',$data->params) : 0)==0?"selected":"" }}>Giống nhau</option>
                                                <option value="1" {{old('server_price', isset($data) ? \App\Library\Helpers::DecodeJson('server_price',$data->params) : null)==1?"selected":"" }}>Khác nhau</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="display:{{old('server_price', isset($data) ? \App\Library\Helpers::DecodeJson('server_mode',$data->params) : null)==1?"block":"none" }}"   class="col-sm-12 server-container options-container">
                                <label>Danh sách máy chủ (Hỗ trợ tối đa 50 máy chủ)</label>
                                @if(isset($data))
                                    @php
                                        $server_id =  \App\Library\Helpers::DecodeJson('server_id',$data->params);
                                        $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                    @endphp

                                    @if(!empty($server_id) && count($server_id)>0)
                                        @for ($i = 0; $i < count($server_id); $i++)
                                            @if($server_data[$i]!="" && $server_data[$i]!='null')

                                                <div class="data-item">
                                                    <div class="input-group">
                                                        <input value="{{$server_id[$i]}}" style="display:none;" type="text" name="server_id[]">
                                                        <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
                                                        <input value="{{$server_data[$i]}}" type="text" class="send-data form-control m-input m-input--air" name="server_data[]" placeholder="Tên máy chủ">
                                                        <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                        <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endfor
                                    @else
                                        <div class="data-item" >
                                            <div class="input-group">
                                                <input value="0" style="display:none;" type="text" name="server_id[]">
                                                <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
                                                <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data[]" placeholder="Tên máy chủ">
                                                <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="data-item" >
                                        <div class="input-group">
                                            <input value="0" style="display:none;" type="text" name="server_id[]">
                                            <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
                                            <input value="" type="text" class="send-data form-control m-input m-input--air" name="server_data[]" placeholder="Tên máy chủ">
                                            <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                            <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div id="price_container" class="form-group m-form__group row cat-item">
                            <div class="col-sm-6">
                                <div class="row m-form__group">
                                    <label class="col-md-4 col-form-label">Tên bảng giá</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input name="filter_name" value="{{old('filter_name', isset($data) ? \App\Library\Helpers::DecodeJson('filter_name',$data->params) : null)}}" type="text" class="form-control m-input m-input--air" placeholder="Tên thuộc tính giá" required="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row m-form__group">
                                    <label class="col-md-4 col-form-label">Loại</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <select class="form-control m-input m-input--air" name="filter_type">
                                                <option value="3" {{old('filter_type', isset($data) ? \App\Library\Helpers::DecodeJson('filter_type',$data->params) : 3)==3?"selected":"" }}>Dạng tiền tệ</option>
                                                <option value="4" {{old('filter_type', isset($data) ? \App\Library\Helpers::DecodeJson('filter_type',$data->params) : null)==4?"selected":"" }}>Dạng chọn một</option>
                                                <option value="5" {{old('filter_type', isset($data) ? \App\Library\Helpers::DecodeJson('filter_type',$data->params) : null)==5?"selected":"" }}>Dạng chọn nhiều</option>
                                                <option value="6" {{old('filter_type', isset($data) ? \App\Library\Helpers::DecodeJson('filter_type',$data->params) : null)==6?"selected":"" }}>Dạng chọn từ A->B</option>
                                                <option value="7" {{old('filter_type', isset($data) ? \App\Library\Helpers::DecodeJson('filter_type',$data->params) : null)==7?"selected":"" }}>Dạng nhập tiền để thanh toán</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group m-form__group pack-settings">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <label class="input-group-addon addon">
                                            Số tiền thấp nhất
                                        </label>
                                        <input value="{{old('input_pack_min', isset($data) ? \App\Library\Helpers::DecodeJson('input_pack_min',$data->params) : null)}}" type="text" class="form-control m-input m-input--air" name="input_pack_min" placeholder="Số tiền">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <label class="input-group-addon addon">
                                            Số tiền cao nhất
                                        </label>
                                        <input value="{{old('input_pack_max', isset($data) ? \App\Library\Helpers::DecodeJson('input_pack_max',$data->params) : null)}}" type="text" class="form-control m-input m-input--air" name="input_pack_max" placeholder="Số tiền">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <label class="input-group-addon addon">
                                            Tỉ lệ quy đổi trên 1k
                                        </label>
                                        <input value="{{old('input_pack_rate', isset($data) ? \App\Library\Helpers::DecodeJson('input_pack_rate',$data->params) : null)}}" type="text" class="form-control m-input m-input--air" name="input_pack_rate" placeholder="Tỉ lệ">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="field_filter_container" class="form-group m-form__group">
                            <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">
                                <thead>
                                <tr>
                                    <th style="width: 92px;" class="range muilti pack"></th>
                                    <th class="all">Máy chủ</th>
                                    <th class="range muilti single">Thuộc tính</th>
                                    <th class="pack">Gói ưu đãi</th>
                                    <th class="all">Giá</th>
                                    <th class="pack">Hệ số</th>
                                    <th class="pack">= (Tiền x Hệ số)</th>
                                    <th class="all">Thời hạn hoàn thành</th>
                                    <th class="all">Phạt quá hạn</th>
                                    <th class="all">Thời hạn thưởng</th>
                                    <th class="all">Tiền thưởng</th>
                                    <th class="range muilti pack">Xóa</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($data))
                                    @php
                                        $name =  \App\Library\Helpers::DecodeJson('name',$data->params);
                                    @endphp

                                    @for ($i = 0; $i < count($name); $i++)
                                        @if($name[$i]!="" && $name[$i]!=null)
                                        <tr>
                                            <th class="range muilti pack">
                                                        <span>
                                                            <a class="btnUp m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                                <i class="la la-arrow-up"></i>
                                                            </a>
                                                            <a class="btnDown m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                                <i class="la la-arrow-down"></i>
                                                            </a>
                                                        </span>
                                            </th>

                                            <th class="all"></th>

                                            <th><input value="7" type="text" class="form-control m-input m-input--air" name="id[]"></th>


                                            <th class="all"><input value="{{$name[$i]}}" type="text" class="form-control m-input m-input--air" name="name[]" placeholder="Tên thuộc tính"></th>

                                            <th class="all">
                                                @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")

                                                    @php
                                                        $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                    @endphp

                                                    @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                        @if($server_data[$p]!=null)
                                                            @php
                                                                $price =  \App\Library\Helpers::DecodeJson('price'.$p,$data->params);
                                                            @endphp
                                                            <input id="sv{{$p}}" value="{{$price[$i]}}" type="text" class="form-control m-input m-input--air pack-input pack-price" name="price{{$p}}[]" placeholder="Giá">
                                                        @endif

                                                    @endfor

                                                @else

                                                    @php
                                                        $price =  \App\Library\Helpers::DecodeJson('price',$data->params);
                                                    @endphp
                                                    <input  value="{{$price[$i]}}" type="text" class="form-control m-input m-input--air pack-input pack-price" name="price[]" placeholder="Giá">

                                                @endif


                                            </th>

                                            <th class="pack">
                                                @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")
                                                    @php
                                                        $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                    @endphp

                                                    @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                        @if($server_data[$p]!=null)
                                                            @php
                                                                $discount =  \App\Library\Helpers::DecodeJson('discount'.$p,$data->params);
                                                            @endphp

                                                            <input id="sv{{$p}}" value="{{$discount[$i]}}" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount{{$p}}[]" placeholder="Hệ số">
                                                        @endif
                                                    @endfor

                                                @else

                                                    @php
                                                        $discount =  \App\Library\Helpers::DecodeJson('discount',$data->params);
                                                    @endphp
                                                    <input  value="{{$discount[$i]}}" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số">

                                                @endif


                                            </th>

                                            <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total" name="total[]" placeholder="Tiền"></th>

                                            <th class="all">
                                                @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")
                                                    @php
                                                        $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                    @endphp

                                                    @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                        @if($server_data[$p]!=null)
                                                            @php
                                                                $day =  \App\Library\Helpers::DecodeJson('day'.$p,$data->params);
                                                            @endphp

                                                            <input id="sv{{$p}}" value="{{$day[$i]}}" type="text" class="form-control m-input m-input--air" name="day{{$p}}[]" placeholder="Phút">
                                                        @endif
                                                    @endfor

                                                @else
                                                    @php
                                                        $day =  \App\Library\Helpers::DecodeJson('day',$data->params);
                                                    @endphp
                                                    <input  value="{{$day[$i]}}" type="text" class="form-control m-input m-input--air" name="day[]" placeholder="Phút">
                                                @endif
                                            </th>

                                            <th class="all">
                                                @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")
                                                    @php
                                                        $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                    @endphp

                                                    @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                        @if($server_data[$p]!=null)
                                                            @php
                                                                $punish_price =  \App\Library\Helpers::DecodeJson('punish_price'.$p,$data->params);
                                                            @endphp

                                                            <input id="sv{{$p}}" value="{{$punish_price[$i]}}" type="text" class="form-control m-input m-input--air" name="punish_price{{$p}}[]" placeholder="Tiền">
                                                        @endif
                                                    @endfor

                                                @else
                                                    @php
                                                        $punish_price =  \App\Library\Helpers::DecodeJson('punish_price',$data->params);
                                                    @endphp
                                                    <input  value="{{$punish_price[$i]}}" type="text" class="form-control m-input m-input--air " name="punish_price[]" placeholder="Tiền">
                                                @endif
                                            </th>

                                            <th class="all">
                                                @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")
                                                    @php
                                                        $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                    @endphp

                                                    @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                        @if($server_data[$p]!=null)
                                                            @php
                                                                $praise_day =  \App\Library\Helpers::DecodeJson('praise_day'.$p,$data->params);
                                                            @endphp

                                                            <input id="sv{{$p}}" value="{{$praise_day[$i]}}" type="text" class="form-control m-input m-input--air" name="praise_day{{$p}}[]" placeholder="Phút">
                                                        @endif
                                                    @endfor

                                                @else
                                                    @php
                                                        $praise_day =  \App\Library\Helpers::DecodeJson('praise_day',$data->params);
                                                    @endphp
                                                    <input  value="{{$praise_day[$i]}}" type="text" class="form-control m-input m-input--air " name="praise_day[]" placeholder="Phút">
                                                @endif
                                            </th>

                                            <th class="all">

                                                @if(\App\Library\Helpers::DecodeJson('server_mode',$data->params)=="1" && \App\Library\Helpers::DecodeJson('server_price',$data->params)=="1")
                                                    @php
                                                        $server_data =  \App\Library\Helpers::DecodeJson('server_data',$data->params);
                                                    @endphp

                                                    @for ($p = 0; $p < (!empty($server_data)?count($server_data):0); $p++)
                                                        @if($server_data[$p]!=null)
                                                            @php
                                                                $praise_price =  \App\Library\Helpers::DecodeJson('praise_price'.$p,$data->params);
                                                            @endphp

                                                            <input id="sv{{$p}}" value="{{$praise_price[$i]}}" type="text" class="form-control m-input m-input--air " name="praise_price{{$p}}[]" placeholder="Phút">
                                                        @endif
                                                    @endfor
                                                @else
                                                    @php
                                                        $praise_day =  \App\Library\Helpers::DecodeJson('praise_day',$data->params);
                                                    @endphp
                                                    <input  value="{{$praise_day[$i]}}" type="text" class="form-control m-input m-input--air " name="praise_day[]" placeholder="Tiền">
                                                @endif
                                            </th>

                                            <th class="range muilti pack">
                                                <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                    <i class="la la-trash-o"></i>
                                                </a>
                                            </th>
                                        </tr>
                                        @endif
                                    @endfor
                                @else

                                    <tr>
                                        <th class="range muilti pack">
                                    <span>
                                        <a class="btnUp m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                            <i class="la la-arrow-up"></i>
                                        </a>
                                        <a class="btnDown m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                            <i class="la la-arrow-down"></i>
                                        </a>
                                    </span>
                                        </th>
                                        <th class="all"></th>
                                        <th><input type="text" class="form-control m-input m-input--air" name="id[]"></th>
                                        <th class="all"><input type="text" class="form-control m-input m-input--air" name="name[]" placeholder="Tên thuộc tính"></th>
                                        <th class="all"><input type="text" class="form-control m-input m-input--air pack-input pack-price" name="price[]" placeholder="Giá"></th>
                                        <th class="pack"><input value="1" type="text" class="form-control m-input m-input--air pack-input pack-discount" name="discount[]" placeholder="Hệ số"></th>
                                        <th class="pack"><input type="text" class="form-control m-input m-input--air pack-input pack-total" name="total[]" placeholder="Tiền"></th>
                                        <th class="all"><input type="text" class="form-control m-input m-input--air" name="day[]" placeholder="Phút"></th>
                                        <th class="all"><input type="text" class="form-control m-input m-input--air" name="punish_price[]" placeholder="Tiền"></th>
                                        <th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_day[]" placeholder="Phút"></th>
                                        <th class="all"><input type="text" class="form-control m-input m-input--air" name="praise_price[]" placeholder="Tiền"></th>

                                        <th class="range muilti pack">
                                            <a class="btnRemove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Lên">
                                                <i class="la la-trash-o"></i>
                                            </a>
                                        </th>
                                    </tr>


                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div style="padding: 0;" class="m-content">
                <div class="m-portlet m-form">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">Thuộc tính xác nhận (Yêu cầu khách hàng nhập)</h3>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div id="field_send_container" class="form-group m-form__group">
                            @if(isset($data))
                                @php
                                    $send_name =  \App\Library\Helpers::DecodeJson('send_name',$data->params);
                                    $send_type =  \App\Library\Helpers::DecodeJson('send_type',$data->params);

                                @endphp
                                @if(!empty($send_name))
                                    @for ($i = 0; $i < count($send_name); $i++)

                                        @if( $send_name[$i]!=null)
                                            <div class="row cat-item">
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
                                                        <input value="{{$send_name[$i]}}" type="text" class="form-control m-input m-input--air" name="send_name[]" placeholder="Tên thuộc tính">
                                                        <select class="form-control m-input m-input--air" name="send_type[]">

                                                            <option value="1" {{$send_type[$i]=="1"?"selected":""}}>Dạng chữ</option>
                                                            <option value="2" {{$send_type[$i]=="2"?"selected":""}}>Dạng số</option>
                                                            <option value="3" {{$send_type[$i]=="3"?"selected":""}}>Dạng tiền tệ</option>
                                                            <option value="4" {{$send_type[$i]=="4"?"selected":""}}>Dạng hình ảnh</option>
                                                            <option value="5" {{$send_type[$i]=="5"?"selected":""}}>Dạng mật khẩu</option>
                                                            <option value="6" {{$send_type[$i]=="6"?"selected":""}}>Dạng chọn</option>
                                                        </select>
                                                        <span class="input-group-addon btnUp"><i class="la la-arrow-up"></i></span>
                                                        <span class="input-group-addon btnDown"><i class="la la-arrow-down"></i></span>
                                                    </div>
                                                </div>





                                                @if($send_type[$i]=="6")

                                                    @php
                                                        $send_data =  \App\Library\Helpers::DecodeJson('send_data'.$i,$data->params);
                                                    @endphp
                                                    @if(!empty($send_data))
                                                        <div class="col-sm-12 cat-container options-container" style="">
                                                            <label>Cài đặt lựa chọn</label>
                                                            @for ($sd = 0; $sd < count($send_data); $sd++)
                                                                @if( $send_data[$sd]!=null)
                                                                    <div class="data-item">
                                                                        <div class="input-group">
                                                                            <input  value="{{$sd}}" style="display:none" type="text" class="send-id" name="send_id{{$i}}[]">
                                                                            <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
                                                                            <input type="text" value="{{$send_data[$sd]}}" class="send-data form-control m-input m-input--air" name="send_data{{$i}}[]" placeholder="Tên lựa chọn">
                                                                            <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                                            <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                                        </div>
                                                                    </div>
                                                                @endif


                                                            @endfor
                                                        </div>

                                                    @else

                                                        <div class="col-sm-12 cat-container options-container" style="">
                                                            <label>Cài đặt lựa chọn</label>
                                                            <div class="data-item">
                                                                <div class="input-group">
                                                                    <input style="display:none;" type="text" class="send-id" name="send_id{{$i}}[]">
                                                                    <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
                                                                    <input type="text" class="send-data form-control m-input m-input--air" name="send_data{{$i}}[]" placeholder="Tên lựa chọn">
                                                                    <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                                    <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                @else

                                                    <div class="col-sm-12 cat-container options-container" style="display:none">
                                                        <label>Cài đặt lựa chọn</label>
                                                        <div class="data-item">
                                                            <div class="input-group">
                                                                <input style="display:none;" type="text" class="send-id" name="send_id{{$i}}[]">
                                                                <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
                                                                <input type="text" class="send-data form-control m-input m-input--air" name="send_data{{$i}}[]" placeholder="Tên lựa chọn">
                                                                <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                                <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>


                                                @endif

                                            </div>
                                        @endif
                                    @endfor
                                @else
                                    <div class="row cat-item">
                                        <div class="col-sm-12">
                                            <div class="input-group">
                                                <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
                                                <input type="text" class="form-control m-input m-input--air" name="send_name[]" placeholder="Tên thuộc tính">
                                                <select class="form-control m-input m-input--air" name="send_type[]">
                                                    <option value="1">Dạng chữ</option>
                                                    <option value="2">Dạng số</option>
                                                    <option value="3">Dạng tiền tệ</option>
                                                    <option value="4">Dạng hình ảnh</option>
                                                    <option value="5">Dạng mật khẩu</option>
                                                    <option value="6">Dạng chọn</option>
                                                </select>
                                                <span class="input-group-addon btnUp"><i class="la la-arrow-up"></i></span>
                                                <span class="input-group-addon btnDown"><i class="la la-arrow-down"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 cat-container options-container" style="display: none;">
                                            <label>Cài đặt lựa chọn</label>
                                            <div class="data-item">
                                                <div class="input-group">
                                                    <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
                                                    <input type="text" class="send-data form-control m-input m-input--air" name="send_data0[]" placeholder="Tên lựa chọn">
                                                    <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                    <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="row cat-item">
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <span class="input-group-addon btnRemove"><i class="la la-trash"></i></span>
                                            <input type="text" class="form-control m-input m-input--air" name="send_name[]" placeholder="Tên thuộc tính">
                                            <select class="form-control m-input m-input--air" name="send_type[]">
                                                <option value="1">Dạng chữ</option>
                                                <option value="2">Dạng số</option>
                                                <option value="3">Dạng tiền tệ</option>
                                                <option value="4">Dạng hình ảnh</option>
                                                <option value="5">Dạng mật khẩu</option>
                                                <option value="6">Dạng chọn</option>
                                            </select>
                                            <span class="input-group-addon btnUp"><i class="la la-arrow-up"></i></span>
                                            <span class="input-group-addon btnDown"><i class="la la-arrow-down"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 cat-container options-container" style="display: none;">
                                        <label>Cài đặt lựa chọn</label>
                                        <div class="data-item">
                                            <div class="input-group">
                                                <span class="input-group-addon btnRemoveOpt"><i class="la la-trash"></i></span>
                                                <input type="text" class="send-data form-control m-input m-input--air" name="send_data0[]" placeholder="Tên lựa chọn">
                                                <span class="input-group-addon btnUpOpt"><i class="la la-arrow-up"></i></span>
                                                <span class="input-group-addon btnDownOpt"><i class="la la-arrow-down"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif


                        </div>
                        <div class="form-group m-form__group">
                            <div class="text-right">
                                <button id="btnAddSend" type="button" class="btn btn-primary m-btn m-btn--air">
                                    + Thêm thuộc tính
                                </button>
                            </div>
                            <span style="margin-top:5px;" class="m-form__help">Cho phép cấu hình tối đa 5 thuộc tính động</span>
                        </div>
                        <div class="form-group m-form__group">
                            <div class="row m-form__group">
                                <label class="col-sm-4 col-lg-2 col-form-label">Ghi chú</label>
                                <div class="col-sm-8 col-lg-10">
                                    <textarea style="min-height: 80px;" placeholder="Nội dung" name="input_send_desc" class="form-control m-input m-input--air">Khi mua ngọc tại web các bạn lưu ý để trong nick 1 ngọc và đứng tại siêu thị để nhận ngọc nhanh nhé</textarea>
                                    <span style="margin-top:5px;" class="m-form__help">Nội dung ghi chú sẽ hiện trên trang thanh toán ở ô nhập thông tin của khách hàng</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="padding: 0;" class="m-content">
                <div class="m-portlet m-form">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">Bảo mật</h3>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="row m-form__group">
                            <label class="col-sm-2 col-form-label">Mã bảo vệ</label>
                            <div class="col-sm-10 col-lg-4">
                                <div class="input-group">
                                    <input  type="text" class="form-control m-input m-input--air" id="captcha" name="captcha" placeholder="Mã bảo vệ" maxlength="3" autocomplete="off">
                                    <span class="input-group-addon" style="padding: 0px;width: 70px;border: 0px solid rgba(255, 255, 255, 0.3);">
                <img src="/captcha.html" id="imgcaptcha" onclick="document.getElementById('imgcaptcha').src = '/captcha.html'; document.getElementById('captcha').focus();">
            </span>
                                </div>

                                <span style="margin-top:5px;" class="m-form__help">Có thể Click hoặc bấm vào hình để đổi mã số khác</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot m-portlet__foot--fit">
                        <div class="m-form__actions m-form__actions text-right">
                            <a href="/admin/service.html" class="btn btn-secondary m-btn">
                                Hủy bỏ
                            </a>
                            <button type="submit" class="btn btn-primary m-btn m-btn--air">
                                Lưu
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <style>
            .cat-item+.cat-item{
                margin-top: 10px;
            }
            .cat-item.hide-data .cat-price{
                display: block !important;
            }
            .cat-item.hide-data #field_filter{
                display: none;
            }
            .data-item+.data-item{
                margin-top: 5px;
            }
            .lock{
                pointer-events: none;
            }

            .options-container{
                border: 1px solid #f3f2f2;
                border-radius: 3px;
                padding: 10px 20px 20px 20px;
                margin: 10px;
                width: calc(100% - 20px);
                background-color: #fbfbfb;
                float: left;
                display: block;
            }

            th{
                display: none;
            }
            th input+input{
                margin-top: 5px;
            }
            th.all{
                display: table-cell;
            }
            .pack-settings{
                display: none;
            }
            .pack-mode .pack-settings{
                display: block;
            }
            .multi-mode th.muilti{
                display: table-cell;
            }
            .single-mode th.single{
                display: table-cell;
            }

            .range-mode th.range{
                display: table-cell;
            }
            .pack-mode th.pack{
                display: table-cell;
            }

            .multi-mode .range{
                display: none;
            }
            .range-mode tbody tr:nth-child(1){
                pointer-events: none;
            }
            .range-mode tbody tr:nth-child(1) input{
                background-color: #eae9e9;
            }
            .range-mode tbody tr:nth-child(1) th:nth-child(1),.range-mode tbody tr:nth-child(1) th:nth-child(4),.range-mode tbody tr:nth-child(1) th:nth-child(12){
                pointer-events: initial;
            }
            .range-mode tbody tr:nth-child(1) th:nth-child(4) input{
                background-color: white;
            }
            .single-mode .single{
                display: none;
            }
            .single-mode tbody tr:nth-child(n+2){
                display: none;
            }

            tbody th:nth-child(2),thead th:nth-child(2){
                display: none;
            }

            tbody th:nth-child(2), tbody th:nth-child(7){
                pointer-events: none;
            }

            tbody th:nth-child(2) input, tbody th:nth-child(7) input{
                background-color: #eae9e9;
            }

            .server-all tbody th:nth-child(2),.server-all thead th:nth-child(2){
                display: table-cell;
            }
        </style>

        <script src="https://dichvu.me/assets/js/jquery.minicolors.min.js"></script>
        <link rel="stylesheet" href="https://dichvu.me/assets/css/jquery.minicolors.css" />
        <script>
            $('#btnAddSend').click(function(){
                var fcount = $('#field_send_container>.row').length;
            if (fcount >= 5)
                return;
            var a = $('#field_send_container>.row').first().clone();
            a.addClass('hide-data');
            $('input[type="text"]', a).val('');
            $('[name="send_type[]"]', a).val(1);
            a.appendTo($('#field_send_container'));
            $('input[type="text"]', a).focus();
            SendEvents(a);
            $('select[name="send_type[]"]', a).change();
            });
            $('[name="input_img"]').change(function () {
                $('#img_preview').hide();
            });
            $('select[name="filter_type"]').change(function () {
                $('#price_wrapper').removeClass('single-mode multi-mode range-mode pack-mode');
                if ([4, 5].indexOf(parseInt($(this).val())) != -1) {
                    $('#price_wrapper').addClass('multi-mode');
                } else if ([3].indexOf(parseInt($(this).val())) != -1) {
                    $('#price_wrapper').addClass('single-mode');
                } else if ([7].indexOf(parseInt($(this).val())) != -1) {
                    $('#price_wrapper').addClass('pack-mode');
                } else {
                    $('#price_wrapper').addClass('range-mode');
                }
            });
            $('select[name="filter_type"]').change();
            function UpdatePack() {
                if ($('.pack-discount:visible').length != 0) {
                    $('table tbody tr').each((idx, elm) => {
                        $('.pack-price').each((inpi, inpe) => {
                        var id = $(inpe).attr('id');
                    if (typeof id == 'undefined') {
                        var price = parseInt($('.pack-price', elm).val());

                        var discount = parseFloat($('.pack-discount', elm).val());
                        var rate = parseInt($('[name="input_pack_rate"]').val());
                        $('.pack-total', elm).val(parseInt((price / 1000) * rate * discount));
                    } else {

                        var price = parseInt($('#' + id + '.pack-price', elm).val());
                        console.log(price);
                        var discount = parseFloat($('#' + id + '.pack-discount', elm).val());
                        var rate = parseInt($('[name="input_pack_rate"]').val());
                        $('#' + id + '.pack-total', elm).val(parseInt((price / 1000) * rate * discount));
                    }

                });
                });
                }
            }
            function UpdatePrice() {
                var server_mode = parseInt($('[name="server_mode"]').val());
                var server_price = parseInt($('[name="server_price"]').val());
                if (server_mode == 1 && server_price == 1) {
                    $('#price_wrapper').addClass('server-all');
                    $('.server-container .data-item').each((idx, elm) => {
                        var id = $('[name="server_id[]"]', elm).val();
                    var name = $.trim($('[name="server_data[]"]', elm).val());
                    if (name != '') {
                        $('table tbody tr').each((tri, tre) => {
                            $('th', tre).each((thi, the) => {
                            if (thi == 0 || thi == 2 || thi == 3 || thi == 11) {
                            return;
                        }
                        if ($('input', the).length == 1) {
                            if (!$('input', the).attr('id')) {
                                $('input', the).attr('id', 'sv' + id);
                            }
                        }
                        var input = $('input#sv' + id, the);
                        if (input.length == 0) {
                            input = $('<input class="form-control m-input m-input--air" type="text" id="sv' + id + '" />');
                            TableInpEvents(input);
                            input.appendTo(the);
                        }
                        switch (thi) {
                            case 1:
                                input.val(name).addClass('server-name');
                                break;
                            case 4:
                                input.attr('name', 'price' + id + '[]');
                                input.addClass('pack-input pack-price');
                                break;
                            case 5:
                                input.attr('name', 'discount' + id + '[]');
                                input.addClass('pack-input pack-discount');
                                break;
                            case 6:
                                input.attr('name', 'total' + id + '[]');
                                input.addClass('pack-input pack-total');
                                break;
                            case 7:
                                input.attr('name', 'day' + id + '[]');
                                break;
                            case 8:
                                input.attr('name', 'punish_price' + id + '[]');
                                break;
                            case 9:
                                input.attr('name', 'praise_day' + id + '[]');
                                break;
                            case 10:
                                input.attr('name', 'praise_price' + id + '[]');
                                break;
                        }
                    });
                    });
                    }
                });
                } else {
                    $('#price_wrapper').removeClass('server-all');
                    $('table tbody tr').each((tri, tre) => {
                        $('th', tre).each((thi, the) => {
                        $('input', the).each((inpi, inpe) => {
                        if (inpi != 0) {
                        $(inpe).remove();
                    }
                });
                    input = $('input', the);
                    if (input.length == 0)
                        return;
                    switch (thi) {
                        case 1:
                            input.val(name).addClass('server-name');
                            break;
                        case 4:
                            input.attr('name', 'price[]');
                            input.addClass('pack-input pack-price');
                            break;
                        case 5:
                            input.attr('name', 'discount[]');
                            input.addClass('pack-input pack-discount');
                            break;
                        case 6:
                            input.attr('name', 'total[]');
                            input.addClass('pack-input pack-total');
                            break;
                        case 7:
                            input.attr('name', 'day[]');
                            break;
                        case 8:
                            input.attr('name', 'punish_price[]');
                            break;
                        case 9:
                            input.attr('name', 'praise_day[]');
                            break;
                        case 10:
                            input.attr('name', 'praise_price[]');
                            break;
                    }
                });
                });
                }
            }
            UpdatePrice();
            $('[name="input_pack_rate"]').keyup(function () {
                UpdatePack();
            });
            UpdatePack();
            function TableInpEvents(selector) {
                $(selector).focus(function () {
                    var th = $(this).closest('tr');
                    var index = th.index();
                    var count = $('tbody tr').length;
                    if (th.is(':last-child')) {
                        var a = $(this).closest('tr').first().clone(true);
                        $('input', a).not('.server-name').val('');
                        a.appendTo($('tbody'));
                        //TableEvents(a);
                    }
                }).blur(function () {
                    var th = $(this).closest('tr');
                    if ($(this).hasClass('pack-input')) {
                        var id = $(this).attr('id');

                        if (typeof id == 'undefined') {
                            var price = parseInt($('.pack-price', th).val());
                            var discount = parseFloat($('.pack-discount', th).val());
                            var rate = parseInt($('[name="input_pack_rate"]').val());
                            $('.pack-total', th).val(parseInt((price / 1000) * rate * discount));
                        } else {

                            var price = parseInt($('#' + id + '.pack-price', th).val());

                            var discount = parseFloat($('#' + id + '.pack-discount', th).val());

                            var rate = parseInt($('[name="input_pack_rate"]').val());

                            $('#' + id + '.pack-total', th).val(parseInt((price / 1000) * rate * discount));
                        }

                    }
                });
            }
            function TableEvents(selector) {
                TableInpEvents($('input', selector));
                $('.btnUp', selector).click(function () {
                    var item = $(this).closest('tr');
                    var prev = item.prev();
                    if (prev.length != 0) {
                        item.insertBefore(prev);
                    }
                });
                $('.btnDown', selector).click(function () {
                    var item = $(this).closest('tr');
                    var next = item.next();
                    if (next.length != 0) {
                        item.insertAfter(next);
                    }
                });
                $('.btnRemove', selector).click(function () {
                    var fcount = $(this).closest('tbody').find('tr').length;
                    if (fcount == 1)
                        return;
                    $(this).closest('tr').remove();
                    Update();
                });
            }
            TableEvents('table');
            function events() {
                $('.server-container input').focus(function () {
                    var container = $(this).closest('.server-container');
                    var index = $(this).closest('.data-item').index();
                    var count = container.find('.data-item').length;
                    if (index == count) {
                        var a = $('.data-item', container).first().clone(true);
                        $('input[type="text"]', a).val('');
                        var newid = 0;
                        while ($('.data-item [name="server_id[]"][value="' + newid + '"]', container).length != 0) {
                            newid++;
                        }
                        $('[name="server_id[]"]', a).attr('value', newid).val(newid);
                        a.appendTo(container);
                    }
                    Update();
                }).blur(function () {
                    UpdatePrice();
                });
                $('[name="filter"]').change(function () {
                    UpdatePrice();
                });
                $('[name="server_mode"]').change(function () {
                    if ($(this).val() == '1') {
                        $('.server-container,.server-price').show();
                    } else {
                        $('.server-container,.server-price').hide();
                    }
                    UpdatePrice();
                });
                $('[name="server_price"]').change(function () {
                    UpdatePrice();
                });
                $('.server-container .btnRemoveOpt').click(function () {
                    var fcount = $('.server-container .data-item').length;
                    if (fcount == 1)
                        return;
                    var id = $(this).closest('.data-item').find('[name="server_id[]"]').val();
                    $('table tbody tr').each((tri, tre) => {
                        $('input#sv' + id, tre).remove();
                });
                    $(this).closest('.data-item').remove();
                });
                $('.server-container .btnUpOpt').click(function () {
                    var item = $(this).closest('.data-item');
                    var prev = item.prev();
                    if (prev.length != 0 && prev.hasClass('data-item')) {
                        item.insertBefore(prev);
                    }
                });
                $('.server-container .btnDownOpt').click(function () {
                    var item = $(this).closest('.data-item');
                    var next = item.next();
                    if (next.length != 0) {
                        item.insertAfter(next);
                    }
                });
            }
            events();
            function SendEvents(selector) {
                $('input', selector).focus(function () {
                    var container = $(this).closest('.cat-container');
                    var index = $(this).closest('.data-item').index();
                    var count = container.find('.data-item').length;
                    if (index == count) {
                        var a = $('.data-item', container).first().clone();
                        $('input[type="text"]', a).val('');
                        a.appendTo(container);
                        SendEvents(a);
                    }
                    Update();
                });
                $('.btnRemoveOpt', selector).click(function () {
                    var container = $(this).closest('.cat-container');
                    var count = container.find('.data-item').length;
                    if (count == 1)
                        return;
                    $(this).closest('.data-item').remove();
                });
                $('.btnUpOpt', selector).click(function () {
                    var item = $(this).closest('.data-item');
                    var prev = item.prev();
                    if (prev.length != 0 && prev.hasClass('data-item')) {
                        item.insertBefore(prev);
                    }
                });
                $('.btnDownOpt', selector).click(function () {
                    var item = $(this).closest('.data-item');
                    var next = item.next();
                    if (next.length != 0) {
                        item.insertAfter(next);
                    }
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
                    Update();
                });
                $('.btnUp', selector).click(function () {
                    var item = $(this).closest('.cat-item');
                    var prev = item.prev();
                    if (prev.length != 0) {
                        item.insertBefore(prev);
                    }
                });
                $('.btnDown', selector).click(function () {
                    var item = $(this).closest('.cat-item');
                    var next = item.next();
                    if (next.length != 0) {
                        item.insertAfter(next);
                    }
                });
            }
            SendEvents('#field_send_container');
            function Update() {
                $('.cat-container').each(function (idx, elm) {
                    $('.data-item input.send-data', elm).attr('name', 'send_data' + idx + '[]');
                    $('.data-item input.send-id', elm).attr('name', 'send_id' + idx + '[]');
                });
            }
            Update();
            $('.summernote').summernote({
                height: 200,
            });

            $('[name="input_notify"]').summernote({
                height: 100,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']]
                ]
            });
        </script>                        </div>
@endsection

@section('js_cs_bottom')

    <script>
        $(document).ready(function () {

            //file input upload file
            $('.fileinput').fileinput();


//			$.get('/admin/game-item/load-attribute?category_id='+$("#group_id").val(), function(data) {
//						$('#attribute_loader').html(data);
//					})
//					.fail(function() {
//						alert( "Không thể load dữ liệu danh mục game" );
//					})
//


            function updateQueryStringParameter(uri, key, value) {
                var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                var separator = uri.indexOf('?') !== -1 ? "&" : "?";
                if (uri.match(re)) {
                    return uri.replace(re, '$1' + key + "=" + value + '$2');
                }
                else {
                    return uri + separator + key + "=" + value;
                }
            }


        });
    </script>
    <script>
        $(document).ready(function () {

            $('.price').mask('000,000,000,000,000', {reverse: true});
            //file input upload file
            $('.fileinput').fileinput();
            $(".attribute-box input[type='checkbox']").change(function () {

                //click children
                $(this).closest('li').find("input[type='checkbox']").prop('checked', this.checked);
                var is_checked = $(this).is(':checked');

            });


        });


        jQuery(document).ready(function ($) {
            for (name in CKEDITOR.instances) {
                CKEDITOR.instances[name].destroy(true);
            }
            $('.ckeditor_post').each(function () {
                CKEDITOR.replace($(this).attr('id'));
            });
        })


    </script>

@endsection