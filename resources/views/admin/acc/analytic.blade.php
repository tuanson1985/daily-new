{{-- Extends layout --}}
@extends('admin._layouts.master')

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
            <form class="mb-10">
                <div class="row">
                    {{--author--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="la la-user glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" name="author" value="{{ $_GET['author']??null }}" placeholder="{{__('Tên người bán')}}">
                        </div>
                    </div>
                    {{--author--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="la la-user glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" name="customer" value="{{ $_GET['customer']??null }}" placeholder="{{__('Tên người mua')}}">
                        </div>
                    </div>
                    {{--started_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="started_at" id="started_at" autocomplete="off" value="{{ $_GET['started_at']??null }}"
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
                            <input type="text" name="ended_at" id="ended_at" autocomplete="off" value="{{ $_GET['ended_at']??null }}"
                                   class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian kết thúc')}}" data-toggle="datetimepicker">

                        </div>
                    </div>

                    {{--group_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <select class="form-control datatable-input datatable-input-select selectpicker" name="group_id" title="-- {{__('Tất cả danh mục')}} --">
                                <option value=''>-- Không chọn --</option>
                                @include('admin.acc.widget.category-select', ['data' => $properties, 'selected' => [$_GET['group_id']??null], 'stSpecial' => ''])
                            </select>

                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <a class="btn btn-secondary" href="{{ url()->current() }}?{{ http_build_query(array_merge($input, [
                            'started_at' => \Carbon\Carbon::now()->subMonth()->startOfMonth()->format('d/m/Y 00:00:00'),
                            'ended_at' => \Carbon\Carbon::now()->startOfMonth()->format('d/m/Y 00:00:00')
                        ])) }}">Tháng trước</a>
                        <a class="btn btn-secondary" href="{{ url()->current() }}?{{ http_build_query(array_merge($input, [
                            'started_at' => \Carbon\Carbon::now()->startOfMonth()->format('d/m/Y 00:00:00'),
                            'ended_at' => \Carbon\Carbon::now()->format('d/m/Y 23:59:59')
                        ])) }}">Tháng này</a>
                        <a class="btn btn-info" href="{{ url()->current() }}?{{ http_build_query(array_merge($input, [
                            'started_at' => \Carbon\Carbon::now()->subDay()->format('d/m/Y 00:00:00'),
                            'ended_at' => \Carbon\Carbon::now()->format('d/m/Y 00:00:00')
                        ])) }}">Hôm qua</a>
                        <a class="btn btn-info" href="{{ url()->current() }}?{{ http_build_query(array_merge($input, [
                            'started_at' => \Carbon\Carbon::now()->format('d/m/Y 00:00:00'),
                            'ended_at' => \Carbon\Carbon::now()->format('d/m/Y 23:59:59')
                        ])) }}">Hôm nay</a>
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
                        <a class="btn btn-secondary btn-secondary--icon" href="{{ url()->current() }}">
                            <span>
                                <i class="la la-close"></i>
                                <span>Reset</span>
                            </span>
                        </a>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bolder text-dark">Tổng quan</h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-2">
                            <div class="d-flex flex-wrap align-items-center mb-10 border-bottom">
                                <!--begin::Title-->
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">
                                        Tổng số nick đã bán
                                    </a>
                                </div>
                                <!--end::Title-->
                                <!--begin::Info-->
                                <div class="d-flex align-items-center py-lg-0 py-2">
                                    <div class="d-flex flex-column text-right">
                                        <span class="text-dark-75 font-weight-bolder font-size-h4">{{ number_format($result['count']) }}</span>
                                    </div>
                                </div>
                                <!--end::Info-->
                            </div>
                            <div class="d-flex flex-wrap align-items-center mb-10 border-bottom">
                                <!--begin::Title-->
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">
                                        Doanh thu (Giá gốc)
                                    </a>
                                </div>
                                <!--end::Title-->
                                <!--begin::Info-->
                                <div class="d-flex align-items-center py-lg-0 py-2">
                                    <div class="d-flex flex-column text-right">
                                        <span class="text-dark-75 font-weight-bolder font-size-h4">{{ number_format($result['price']) }}</span>
                                    </div>
                                </div>
                                <!--end::Info-->
                            </div>
                            <div class="d-flex flex-wrap align-items-center mb-10 border-bottom">
                                <!--begin::Title-->
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">
                                        Doanh thu của CTV (CTV hưởng)
                                    </a>
                                </div>
                                <!--end::Title-->
                                <!--begin::Info-->
                                <div class="d-flex align-items-center py-lg-0 py-2">
                                    <div class="d-flex flex-column text-right">
                                        <span class="text-dark-75 font-weight-bolder font-size-h4">{{ number_format($result['amount_ctv']) }}</span>
                                    </div>
                                </div>
                                <!--end::Info-->
                            </div>
                            @if(auth()->user()->hasRole('admin') || (auth()->user()->account_type == 1 && auth()->user()->hasPermissionTo('acc-history')))
                            <div class="d-flex flex-wrap align-items-center mb-10 border-bottom">
                                <!--begin::Title-->
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">
                                        Doanh thu (Giá bán tại shop)
                                    </a>
                                </div>
                                <!--end::Title-->
                                <!--begin::Info-->
                                <div class="d-flex align-items-center py-lg-0 py-2">
                                    <div class="d-flex flex-column text-right">
                                        <span class="text-dark-75 font-weight-bolder font-size-h4">{{ number_format($result['amount_total']) }}</span>
                                    </div>
                                </div>
                                <!--end::Info-->
                            </div>
                            <div class="d-flex flex-wrap align-items-center mb-10 border-bottom">
                                <!--begin::Title-->
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <a href="#" class="text-success font-weight-bolder text-hover-primary font-size-lg">
                                        Lợi nhuận
                                    </a>
                                </div>
                                <!--end::Title-->
                                <!--begin::Info-->
                                <div class="d-flex align-items-center py-lg-0 py-2">
                                    <div class="d-flex flex-column text-right">
                                        <span class="text-success font-weight-bolder font-size-h4">{{ number_format($result['amount_total'] - $result['amount_ctv']) }}</span>
                                    </div>
                                </div>
                                <!--end::Info-->
                            </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
@endsection
@section('scripts')
@endsection
