{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">

        {{--<div class="btn-group">--}}
        {{--    <a href="{{route('admin.'.$module.'.create')}}" type="button"  class="btn btn-success font-weight-bolder">--}}
        {{--        <i class="fas fa-plus-circle icon-md"></i>--}}
        {{--        {{__('Thêm mới')}}--}}
        {{--    </a>--}}
        {{--</div>--}}
    </div>
@endsection

{{-- Content --}}
@section('content')
    <div class="col-md-12 left__right" style="padding-bottom: 16px">
        <div class="card card-custom" style="background: none;box-shadow: none">
            <div class="card-header card-header-tabs-line nav-tabs-line-3x" style="border-bottom: none;padding: 0;min-height: 40px">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs nav-bold nav-tabs-line nav-tabs-line-3x" id="list-tab-action">
                        <li class="nav-item mr-3 btn-show-group-shop">
                            <a class="nav-link active choice_status" style="padding-top: 0;padding-bottom: 12px" data-toggle="tab" data-type="1" href="#order_pendding">
                                <span class="nav-text font-size-lg">Đang chờ</span>
                            </a>
                        </li>

                        <li class="nav-item mr-3 btn-show-group-user">
                            <a class="nav-link choice_status" style="padding-top: 0;padding-bottom: 12px" data-toggle="tab" data-type="2" href="#order_processing">
                                <span class="nav-text font-size-lg">Đang thực hiện</span>
                            </a>
                        </li>

                        <li class="nav-item mr-3 btn-show-log-edit">
                            <a class="nav-link choice_status" style="padding-top: 0;padding-bottom: 12px" data-toggle="tab" data-type="3" href="#order_failure">
                                <span class="nav-text font-size-lg">Thất bại/Từ chối</span>
                            </a>
                        </li>

                        <li class="nav-item mr-3 btn-show-group-shop-edit">
                            <a class="nav-link choice_status" style="padding-top: 0;padding-bottom: 12px" data-toggle="tab" data-type="4" href="#order_success">
                                <span class="nav-text font-size-lg">Thành công</span>
                            </a>
                        </li>

                        <li class="nav-item mr-3 btn-show-log-edit">
                            <a class="nav-link choice_status" style="padding-top: 0;padding-bottom: 12px" data-toggle="tab" data-type="5" href="#order_refund">
                                <span class="nav-text font-size-lg">Kết nối ncc thất bại</span>
                            </a>
                        </li>
                        <li class="nav-item mr-3 btn-show-log-edit">
                            <a class="nav-link choice_status" style="padding-top: 0;padding-bottom: 12px" data-toggle="tab" data-type="6" href="#order_refund">
                                <span class="nav-text font-size-lg">Xử lý thủ công</span>
                            </a>
                        </li>
                        @if(Auth::user()->can('service-reception-all'))
                            <li class="nav-item mr-3">
                                <a class="nav-link choice_status choice_status_fist" style="padding-top: 0;padding-bottom: 12px" data-type="0" data-toggle="tab" href="#order_all">
                                    <span class="nav-text font-size-lg">Tất cả</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="card-body p-0"  >
                <div class="tab-content">

                </div>
            </div>
        </div>
    </div>

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
            <form class="mb-10" action="{{route('admin.service-purchase-auto.export-excel')}}" method="POST">
                @csrf

            <input type="hidden" name="type" value="1" id="type">
            <div class="row">
                    {{--ID--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="id" name="id" value="{{request('id')}}"   placeholder="{{__('ID hoặc Request ID')}}">
                        </div>
                    </div>

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="request_id" name="request_id" value="{{request('request_id')}}"   placeholder="{{__('Tìm kiếm nhiều request_id')}}">
                        </div>
                    </div>

                    {{--processor--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                    <span class="input-group-text"><i
                                            class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input"   id="work_name" name="work_name" value="{{request('work_name')}}"
                                   placeholder="{{__('Tên công việc')}}">
                        </div>
                    </div>


                    {{--author--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="author" name="author" value="{{request('author')}}"
                                   placeholder="{{__('Người order')}}">
                        </div>
                    </div>

                    {{--processor--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input"  id="processor" name="processor" value="{{request('processor')}}"
                                   placeholder="{{__('Người thực hiện')}}">
                        </div>
                    </div>

                    {{--processor--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                    <span class="input-group-text"><i
                                            class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input"  id="roblox_acc" name="roblox_acc" value="{{request('roblox_acc')}}"
                                   placeholder="{{__('Bot thuc hien')}}">
                        </div>
                    </div>

                {{--processor--}}
{{--                <div class="form-group col-12 col-sm-6 col-lg-3">--}}
{{--                    <div class="input-group">--}}
{{--                        <div class="input-group-prepend">--}}
{{--                                    <span class="input-group-text"><i--}}
{{--                                            class="la la-calendar-check-o glyphicon-th"></i></span>--}}
{{--                        </div>--}}
{{--                        <input type="text" class="form-control datatable-input"  id="roblox_acc_old" name="roblox_acc_old" value="{{request('roblox_acc_old')}}"--}}
{{--                               placeholder="{{__('Bot thuc hien (Cũ)')}}">--}}
{{--                    </div>--}}
{{--                </div>--}}

                    @if(isset($dataCategory) && count($dataCategory))
                        <div class="form-group col-12 col-sm-6 col-lg-3" id="categorys">
                            <div style="display: flex">
                                <div class="input-group-prepend">
                                                    <span class="input-group-text" style="border-top-right-radius: 0;border-bottom-right-radius: 0">
                                                        <i class="la la-calendar-check-o glyphicon-th"></i>
                                                    </span>
                                </div>
                                <select  multiple="multiple" name="group_id[]" title="{{__('Tất cả danh mục')}}" class="form-control select2 col-md-5 datatable-input"  data-placeholder="{{__('Chọn danh mục')}}" id="kt_select2_2" style="width: 100%;border-bottom-left-radius: 0;border-top-left-radius: 0">
                                    <option value="0">-- {{ __('Không chọn') }} --</option>
                                    @foreach($dataCategory as $category)
                                        <option value="{{$category->id}}">{{$category->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    {{--status--}}
                <div class="form-group col-12 col-sm-6 col-lg-3 data_status">
                    {{--                        @include('admin.service.purchase.widget.__status')--}}
                </div>
{{--                    <div class="form-group col-12 col-sm-6 col-lg-3">--}}
{{--                        <div class="input-group">--}}
{{--                            <select name="status[]" multiple="multiple" data-placeholder="{{__('Tất cả trạng thái')}}" title="Tất cả trạng thái" id="status" class="form-control datatable-input">--}}
{{--                                <option value="">-- Tất cả trạng thái --</option>--}}
{{--                                @foreach(config('module.service-purchase-auto.status') as $key => $status)--}}
{{--                                    <option--}}
{{--                                        @if($key == 1)--}}
{{--                                        selected--}}
{{--                                        @endif--}}
{{--                                        value="{{ $key }}"> {{ $status }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                    <span class="input-group-text"><i
                                            class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select id="status_nrogem" name="status_nrogem" class="form-control">
                                <option value="">-- Tất cả trạng thái tool --</option>
                                <option value="danhanngoc">Đã nhận ngọc</option>
                                <option value="thieungoc">Thiếu ngọc</option>
                                <option value="koosieuthi">Không siêu thị</option>
                                <option value="matitem">Mất item</option>
                                <option value="kodusucmanh">Không sức mạnh</option>
                                <option value="kconhanvat">Bán Ngọc</option>
                                <option value="dahuybo">Đã hủy bỏ</option>
                                <option value="hanhtrangday">Hành trang đầy</option>
                                <option value="muanhamitem">Mua nhầm item</option>
                                <option value="taikhoansai">Tài khoản sai</option>
                                <option value="thieungoc">Thiếu ngọc</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                    <span class="input-group-text"><i
                                            class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select id="payment_type" name="payment_type" class="form-control">
                                <option value="">-- Cấu hình nhà cung cấp --</option>
                                <option value="1">ĐAI LÝ</option>
                                <option value="2">RBX API</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('type_information',[''=>'-- Loại tài khoản --']+config('module.user-qtv.type_information'),old('type_information', isset($data) ? $data->type_information : null),array('id'=>'type_information','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>


                    {{--started_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="started_at" id="started_at" value="{{request('started_at')}}" autocomplete="off"
                                   class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian tạo từ')}}" data-toggle="datetimepicker">

                        </div>
                    </div>

                    {{--ended_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Đến</span>
                            </div>
                            <input type="text" name="ended_at" id="ended_at" value="{{request('ended_at')}}"  autocomplete="off"
                                   class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian tạo đến')}}" data-toggle="datetimepicker">

                        </div>
                    </div>



                    {{--finished_started_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="finished_started_at" id="finished_started_at" value="{{request('finished_started_at')}}" autocomplete="off"
                                   class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian hoàn tất từ')}}" data-toggle="datetimepicker">

                        </div>
                    </div>

                    {{--finished_ended_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Đến</span>
                            </div>
                            <input type="text" name="finished_ended_at" id="finished_ended_at" value="{{request('finished_ended_at')}}"  autocomplete="off"
                                   class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian hoàn tất đến')}}" data-toggle="datetimepicker">

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


{{--                        @if ( auth()->user()->can('store-card-export'))--}}
                            <input type="hidden" name="export_excel" value="">
                            <button class="btn btn-danger btn-secondary--icon" value="1" name="export_excel" type="submit">
                                <span>
                                    <i class="flaticon-folder-2"></i>
                                    <span>Xuất Excel</span>
                                </span>
                            </button>
{{--                        @endif--}}

                        <button class="btn btn-secondary btn-secondary--icon" id="kt_reset">
                            <span>
                                <i class="la la-close"></i>
                                <span>Reset</span>
                            </span>
                        </button>

                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-6">
                        <div class="btn-group m-btn-group" role="group" aria-label="...">
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date" >Hôm nay</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::yesterday()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::yesterday()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Hôm qua</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng này</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->startOfMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->endOfMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng trước</a>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                        <div class="col-md-3">
                            <button type="button" id="btn__attribute_tk" class="btn btn-primary">Thống kê</button>
                            @if(Auth::user()->can('report-api-rpx'))
                            <button type="button" id="btn_rbx" class="btn btn-success btn_rbx">Available Robux</button>
                            @endif
                        </div>
                        <div class="col-auto ml-auto">

                            @if(Auth::user()->can('swich-api-rpx'))
                                <button data-toggle="tooltip" data-placement="top" title="SWITCH DAILY" type="button" class="btn btn-danger btn_daily">DAILY</button>
                                <button data-toggle="tooltip" data-placement="top" title="SWITCH RBX" type="button" class="btn btn-success btn_rbx_api">RBX</button>
                            @endif
                            @if(Auth::user()->can('service-purchase-edit-pengiriman'))
                            <button data-toggle="tooltip" data-placement="top" title="EDIT LÔ HÀNG" type="button" class="btn btn-primary btn_edut_lohang">LÔ HÀNG</button>
                            @endif
                            @if(Auth::user()->can('service-purchase-auto-roblox-psx'))
                            <button type="button" class="btn btn-primary btn_roblox_psx_huge" data-toggle="tooltip" data-placement="top" title="CHUYỂN TRẠNG THÁI ĐƠN PSX HUGE UNIT"> PSX - HUGE - UNIT</button>
                            @endif
                            @if(Auth::user()->can('service-purchase-auto-roblox-unit'))
                            <button type="button" class="btn btn-primary btn_order_unit" data-toggle="tooltip" data-placement="top" title="HOÀN TẤT ĐƠN HÀNG UNIT">UNIT</button>
                            @endif
                            @if(Auth::user()->id == 301 || Auth::user()->id == 5551 || Auth::user()->id == 198544)
                                <button data-toggle="tooltip" data-placement="top" title="HỦY ĐƠN DỊCH VỤ GEM UNIT AUTO THỦ CÔNG" type="button" class="btn btn-danger btn_delete_gem_unit_auto">D - UNIT</button>
                            @endif
                        </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-12">

                        <div class="checkbox-inline">
                            <label for="username" class="checkbox toggle-vis" data-column="4">
                                <input id="username" type="checkbox" name="checkboxes1">
                                <span></span>Số vật phẩm/Tên Huge
                            </label>
                            <label for="parameters_1" class="checkbox toggle-vis" data-column="5">
                                <input id="parameters_1" type="checkbox" name="checkboxes2">
                                <span></span>Server tên tài khoản</label>
                            <label for="parameters_2" class="checkbox toggle-vis" data-column="6">
                                <input id="parameters_2" type="checkbox" name="checkboxes3" >
                                <span></span>Bot thực hiện
                            </label>
{{--                            <label for="parameters_3" class="checkbox toggle-vis" data-column="7">--}}
{{--                                <input id="parameters_3" type="checkbox" name="checkboxes4" >--}}
{{--                                <span></span>Cấu hình nhà cung cấp--}}
{{--                            </label>--}}
                            <label for="parameters_4" class="checkbox toggle-vis" data-column="14">
                                <input id="parameters_4" type="checkbox" name="checkboxes5" >
                                <span></span>Người order
                            </label>
                        </div>

                    </div>
                </div>
            </form>

{{--            @if(Auth::user()->id == 301 || Auth::user()->id == 5551)--}}
{{--                <form class="mb-10" action="{{route('admin.service-purchase-auto.delete-desc-auto')}}" method="POST">--}}
{{--                    @csrf--}}
{{--                    <button class="btn btn-danger btn-secondary--icon" type="submit">--}}
{{--                                <span>--}}
{{--                                    <i class="flaticon-folder-2"></i>--}}
{{--                                    <span>delete desc auto</span>--}}
{{--                                </span>--}}
{{--                    </button>--}}
{{--                </form>--}}
{{--            @endif--}}
{{--            @if(Auth::user()->id == 301 || Auth::user()->id == 5551 || Auth::user()->id == 198544)--}}
{{--                <form class="mb-10" action="{{route('admin.service-purchase-auto.delete-all-auto')}}" method="POST">--}}
{{--                    @csrf--}}
{{--                    <button class="btn btn-danger btn-secondary--icon" type="submit">--}}
{{--                                <span>--}}
{{--                                    <i class="flaticon-folder-2"></i>--}}
{{--                                    <span>delete all auto</span>--}}
{{--                                </span>--}}
{{--                    </button>--}}
{{--                </form>--}}
{{--        @endif--}}
            <!--begin: Search Form-->

            <!--begin: Datatable-->
            <table class="table table-bordered table-hover table-checkable " id="kt_datatable">
            </table>
            <!--end: Datatable-->
        </div>
    </div>

    <div class="modal fade" id="deleteGemUnitAuto">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="mb-10" action="{{route('admin.service-purchase-auto.delete-all-auto')}}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"> {{__('Hủy đơn gem unit auto thủ công')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        Bạn muốn hủy hết đơn hàng thủ công?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success m-btn m-btn--custom">Xác nhận</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- error item Modal -->
    <div class="modal fade" id="attributeTkModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> {{__('Thống kê dịch vụ tự động')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body data_attibute_tk">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                </div>
            </div>
        </div>
    </div>


    {{---------------all modal controll-------}}

    <!-- recallback item Modal -->
    <div class="modal fade" id="recallbackModal">
        <div class="modal-dialog">
            <div class="modal-content">

                {{Form::open(array('route'=>array('admin.service-purchase-auto.recallback',0),'class'=>'form-horizontal','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xác nhận thao tác</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    Bạn muốn recallback lại cho shop?
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

    <!-- refund item Modal -->
    <div class="modal fade" id="refundModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase-auto.refund',0),'class'=>'form-horizontal','id'=>'form-refund','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" class="refund_id" value=""/>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Lý do hoàn</span>
                            </div>
                            <input type="text" name="note" class="form-control note">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Trạng thái dịch vụ</span>
                            </div>
                            <select class="form-control" name="status">
                                <option value="0">Đã hủy</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-12 col-form-label font-bold">Chọn loại chuyển đổi trạng thái</label>
                        <div class="col-12 col-form-label">
                            <div class="radio-inline">
                                <label class="radio radio-primary">
                                    <input type="radio" name="btn_submit_refund" value="refund" checked="checked">
                                    <span></span>
                                    Hoàn tiền
                                </label>
                                <label class="radio radio-primary">
                                    <input type="radio" name="btn_submit_refund" value="refund_nick_only">
                                    <span></span>
                                    Chỉ đổi trạng thái
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-refund" data-form="form-refund">{{__('Xác nhận')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="swichRBX">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase-auto.switch-rbx-api',0),'class'=>'form-horizontal','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xác nhận thao tác</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select name="url_type" class="form-control">
                        <option value="">=== Chọn nhà cung cấp ===</option>
                        @foreach(config('module.service-purchase-auto.supplier')??[] as $key => $rbx)
                            @if($key != 1)
                                <option
                                    value="{{ $key }}"> {{ $rbx }} </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary m-btn m-btn--custom">Xác nhận</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="swichDAILY">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase-auto.switch-daily',0),'class'=>'form-horizontal','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xác nhận thao tác</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    NẾU CHUYỂN VỀ PHƯƠNG THỨC XỬ LÝ VỀ DAILY, CÁC ĐƠN HÀNG TRÊN RBX API SẼ BỊ HỦY ,BẠN CÓ MUỐN TIẾP TÚC THAO TÁC KHÔNG?
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary m-btn m-btn--custom">Xác nhận</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase.destroy',0),'class'=>'form-horizontal','method'=>'DELETE'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xác nhận thao tác</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    Bạn thực sự muốn hủy yêu cầu dịch vụ?
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom">Xác nhận</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- rechargeGamePassModal -->
    <div class="modal fade" id="rechargeBuyGamePassModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase-auto.recharge-gamepass',0),'class'=>'form-horizontal form-submit-ajax','method'=>'POST'))}}

                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận thao tác</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn nạp lại đơn này?')}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom">Chuyển trạng thái</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- rechargeModal -->
    <div class="modal fade" id="rechargeModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase-auto.recharge',0),'class'=>'form-horizontal form-submit-ajax','method'=>'POST'))}}

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xác nhận thao tác</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn nạp lại đơn này?')}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom">Nạp lại</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- rechargeModal -->
    <div class="modal fade" id="rechargeRbxModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase-auto.recharge-rbx',0),'class'=>'form-horizontal form-submit-ajax','method'=>'POST'))}}

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xác nhận thao tác</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn nạp lại đơn này?')}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom">Nạp lại</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- rechargeModal -->
    <div class="modal fade" id="psxModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.service-purchase-auto.roblox-psx',0) }}" method="POST" enctype="multipart/form-data" class="form-horizontal form-submit-ajax">
                @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Xác nhận thao tác</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{__('Bạn thực sự muốn chuyển trạng thái lại đơn này?')}}
                        <br>
                        <br>
                        <span style="font-size: 16px;color: red">{{__('Vui lòng tắt tool game để thực hiện thao tác này.')}}</span>
                        <br>
                        <br>
                        <span style="font-size: 16px" class="text-success">{{__('Kiểm tra đơn tool đã thực hiện.')}}</span>
                        <br>
                        <br>
                        <textarea name="txt_file" class="form-control" style="min-height: 590px">

                        </textarea>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" class="id" value=""/>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success m-btn m-btn--custom" >Chuyển trạng thái</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="unitModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.service-purchase-auto.roblox-unit',0) }}" method="POST" enctype="multipart/form-data" class="form-horizontal form-submit-ajax">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Xác nhận thao tác</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{__('Bạn thực sự muốn chuyển trạng thái lại đơn này?')}}
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" class="id" value=""/>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger m-btn m-btn--custom" >Chuyển trạng thái</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase-auto.roblox-user-id',0),'class'=>'form-horizontal form-submit-ajax','method'=>'POST'))}}

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xác nhận thao tác</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn lấy user id?')}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom">Lấy user id</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!--end::Portlet-->

    <div class="modal fade" id="editPengirimanModal" tabindex="-1"
         role="basic" aria-hidden="true">
        <div style="text-align:initial;" class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase-auto.pengiriman-all',0),'class'=>'m-form','method'=>'POST'))}}
                <div class="modal-header">
                    <h4 class="modal-title">Chỉnh sửa thông tin lô hàng</h4>
                    <button type="button" class="close"
                            data-dismiss="modal"
                            aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group col-12">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="edit_id_pengiriman"> ID lô hàng:</label>
                                <input type="text"
                                       required
                                       class="form-control" id="edit_id_pengiriman" name="edit_id_pengiriman" value=""  placeholder="{{__('ID lô hàng')}}"     >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary btn-outline"
                            data-dismiss="modal">Đóng
                    </button>
                    <input type="hidden" name="id_pengiriman" class="id_pengiriman">
                    <button type="submit"
                            class="btn btn-primary m-btn m-btn--air">Xác
                        nhận
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="productModal">
        <div class="modal-dialog  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="text-center modal-title label-service-selected" > {{__("Available Robux")}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="mb-10">
                        <div class="row mt-5">
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                        Balance USD: <b id="total_record">{{ number_format($balance)??0 }} $</b>
                                    </div>
                                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                        Balance VNĐ: <b id="total_record">{{ number_format($vnd)??0 }} VNĐ</b>
                                    </div>
                                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                        Robux Available: <b id="total_record">{{ number_format($robuxAvailable)??0 }} R$</b>
                                    </div>
                                    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                        Max Robux Available: <b id="total_real_received_price_ctv">{{ number_format($maxRobuxAvailable)??0 }} R$</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!--begin: Datatable-->
                    <table class="table table-bordered table-hover table-checkable " id="kt_datatable_product_modal">

                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                </div>
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
                    // Pagination settings
                    //full dom i và lp
                    // dom: `
                    //         <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>
                    //         <'row'<'col-sm-12'tr>>
                    //         <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    // read more: https://datatables.net/examples/basic_init/dom.html

                    dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                            <'row'<'col-sm-12'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,

                    // dom: "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
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
                        url: '{{url()->current()}}' + '?ajax=1',
                        type: 'GET',
                        data: function (d) {

                            d.id = $('#id').val();
                            d.title = $('#title').val();
                            d.status = $('#status').val();
                            d.group_id = $('#group_id').val();
                            d.group_id2 = $('#group_id2').val();
                            d.author = $('#author').val();
                            d.processor = $('#processor').val();
                            d.payment_type = $('#payment_type').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.finished_started_at = $('#finished_started_at').val();
                            d.finished_ended_at = $('#finished_ended_at').val();
                            d.status_nrogem = $('#status_nrogem').val();
                            d.group_id = $('#kt_select2_2').val();
                            d.check_status = $('#check_status').val();
                            d.check_status_ninjaxu = $('#check_status_ninjaxu').val();
                            d.check_status_nrogem = $('#check_status_nrogem').val();
                            d.check_status_roblox = $('#check_status_roblox').val();
                            d.type_information = $('#type_information').val();
                            d.request_id = $('#request_id').val();
                            d.roblox_acc = $('#roblox_acc').val();
                            d.type = $('#type').val();
                            d.roblox_acc_old = $('#roblox_acc_old').val();
                            d.work_name = $('#work_name').val();
                        }
                    },
                    buttons: [
                            @if(Auth::user()->can('service-purchase-auto-recallback'))
                        {
                            text: '<i class="m-nav__link-icon la la-compress"></i> Recallback',
                            action: function (e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatable.$('input[type="checkbox"]:checked').length;
                                if (total <= 0) {
                                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                                    return;
                                }

                                datatable.$('input[type="checkbox"]').each(function (index, elem) {
                                    if ($(elem).is(':checked')) {
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                        }
                                    }
                                })
                                $('#recallbackModal .id').attr('value', allSelected);
                                $('#recallbackModal').modal('toggle');

                            }
                        },
                            @endif
                            @if(Auth::user()->can('service-purchase-auto-recharge'))
                        {
                            text: '<i class="m-nav__link-icon la la-rotate-right"></i> Nạp lại ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatable.$('input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                                    return;
                                }

                                datatable.$('input[type="checkbox"]').each(function (index, elem) {
                                    if ($(elem).is(':checked')) {
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                        }
                                    }
                                });
                                $('#rechargeModal').modal('toggle');
                                $('#rechargeModal .id').attr('value', allSelected);

                            }
                        },
                            @endif
                            @if(Auth::user()->can('service-purchase-auto-recharge-rbx'))
                        {
                            text: '<i class="m-nav__link-icon la la-rotate-right"></i> Nạp lại RBX',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatable.$('input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                                    return;
                                }

                                datatable.$('input[type="checkbox"]').each(function (index, elem) {
                                    if ($(elem).is(':checked')) {
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                        }
                                    }
                                });
                                $('#rechargeRbxModal').modal('toggle');
                                $('#rechargeRbxModal .id').attr('value', allSelected);

                            }
                        },
                            @endif
                            @if(Auth::user()->id == 301 || Auth::user()->id == 28 || Auth::user()->id == 198767 || Auth::user()->id == 198988)
                        {
                            text: '<i class="m-nav__link-icon la la-rotate-right"></i> Chuyển trạng thái ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatable.$('input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                                    return;
                                }

                                datatable.$('input[type="checkbox"]').each(function (index, elem) {
                                    if ($(elem).is(':checked')) {
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                        }
                                    }
                                });
                                $('#rechargeBuyGamePassModal').modal('toggle');
                                $('#rechargeBuyGamePassModal .id').attr('value', allSelected);

                            }
                        },
                        @endif
                    ],
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

                        {
                            data: 'id', title: 'ID',
                            render: function (data, type, row) {

                                return  row.id+ "<br/> Request ID: "+row.request_id_customer;
                            }
                        },
                        {
                            data: 'id', title: 'Dịch vụ',
                            render: function (data, type, row) {
                               var temp="";
                               if (row.item_ref){

                                   if (row.item_ref.title!==null){
                                       temp+=row.item_ref.title;
                                   }
                                   temp+="<br />";
                                   if( row.idkey !=null){

                                       temp+="<i style='font-size:12px'>"+"Cổng Auto: "+ row.idkey +"</i>";
                                   }

                                   return temp;
                               }

                                return temp;
                            }
                        },
                        {
                            data: 'information', title: 'Loại tài khoản',
                            render: function (data, type, row) {

                                let html ='';
                                if (row.information){

                                    html = row.information;
                                }
                                return html;
                            }
                        },
                        {
                            data: 'price_base', title: 'Số vật phẩm/Tên huge',visible: false,
                            render: function (data, type, row) {

                                return row.price_base;
                            }
                        },
                        {
                            data: 'server', title: 'Server/Tên tài khoản',visible: false,
                            render: function (data, type, row) {

                                return row.server;
                            }
                        },
                        {
                            data: 'roblox_acc', title: 'Bot thực hiện',visible: false,
                            render: function (data, type, row) {

                                return row.roblox_acc;
                            }
                        },
                        {
                            data: 'payment_type', title: 'Nhà cung cấp',
                            render: function (data, type, row) {
                                if(row.payment_type){
                                    return  row.payment_type;
                                }
                                return "";
                            }
                        },
                        {
                            data: 'rate', title: 'Tỷ giá',
                            render: function (data, type, row) {
                                if(row.rate){
                                    return  row.rate;
                                }
                                return "";
                            }
                        },
                        {
                            data: 'vnd', title: 'VNĐ',
                            render: function (data, type, row) {
                                if(row.vnd){
                                    return  row.vnd;
                                }
                                return "";
                            }
                        },
                        {
                            data: 'price', title: 'Trị giá',
                            render: function (data, type, row) {
                                return number_format(row.price,'.');

                            }
                        },
                        {
                            data: 'status', title: 'Trạng thái',
                            render: function (data, type, row) {

                                if (row.status == 0) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-danger \">" + "{{config('module.service-purchase-auto.status.0')}}" + "</span>";
                                }
                                else if (row.status == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-warning \">" + "{{config('module.service-purchase-auto.status.1')}}" + "</span>";

                                } else if (row.status == 2) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-info \">" + "{{config('module.service-purchase-auto.status.2')}}" + "</span>";
                                }
                                else if (row.status == 3) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service-purchase-auto.status.3')}}" + "</span>";
                                }
                                else if (row.status == 4) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-success \">" + "{{config('module.service-purchase-auto.status.4')}}" + "</span>";
                                }
                                else if (row.status == 5) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service-purchase-auto.status.5')}}" + "</span>";
                                }

                                else if (row.status == 6) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.service-purchase-auto.status.6')}}" + "</span>";
                                }

                                else if (row.status == 7) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.service-purchase-auto.status.7')}}" + "</span>";
                                }

                                else if (row.status == 77) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service-purchase-auto.status.77')}}" + "</span>";
                                }
                                else if (row.status == 88) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service-purchase-auto.status.88')}}" + "</span>";
                                }
                                else if (row.status == 89) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-info \">" + "{{config('module.service-purchase-auto.status.89')}}" + "</span>";
                                }

                                else if (row.status == 9) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.service-purchase-auto.status.9')}}" + "</span>";
                                }

                                else {
                                    return "";
                                }

                            }
                        },

                        {
                            data: 'created_at', title: 'Ngày tạo',
                            render: function (data, type, row) {
                                return row.created_at;
                            }
                        },
                        {
                            data: 'process_at', title: 'Ngày hoàn tất',
                            render: function (data, type, row) {
                                if(row.status == 4){
                                    return row.process_at;
                                }else{
                                    return "";
                                }
                            }
                        },

                        {
                            data: 'author', title: 'Người Order',visible: false,
                            render: function (data, type, row) {
                                return row.author;
                            }
                        },
                        {data: 'action', title: 'Thao tác', orderable: false, searchable: false}

                    ],
                    "drawCallback": function (settings) {
                        var api = this.api();
                        var apiJson = api.ajax.json();
                        var html = apiJson.totalSumary;
                        $('.data_status').html('');
                        $('.data_status').html(html);

                        $('#status').select2();
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

                $('.btn_roblox_psx_huge').on('click', function (e) {
                    e.preventDefault();
                    var allSelected = '';
                    var total = datatable.$('input[type="checkbox"]:checked').length;
                    if (total <= 0) {
                        alert("Vui lòng chọn dòng để thực hiện thao tác");
                        return;
                    }

                    let idkey = $("#group_id option:selected").data('idkey');

                    // if (idkey != 'huge_psx_auto' && idkey != 'roblox_gem_pet' ){
                    //     alert("Vui lòng chọn danh mục");
                    //     return;
                    // }

                    datatable.$('input[type="checkbox"]').each(function (index, elem) {
                        if ($(elem).is(':checked')) {

                            if (allSelected == ''){
                                allSelected = $(elem).attr('rel');
                            }else {
                                allSelected = allSelected + ',' + $(elem).attr('rel');
                            }
                        }
                    })
                    $('#psxModal .id').attr('value', allSelected);
                    $('#psxModal').modal('toggle');
                })

                $('body').on('click', '.btn_rbx_api', function(e){

                    e.preventDefault();
                    var allSelected = '';
                    var total = datatable.$('input[type="checkbox"]:checked').length;
                    if (total <= 0) {
                        alert("Vui lòng chọn dòng để thực hiện thao tác");
                        return;
                    }

                    datatable.$('input[type="checkbox"]').each(function (index, elem) {
                        if ($(elem).is(':checked')) {
                            allSelected = allSelected + $(elem).attr('rel');
                            if (index !== total - 1) {
                                allSelected = allSelected + ',';
                            }
                        }
                    })

                    $('#swichRBX .id').attr('value', allSelected);
                    $('#swichRBX').modal('toggle');
                });


                $('body').on('click', '.btn_daily', function(e){

                    e.preventDefault();
                    var allSelected = '';
                    var total = datatable.$('input[type="checkbox"]:checked').length;
                    if (total <= 0) {
                        alert("Vui lòng chọn dòng để thực hiện thao tác");
                        return;
                    }

                    datatable.$('input[type="checkbox"]').each(function (index, elem) {
                        if ($(elem).is(':checked')) {
                            allSelected = allSelected + $(elem).attr('rel');
                            if (index !== total - 1) {
                                allSelected = allSelected + ',';
                            }
                        }
                    })

                    $('#swichDAILY .id').attr('value', allSelected);
                    $('#swichDAILY').modal('toggle');
                });

                $('.btn_edut_lohang').on('click', function (e) {
                    e.preventDefault();
                    var allSelected = '';
                    var total = datatable.$('input[type="checkbox"]:checked').length;
                    if (total <= 0) {
                        alert("Vui lòng chọn dòng để thực hiện thao tác");
                        return;
                    }

                    datatable.$('input[type="checkbox"]').each(function (index, elem) {
                        if ($(elem).is(':checked')) {
                            allSelected = allSelected + $(elem).attr('rel');
                            if (index !== total - 1) {
                                allSelected = allSelected + ',';
                            }
                        }
                    })
                    $('#editPengirimanModal .id_pengiriman').attr('value', allSelected);
                    $('#editPengirimanModal').modal('toggle');
                })

                $('.btn_order_unit').on('click', function (e) {
                    e.preventDefault();
                    var allSelected = '';
                    var total = datatable.$('input[type="checkbox"]:checked').length;
                    if (total <= 0) {
                        alert("Vui lòng chọn dòng để thực hiện thao tác");
                        return;
                    }

                    let idkey = $("#group_id option:selected").data('idkey');

                    // if (idkey != 'huge_psx_auto' && idkey != 'roblox_gem_pet' ){
                    //     alert("Vui lòng chọn danh mục");
                    //     return;
                    // }

                    datatable.$('input[type="checkbox"]').each(function (index, elem) {
                        if ($(elem).is(':checked')) {

                            if (allSelected == ''){
                                allSelected = $(elem).attr('rel');
                            }else {
                                allSelected = allSelected + ',' + $(elem).attr('rel');
                            }
                        }
                    })
                    $('#unitModal .id').attr('value', allSelected);
                    $('#unitModal').modal('toggle');
                });

                $('label.toggle-vis').on('click', function (e) {
                    e.preventDefault();

                    var input = $(this).find('input');
                    if (input.is(":checked")){
                        input.prop('checked', false);
                    }else{
                        input.prop('checked', true);

                    }
                    // Get the column API object
                    var column = datatable.column($(this).attr('data-column'));

                    // Toggle the visibility
                    column.visible(!column.visible());
                });


                $('#group_id').on('change', function (e) {
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
                    $('.datatable-input').each(function (e) {
                        if($(this).hasClass('select2')){
                            $(this).val('').change();
                        }
                        $(this).val('')
                        datatable.column($(this).data('col-index')).search('', false, false);
                    });

                    $('.choice_status').each(function (k, elem) {
                        $(elem).removeClass('active');
                    });
                    $('.choice_status_fist').addClass('active');

                    datatable.table().draw();
                });

                $('body').on('click','.choice_status',function () {
                    console.log(1111111111111111)
                    let type = $(this).data('type');
                    $('#type').val(type);
                    datatable.draw();
                })

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

                $('.btn-filter-date').click(function (e) {
                    e.preventDefault();
                    var startedAt=$(this).data('started-at');
                    var endeddAt=$(this).data('ended-at');


                    $('#finished_started_at').val(startedAt);
                    $('#finished_ended_at').val(endeddAt);
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

            $("#btn__attribute_tk").on("click", function (e) {
                let id = $('#id').val();
                let request_id = $('#request_id').val();
                let author = $('#author').val();
                let type_information = $('#type_information').val();
                let processor = $('#processor').val();
                let roblox_acc = $('#roblox_acc').val();

                let roblox_acc_old = $('#roblox_acc_old').val();
                let kt_select2_2 = $('#kt_select2_2').val();
                let group_id = $('#group_id').val();
                let group_id2 = $('#group_id2').val();
                let status = $('#status').val();
                let started_at = $('#started_at').val();
                let ended_at = $('#ended_at').val();
                let finished_started_at = $('#finished_started_at').val();
                let finished_ended_at = $('#finished_ended_at').val();
                let check_status = $('#check_status').val();
                let check_status_nrogem = $('#check_status_nrogem').val();
                let check_status_ninjaxu = $('#check_status_ninjaxu').val();
                let check_status_roblox = $('#check_status_roblox').val();
                let payment_type = $('#payment_type').val();
                let type = $('#type').val();

                var url = '/admin/service-purchase-auto/load-attribute-tk';

                $.ajax({
                    type: 'GET',
                    url: url,
                    async:true,
                    cache:false,
                    data: {
                        id:id,
                        request_id:request_id,
                        type_information:type_information,
                        roblox_acc:roblox_acc,
                        roblox_acc_old:roblox_acc_old,
                        check_status:check_status,
                        author:author,
                        payment_type:payment_type,
                        processor:processor,
                        type:type,
                        group_id2:group_id2,
                        group_id:kt_select2_2,
                        status:status,
                        check_status_nrogem:check_status_nrogem,
                        check_status_ninjaxu:check_status_ninjaxu,
                        check_status_roblox:check_status_roblox,
                        started_at:started_at,
                        ended_at:ended_at,
                        finished_started_at:finished_started_at,
                        finished_ended_at:finished_ended_at,
                    },
                    beforeSend: function (xhr) {

                    },
                    success: (data) => {
                        $('.loading').css('display','none');

                        if (data.status == 1){
                            $(".data_attibute_tk").empty().html('');
                            $(".data_attibute_tk").empty().html(data.data);
                            $('#attributeTkModal').modal('show');

                        }

                    },
                    error: function (data) {

                    },
                    complete: function (data) {

                    }
                });
            });

            KTDatatablesDataSourceAjaxServer.init();

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

            $('#refundModal').on('show.bs.modal', function(e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#refundModal .refund_id').attr('value', id);
            });

            $(document).on('click', '.recharge_toggle', function(e){

                var id= $(this).attr('rel');
                $('#rechargeModal .id').attr('value', id);
            });

            $('body').on('click', '.btn_delete_gem_unit_auto', function(e){
                $('#deleteGemUnitAuto').modal('show');
            });



            $(document).on('click', '.recharge_gamepass_toggle', function(e){

                var id= $(this).attr('rel');
                $('#rechargeBuyGamePassModal .id').attr('value', id);
            });

            $("#rechargeBuyGamePassModal form").submit(function(e) {

                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var actionUrl = form.attr('action');



                $("#rechargeBuyGamePassModal button:submit").each(function (index, value) {
                    KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                });

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (data) {
                        if (data.status==1) {
                            toast(data.message);
                            $('#rechargeBuyGamePassModal').modal('toggle');
                            datatable.ajax.reload();
                        } else {
                            $('#rechargeBuyGamePassModal').modal('toggle');
                            toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                        }
                    },
                    error: function (data) {
                        $('#rechargeBuyGamePassModal').modal('toggle');
                        toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function(data) {
                        $("#rechargeBuyGamePassModal button:submit").each(function (index, value) {
                            KTUtil.btnRelease(this);
                        });

                    }

                });

            });

            $("#rechargeModal form").submit(function(e) {

                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var actionUrl = form.attr('action');



                $("#rechargeModal button:submit").each(function (index, value) {
                    KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                });

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (data) {
                        if (data.status==1) {
                            toast(data.message);
                            $('#rechargeModal').modal('toggle');
                            datatable.ajax.reload();
                        } else {
                            $('#rechargeModal').modal('toggle');
                            toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                        }
                    },
                    error: function (data) {
                        $('#rechargeModal').modal('toggle');
                        toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function(data) {
                        $("#rechargeModal button:submit").each(function (index, value) {
                            KTUtil.btnRelease(this);
                        });

                    }

                });

            });

            $("#userModal form").submit(function(e) {

                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var actionUrl = form.attr('action');



                $("#userModal button:submit").each(function (index, value) {
                    KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                });

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (data) {
                        if (data.status==1) {
                            toast(data.message);
                            $('#userModal').modal('toggle');
                            datatable.ajax.reload();
                        } else {
                            $('#userModal').modal('toggle');
                            toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                        }
                    },
                    error: function (data) {
                        $('#userModal').modal('toggle');
                        toast('{{__('Thực hiện thất bại.Vui lòng thử lại')}}', 'error');
                    },
                    complete: function(data) {
                        $("#userModal button:submit").each(function (index, value) {
                            KTUtil.btnRelease(this);
                        });

                    }

                });

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

            $('#status').select2();

            var datatable1;
            var KTDatatablesDataSourceAjaxServer1 = function () {
                var initTable1 = function () {
                    // begin first table
                    datatable1 = $('#kt_datatable_product_modal').DataTable({
                        responsive: true,
                        destroy: true,
                        dom: `
                            <'row'<'col-sm-12'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,

                        // dom: "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
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
                            url: '{{url()->current()}}' + '?rbx_api=1&ajax=1',
                            type: 'GET',
                            data: function (d) {

                            }
                        },
                        buttons: [
                            {
                                "extend": 'excelHtml5',
                                "text": ' <i class="far fa-file-excel icon-md"></i> {{__('Xuất excel')}} ',
                                "action": newexportaction,
                            },
                        ],
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

                            {data: 'rate', title: 'RATE USD'},
                            {data: 'ratio', title: 'TỶ GIÁ'},
                            {data: 'rate_vnd', title: 'RATE VND'},
                            { data: 'accountsCount',title:'ACCOUNTS COUNT'},
                            // { data: 'server',title:'Server'},
                            { data: 'maxInstantOrder',title:'MAX INSTANT ORDER'},
                            { data: 'totalRobuxAmount',title:'TOTAL ROBUX AMOUNT'},
                        ],
                        "drawCallback": function (settings) {
                        }

                    });
                    var filter = function () {
                        var val = $.fn.datatable1.util.escapeRegex($(this).val());
                        datatable1.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                    };
                    $('#kt_search_product_modal').on('click', function (e) {
                        e.preventDefault();
                        var params = {};
                        $('.datatable-input-product-modal').each(function () {
                            var i = $(this).attr('col-index');
                            if (params[i]) {
                                params[i] += '|' + $(this).val();
                            } else {
                                params[i] = $(this).val();
                            }
                        });

                        $.each(params, function (i, val) {
                            // apply search params to datatable
                            datatable1.column(i).search(val ? val : '', false, false);
                        });
                        datatable1.table().draw();
                    });
                    $('#kt_reset_product_modal').on('click', function (e) {
                        e.preventDefault();
                        $('.datatable-input-product-modal').each(function () {
                            $(this).val('');
                            datatable1.column($(this).data('col-index')).search('', false, false);
                        });
                        datatable1.table().draw();
                    });
                    datatable1.on("click", "#btnCheckProduct", function () {
                        $(".ckb_item .label_checkbox_product input[type='checkbox']").prop('checked', this.checked).change();
                    })
                    datatable1.on("change", ".ckb_item .label_checkbox_product input[type='checkbox']", function () {
                        if (this.checked) {
                            var currTr = $(this).closest("tr");
                            datatable1.rows(currTr).select();
                        } else {
                            var currTr = $(this).closest("tr");
                            datatable1.rows(currTr).deselect();
                        }
                    });
                    //function update field
                    datatable1.on("change", ".update_field", function (e) {
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
                        initTable1();
                    },

                };
            }();

            function showRbx(){
                KTDatatablesDataSourceAjaxServer1.init();
            }

            $('body').on('click','.btn_rbx',function(e){
                e.preventDefault();
                showRbx();
                $('#productModal').modal('show');

            });
        });

    </script>



@endsection
