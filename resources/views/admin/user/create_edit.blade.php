{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.user.index')}}"
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
        {{Form::open(array('route'=>array('admin.user.update',$data->id),'method'=>'PUT','id'=>'formMain','enctype'=>"multipart/form-data" , 'files' => true))}}
    @else
        {{Form::open(array('route'=>array('admin.user.store'),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
    @endif
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                        </h3>
                    </div>
                </div>

                <div class="card-body">

                    <input type="hidden" name="submit-close" id="submit-close">

                    <div class="form-group row">
                        {{--username--}}
                        <div class="col-12 col-md-6">
                            <label for="username">{{ __('Tên tài khoản')}} <span style="color: red">(*)</span></label>
                            <input type="text" name="username"
                                   value="{{ old('username', isset($data) ? $data->username : null) }}"
                                   placeholder="{{ __('Tên tài khoản') }}" {{isset($data)?"readonly":""}}  autocomplete="off"
                                   class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}">
                            @if ($errors->has('username'))
                                <span class="form-text text-danger">{{ $errors->first('username') }}</span>
                            @endif
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="title">{{ __('Phân loại:')}}</label>
                            {{Form::select('type_information',[''=>'-- Chưa chọn thông tin --']+config('module.user-qtv.type_information'),old('type_information', isset($data) ? $data->type_information : null),array('class'=>'form-control'))}}
                            @if($errors->has('type_information'))
                                <div class="form-control-feedback">{{ $errors->first('type_information') }}</div>
                            @endif
                            @if ($errors->has('type_information'))
                                <span class="form-text text-danger">{{ $errors->first('type_information') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        @if(isset($data))
                            @if (Auth::user()->can(['edit-password-user']))
                                {{-----password------}}
                                <div class="col-12 col-md-6">
                                    <label for="title">{{ __('Mật khẩu cấp 1')}} <span style="color: red">(*)</span></label>
                                    <input type="password" name="password" value=""
                                           placeholder="{{ __('Mật khẩu cấp 1') }}"
                                           class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}">
                                    @if ($errors->has('password'))
                                        <span class="form-text text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                                {{-----password2------}}
                                <div class="col-12 col-md-6">
                                    <label for="title">{{ __('Mật khẩu cấp 2')}} </label>
                                    <input type="password" name="password2" value=""
                                           placeholder="{{ __('Mật khẩu cấp 2') }}"
                                           class="form-control {{ $errors->has('password2') ? ' is-invalid' : '' }}">
                                    @if ($errors->has('password2'))
                                        <span class="form-text text-danger">{{ $errors->first('password2') }}</span>
                                    @endif
                                </div>
                            @endif
                        @else
                            {{-----password------}}
                            <div class="col-12 col-md-6">
                                <label for="title">{{ __('Mật khẩu cấp 1')}} <span style="color: red">(*)</span></label>
                                <input type="password" name="password" value=""
                                       placeholder="{{ __('Mật khẩu cấp 1') }}"
                                       class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}">
                                @if ($errors->has('password'))
                                    <span class="form-text text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>
                            {{-----password2------}}
                            <div class="col-12 col-md-6">
                                <label for="title">{{ __('Mật khẩu cấp 2')}} </label>
                                <input type="password" name="password2" value=""
                                       placeholder="{{ __('Mật khẩu cấp 2') }}"
                                       class="form-control {{ $errors->has('password2') ? ' is-invalid' : '' }}">
                                @if ($errors->has('password2'))
                                    <span class="form-text text-danger">{{ $errors->first('password2') }}</span>
                                @endif
                            </div>
                        @endif

                    </div>

                    <div class="form-group row">
                        {{-----phone------}}
                        <div class="col-12 col-md-6">
                            <label>{{ __('Số điện thoại')}}</label>
                            <input type="text" name="phone" value="{{ old('phone', isset($data) ? $data->phone : null) }}"
                                   placeholder="{{ __('Số điện thoại') }}"
                                   class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}">
                            @if ($errors->has('phone'))
                                <span class="form-text text-danger">{{ $errors->first('phone') }}</span>
                            @endif
                        </div>
                        {{-----email------}}
                        <div class="col-12 col-md-6">
                            <label for="title">{{ __('Email')}} <span style="color: red">(*)</span></label>
                            <input type="text" name="email" value="{{ old('email', isset($data) ? $data->email : null) }}"
                                   placeholder="{{ __('Email') }}"
                                   class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}">
                            @if ($errors->has('email'))
                                <span class="form-text text-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group m-form__group ">
                        <div class="row">
                            <div class="col-md-6">
                                {{--payment_limit--}}
                                <div class="{{ $errors->has('payment_limit')? 'has-danger':'' }}">
                                    <label>Hạn mức thanh toán</label>
                                    <input type="text" name="payment_limit"
                                           value="{{ old('payment_limit', isset($data) ? $data->payment_limit : config('module.service.payment_limit')) }}"
                                           autocomplete="false" class="form-control m-input  price"
                                           placeholder="Không đặt giá trị thì mặc định là :{{number_format(config('module.service.payment_limit'))}}">
                                    @if($errors->has('payment_limit'))
                                        <div
                                            class="form-control-feedback">{{ $errors->first('payment_limit') }}</div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    <hr/>

                    <div class="form-group m-form__group  area_api_nrogem"
                         style="display:block">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group m-form__group">
                                    <label class="text-danger">Partner key dịch vụ:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control m-input"
                                               value="{{ old('partner_key_service', isset($data) ? $data->partner_key_service : "") }}"
                                               id="txtPartnerKeyService" name="partner_key_service"
                                               placeholder="Partner Key Nrogem" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button type="button" id="btnGenKeySerivce"
                                                    class="btn m-btn btn-success m-btn--custom ">
                                                Tạo mới
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <hr/>

                    <div class="form-group m-form__group ">
                        <div class="row">

                            <div class="col-md-6">
                                {{--active_api_buy_nrogem--}}
                                <div class="{{ $errors->has('active_api_buy_nrogem')? 'has-danger':'' }}">
                                    <label class="text-info">Active API (Mua Nrogem):</label>
                                    {{Form::select('active_api_buy_nrogem',['0'=>'Không','1'=>'Có'],old('active_api_buy_nrogem', isset($data) ? $data->active_api_buy_nrogem : null),array('class'=>'form-control m-input  col-md-6','id'=>'active_api_buy_nrogem'))}}
                                    @if($errors->has('active_api_buy_nrogem'))
                                        <div
                                            class="form-control-feedback">{{ $errors->first('active_api_buy_nrogem') }}</div>
                                    @endif
                                </div>
                            </div>


                            <div class="col-md-6">
                                {{--is_agency_buygem--}}
                                <div class="{{ $errors->has('is_agency_buygem')? 'has-danger':'' }}">
                                    <label class="text-info">Đại lý mua ngọc (Nro Gem):</label>
                                    {{Form::select('is_agency_buygem',['0'=>'Không','1'=>'Có'],old('is_agency_buygem', isset($data) ? $data->is_agency_buygem : null),array('class'=>'form-control m-input col-md-6','id'=>'is_agency_buygem'))}}
                                    @if($errors->has('is_agency_buygem'))
                                        <div
                                            class="form-control-feedback">{{ $errors->first('is_agency_buygem') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>


                    <hr/>

                    <div class="form-group m-form__group ">
                        <div class="row">

                            <div class="col-md-6">
                                {{--active_api_buy_ninjaxu--}}
                                <div class="{{ $errors->has('active_api_buy_ninjaxu')? 'has-danger':'' }}">
                                    <label class="text-warning">Active API (Mua Ninja Xu):</label>
                                    {{Form::select('active_api_buy_ninjaxu',['0'=>'Không','1'=>'Có'],old('active_api_buy_ninjaxu', isset($data) ? $data->active_api_buy_ninjaxu : null),array('class'=>'form-control m-input  col-md-6','id'=>'active_api_buy_ninjaxu'))}}
                                    @if($errors->has('active_api_buy_ninjaxu'))
                                        <div
                                            class="form-control-feedback">{{ $errors->first('active_api_buy_ninjaxu') }}</div>
                                    @endif
                                </div>
                            </div>


                            <div class="col-md-6">
                                {{--is_agency_ninjaxu--}}
                                <div class="{{ $errors->has('is_agency_ninjaxu')? 'has-danger':'' }}">
                                    <label class="text-warning">Đại lý xu (Ninja xu):</label>
                                    {{Form::select('is_agency_ninjaxu',['0'=>'Không','1'=>'Có'],old('is_agency_ninjaxu', isset($data) ? $data->is_agency_ninjaxu : null),array('class'=>'form-control m-input col-md-6','id'=>'is_agency_ninjaxu'))}}
                                    @if($errors->has('is_agency_ninjaxu'))
                                        <div
                                            class="form-control-feedback">{{ $errors->first('is_agency_ninjaxu') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group m-form__group ">
                        <div class="row">

                            <div class="col-md-6">
                                {{--active_api_buy_nrocoin--}}
                                <div class="{{ $errors->has('active_api_buy_nrocoin')? 'has-danger':'' }}">
                                    <label class="" style="color:#128416;">Active API (Mua Vàng NRO):</label>
                                    {{Form::select('active_api_buy_nrocoin',['0'=>'Không','1'=>'Có'],old('active_api_buy_nrocoin', isset($data) ? $data->active_api_buy_nrocoin : null),array('class'=>'form-control m-input  col-md-6','id'=>'active_api_buy_nrocoin'))}}
                                    @if($errors->has('active_api_buy_nrocoin'))
                                        <div
                                            class="form-control-feedback">{{ $errors->first('active_api_buy_nrocoin') }}</div>
                                    @endif
                                </div>
                            </div>


                            <div class="col-md-6">
                                {{--is_agency_ninjaxu--}}
                                <div class="{{ $errors->has('is_agency_nrocoin')? 'has-danger':'' }}">
                                    <label class=""  style="color:#128416;">Đại lý mua vàng NRO:</label>
                                    {{Form::select('is_agency_nrocoin',['0'=>'Không','1'=>'Có'],old('is_agency_nrocoin', isset($data) ? $data->is_agency_nrocoin : null),array('class'=>'form-control m-input col-md-6','id'=>'is_agency_nrocoin'))}}
                                    @if($errors->has('is_agency_nrocoin'))
                                        <div
                                            class="form-control-feedback">{{ $errors->first('is_agency_nrocoin') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr/>

                    <div class="form-group row">
                        <div class="col-12 col-md-6">
                            <label for="status" class="form-control-label">{{ __('Trạng thái') }}</label>
                            {{Form::select('status',config('module.user.status'),old('status', isset($data) ? $data->status : null),array('class'=>'form-control m-input col-md-6'))}}
                            @if($errors->has('status'))
                                <div class="form-control-feedback">{{ $errors->first('status') }}</div>
                            @endif
                        </div>

                    </div>

                    <div class="form-group row">
                        <div class="col-12 col-md-6">
                            <label>{{ __('Ngày tạo') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control  datetimepicker-input datetimepicker-default m-input col-md-6"
                                       name="created_at"
                                       value="{{ old('created_at', isset($data) ? $data->created_at->format('d/m/Y H:i:s') : date('d/m/Y H:i:s')) }}"
                                       placeholder="{{ __('Ngày tạo') }}" autocomplete="off"
                                       data-toggle="datetimepicker">

                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar"></i></span>
                                </div>
                            </div>
                            @if($errors->has('created_at'))
                                <div class="form-control-feedback">{{ $errors->first('created_at') }}</div>
                            @endif
                        </div>
                    </div>

                    {{-----gallery block------}}
                    <div class="form-group  {{ $errors->has('locale') ? ' text-danger' : '' }} ">
                        <div class="row">
                            {{-----image------}}
                            <div class="col-12 col-md-4">
                                <label for="locale">{{ __('Hình đại diện') }}:</label>
                                <div class="">
                                    <div class="fileinput ck-parent" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px">

                                            @if(old('image', isset($data) ? $data->image : null)!="")
                                                <img class="ck-thumb" src="{{ old('image', isset($data) ? \App\Library\MediaHelpers::media($data->image) : null) }}">
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
                    </div>
                </div>
                <div class="card-footer">
                    <div class="m-form__actions text-right">
                        <button type="button" class="btn btn-secondary">Hủy</button>
                        <div class="btn-group">
                            @if(isset($data))
                                <button type="submit"
                                        class="btn btn-primary  m-btn m-btn--icon m-btn--wide m-btn--sm">
									<span>
										<i class="la la-check"></i>
										<span>Lưu</span>
									</span>
                                </button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary  m-btn m-btn--icon m-btn--wide m-btn--sm">
									<span>
										<i class="la la-check"></i>
										<span>Thêm</span>
									</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 panel_agency_buygem" style="display:{{(isset($data->is_agency_buygem)?$data->is_agency_buygem:null)==1?"block":"none"}}">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <i class="flaticon-clipboard"></i>
                        <h3 class="card-label" style="font-weight: bold;margin-left: 4px">
                            Chiết khấu đại lý mua ngọc (Nro Gem)
                        </h3>
                    </div>
                </div>
                <div class="card-body mt-3">
                    <!--begin: Datatable -->
                    <div id="table_main_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="dataTables_scroll">
                                    <div class="dataTables_scrollBody"
                                         style="position: relative; overflow: auto; width: 100%;">
                                        <table
                                            class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline"
                                            id="table_main" role="grid" aria-describedby="table_main_info"
                                            style="width: 988px;">
                                            <thead>
                                            <tr role="row">
                                                <th class="text-center">Máy chủ</th>
                                                <th class="text-center">Hệ số</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @if(\App\Library\Helpers::DecodeJson('server_mode',$serviceNrogem->params??"") =="1")

                                                @php
                                                    $server_data=\App\Library\Helpers::DecodeJson('server_data',$serviceNrogem->params);
                                                    if(isset($data)){
                                                        $buygem_discount=json_decode($data->buygem_discount);
                                                    }
                                                @endphp

                                                @if(!empty($server_data))
                                                    @foreach($server_data as $index=>$value)
                                                        <tr>

                                                            <td> {{$value}}</td>

                                                            <td><input type="text" class="form-control"
                                                                       name="discount[]"
                                                                       value="{{isset($buygem_discount[$index])?$buygem_discount[$index]:"" }}"
                                                                       placeholder="Hệ số"></td>

                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!--begin: Datatable -->
                </div>
                <div class="card-footer">
                    <div class="m-form__actions text-right">
                        <button type="button" class="btn btn-secondary">Hủy</button>
                        <div class="btn-group">
                            @if(isset($data))
                                <button type="submit"
                                        class="btn btn-primary  m-btn m-btn--icon m-btn--wide m-btn--sm">
									<span>
										<i class="la la-check"></i>
										<span>Lưu</span>
									</span>
                                </button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary  m-btn m-btn--icon m-btn--wide m-btn--sm">
									<span>
										<i class="la la-check"></i>
										<span>Thêm</span>
									</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 panel_agency_ninjaxu" style="display:{{(isset($data->is_agency_ninjaxu)?$data->is_agency_ninjaxu:null)==1?"block":"none"}}">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <i class="flaticon-clipboard"></i>
                        <h3 class="card-label" style="font-weight: bold;margin-left: 4px">
                            Chiết khấu đại lý mua ngọc (Nro Gem)
                        </h3>
                    </div>
                </div>
                <div class="card-body mt-3">
                    <!--begin: Datatable -->
                    <div id="table_main_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="dataTables_scroll">
                                    <div class="dataTables_scrollBody"
                                         style="position: relative; overflow: auto; width: 100%;">
                                        <table
                                            class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline"
                                            id="table_main" role="grid" aria-describedby="table_main_info"
                                            style="width: 988px;">
                                            <thead>
                                            <tr role="row">
                                                <th class="text-center">Máy chủ</th>
                                                <th class="text-center">Hệ số</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @if(\App\Library\Helpers::DecodeJson('server_mode',$serviceNinjaxu->params??"") =="1")

                                                @php
                                                    $server_data=\App\Library\Helpers::DecodeJson('server_data',$serviceNinjaxu->params);
                                                    if(isset($data)){
                                                        $ninjaxu_discount=json_decode($data->ninjaxu_discount);

                                                    }
                                                @endphp

                                                @if(!empty($server_data))
                                                    @foreach($server_data as $index=>$value)
                                                        <tr>

                                                            <td> {{$value}}</td>

                                                            <td><input type="text" class="form-control"
                                                                       name="ninjaxu_discount[]"
                                                                       value="{{isset($ninjaxu_discount[$index])?$ninjaxu_discount[$index]:"" }}"
                                                                       placeholder="Hệ số"></td>

                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!--begin: Datatable -->
                </div>
                <div class="card-footer">
                    <div class="m-form__actions text-right">
                        <button type="button" class="btn btn-secondary">Hủy</button>
                        <div class="btn-group">
                            @if(isset($data))
                                <button type="submit"
                                        class="btn btn-primary  m-btn m-btn--icon m-btn--wide m-btn--sm">
									<span>
										<i class="la la-check"></i>
										<span>Lưu</span>
									</span>
                                </button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary  m-btn m-btn--icon m-btn--wide m-btn--sm">
									<span>
										<i class="la la-check"></i>
										<span>Thêm</span>
									</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 panel_agency_nrocoin" style="display:{{(isset($data->is_agency_nrocoin)?$data->is_agency_nrocoin:null)==1?"block":"none"}}">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <i class="flaticon-clipboard"></i>
                        <h3 class="card-label" style="font-weight: bold;margin-left: 4px">
                            Chiết khấu đại lý bán vàng NRO
                        </h3>
                    </div>
                </div>
                <div class="card-body mt-3">
                    <!--begin: Datatable -->
                    <div id="table_main_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="dataTables_scroll">
                                    <div class="dataTables_scrollBody"
                                         style="position: relative; overflow: auto; width: 100%;">
                                        <table
                                            class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline"
                                            id="table_main" role="grid" aria-describedby="table_main_info"
                                            style="width: 988px;">
                                            <thead>
                                            <tr role="row">
                                                <th class="text-center">Máy chủ</th>
                                                <th class="text-center">Hệ số</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @if(\App\Library\Helpers::DecodeJson('server_mode',$serviceNrocoin->params??"") =="1")

                                                @php
                                                    $server_data=\App\Library\Helpers::DecodeJson('server_data',$serviceNrocoin->params);
                                                    if(isset($data)){
                                                        $nrocoin_discount=json_decode($data->nrocoin_discount);

                                                    }
                                                @endphp

                                                @if(!empty($server_data))
                                                    @foreach($server_data as $index=>$value)
                                                        <tr>

                                                            <td> {{$value}}</td>

                                                            <td><input type="text" class="form-control"
                                                                       name="nrocoin_discount[]"
                                                                       value="{{isset($nrocoin_discount[$index])?$nrocoin_discount[$index]:"" }}"
                                                                       placeholder="Hệ số"></td>

                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!--begin: Datatable -->
                </div>
                <div class="card-footer">
                    <div class="m-form__actions text-right">
                        <button type="button" class="btn btn-secondary">Hủy</button>
                        <div class="btn-group">
                            @if(isset($data))
                                <button type="submit"
                                        class="btn btn-primary  m-btn m-btn--icon m-btn--wide m-btn--sm">
									<span>
										<i class="la la-check"></i>
										<span>Lưu</span>
									</span>
                                </button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary  m-btn m-btn--icon m-btn--wide m-btn--sm">
									<span>
										<i class="la la-check"></i>
										<span>Thêm</span>
									</span>
                                </button>
                            @endif
                        </div>
                    </div>
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

    <script src="/assets/backend/assets/js/jquery.md5.min.js" type="text/javascript"></script>

    <script>
        $(document).ready(function () {

            $('#is_agency_card').on('change', function () {
                if (this.value == 1) {
                    $('.panel_agency_card').show();
                } else {
                    $('.panel_agency_card').hide();
                }
            });

            $('#is_agency_charge').on('change', function () {
                if (this.value == 1) {
                    $('.panel_agency_charge').show();
                } else {
                    $('.panel_agency_charge').hide();
                }
            });


            $('#active_api_charge').on('change', function () {
                if (this.value == 1) {
                    $('.area_api').show();
                } else {
                    $('.area_api').hide();
                }
            });

            $('#is_agency_buygem').on('change', function () {
                if (this.value == 1) {
                    $('.panel_agency_buygem').show();
                } else {
                    $('.panel_agency_buygem').hide();
                }
            });

            $('#is_agency_ninjaxu').on('change', function () {
                if (this.value == 1) {
                    $('.panel_agency_ninjaxu').show();
                } else {
                    $('.panel_agency_ninjaxu').hide();
                }
            });


            $('#is_agency_nrocoin').on('change', function () {
                if (this.value == 1) {
                    $('.panel_agency_nrocoin').show();
                } else {
                    $('.panel_agency_nrocoin').hide();
                }
            });


            $("#btnGenKey").click(function () {
                var text = "";
                var possible = "abcdefghijklmnopqrstuvwxyz0123456789";

                for (var i = 0; i < 10; i++)
                    text += possible.charAt(Math.floor(Math.random() * possible.length));

                $("#txtPartnerKey").val($.MD5(text));

            });

            $("#btnGenKeySerivce").click(function () {
                var text = "";
                var possible = "abcdefghijklmnopqrstuvwxyz0123456789";

                for (var i = 0; i < 10; i++) {
                    text += possible.charAt(Math.floor(Math.random() * possible.length));
                }

                $("#txtPartnerKeyService").val($.MD5(text));

            });


        });
    </script>

    <script>
        "use strict";

        jQuery(document).ready(function () {

        });

        $(document).ready(function () {

            // Demo 6
            $('.datetimepicker-default').datetimepicker({
                useCurrent: true,
                autoclose: true,
                format: "DD/MM/YYYY HH:mm:ss"
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

                // var url = formSubmit.attr('action');
                {{--$.ajax({--}}
                {{--    type: "POST",--}}
                {{--    url: url,--}}
                {{--    data: formSubmit.serialize(), // serializes the form's elements.--}}
                {{--    beforeSend: function (xhr) {--}}

                {{--    },--}}
                {{--    success: function (data) {--}}
                {{--        if (data.success) {--}}
                {{--            if (data.redirect + "" != "") {--}}
                {{--                location.href = data.redirect;--}}
                {{--            }--}}
                {{--            toast('{{__('Cập nhật thành công')}}');--}}
                {{--        } else {--}}
                {{--            toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');--}}
                {{--        }--}}
                {{--    },--}}
                {{--    error: function (data) {--}}
                {{--        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');--}}
                {{--    },--}}
                {{--    complete: function (data) {--}}
                {{--        KTUtil.btnRelease(btn);--}}
                {{--    }--}}
                {{--});--}}

            });

        });

    </script>


    <script>

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
                        elemThumb.attr("src", url);
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

    </script>



@endsection

