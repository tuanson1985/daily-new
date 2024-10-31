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
                                <span class="nav-text font-size-lg">Trả hàng/Hoàn tiền</span>
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
{{--            @if(Auth::user()->id == 301 || Auth::user()->id == 5551)--}}
{{--                <form class="mb-10" action="{{route('admin.service-purchase.delete-all')}}" method="POST">--}}
{{--                    @csrf--}}
{{--                <button class="btn btn-danger btn-secondary--icon" type="submit">--}}
{{--                                <span>--}}
{{--                                    <i class="flaticon-folder-2"></i>--}}
{{--                                    <span>delete all</span>--}}
{{--                                </span>--}}
{{--                </button>--}}
{{--                </form>--}}
{{--            @endif--}}
        </div>

        <div class="card-body">
            <!--begin: Search Form-->
            <form class="mb-10" action="{{route('admin.service-purchase.export-excel')}}" method="POST">
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
                            <input type="text" class="form-control datatable-input" id="id" name="id" value="{{request('id')}}"  placeholder="{{__('ID hoặc Request ID')}}"     >
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


                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="id_pengiriman" name="id_pengiriman" value="{{request('id_pengiriman')}}"   placeholder="{{__('Tìm kiếm id Lo hang')}}">
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
                            <input type="text" class="form-control datatable-input"   id="processor" name="processor" value="{{request('processor')}}"
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
                            <input type="text" class="form-control datatable-input"   id="work_name" name="work_name" value="{{request('work_name')}}"
                                   placeholder="{{__('Tên công việc')}}">
                        </div>
                    </div>

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
                    @if(isset($params_error) && !empty($params_error) && count($params_error))
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select name="mistake_error_by" id="mistake_error_by" class="form-control datatable-input">
                                <option value="">Tất cả lý do từ chối</option>
                                @foreach($params_error as $params)
                                    <option value="{{ $params }}">{{ $params }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3 data_status">
{{--                        @include('admin.service.purchase.widget.__status')--}}
                    </div>


                    @if(!empty(config('module.'.$module.'.position')))

                        {{--position--}}
                        <div class="form-group col-12 col-sm-6 col-lg-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                </div>
                                {{Form::select('position',[''=>'-- '.__('Tất cả vị trí').' --']+config('module.'.$module.'.position'),old('status', isset($data) ? $data->position : null),array('id'=>'position','class'=>'form-control datatable-input',))}}
                            </div>
                        </div>
                    @endif

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('type_information',[''=>'-- Loại tài khoản --']+config('module.user-qtv.type_information'),old('type_information', isset($data) ? $data->type_information : null),array('id'=>'type_information','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>

                    {{-- CTV --}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('type_information_ctv',[''=>'-- Tất cả loại ctv --']+config('module.user-qtv.type_information_ctv'),old('type_information_ctv', isset($data) ? $data->type_information_ctv : null),array('id'=>'type_information_ctv','class'=>'form-control datatable-input',))}}
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
                            <input type="text" name="ended_at" id="ended_at" value="{{request('ended_at')}}" autocomplete="off"
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

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input"  id="top_limit" name="top_limit"
                                   placeholder="{{__('Top hiển thị')}}">
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

                        <button class="btn btn-info btn-primary--icon btn-filter-processor" data-filter="{{Auth::user()->username}}">
                            <span>
                                <i class="la la-user"></i>
                                <span>Đơn đã nhận</span>
                            </span>
                        </button>&#160;&#160;
                        <input type="hidden" value="">
                        <button class="btn btn-danger btn-secondary--icon" type="submit">
                                <span>
                                    <i class="flaticon-folder-2"></i>
                                    <span>Xuất Excel</span>
                                </span>
                        </button>

                        <button class="btn btn-secondary btn-secondary--icon" id="kt_reset">
                            <span>
                                <i class="la la-close"></i>
                                <span>Reset</span>
                            </span>
                        </button>


                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-auto">
                        <div class="btn-group m-btn-group" role="group" aria-label="...">
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date" >Hôm nay</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::yesterday()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::yesterday()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Hôm qua</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng này</a>
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->startOfMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->endOfMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng trước</a>
                        </div>
                    </div>

                </div>

                <div class="row mt-5">
                    <div class="col-auto">
                        <button type="button" id="btn__errors" class="btn btn-primary">Lý do lỗi</button>
                    </div>
                    @if (Auth::user()->can('service-purchase-top-attribute'))
                        <div class="col-auto">
                            <button type="button" id="btn__attribute" class="btn btn-primary">Top sản phẩm</button>
                        </div>
                    @endif
                    <div class="col-auto">
                        <button type="button" id="btn__attribute_tk" class="btn btn-primary">Thống kê</button>
                    </div>
                    <div class="col-auto ml-auto">
                        @if(Auth::user()->can('service-purchase-edit-pengiriman'))
                            <button class="btn btn-primary btn_edut_lohang"> Edit lô hàng</button>
                        @endif
                    </div>
                </div>
            </form>


            <!--begin: Search Form-->

            <!--begin: Datatable-->
            <table class="table table-bordered table-hover table-checkable " id="kt_datatable">
            </table>
            <!--end: Datatable-->
        </div>
    </div>

    {{---------------all modal controll-------}}

    <div class="modal fade" id="recallbackModal">
        <div class="modal-dialog">
            <div class="modal-content">

                {{Form::open(array('route'=>array('admin.service-purchase.recallback',0),'class'=>'form-horizontal','method'=>'POST'))}}
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

    <!-- error item Modal -->
    <div class="modal fade" id="errorModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Thống kê lý do từ chối đơn hàng')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                    <b>Lỗi do: </b>
                                </div>
                                <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                    Account Password Incorrect: <b id="incorrect">0</b>
                                </div>
                                <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                    Verify 3 Recent Games: <b id="recent_games">0</b>
                                </div>

                                <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                    Please turn off 2-Step Verification: <b id="verification">0</b>
                                </div>

                                <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                    An error occurred, please try again: <b id="try_again">0</b>
                                </div>

                                <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                    This product is out of stock: <b id="stock">0</b>
                                </div>

                                <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                    Your Product Already Exists: <b id="exists">0</b>
                                </div>

                                <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                    Password Reset Error: <b id="reset_error">0</b>
                                </div>

                                <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                    Please Turn Off 2-step Verification And Make A Red Emal: <b id="red_emal">0</b>
                                </div>

                                <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                                    Not Reach Required Level: <b id="required_level">0</b>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto" style="margin-left: auto">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- error item Modal -->
    <div class="modal fade" id="attributeModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Top vật phẩm')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body data_attibute">
{{--                    @include('admin.service.purchase.widget.__attribute')--}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- error item Modal -->
    <div class="modal fade" id="attributeTkModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> {{__('Thống kê dịch vụ thủ công')}}</h5>
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

    <!-- refund item Modal -->
    <div class="modal fade" id="refundModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase.refund',0),'class'=>'form-horizontal','id'=>'form-refund','method'=>'POST'))}}
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

    <!-- refund item Modal -->

    <div class="modal fade" id="refundDeleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase.refund-delete',0),'class'=>'form-horizontal','id'=>'form-refund-delete','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" class="refund_delete_id" value=""/>
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
                                <option value="0">Đã bán chờ xác nhận</option>
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-success m-btn m-btn--custom btn-submit-refund-delete" data-form="form-refund-delete">{{__('Xác nhận')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPengirimanModal" tabindex="-1"
         role="basic" aria-hidden="true">
        <div style="text-align:initial;" class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase.pengiriman-all',0),'class'=>'m-form','method'=>'POST'))}}
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
                            <div class="form-group col-md-12">
                                <label for="edit_account_pengiriman"> Account lô hàng:</label>
                                <input type="text"
                                       required
                                       class="form-control" id="edit_account_pengiriman" name="edit_account_pengiriman" value=""   placeholder="{{__('Account lô hàng')}}">
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
                            d.position = $('#position').val();
                            d.group_id = $('#kt_select2_2').val();
                            d.group_id2 = $('#group_id2').val();
                            d.shop_id = $('.shop_id').val();
                            d.author = $('#author').val();
                            d.processor = $('#processor').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.mistake_error_by = $('#mistake_error_by').val();
                            d.type_information = $('#type_information').val();
                            d.type_information_ctv = $('#type_information_ctv').val();
                            d.account_type = $('#account_type').val();
                            d.finished_started_at = $('#finished_started_at').val();
                            d.finished_ended_at = $('#finished_ended_at').val();
                            d.request_id = $('#request_id').val();
                            d.arrange = $('#arrange').val();
                            d.work_name = $('#work_name').val();
                            d.type = $('#type').val();
                            d.id_pengiriman = $('#id_pengiriman').val();

                        }
                    },

                    buttons: [
                        {
                            text: '<select style="width: 230px" class="form-control datatable-input" name="arrange" id="arrange" data-placeholder="Chọn danh mục">\n' +
                                '                            <option value="1">Thời gian tạo cũ nhất</option>\n' +
                                '                            <option value="0">Thời gian tạo mới nhất</option>\n' +
                                '                            <option value="2">Thời gian cập nhật mới nhất</option>\n' +
                                '                            <option value="3">Thời gian cập nhật cũ nhất</option>\n' +
                                '                            <option value="4">Giá tiền lớn nhất</option>\n' +
                                '                            <option value="5">Giá tiền nhỏ nhất</option>\n' +
                                '                        </select>',
                            action: function (e) {
                                e.preventDefault();

                            }
                        },
                            @if(Auth::user()->can('service-purchase-recallback'))
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
                            data: 'item_ref',name:'item_ref.title', title: 'Dịch vụ',
                            render: function (data, type, row) {

                                if(data!==null && data.title!==null){
                                    return data.title
                                }
                                else{
                                    return "";
                                }
                            }
                        },
                        // {
                        //     data: 'information_ctv', title: 'Loại tài khoản CTV',
                        //     render: function (data, type, row) {
                        //         let html ='';
                        //         if (row.information_ctv){
                        //             html = row.information_ctv;
                        //         }
                        //         return html;
                        //     }
                        // },
                        @if(Auth::user()->can('show-information'))
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
                        @endif
                        {
                            data: 'work_name',name:'params', title: 'Tên công việc',
                            render: function (data, type, row) {

                                return row.work_name;
                            }
                        },
                        @if(Auth::user()->can('service-purchase-view-price'))
                        {
                            data: 'price',name:'price', title: 'Trị giá',
                            render: function (data, type, row) {
                                return number_format(row.price,'.');

                            }
                        },
                        @endif

                        @if(Auth::user()->can('service-purchase-view-price-ctv'))
                        {
                            data: 'price_ctv', title: 'Trị giá dành cho CTV',
                            render: function (data, type, row) {
                                var temp="";
                                temp+="<b>"+number_format(row.price_ctv,'.')+"</b>" +"<br />";
                                temp+="<span  style=\"font-style: italic;\">"
                                    + "- CK dự kiến: "+row.ratio +"%<br />";
                                if(row.status!=4){
                                    temp+= "- Số tiền dự kiến: "+"<b>"+number_format(Math.ceil((row.price_ctv*row.ratio/100)),'.')+"</b>";
                                }
                                else{
                                    temp+= "- Số tiền dự kiến: "+"<b>"+number_format((row.real_received_amount),'.')+"</b>";
                                }

                                temp+="<br />";
                                    +"</span>";
                                return temp;

                            }
                        },
                        @endif
                        {
                            data: 'ratio', title: 'Chiết khấu dành cho CTV',
                            render: function (data, type, row) {
                                return row.ratio;
                            }
                        },
                        {
                            data: 'real_received_price_ctv', title: 'Số tiền CTV Nhận',
                            render: function (data, type, row) {
                                if(row.real_received_price_ctv==null){
                                    return "";
                                }
                                return "<b>"+number_format(row.real_received_price_ctv,'.')+"</b>";
                            }
                        },
                        @if(Auth::user()->can('service-purchase-view-profit'))
                        {
                            data: 'profit', title: 'Lợi nhuận',
                            render: function (data, type, row) {
                                if(row.profit==null|| row.profit==0){
                                    return "";
                                }
                                return "<b>"+number_format(row.profit,'.')+"</b>";
                            }
                        },
                        @endif
                        {
                            data: 'content',name:'params', title: '{{__('Lý do từ chối')}}',
                            render: function (data, type, row) {
                                if (row.status == 3){
                                    return row.content;
                                }
                                return '';
                            }
                        },
                        {
                            data: 'status', title: 'Trạng thái',
                            render: function (data, type, row) {

                                if (row.status == 0) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-danger \">" + "{{config('module.service-purchase.status.0')}}" + "</span>";
                                }
                                else if (row.status == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-warning \">" + "{{config('module.service-purchase.status.1')}}" + "</span>";

                                } else if (row.status == 2) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-info \">" + "{{config('module.service-purchase.status.2')}}" + "</span>";
                                }
                                else if (row.status == 10) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-success \">" + "{{config('module.service-purchase.status.10')}}" + "</span>";
                                }
                                else if (row.status == 11) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-info \">" + "{{config('module.service-purchase.status.11')}}" + "</span>";
                                }
                                else if (row.status == 12) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service-purchase.status.12')}}" + "</span>";
                                }
                                else if (row.status == 3) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service-purchase.status.3')}}" + "</span>";
                                }
                                else if (row.status == 4) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-success \">" + "{{config('module.service-purchase.status.4')}}" + "</span>";
                                }
                                else if (row.status == 5) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.service-purchase.status.5')}}" + "</span>";
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
                            data: 'reception_at', title: 'Ngày nhận đơn',
                            render: function (data, type, row) {
                                if(row.reception_at){
                                    return row.reception_at;
                                }
                                else{
                                    return "";
                                }
                            }
                        },

                        {
                            data: 'process_at', title: 'Ngày hoàn tất',
                            render: function (data, type, row) {
                                if(row.status == 4){
                                    return row.process_at;
                                }else if (row.status == 11 || row.status == 10){
                                    return row.process_at;
                                }
                                else{
                                    return "";
                                }
                            }
                        },
                        @if(Auth::user()->account_type!=3)
                        {
                            data: 'author', title: 'Người Order',
                            render:   function (data, type, row){
                                return row.author;
                            }

                        },
                        @endif

                        @if(Auth::user()->can('service-purchase-view-processor'))
                        {
                            data: 'processor', title: 'Người nhận',
                            render: function (data, type, row) {
                                return row.processor;
                            }
                        },
                        @endif


                        {data: 'action', title: 'Thao tác', orderable: false, searchable: false}

                    ],
                    "drawCallback": function (settings) {

                        $('#arrange').select2();
                        $('#arrange').parent().parent().css('background','none');
                        $('#arrange').parent().parent().css('border','none');
                        $('#arrange').parent().parent().css('padding','0');

                        var api = this.api();
                        var apiJson = api.ajax.json();
                        var html = apiJson.totalSumary;
                        $('.data_status').html('');
                        $('.data_status').html(html);

                        $('#status').select2();

                        // var rows = api.rows({page: 'current'}).nodes();
                        //
                        // $('#total_record').text(number_format(apiJson.recordsFiltered,'.'));
                        // $('#total_price').text(number_format(apiJson.totalSumary.total_price,'.'));
                        // $('#total_real_received_price_ctv').text(number_format(apiJson.totalSumary.total_real_received_price_ctv,'.'));
                        // $('#total_profit').text(number_format(apiJson.totalSumary.total_profit,'.'));
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
                $('.btn-filter-processor').click(function (e) {

                    e.preventDefault();
                    var filter=$(this).data('filter');
                    $('#processor').val(filter);
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


            $(".btn_delete_all").on("click", function (e) {
                $('#deleteAll').modal('show');

            });
            $("#btn__errors").on("click", function (e) {
                $('#errorModal').modal('show');

            });
            $("#btn__attribute").on("click", function (e) {

                let id = $('#id').val();
                let request_id = $('#request_id').val();
                let group_id = $('#kt_select2_2').val();
                let group_id2 = $('#group_id2').val();
                let work_name = $('#work_name').val();
                let position = $('#position').val();
                let author = $('#author').val();
                let processor = $('#processor').val();
                let kt_select2_2 = $('#kt_select2_2').val();
                let mistake_error_by = $('#mistake_error_by').val();
                let status = $('#status').val();
                let type_information = $('#type_information').val();
                let type_information_ctv = $('#type_information_ctv').val();
                let started_at = $('#started_at').val();
                let ended_at = $('#ended_at').val();
                let finished_started_at = $('#finished_started_at').val();
                let finished_ended_at = $('#finished_ended_at').val();
                let top_limit = $('#top_limit').val();
                let type = $('#type').val();
                let id_pengiriman = $('#id_pengiriman').val();

                var url = '/admin/service-purchase/load-top-attribute';

                $.ajax({
                    type: 'GET',
                    url: url,
                    async:true,
                    cache:false,
                    data: {
                        id:id,
                        request_id:request_id,
                        group_id:group_id,
                        group_id2:group_id2,
                        position:position,
                        work_name:work_name,
                        mistake_error_by:mistake_error_by,
                        author:author,
                        processor:processor,
                        kt_select2_2:kt_select2_2,
                        status:status,
                        type_information:type_information,
                        type_information_ctv:type_information_ctv,
                        started_at:started_at,
                        ended_at:ended_at,
                        finished_started_at:finished_started_at,
                        finished_ended_at:finished_ended_at,
                        top_limit:top_limit,
                        type:type,
                        id_pengiriman:id_pengiriman,
                    },
                    beforeSend: function (xhr) {

                    },
                    success: (data) => {
                        $('.loading').css('display','none');

                        if (data.status == 1){
                            // let html = '<input type="hidden" value="0" class="check_sms"/>';
                            // html += '<span style="font-size: 16px;color: #f42c41;font-weight: bold">' + data.message + '<span/>';
                            //
                            $(".data_attibute").empty().html('');
                            $(".data_attibute").empty().html(data.data);
                            $('#attributeModal').modal('show');

                        }

                    },
                    error: function (data) {

                    },
                    complete: function (data) {

                    }
                });
            });

            $("#btn__attribute_tk").on("click", function (e) {
                let id = $('#id').val();
                let request_id = $('#request_id').val();
                let group_id = $('#kt_select2_2').val();
                let group_id2 = $('#group_id2').val();
                let work_name = $('#work_name').val();
                let position = $('#position').val();
                let author = $('#author').val();
                let processor = $('#processor').val();
                let kt_select2_2 = $('#kt_select2_2').val();
                let mistake_error_by = $('#mistake_error_by').val();
                let status = $('#status').val();
                let type_information = $('#type_information').val();
                let type_information_ctv = $('#type_information_ctv').val();
                let started_at = $('#started_at').val();
                let ended_at = $('#ended_at').val();
                let finished_started_at = $('#finished_started_at').val();
                let finished_ended_at = $('#finished_ended_at').val();
                let type = $('#type').val();
                let id_pengiriman = $('#id_pengiriman').val();
                var url = '/admin/service-purchase/load-attribute-tk';

                $.ajax({
                    type: 'GET',
                    url: url,
                    async:true,
                    cache:false,
                    data: {
                        id:id,
                        request_id:request_id,
                        group_id:group_id,
                        group_id2:group_id2,
                        position:position,
                        work_name:work_name,
                        mistake_error_by:mistake_error_by,
                        author:author,
                        processor:processor,
                        kt_select2_2:kt_select2_2,
                        status:status,
                        type_information:type_information,
                        type_information_ctv:type_information_ctv,
                        started_at:started_at,
                        ended_at:ended_at,
                        finished_started_at:finished_started_at,
                        finished_ended_at:finished_ended_at,
                        id_pengiriman:id_pengiriman,
                        type:type,
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

            $('#refundModal').on('show.bs.modal', function(e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#refundModal .refund_id').attr('value', id);
            });

            $('#refundDeleteModal').on('show.bs.modal', function(e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#refundDeleteModal .refund_delete_id').attr('value', id);
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

            $("#arrange").on("change", function (e) {

                datatable.draw();

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

            $('.huyTranTCModal').click(function (e) {
                e.preventDefault();
                $('#huyTranTCModal').modal('show');
            });

            $('.huyTranDModal').click(function (e) {
                e.preventDefault();
                $('#huyTranDModal').modal('show');
            });

            $('body').on('click','.choice_status',function () {
                let type = $(this).data('type');
                $('#type').val(type);
                datatable.draw();
            })

        });





    </script>



@endsection
