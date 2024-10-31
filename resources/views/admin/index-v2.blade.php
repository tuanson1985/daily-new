{{-- Extends layout --}}
@extends('admin._layouts.master')
{{-- Content --}}
@section('content')
    @php
        $year = Carbon\Carbon::now()->year;
        $month = Carbon\Carbon::now()->month;
    @endphp
    <div class="row">
        <div class="col-xl-12">
            <!--begin::Nav Panel Widget 1-->
            <div class="card card-custom gutter-b">
                <!--begin::Body-->
                <div class="card-body">
                    <!--begin::Nav Tabs-->
                    <div class="swiper swiper-tablist">
                        <ul class="nav swiper-wrapper nav-danger nav-pills" role="tablist" style="flex-wrap: nowrap">
                            <!--begin::Item-->
                            <li class="nav-item swiper-slide">
                                <a class="nav-link border  d-flex flex-grow-1 rounded flex-column align-items-center active"
                                   data-toggle="pill" href="#widget-tab-1">
                                    <span class="nav-icon py-2 w-auto">
																<span class="svg-icon svg-icon-3x">
																	<!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
																	<svg xmlns="http://www.w3.org/2000/svg"
                                                                         xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                         width="24px" height="24px" viewBox="0 0 24 24"
                                                                         version="1.1">
																		<g stroke="none" stroke-width="1" fill="none"
                                                                           fill-rule="evenodd">
																			<rect x="0" y="0" width="24"
                                                                                  height="24"></rect>
																			<rect fill="#000000" opacity="0.3" x="13"
                                                                                  y="4" width="3" height="16"
                                                                                  rx="1.5"></rect>
																			<rect fill="#000000" x="8" y="9" width="3"
                                                                                  height="11" rx="1.5"></rect>
																			<rect fill="#000000" x="18" y="11" width="3"
                                                                                  height="9" rx="1.5"></rect>
																			<rect fill="#000000" x="3" y="13" width="3"
                                                                                  height="7" rx="1.5"></rect>
																		</g>
																	</svg>
                                                                    <!--end::Svg Icon-->
																</span>
                                    </span>
                                    <span
                                        class="nav-text font-size-lg py-2 font-weight-bold text-center">Tổng sản lượng</span>
                                </a>
                            </li>
                            <!--end::Item-->
                        </ul>
                    </div>
                    <!--end::Nav Tabs-->
                </div>
            </div>
            <div class="card gutter-b border-0 sticky-custom" style="--top:136px;--top-mobile:66px" id="card-filter">
                <div class="card-body p-5">
                    <div class="row">
                        <div class="col-12 col-lg-3 d-flex c-pb-lg-16">
                            <div class="font-weight-boldest text-nowrap c-my-auto mr-2">Khung thời gian:</div>
                            <div class="dropdown w-100" data-value="today" id="select-time">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle w-100"
                                        id="dropdown-time" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false" data-offset="10,20">
                                    {{ __('Hôm nay') }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdown-time">
                                    <a class="dropdown-item" href="javascript:;"
                                       data-value="today">{{ __('Hôm nay') }}</a>
                                    <a class="dropdown-item" href="javascript:;"
                                       data-value="7-day">{{ __('7 Ngày qua') }}</a>
                                    <a class="dropdown-item" href="javascript:;"
                                       data-value="this-month">{{ __('Tháng này') }}</a>
                                    <a class="dropdown-item" href="javascript:;"
                                       data-value="this-year">{{ __('Năm nay') }}</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:;"
                                       data-value="day">{{ __('Theo ngày') }}</a>
                                    <a class="dropdown-item" href="javascript:;"
                                       data-value="week">{{ __('Theo tuần') }}</a>
                                    <a class="dropdown-item" href="javascript:;"
                                       data-value="month">{{ __('Theo tháng') }}</a>
                                    <a class="dropdown-item" href="javascript:;"
                                       data-value="year">{{ __('Theo năm') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 d-none" id="time-day">
                            <div class="form-group mb-0 c-pb-lg-16">
                                <input type="text" class="form-control form-control-sm" readonly
                                       placeholder="Chọn ngày"/>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 d-none" id="time-week">
                            <div class="form-group mb-0 c-pb-lg-16">
                                <input type="text" class="form-control form-control-sm" readonly
                                       placeholder="Chọn tuần"/>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 d-none" id="time-month">
                            <div class="form-group mb-0 c-pb-lg-16">
                                <input type="text" class="form-control form-control-sm" readonly
                                       placeholder="Chọn tháng"/>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 d-none" id="time-year">
                            <div class="form-group mb-0 c-pb-lg-16">
                                <input type="text" class="form-control form-control-sm" readonly
                                       placeholder="Chọn năm"/>
                            </div>
                        </div>
                        <div class="col-lg-3 col-12">
                            <div class="d-flex">
                                <button type="button" class="btn btn-sm btn-success" id="submit-data-filter"><i
                                        class="flaticon-search"></i>
                                    Tìm kiếm
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-light toggle-filter-mobile d-inline-flex d-lg-none"><i
                                        class="flaticon-close"></i>
                                    Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--begin::Nav Content-->
            <div class="tab-content m-0 p-0">
                <div class="tab-pane active" id="widget-tab-1" role="tabpanel">
                    <div class="row">
                        <div class="col-xl-8">
                            @if(auth()->user()->can('dashboard-revenue-overview'))
                                <div class="card card-custom gutter-b card-stretch">
                                    <!--begin::Beader-->
                                    <div class="card-header py-5">
                                        <h3 class="card-title font-weight-bolder">Tổng quan doanh thu</h3>
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card-body p-0 d-flex flex-column overlay-block">
                                        <div class="spinner spinner-track spinner-primary spinner-lg center-both"
                                             style="z-index: 1"></div>
                                        <!--begin::Stats-->
                                        <div id="generality_chart" class="d-flex justify-content-center"></div>
                                    </div>
                                    <!--end::Body-->
                                </div>
                            @endif
                        </div>
                        <div class="col-xl-4">
                            <!--begin::Mixed Widget 13-->
                            @if(auth()->user()->can('dashboard-revenue-ratio'))
                                <div class="card card-custom gutter-b card-stretch">
                                    <!--begin::Beader-->
                                    <div class="card-header py-5">
                                        <h3 class="card-title font-weight-bolder">Tỉ trọng doanh thu</h3>
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card-body p-0 d-flex flex-column">
                                        <!--begin::Stats-->
                                        <div class="spinner spinner-track spinner-primary spinner-lg center-both"
                                             style="z-index: 1"></div>
                                        <div id="chart_density_turnover"></div>
                                    </div>
                                    <!--end::Body-->
                                </div>
                        @endif
                        <!--end::Mixed Widget 13-->
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-xl-4">
                            @if(auth()->user()->can('dashboard-service'))
                                <div class="card card-custom gutter-b card-stretch">
                                    <!--begin::Beader-->
                                    <div class="card-header py-5">
                                        <h3 class="card-title font-weight-bolder">Dịch vụ thủ công</h3>
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card-body p-0 d-flex flex-column overlay-block">
                                        <div class="spinner spinner-track spinner-primary spinner-lg center-both"
                                             style="z-index: 1"></div>
                                        <!--begin::Stats-->
                                        <div id="chart_service" class="d-flex justify-content-center"></div>
                                        <div class="card-spacer pt-5 bg-white flex-grow-1">
                                            <!--begin::Row-->
                                            <div class="separator separator-solid my-7"></div>

                                            <div class="row row-paddingless">
                                                <div class="col-12 col-lg-6 mb-8 d-flex">
                                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                                        <span class="mr-4">
                                                            <i class="flaticon-share icon-2x text-muted font-weight-bold"></i>
                                                        </span>
                                                        <div class="d-flex flex-column text-dark-75">
                                                            <span class="font-weight-bolder font-size-sm">Số giao dịch phát sinh</span>
                                                            <span class="font-weight-bolder font-size-h5"
                                                                  id="total_service_happens">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6 mb-8 d-flex">
                                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                                        <span class="mr-4">
                                                            <i class="flaticon-list-3 icon-2x text-muted font-weight-bold"></i>
                                                        </span>
                                                        <div class="d-flex flex-column text-dark-75">
                                                            <span class="font-weight-bolder font-size-sm">Khách hàng thanh toán thành công</span>
                                                            <span class="font-weight-bolder font-size-h5"
                                                                  id="total_service_paid">0</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-lg-6 mb-8 d-flex">
                                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                                        <span class="mr-4">
                                                            <i class="flaticon-line-graph icon-2x text-muted font-weight-bold"></i>
                                                        </span>
                                                        <div class="d-flex flex-column text-dark-75">
                                                            <span class="font-weight-bolder font-size-sm">Doanh thu thành công</span>
                                                            <span class="font-weight-bolder font-size-h5"
                                                                  id="total_service_turnover_success">0</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{--                                                    <div class="col-12 col-lg-6 mb-8 d-flex">--}}
                                                {{--                                                        <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">--}}
                                                {{--                                                        <span class="mr-4">--}}
                                                {{--                                                            <i class="flaticon-user-ok icon-2x text-muted font-weight-bold"></i>--}}
                                                {{--                                                        </span>--}}
                                                {{--                                                            <div class="d-flex flex-column text-dark-75">--}}
                                                {{--                                                                <span class="font-weight-bolder font-size-sm">Doanh thu CTV khi hoàn tất</span>--}}
                                                {{--                                                                <span class="font-weight-bolder font-size-h5"--}}
                                                {{--                                                                      id="total_price_ctv_service">0</span>--}}
                                                {{--                                                            </div>--}}
                                                {{--                                                        </div>--}}
                                                {{--                                                    </div>--}}

                                                <div class="col-12 col-lg-6 mb-8 d-flex">
                                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                                        <span class="mr-4">
                                                            <i class="flaticon-price-tag icon-2x text-muted font-weight-bold"></i>
                                                        </span>
                                                        <div class="d-flex flex-column text-dark-75">
                                                            <span class="font-weight-bolder font-size-sm">Giá vốn đơn hoàn tất</span>
                                                            <span class="font-weight-bolder font-size-h5"
                                                                  id="total_complete_price_service">0</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-lg-6 mb-8 d-flex">
                                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                                        <span class="mr-4">
                                                            <i class="flaticon-coins icon-2x text-muted font-weight-bold"></i>
                                                        </span>
                                                        <div class="d-flex flex-column text-dark-75">
                                                            <span class="font-weight-bolder font-size-sm">Lợi nhuận đơn hoàn tất</span>
                                                            <span class="font-weight-bolder font-size-h5"
                                                                  id="total_price_profit_service">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Body-->
                                </div>
                            @endif
                        </div>
                        <div class="col-xl-4">
                            @if(auth()->user()->can('dashboard-service-auto'))
                                <div class="card card-custom gutter-b card-stretch">
                                    <!--begin::Beader-->
                                    <div class="card-header py-5">
                                        <h3 class="card-title font-weight-bolder">Dịch vụ tự động</h3>
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card-body p-0 d-flex flex-column overlay-block">
                                        <div class="spinner spinner-track spinner-primary spinner-lg center-both"
                                             style="z-index: 1"></div>
                                        <!--begin::Stats-->
                                        <div id="chart_service_auto" class="d-flex justify-content-center"></div>

                                        <div class="card-spacer pt-5 bg-white flex-grow-1">
                                            <!--begin::Row-->
                                            <div class="separator separator-solid my-7"></div>

                                            <div class="row row-paddingless">

                                                <div class="col-12 col-lg-6 mb-8 d-flex">
                                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                                        <span class="mr-4">
                                                            <i class="flaticon2-up icon-2x text-muted font-weight-bold"></i>
                                                        </span>
                                                        <div class="d-flex flex-column text-dark-75">
                                                            <span class="font-weight-bolder font-size-sm">Số vật phẩm cộng thủ công</span>
                                                            <span class="font-weight-bolder font-size-h5"
                                                                  id="total_item_add">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{--                                                <div class="col-12 col-lg-6 mb-8 d-flex">--}}
                                                {{--                                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">--}}
                                                {{--                                                        <span class="mr-4">--}}
                                                {{--                                                            <i class="flaticon2-down icon-2x text-muted font-weight-bold"></i>--}}
                                                {{--                                                        </span>--}}
                                                {{--                                                        <div class="d-flex flex-column text-dark-75">--}}
                                                {{--                                                            <span class="font-weight-bolder font-size-sm">Số vật phẩm trừ thủ công</span>--}}
                                                {{--                                                            <span class="font-weight-bolder font-size-h5"--}}
                                                {{--                                                                  id="total_item_minus">0</span>--}}
                                                {{--                                                        </div>--}}
                                                {{--                                                    </div>--}}
                                                {{--                                                </div>--}}
                                                <div class="col-12 col-lg-6 mb-8 d-flex">
                                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                                        <span class="mr-4">
                                                            <i class="flaticon-coins icon-2x text-muted font-weight-bold"></i>
                                                        </span>
                                                        <div class="d-flex flex-column text-dark-75">
                                                        <span class="font-weight-bolder font-size-sm">Doanh thu thanh toán thành công
                                                    </span>
                                                            <span class="font-weight-bolder font-size-h5"
                                                                  id="total_price_service_auto">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{--                                                <div class="col-12 col-lg-6 mb-8 d-flex">--}}
                                                {{--                                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">--}}
                                                {{--                                                        <span class="mr-4">--}}
                                                {{--                                                            <i class="flaticon-price-tag icon-2x text-muted font-weight-bold"></i>--}}
                                                {{--                                                        </span>--}}
                                                {{--                                                        <div class="d-flex flex-column text-dark-75">--}}
                                                {{--                                                            <span class="font-weight-bolder font-size-sm">Doanh thu đơn hàng CTV hoàn tất</span>--}}
                                                {{--                                                            <span class="font-weight-bolder font-size-h5"--}}
                                                {{--                                                                  id="total_price_ctv_service_auto">0</span>--}}
                                                {{--                                                        </div>--}}
                                                {{--                                                    </div>--}}
                                                {{--                                                </div>--}}
                                                <div class="col-12 col-lg-6 mb-8 d-flex">
                                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                                        <span class="mr-4">
                                                            <i class="flaticon-users icon-2x text-muted font-weight-bold"></i>
                                                        </span>
                                                        <div class="d-flex flex-column text-dark-75">
                                                            <span class="font-weight-bolder font-size-sm">Số người giao dịch (Thành công)</span>
                                                            <span class="font-weight-bolder font-size-h5" id="total_user_service_auto">0</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-lg-6 mb-8 d-flex">
                                                    <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
												<span class="mr-4">
													<i class="flaticon-line-graph icon-2x text-muted font-weight-bold"></i>
												</span>
                                                        <div class="d-flex flex-column text-dark-75">
                                                            <span class="font-weight-bolder font-size-sm">GD trung bình thành công / 1 khách hàng</span>
                                                            <span class="font-weight-bolder font-size-h5"
                                                                  id="avg_service_auto">0 đ</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Body-->
                                </div>
                            @endif
                        </div>
                        <div class="col-xl-4">
                            @if(auth()->user()->can('dashboard-plus-money'))
                                <div class="card card-custom card-stretch gutter-b">
                                    <!--begin::Header-->
                                    <div class="card-header border-0">
                                        <h3 class="card-title font-weight-bolder text-dark">Cộng/Trừ tiền thủ công hệ
                                            thống</h3>
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card-body pt-2">
                                        @if(!session()->has('shop_id'))
                                            <div class="d-flex flex-wrap align-items-center mb-10">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-60 symbol-2by3 flex-shrink-0 mr-4">
                                                    <div class="symbol-label"
                                                         style="background-image: url('/assets/media/stock-600x400/img-1.jpg')"></div>
                                                </div>
                                                <!--end::Symbol-->
                                                <!--begin::Title-->
                                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                                    <a href="#"
                                                       class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">Cộng
                                                        tiền QTV</a>
                                                    <span class="text-muted font-weight-bold font-size-sm my-1">Số tiền được cộng cho quản trị viên</span>
                                                </div>
                                                <!--end::Title-->
                                                <!--begin::Info-->
                                                <div class="d-flex align-items-center py-lg-0 py-2">
                                                    <div class="d-flex flex-column text-right">
                                                    <span class="text-dark-75 font-weight-bolder font-size-h4"
                                                          id="add_money_qtv">0</span>
                                                        <span class="text-muted font-size-sm font-weight-bolder">đ</span>
                                                    </div>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center mb-10">
                                                <!--begin::Symbol-->
                                                <div class="symbol symbol-60 symbol-2by3 flex-shrink-0 mr-4">
                                                    <div class="symbol-label"
                                                         style="background-image: url('/assets/media/stock-600x400/img-2.jpg')"></div>
                                                </div>
                                                <!--end::Symbol-->
                                                <!--begin::Title-->
                                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                                    <a href="#"
                                                       class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">Trừ
                                                        tiền QTV</a>
                                                    <span class="text-muted font-weight-bold font-size-sm my-1">Số tiền bị trừ cho quản trị viên</span>
                                                </div>
                                                <!--end::Title-->
                                                <!--begin::Info-->
                                                <div class="d-flex align-items-center py-lg-0 py-2">
                                                    <div class="d-flex flex-column text-right">
                                                    <span class="text-dark-75 font-weight-bolder font-size-h4"
                                                          id="minus_money_qtv">0</span>
                                                        <span class="text-muted font-weight-bolder font-size-sm">đ</span>
                                                    </div>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                    @else
                                    @endif
                                    <!--begin::Item-->
                                        <!--end::Item-->
                                        <!--begin: Item-->

                                        <div class="d-flex flex-wrap align-items-center mb-10">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-60 symbol-2by3 flex-shrink-0 mr-4">
                                                <div class="symbol-label"
                                                     style="background-image: url('/assets/media/stock-600x400/img-18.jpg')"></div>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Title-->
                                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                                <a href="#"
                                                   class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">Cộng
                                                    tiền User</a>
                                                <span class="text-muted font-weight-bold font-size-sm my-1">Số tiền được cộng cho thành viên</span>
                                            </div>
                                            <!--end::Title-->
                                            <!--begin::Info-->
                                            <div class="d-flex align-items-center py-lg-0 py-2">
                                                <div class="d-flex flex-column text-right">
                                                <span class="text-dark-75 font-weight-bolder font-size-h4"
                                                      id="add_money_user">0</span>
                                                    <span class="text-muted font-weight-bolder font-size-sm">đ</span>
                                                </div>
                                            </div>
                                            <!--end::Info-->
                                        </div>

                                        <div class="d-flex flex-wrap align-items-center mb-10">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-60 symbol-2by3 flex-shrink-0 mr-4">
                                                <div class="symbol-label"
                                                     style="background-image: url('/assets/media/stock-600x400/img-3.jpg')"></div>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Title-->
                                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                                <a href="#"
                                                   class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">Trừ
                                                    tiền User</a>
                                                <span class="text-muted font-weight-bold font-size-sm my-1">Số tiền bị trừ cho thành viên</span>
                                            </div>
                                            <!--end::Title-->
                                            <!--begin::Info-->
                                            <div class="d-flex align-items-center py-lg-0 py-2">
                                                <div class="d-flex flex-column text-right">
                                                <span class="text-dark-75 font-weight-bolder font-size-h4"
                                                      id="minus_money_user">0</span>
                                                    <span class="text-muted font-weight-bolder font-size-sm">đ</span>
                                                </div>
                                            </div>
                                            <!--end::Info-->
                                        </div>

                                        <div class="d-flex flex-wrap align-items-center mb-10">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-60 symbol-2by3 flex-shrink-0 mr-4">
                                                <div class="symbol-label"
                                                     style="background-image: url('/assets/media/stock-600x400/img-4.jpg')"></div>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Title-->
                                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                                <a href="#"
                                                   class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">Số
                                                    lệnh cộng tiền</a>
                                                <span class="text-muted font-weight-bold font-size-sm my-1">Tổng số các lệnh yêu cầu cộng tiền</span>
                                            </div>
                                            <!--end::Title-->
                                            <!--begin::Info-->
                                            <div class="d-flex align-items-center py-lg-0 py-2">
                                                <div class="d-flex flex-column text-right">
                                                <span class="text-dark-75 font-weight-bolder font-size-h4"
                                                      id="count_user_add_money">0</span>
                                                    <span class="text-muted font-weight-bolder font-size-sm">lệnh</span>
                                                </div>
                                            </div>
                                            <!--end::Info-->
                                        </div>

                                        <div class="d-flex flex-wrap align-items-center mb-10">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-60 symbol-2by3 flex-shrink-0 mr-4">
                                                <div class="symbol-label"
                                                     style="background-image: url('/assets/media/stock-600x400/img-5.jpg')"></div>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Title-->
                                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                                <a href="#"
                                                   class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">Số
                                                    lệnh trừ tiền</a>
                                                <span class="text-muted font-weight-bold font-size-sm my-1">Tổng số các lệnh yêu cầu trừ tiền</span>
                                            </div>
                                            <!--end::Title-->
                                            <!--begin::Info-->
                                            <div class="d-flex align-items-center py-lg-0 py-2">
                                                <div class="d-flex flex-column text-right">
                                                <span class="text-dark-75 font-weight-bolder font-size-h4"
                                                      id="count_user_minus_money">0</span>
                                                    <span class="text-muted font-weight-bolder font-size-sm">lệnh</span>
                                                </div>
                                            </div>
                                            <!--end::Info-->
                                        </div>

                                        <div class="d-flex flex-wrap align-items-center mb-10">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-60 symbol-2by3 flex-shrink-0 mr-4">
                                                <div class="symbol-label"
                                                     style="background-image: url('/assets/media/stock-600x400/img-6.jpg')"></div>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Title-->
                                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                                <a href="#"
                                                   class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">User
                                                    được cộng tiền</a>
                                                <span class="text-muted font-weight-bold font-size-sm my-1">Số thành viên được cộng tiền</span>
                                            </div>
                                            <!--end::Title-->
                                            <!--begin::Info-->
                                            <div class="d-flex align-items-center py-lg-0 py-2">
                                                <div class="d-flex flex-column text-right">
                                                    <span class="text-dark-75 font-weight-bolder font-size-h4">0</span>
                                                    <span class="text-muted font-weight-bolder font-size-sm">người</span>
                                                </div>
                                            </div>
                                            <!--end::Info-->
                                        </div>

                                        <div class="d-flex flex-wrap align-items-center">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-60 symbol-2by3 flex-shrink-0 mr-4">
                                                <div class="symbol-label"
                                                     style="background-image: url('/assets/media/stock-600x400/img-7.jpg')"></div>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Title-->
                                            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                                <a href="#"
                                                   class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">User
                                                    bị trừ tiền</a>
                                                <span class="text-muted font-weight-bold font-size-sm my-1">Số thành viên bị trừ tiền</span>
                                            </div>
                                            <!--end::Title-->
                                            <!--begin::Info-->
                                            <div class="d-flex align-items-center py-lg-0 py-2">
                                                <div class="d-flex flex-column text-right">
                                                    <span class="text-dark-75 font-weight-bolder font-size-h4">0</span>
                                                    <span class="text-muted font-weight-bolder font-size-sm">người</span>
                                                </div>
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                    <!--end::Body-->
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Nav Content-->
        </div>
        <!--end::Body-->
    </div>

    <a href="#"
       class="btn btn-sm btn-icon btn-light-success pulse pulse-success toggle-filter-mobile d-inline-flex d-lg-none">
        <i class="flaticon-search-1"></i>
        <span class="pulse-ring"></span>
    </a>
@endsection
@section('styles')
    <style>
        .center-both {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .overlay-block:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, .75);
        }

        .swiper-tablist {
            opacity: 0;
        }

        .swiper-tablist.swiper-horizontal {
            opacity: 1;
        }

        .sticky-custom {
            position: sticky;
            top: var(--top);
            z-index: 10;
            box-shadow: 0 10px 30px 30px rgb(82 63 105 / 8%);
        }

        .is-week .datepicker-days tr:hover td {
            color: #000;
            background: #e5e2e3;
            border-radius: 0;
        }

        #dropdown-time:after {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
        }

        @media all and (max-width: 992px) {
            .toggle-filter-mobile {
                position: fixed;
                bottom: 100px;
                right: 15px;
            }

            .toggle-filter-mobile:not(a) {
                position: unset;
                margin-left: 8px;
            }

            #card-filter {
                transform: translateX(100vw);
                transition: .3s linear;
                position: fixed;
                top: var(--top-mobile);
                left: 16px;
                right: 16px;
            }

            #card-filter.show {
                transform: translateX(0);
            }
        }
        .is-week .datepicker-days tr:hover td {
            color: #000;
            background: #e5e2e3;
            border-radius: 0;
        }
        .time-week .table-condensed tr:hover {
            background-color: #f3f6f9;
        }
        .time-week .table-condensed .active ~ .day {
            background-color: #3699ff;
            color:#FFF;
        }
    </style>
@endsection
{{-- Scripts Section --}}
@section('scripts')
    <script>
        const primary = '#6993FF';
        const success = '#1BC5BD';
        const info = '#8950FC';
        const warning = '#FFA800';
        const danger = '#F64E60';
        const drak = '#181c32';
        var KTAppSettings = {
            "breakpoints": {"sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400},
            "colors": {
                "theme": {
                    "base": {
                        "white": "#ffffff",
                        "primary": "#3699FF",
                        "secondary": "#E5EAEE",
                        "success": "#1BC5BD",
                        "info": "#8950FC",
                        "warning": "#FFA800",
                        "danger": "#F64E60",
                        "light": "#E4E6EF",
                        "dark": "#181C32"
                    },
                    "light": {
                        "white": "#ffffff",
                        "primary": "#E1F0FF",
                        "secondary": "#EBEDF3",
                        "success": "#C9F7F5",
                        "info": "#EEE5FF",
                        "warning": "#FFF4DE",
                        "danger": "#FFE2E5",
                        "light": "#F3F6F9",
                        "dark": "#D6D6E0"
                    },
                    "inverse": {
                        "white": "#ffffff",
                        "primary": "#ffffff",
                        "secondary": "#3F4254",
                        "success": "#ffffff",
                        "info": "#ffffff",
                        "warning": "#ffffff",
                        "danger": "#ffffff",
                        "light": "#464E5F",
                        "dark": "#ffffff"
                    }
                },
                "gray": {
                    "gray-100": "#F3F6F9",
                    "gray-200": "#EBEDF3",
                    "gray-300": "#E4E6EF",
                    "gray-400": "#D1D3E0",
                    "gray-500": "#B5B5C3",
                    "gray-600": "#7E8299",
                    "gray-700": "#5E6278",
                    "gray-800": "#3F4254",
                    "gray-900": "#181C32"
                }
            },
            "font-family": "Poppins"
        };
    </script>
    <!-- Trọng viết script từ đây đổ xuống -->
    <script type="text/javascript">
        $.fn.datepicker.dates['vi'] = {
            days: ["Chủ nhật", "Thứ hai", "Thứ ba", "Thứ tư", "Thứ năm", "Thứ sáu", "Thứ 7"],
            daysShort: ["Hai", "Ba", "Tư", "Năm", "Sáu", "Bảy", "CN"],
            daysMin: ["T2", "T3", "T4", "T5", "T6", "T7", "CN"],
            months: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
            monthsShort: ["Thg 1", "Thg 2", "Thg 3", "Thg 4", "Thg 5", "Thg 6", "Thg 7", "Thg 8", "Thg 9", "Thg 10", "Thg 11", "Thg 12"],
            today: "Hôm nay",
            clear: "Xoá",
            format: "mm/dd/yyyy",
            titleFormat: "MM yyyy",
            weekStart: 0,
        };

        $('#time-day input').datepicker({
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            maxDate: new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()),
            orientation: "bottom left",
            language: 'vi',
        });

        let startDate,
            endDate;

        $('#time-week input').datepicker({
            rtl: KTUtil.isRTL(),
            orientation: "bottom left",
            autoclose: true,
            format: 'dd/mm/yyyy',
            forceParse: false,
            language: 'vi',
        }).on("changeDate", function (e) {
            let date = e.date;
            startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
            endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
            $(this).datepicker('update', startDate);
            $(this).val(startDate.getDate() + '/' + (startDate.getMonth() + 1) + '/' + startDate.getFullYear() + ' - ' + endDate.getDate() + '/' + (endDate.getMonth() + 1) + '/' + endDate.getFullYear());
        }).on('show',function () {
            $('html').find('.datepicker-dropdown').addClass('time-week');
        });

        $('#time-month input').datepicker({
            format: "mm/yyyy",
            startView: "months",
            minViewMode: "months",
            orientation: "bottom left",
            language: 'vi',
        })
        $('#time-year input').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            orientation: "bottom left",
            language: 'vi',
        })

        $('.toggle-filter-mobile').on('click', function (e) {
            e.preventDefault();
            $('#card-filter').toggleClass('show');
        })
        $('#submit-data-filter').on('click', function (e) {
            e.preventDefault();
            let time_value = {};
            time_value.type = $('#select-time').attr('data-value');
            let input;
            switch (time_value.type) {
                case 'day':
                    input = $('#time-day input');
                    input.toggleClass('icon-error is-invalid',!input.val());
                    if(!input.val()) {
                        toastr.error('Hãy điền thông tin !');
                        return;
                    }
                    time_value.time = input.val();
                    break;
                case 'week':
                    input = $('#time-week input');
                    input.toggleClass('icon-error is-invalid',!input.val());
                    if(!input.val()) {
                        toastr.error('Hãy điền thông tin !');
                        return;
                    }
                    time_value.time = input.val();
                    break;
                case 'month':
                    input = $('#time-month input');
                    input.toggleClass('icon-error is-invalid',!input.val());
                    if(!input.val()) {
                        toastr.error('Hãy điền thông tin !');
                        return;
                    }
                    time_value.time = input.val();
                    break;
                case 'year':
                    input = $('#time-year input');
                    input.toggleClass('icon-error is-invalid',!input.val());
                    if(!input.val()) {
                        toastr.error('Hãy điền thông tin !');
                        return;
                    }
                    time_value.time = input.val();
                    break;
            }
            let target = $('.swiper-tablist a.active[data-toggle="pill"]').attr('href');
            if (target === '#widget-tab-1') {
                getDataGeneral(time_value);
                getDataDensityGeneral(time_value);
                getDataService(time_value);
                getDataServiceAuto(time_value);
                getDataPlusMoney(time_value);
            }
            if (target === '#report-user') {
                ShowChartTxnsUser(time_value);
                ShowChartUser(time_value);
                getDataTxnsBiggest(time_value);
            }
        })

        $('#select-time').on('click', '.dropdown-item', function (e) {
            e.preventDefault();
            let elm = $('#select-time');
            elm.attr('data-value', $(this).attr('data-value'));
            elm.find('#dropdown-time').text($(this).text().trim());

            $('#time-day').toggleClass('d-none', !($(this).data('value') === 'day'));
            $('#time-week').toggleClass('d-none', !($(this).data('value') === 'week'));
            $('#time-month').toggleClass('d-none', !($(this).data('value') === 'month'));
            $('#time-year').toggleClass('d-none', !($(this).data('value') === 'year'));
        })

        $(document).ready(function () {
            $(document).on('shown.bs.tab', '.swiper-tablist a[data-toggle="pill"]', function (e) {
                let time_value = {};
                time_value.type = $('#select-time').attr('data-value');
                switch (time_value.type) {
                    case 'day':
                        time_value.time = $('#time-day input').val();
                        break;
                    case 'week':
                        time_value.time = $('#time-week input').val();
                        break;
                    case 'month':
                        time_value.time = $('#time-month input').val();
                        break;
                    case 'year':
                        time_value.time = $('#time-year input').val();
                        break;
                }
                if (!$(this).hasClass('loaded')) {
                    if ($(this).attr('href') === '#report-user') {
                        ShowChartUser(time_value);
                        ShowChartTxnsUser(time_value);
                        getDataSurplusUser(time_value);
                        getDataTopUser(time_value);
                        getDataTxnsBiggest(time_value);
                    }
                    if ($(this).attr('href') === '#widget-tab-1') {
                        getDataGeneral(time_value);
                        getDataDensityGeneral(time_value);
                        getDataService(time_value);
                        getDataServiceAuto(time_value);
                        getDataWithDrawItem(time_value);
                    }
                    $(this).addClass('loaded');
                }
            })

            $('.swiper-tablist a.active[data-toggle="pill"]').trigger('shown.bs.tab');
        })

        function getDataCharge(query = {}) {
            let format_numb = wNumb({
                thousand: '.',
            })

            addLoading($('#chart_charge'));
            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.charge.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    let total = res.total_record * 1;
                    let total_success = res.total_record_success * 1;
                    let total_error = res.total_record_error * 1;
                    let total_pending = res.total_record_pending * 1;
                    // let total_user = res.total_user;
                    let total_charge_real_value = res.total_charge_real_value * 1;
                    // let avg_value = res.avg_value * 1;
                    let total_charge_value = res.total_charge_value * 1;
                    let total_money_received = res.total_money_received * 1;

                    // $('#total-charge-user').text(total_user);
                    // $('#total_charge_avg').text(format_numb.to(avg_value) + ' đ');
                    $('#total_charge_value').text(format_numb.to(total_charge_value) + ' đ');
                    $('#total_money_received').text(format_numb.to(total_money_received) + ' đ');
                    $('#total_charge_real_value').text(format_numb.to(total_charge_real_value) + ' đ');
                    $('#total_promotion_rechare').text(format_numb.to(total_charge_real_value - total_money_received) + ' đ');
                    ChartCharge.init(total_success, total_error, total_pending)
                    removeLoading($('#chart_charge'));
                }
            })
        }

        function getDataStoreCard(query = {}) {
            let format_numb = wNumb({
                thousand: '.',
            })

            addLoading($('#chart_store_card'));

            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.store-card.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    let count_success = res.total_record_success * 1;
                    let count_error = res.total_record_error * 1;
                    let count_pending = res.total_record_pending * 1;

                    $('#total_user_store_card').text(res.total_user);
                    $('#turnover_store_card').text(format_numb.to(res.total_income * 1) + ' đ');
                    $('#store_card_avg').text(format_numb.to(res.avg_income * 1) + ' đ');

                    ChartStoreCard.init(count_success, count_error, count_pending);

                    removeLoading('#chart_store_card');
                }
            })
        }

        function getDataPlusMoney(query = {}) {
            let format_numb = wNumb({
                thousand: '.',
            })
            addLoading($('#add_money_qtv'))
            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.plus-money.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    let data = res;
                    $('#add_money_qtv').text(`${format_numb.to(data.add_money_qtv * 1)}`);
                    $('#minus_money_qtv').text(`${format_numb.to(data.minus_money_qtv * 1)}`);

                    $('#add_money_user').text(`${format_numb.to(data.add_money_user * 1)}`);
                    $('#minus_money_user').text(`${format_numb.to(data.minus_money_user * 1)}`);

                    $('#minus_money_command').text(data.count_command_minus || 0);
                    $('#add_money_command').text(data.count_command_add || 0);

                    $('#count_user_add_money').text(data.count_user_add);
                    $('#count_user_minus_money').text(data.count_user_minus);

                    removeLoading($('#add_money_qtv'));
                }
            })
        }

        function getDataWithdraw(query = {}) {
            let format_numb = wNumb({
                thousand: '.',
            })

            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.withdraw.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {

                    $('#withdraw_money_qtv').text(`${format_numb.to(res.total_withdraw_qtv * 1)} đ`);
                    $('#withdraw_money_user').text(`${format_numb.to(res.total_withdraw_user * 1)} đ`);
                }
            })
        }

        function getDataService(query = {}) {
            let format_numb = wNumb({
                thousand: '.',
            })

            addLoading($('#chart_service'));
            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.service.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {

                    let total_record = res.total_record * 1;
                    let total_pending = res.total_record_pending * 1;
                    let total_success = res.total_record_success * 1;
                    let total_cancle = res.total_record_canceled * 1;

                    let total_paid = res.total_record_paid * 1;
                    let total_turnover_success = res.total_turnover_success * 1;
                    let total_price_ctv = res.total_price_ctv * 1;
                    let total_complete_price_service = res.total_complete_price_service * 1;
                    let total_price_profit = res.total_price_profit * 1;

                    $('#total_service_paid').text(format_numb.to(total_paid));
                    $('#total_service_turnover_success').text(format_numb.to(total_turnover_success) + ' đ');
                    // $('#total_price_ctv_service').text(format_numb.to(total_price_ctv) + ' đ');
                    $('#total_service_happens').text(total_record);
                    $('#total_complete_price_service').text(format_numb.to(total_complete_price_service) + ' đ')
                    $('#total_price_profit_service').text(format_numb.to(total_price_profit) + ' đ');
                    removeLoading($('#chart_service'));

                    ChartService.init(total_success, total_pending, total_cancle);

                }
            })
        }

        function getDataServiceAuto(query = {}) {
            let format_numb = wNumb({
                thousand: '.',
            })
            addLoading($('#chart_service_auto'))
            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.service-auto.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    let success = res.total_record_success * 1;
                    let error = res.total_record_error * 1;
                    let pending = res.total_record_pending * 1;
                    let lost_item = res.total_record_lost_item * 1;
                    let total_price = res.total_price * 1;
                    let total_price_ctv = res.total_price_ctv * 1;
                    let items_add = res.items_add * 1;
                    let items_minus = res.items_minus * 1;

                    ChartServiceAuto.init(success, error, pending, lost_item);


                    $('#total_price_service_auto').text(format_numb.to(total_price) + ' đ');
                    // $('#total_price_ctv_service_auto').text(format_numb.to(total_price_ctv) + ' đ');
                    $('#total_item_add').text(format_numb.to(items_add));
                    // $('#total_item_minus').text(format_numb.to(items_minus));
                    $('#total_user_service_auto').text(res.total_user);
                    $('#avg_service_auto').text(format_numb.to(res.avg_revenue *1 ) + 'đ');

                    removeLoading($('#chart_service_auto'));
                }
            })
        }

        function getDataTransfer(query = {}) {

            addLoading($('#chart_transfer_atm'));

            let format_numb = wNumb({
                thousand: '.',
            })

            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.transfer2.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    let count_success = res.total_record_success * 1;
                    let count_error = res.total_record_error * 1;
                    let count_pending = res.total_record_pending * 1;


                    $('#count_user_transfer_atm').text(res.count_user);
                    $('#total_promotion_transfer_atm').text(format_numb.to((res.total_real_received_price *1) - (res.total_price * 1)) + ' đ');
                    $('#total_price_transfer_atm').text(format_numb.to(res.total_price * 1) + ' đ');
                    $('#total_real_received_price').text(format_numb.to(res.total_real_received_price * 1) + ' đ');
                    ChartTransfer.init(count_success, count_error, count_pending);
                    removeLoading(('#chart_transfer_atm'));
                }
            })
        }

        function getDataMinigame(query = {}) {

            let element = $('#chart_report_minigame').closest('.card');
            element.toggleClass('overlay-block', 1);
            element.find('.spinner').show();
            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.mini.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    let data_record = res.data_record;
                    let data_price = res.data_price;
                    let time = res.time;

                    time = $.map(time, function (value, index) {
                        return [value];
                    });

                    data_record = $.map(data_record, function (value, index) {
                        return [value];
                    });
                    data_price = $.map(data_price, function (value, index) {
                        return [value];
                    });
                    $('#count_user_minigame').text(res.total_user);
                    ChartMinigame.init(time, data_record, data_price);

                    element.toggleClass('overlay-block', 0);
                    element.find('.spinner').hide();
                }
            })
        }

        function getDataSurplusUser(query = {}) {
            let format_numb = wNumb({
                thousand: '.',
            })

            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.surplus-user.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    $('#total_money_user').text(format_numb.to(res.total_price_user * 1) + ' đ');
                    $('#total_money_ctv').text(format_numb.to(res.total_price_ctv * 1) + ' đ');
                    $('#total_money_qtv').text(format_numb.to(res.total_price_qtv * 1) + ' đ');
                }
            })
        }

        function getDataTopUser(query = {}) {

            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.top-user.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    setTableTopUser(res)
                },
            })
        }

        function setTableTopUser(res) {
            let format_numb = wNumb({
                thousand: '.',
            })
            let top_user = res.top_user;
            let top_ctv = res.top_ctv;
            let top_qtv = res.top_qtv;

            let table_user = $('#table-top-user tbody');
            let table_ctv = $('#table-top-ctv tbody');
            let table_qtv = $('#table-top-qtv tbody');

            table_user.empty();
            table_ctv.empty();
            table_qtv.empty();

            let color_top = ['text-warning', 'text-primary', 'text-danger'];

            if (top_user.length) {
                top_user.forEach((user, idx) => {
                    let html = `<tr>
                                                    <td class="pl-0">
                                                        <div class="symbol symbol-50 symbol-fixed mr-2 mt-2">
                                                            <div class="symbol-label">
                                                                <span class="${color_top[idx]}">${idx + 1}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="pl-0">
                                                        <span class="text-muted font-weight-bold d-block">Username</span>
                                                        <span class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">${user.username}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-muted font-weight-bold d-block">Shop</span>
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${user.domain}</span>
                                                    </td>
                                                    <td class="text-right pr-0">
                                                        <span class="text-muted font-weight-bold d-block">Số dư</span>
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${format_numb.to(user.balance * 1)} đ</span>
                                                    </td>
                                                </tr>`;
                    table_user.append(html);
                });
            } else {
                let html = `<tr><td class="pl-0"><div class="font-weight-bold text-muted font-size-lg">Chưa có tài khoản nào</div></td></tr>`;
                table_user.append(html);
            }

            if (top_qtv.length) {
                top_qtv.forEach((user, idx) => {
                    let html = `<tr>
                                                    <td class="pl-0">
                                                        <div class="symbol symbol-50 symbol-fixed mr-2 mt-2">
                                                            <div class="symbol-label">
                                                                <span class="${color_top[idx]}">${idx + 1}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="pl-0">
                                                        <span class="text-muted font-weight-bold d-block">Username</span>
                                                        <span class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">${user.username}</span>
                                                    </td>
                                                    <td class="text-right pr-0">
                                                        <span class="text-muted font-weight-bold d-block">Số dư</span>
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${format_numb.to(user.balance * 1)} đ</span>
                                                    </td>
                                                </tr>`;
                    table_qtv.append(html)

                })
            } else {
                let html = `<tr><td class="pl-0"><div class="font-weight-bold text-muted font-size-lg">Chưa có tài khoản nào</div></td></tr>`;
                table_qtv.append(html);
            }

            if (top_ctv.length) {
                top_ctv.forEach((user, idx) => {
                    let html = `<tr>
                                                    <td class="pl-0">
                                                        <div class="symbol symbol-50 symbol-fixed mr-2 mt-2">
                                                            <div class="symbol-label">
                                                                <span class="${color_top[idx]}">${idx + 1}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="pl-0">
                                                        <span class="text-muted font-weight-bold d-block">Username</span>
                                                        <span class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">${user.username}</span>
                                                    </td>
                                                    <td class="text-right pr-0">
                                                        <span class="text-muted font-weight-bold d-block">Số dư</span>
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${format_numb.to(user.balance * 1)} đ</span>
                                                    </td>
                                                </tr>`;
                    table_ctv.append(html);
                })
            } else {
                let html = `<tr><td class="pl-0"><div class="font-weight-bold text-muted font-size-lg">Chưa có tài khoản nào</div></td></tr>`;
                table_ctv.append(html);
            }
            removeLoading(table_user);
            removeLoading(table_qtv);
            removeLoading(table_ctv);
        }

        function setTableTxnsBiggest(res) {
            let format_numb = wNumb({
                thousand: '.',
            })

            let table_user = $('#table-txns-user tbody').empty();
            let table_qtv = $('#table-txns-qtv tbody').empty();
            let table_ctv = $('#table-txns-ctv tbody').empty();
            addLoading(table_user);
            addLoading(table_ctv);
            addLoading(table_qtv);

            let data_res = res;
            if (data_res.user.add) {
                let txns = data_res.user.add;
                let html = `<tr>
                                                    <td class="pl-0">
                                                        <div class="symbol symbol-50 symbol-fixed mr-2 mt-2">
                                                            <div class="symbol-label">
                                                                <i class="flaticon2-plus text-success"></i>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="pl-0">
                                                        <span class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">${txns.username}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${txns.shop_title}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${txns.description}</span>
                                                    </td>
                                                    <td class="text-right pr-0">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${format_numb.to(txns.amount * 1)}đ</span>
                                                    </td>
                                                </tr>`;
                table_user.append(html);
            }
            if (data_res.user.minus) {
                let txns = data_res.user.minus;
                let html = `<tr>
                                                    <td class="pl-0">
                                                        <div class="symbol symbol-50 symbol-fixed mr-2 mt-2">
                                                            <div class="symbol-label">
                                                                <i class="flaticon2-line text-danger"></i>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="pl-0">
                                                        <span class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">${txns.username}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${txns.shop_title}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${txns.description}</span>
                                                    </td>
                                                    <td class="text-right pr-0">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${format_numb.to(txns.amount * 1)}đ</span>
                                                    </td>
                                                </tr>`;
                table_user.append(html);
            }
            if (table_user.is(':empty')) {
                setEmpty(table_user);
            }

            if (data_res.qtv && data_res.qtv.add) {
                let txns = data_res.qtv.add;
                let html = `<tr>
                                                    <td class="pl-0">
                                                        <div class="symbol symbol-50 symbol-fixed mr-2 mt-2">
                                                            <div class="symbol-label">
                                                                <i class="flaticon2-plus text-success"></i>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="pl-0">
                                                        <span class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">${txns.username}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${txns.description}</span>
                                                    </td>
                                                    <td class="text-right pr-0">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${format_numb.to(txns.amount * 1)}đ</span>
                                                    </td>
                                                </tr>`;
                table_qtv.append(html);
            }
            if (data_res.qtv && data_res.qtv.minus) {
                let txns = data_res.qtv.minus;
                let html = `<tr>
                                                    <td class="pl-0">
                                                        <div class="symbol symbol-50 symbol-fixed mr-2 mt-2">
                                                            <div class="symbol-label">
                                                                <i class="flaticon2-line text-danger"></i>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="pl-0">
                                                        <span class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">${txns.username}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${txns.description}</span>
                                                    </td>
                                                    <td class="text-right pr-0">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${format_numb.to(txns.amount * 1)}đ</span>
                                                    </td>
                                                </tr>`;
                table_qtv.append(html);
            }
            if (table_qtv.is(':empty')) {
                setEmpty(table_qtv);
            }

            if (data_res.ctv && data_res.ctv.add) {
                let txns = data_res.ctv.add;
                let html = `<tr>
                                                    <td class="pl-0">
                                                        <div class="symbol symbol-50 symbol-fixed mr-2 mt-2">
                                                            <div class="symbol-label">
                                                                <i class="flaticon2-plus text-success"></i>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="pl-0">
                                                        <span class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">${txns.username}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${txns.description}</span>
                                                    </td>
                                                    <td class="text-right pr-0">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${format_numb.to(txns.amount * 1)}đ</span>
                                                    </td>
                                                </tr>`;
                table_ctv.append(html);
            }
            if (data_res.ctv && data_res.ctv.minus) {
                let txns = data_res.ctv.minus;
                let html = `<tr>
                                                    <td class="pl-0">
                                                        <div class="symbol symbol-50 symbol-fixed mr-2 mt-2">
                                                            <div class="symbol-label">
                                                                <i class="flaticon2-line text-danger"></i>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="pl-0">
                                                        <span class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">${txns.username}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${txns.description}</span>
                                                    </td>
                                                    <td class="text-right pr-0">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${format_numb.to(txns.amount * 1)}đ</span>
                                                    </td>
                                                </tr>`;
                table_ctv.append(html);
            }
            if (table_ctv.is(':empty')) {
                setEmpty(table_ctv);
            }

            function setEmpty(elm) {
                let html = `<tr><td colspan="5">Chưa có giao dịch nào</td></tr>`;
                elm.html(html);
            }

            removeLoading(table_user);
            removeLoading(table_ctv);
            removeLoading(table_qtv);
        }

        function getDataTxnsBiggest(query = {}) {

            let table_user = $('#table-txns-user');
            let table_qtv = $('#table-txns-qtv');
            let table_ctv = $('#table-txns-ctv');
            addLoading(table_user);
            addLoading(table_ctv);
            addLoading(table_qtv);

            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.txns-biggest.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    setTableTxnsBiggest(res)
                },
            })
        }

        function getDataPointOfSale(query = {}) {
            let format_numb = wNumb({
                thousand: '.',
            })

            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.point-of-sale.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    $('#all_point_of_sale').text(res.shop_work)
                    $('#all_shop_shutdown').text(res.shop_shut_down)
                }
            })
        }

        function getDataWithDrawItem(query = {}) {
            let format_numb = wNumb({
                thousand: '.',
            })

            addLoading($('#chart_withdraw_item'));
            query._token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{route('admin.withdraw-item.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    let success = res.total_record_success * 1;
                    let error = res.total_record_error * 1;
                    let pending = res.total_record_pending * 1;
                    let payment_error = res.total_record_payment_error * 1;
                    let total_item_withdraw = res.total_item;
                    $('#total_item_withdraw_success').text(total_item_withdraw || 0);

                    ChartWithdrawItem.init(success, error, pending, payment_error);

                    removeLoading($('#chart_withdraw_item'));
                }
            })
        }

        function getDataGeneral(query = {}) {

            addLoading($('#generality_chart'))
            query._token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{route('admin.general-turnover.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    let time = res.time;

                    let service = res.service;
                    let service_auto = res.service_auto;

                    time = $.map(time, function (value, index) {
                        return [value];
                    });

                    service = $.map(service, function (value, index) {
                        return [value * 1];
                    });
                    service_auto = $.map(service_auto, function (value, index) {
                        return [value * 1];
                    });


                    GeneralityChart.init( service, service_auto, time);
                    removeLoading($('#generality_chart'))

                }
            })
        }

        function getDataDensityGeneral(query = {}) {

            addLoading($('#chart_density_turnover'))
            query._token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{route('admin.general-density-turnover.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    let service = res.service * 1;
                    let service_auto = res.service_auto * 1;
                    ChartDensityTurnover.init(service, service_auto);
                    removeLoading($('#chart_density_turnover'));
                }
            })
        }

        function getDataAccount(query = {}) {
            let format_numb = wNumb({
                thousand: '.',
            })
            addLoading($('#chart_accounts'))
            query._token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{route('admin.growth-account.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {
                    console.log(res)
                    let total_success = res.total_success;
                    let total_wrong_password = res.total_wrong_password;

                    let data_chart = [
                        total_success,
                        total_wrong_password,
                    ];

                    $('#total_turnover_success_account').text(format_numb.to(res.amount) + ' đ');
                    $('#total_capital_expend').text(format_numb.to(res.amount_ctv) + ' đ');
                    $('#total_profit_account').text(format_numb.to(res.amount - res.amount_ctv) + ' đ');
                    $('#total_user_acc').text(res.count_customer);
                    ChartAccount.init(...data_chart);
                    removeLoading($('#chart_accounts'))
                }
            })
        }

        $('.nav-link[href="#tab-point-of-sale"]').on('click', function () {

            if (!$(this).hasClass('loaded')) {
                getDataPointOfSale();
            }
            $('.nav-link[href="#tab-point-of-sale"]').toggleClass('loaded', true);
        });

        $('.reset-filter').on('click', function (e) {
            e.preventDefault();
            $(this).parent().prev().find('input').val('');
            $(this).prev().trigger('click');
        })

        // viết lại cái mới

        function removeLoading(elm) {
            $(elm).closest('.card').find('.card-body').toggleClass('overlay-block', false);
            $(elm).closest('.card').find('.spinner').hide();
        }

        function addLoading(elm) {
            $(elm).closest('.card').find('.card-body').toggleClass('overlay-block', true);
            $(elm).closest('.card').find('.spinner').show();
        }

        function ShowChartUser(query = {}) {
            let element = $('#chart-report-user');
            element.empty();
            element.closest('.card-body').toggleClass('overlay-block', 1).find('.spinner').show();
            $.ajax({
                url: '{{route('admin.user.report')}}',
                type: 'GET',
                data: query,
                success: function (res) {
                    let month = res.data['growth_month'];
                    let user = res.data['users'];
                    let user_facebook = res.data['user_facebook'];
                    let user_google = res.data['user_google'];
                    month = $.map(month, function (value, index) {
                        return [value];
                    });
                    user = $.map(user, function (value, index) {
                        return [value];
                    });
                    user_facebook = $.map(user_facebook, function (value, index) {
                        return [value];
                    });
                    user_google = $.map(user_google, function (value, index) {
                        return [value];
                    });
                    ChartUser.init(month, user, user_facebook, user_google);
                    removeLoading(element);
                }
            })
        }

        function ShowChartTxnsUser(query = {}) {
            let element = $('#chart-report-txns-user');
            element.empty();
            element.closest('.card-body').toggleClass('overlay-block', 1);
            element.closest('.card-body').find('.spinner').show();
            query._token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{route('admin.transaction-user.report')}}',
                type: 'POST',
                data: query,
                success: function (res) {

                    ChartTxnsUser.init(res.total_user, res.users_have_txns, res.users_havent_txns);

                    removeLoading(element)
                }
            })
        }

        let ChartUser = function () {
            let _user = function (month, user, user_facebook, user_google) {
                let element = document.querySelector('#chart-report-user');
                if (!element) {
                    return;
                }
                let options = {
                    series: [{
                        name: 'Đăng kí qua Facebook',
                        data: user_facebook
                    }, {
                        name: 'Đăng kí qua Google',
                        data: user_google
                    }, {
                        name: 'Đăng kí qua tài khoản',
                        data: user
                    },
                    ],
                    chart: {
                        type: 'bar',
                        height: 350,
                        stacked: true,
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: [primary, danger, warning],
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            legend: {
                                position: 'bottom',
                                offsetX: -10,
                                offsetY: 0
                            }
                        }
                    }],
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            dataLabels: {
                                position: 'right'
                            }
                        },
                    },
                    dataLabels: {
                        enabled: true,
                        enabledOnSeries: [2],
                        formatter: function (_val, opt) {
                            let series = opt.w.config.series
                            let idx = opt.dataPointIndex
                            let total = series.reduce((total, self) => total * 1 + self.data[idx] * 1, 0)
                            return total
                        },
                        style: {
                            colors: ["#000"]
                        }
                    },
                    xaxis: {
                        categories: month,
                    },
                    legend: {
                        position: 'right',
                        offsetY: 40
                    },
                    fill: {
                        opacity: 1
                    }
                };

                let chart = new ApexCharts(element, options);
                chart.render();

            }
            return {
                init: function (month, user, user_facebook, user_google) {
                    _user(month, user, user_facebook, user_google);
                }
            };
        }();

        let ChartTxnsUser = function () {
            let _txnsUser = function (total, has_txns, hasnt_txns) {
                let element = document.querySelector("#chart-report-txns-user");
                let options = {
                    series: [has_txns, hasnt_txns],
                    chart: {
                        width: 430,
                        type: 'pie',
                    },
                    labels: ['User có giao dịch', 'User chưa giao dịch'],
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }],
                    colors: [success, danger]
                };

                let chart = new ApexCharts(element, options);
                chart.render();
            }
            return {
                init: function (total_user, has_txns, hasnt_txns) {
                    _txnsUser(total_user, has_txns, hasnt_txns);
                }
            };
        }();

        let ChartTransfer = function () {
            let chart_transfer = function (count_success, count_error, count_pending) {
                let element = document.querySelector('#chart_transfer_atm');
                $(element).empty();

                let options = {
                    series: [count_success, count_error, count_pending],
                    chart: {
                        width: 400,
                        type: 'pie',
                    },
                    labels: ['Thành công', 'Thất bại', 'Chờ thanh toán'],
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " Giao dịch"
                            }
                        }
                    },
                    colors: [success, danger, warning],
                };

                let chart = new ApexCharts(element, options);
                chart.render();
            }

            return {
                init: function (count_success, count_error, count_pending) {
                    chart_transfer(count_success, count_error, count_pending);
                }
            }
        }();

        let ChartStoreCard = function () {
            let setChartStoreCard = function (count_success, count_error, count_pending) {

                let element = document.querySelector('#chart_store_card');
                $(element).empty();

                let options = {
                    series: [count_success, count_error, count_pending],
                    chart: {
                        width: 400,
                        type: 'pie',
                    },
                    labels: ['Thành công', 'Thất bại', 'Chờ thanh toán'],

                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " Giao dịch"
                            }
                        }
                    },

                    colors: [success, danger, warning],
                };

                let chart = new ApexCharts(element, options);
                chart.render();
            }
            return {
                init: function (count_success, count_error, count_pending) {
                    setChartStoreCard(count_success, count_error, count_pending);
                }
            }
        }();

        let ChartCharge = function () {
            let setChartCharge = function (count_success, count_error, count_pending) {
                let element = document.querySelector('#chart_charge');
                $(element).empty();
                let options = {
                    series: [count_success, count_error, count_pending],
                    chart: {
                        width: 400,
                        type: 'pie',
                    },
                    labels: ['Thành công', 'Thất bại', 'Chờ thanh toán'],
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " Giao dịch"
                            }
                        }
                    },

                    colors: [success, danger, warning],
                };

                let chart = new ApexCharts(element, options);
                chart.render();
            }
            return {
                init: function (count_success, count_error, count_pending) {
                    setChartCharge(count_success, count_error, count_pending)
                }
            }
        }();

        let ChartMinigame = function () {
            let setChartMinigame = function (time, total_record, total_price) {
                let options = {
                    chart: {
                        height: 350,
                        type: "line",
                        stacked: false,
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: true
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    colors: ["#FF1654", "#247BA0"],
                    series: [
                        {
                            name: "Doanh thu",
                            data: total_price
                        },
                        {
                            name: "Số lượng giao dịch",
                            data: total_record
                        }
                    ],
                    stroke: {
                        width: [2, 2]
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: "20%"
                        }
                    },
                    xaxis: {
                        categories: time
                    },
                    yaxis: [
                        {
                            axisTicks: {
                                show: true
                            },
                            axisBorder: {
                                show: true,
                                color: "#FF1654"
                            },
                            labels: {
                                style: {
                                    colors: "#FF1654"
                                },
                                formatter: function (value) {
                                    return format_money(value, 1)
                                }
                            },
                            title: {
                                text: "doanh thu",
                                style: {
                                    color: "#FF1654"
                                }
                            },
                        },
                        {
                            opposite: true,
                            axisTicks: {
                                show: true
                            },
                            axisBorder: {
                                show: true,
                                color: "#247BA0"
                            },
                            labels: {
                                style: {
                                    colors: "#247BA0"
                                }
                            },
                            title: {
                                text: "giao dịch",
                                style: {
                                    color: "#247BA0"
                                }
                            }
                        }
                    ],
                    tooltip: {
                        enabled: true,
                    },
                    legend: {
                        horizontalAlign: "left",
                        offsetX: 40
                    }
                };
                let element = document.querySelector("#chart_report_minigame");
                $(element).empty();
                let chart = new ApexCharts(element, options);

                chart.render();

            }
            return {
                init: function (time, total_record, total_price) {
                    setChartMinigame(time, total_record, total_price);
                }
            }
        }();

        let ChartWithdrawItem = function () {
            let setChartWithdrawItem = function (count_success, count_error, count_pending, count_payment_error) {

                let element = document.querySelector('#chart_withdraw_item');
                $(element).empty();

                let options = {
                    series: [count_success, count_error, count_pending, count_payment_error],
                    chart: {
                        width: 400,
                        type: 'pie',
                    },
                    title: {
                        text: 'Rút vật phẩm',
                        style: {
                            fontWeight: 600,
                        },
                    },
                    labels: ['Hoàn thành', 'Thanh toán lỗi', 'Chờ xử lý', 'Giao dịch lỗi'],
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " lệnh"
                            }
                        }
                    },
                    colors: [success, danger, warning, info],
                };

                let chart = new ApexCharts(element, options);
                chart.render();
            }
            return {
                init: function (count_success, count_error, count_pending, count_payment_error) {
                    setChartWithdrawItem(count_success, count_error, count_pending, count_payment_error);
                }
            }
        }();

        let ChartDensityTurnover = function () {
            let set_chart_density_turnover = function ( service, service_auto) {
                let element = document.querySelector('#chart_density_turnover');
                $(element).empty();
                let options = {
                    series: [ service, service_auto],
                    chart: {
                        width: 500,
                        type: 'donut',
                    },
                    labels: ['Dịch vụ thủ công', 'Dịch vụ tự động'],
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        showAlways: true,
                                        label: 'Tổng',
                                        show: true,
                                        formatter: function (w) {
                                            let total = w.globals.seriesTotals.reduce((a, b) => {
                                                return a + b;
                                            }, 0)
                                            return wNumb({
                                                thousand: '.',
                                            }).to(total) + ' đ';
                                        }
                                    },
                                }
                            }
                        }
                    },
                    yaxis: [
                        {
                            axisTicks: {
                                show: true,
                            },
                            axisBorder: {
                                show: true,
                                color: danger
                            },
                            labels: {
                                style: {
                                    colors: danger,
                                },
                                formatter: function (value) {
                                    return format_money(value, 1)
                                }
                            },
                        },
                    ],
                    colors: [primary, success]
                };

                let chart = new ApexCharts(element, options);
                chart.render();
            }
            return {
                init: function (service, service_auto) {
                    set_chart_density_turnover(service, service_auto);
                }
            }
        }();

        let GeneralityChart = function () {
            let general_chart = function (service, service_auto, time) {
                let element = document.querySelector('#generality_chart');
                let options = {
                    series: [{
                        name: 'Dịch vụ thủ công',
                        type: 'line',
                        data: service
                    }, {
                        name: 'Dịch vụ tự động',
                        type: 'line',
                        data: service_auto
                    },
                    ],
                    chart: {
                        height: 350,
                        type: 'line',
                        stacked: false,
                        toolbar: {
                            show: true,
                        },
                        zoom: {
                            enabled: false,
                        }
                    },
                    colors: [primary, success],
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: [4, 4],
                    },
                    xaxis: {
                        categories: time,
                    },
                    yaxis: [
                        {
                            axisTicks: {
                                show: true,
                            },
                            axisBorder: {
                                show: true,
                                color: danger
                            },
                            labels: {
                                style: {
                                    colors: danger,
                                },
                                formatter: function (value) {
                                    return format_money(value, 1)
                                }
                            },
                            tooltip: {
                                enabled: true
                            }
                        },
                    ],
                    tooltip: {
                        enabled: true,
                    },
                    legend: {
                        horizontalAlign: 'left',
                        offsetX: 40,
                    }
                };

                let chart = new ApexCharts(element, options);
                $(element).empty();
                chart.render();
            }
            return {
                init: function (service, service_auto, time) {
                    general_chart(service, service_auto, time);
                }
            };
        }();

        let ChartServiceAuto = function () {
            let setChartServiceAuto = function (count_success, count_error, count_pending, lost_item) {
                let element = document.querySelector('#chart_service_auto');
                $(element).empty();
                let options = {
                    series: [count_success, count_error, count_pending, lost_item],
                    chart: {
                        width: 400,
                        type: 'donut',
                    },
                    labels: ['Thành công', 'Thất bại', 'Đang chờ', 'Mất item'],
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        showAlways: true,
                                        label: 'Tổng GD',
                                        show: true,
                                        formatter: function (w) {
                                            return w.globals.seriesTotals.reduce((a, b) => {
                                                return a + b;
                                            }, 0);
                                        }
                                    },
                                }
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " Giao dịch"
                            }
                        }
                    },
                    colors: [success, danger, warning, info],
                };

                let chart = new ApexCharts(element, options);
                chart.render();
            }
            return {
                init: function (success, error, pending, lost_item) {
                    setChartServiceAuto(success, error, pending, lost_item)
                },
            }
        }();

        let ChartService = function () {
            let setChartService = function (total_success, total_pending, total_cancle) {
                let element = document.querySelector('#chart_service');
                $(element).empty();
                let options = {
                    series: [total_success, total_pending, total_cancle],
                    chart: {
                        width: 400,
                        type: 'pie',
                    },
                    labels: ['Hoàn thành', 'Đang chờ', 'Đã huỷ'],
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " giao dịch"
                            }
                        }
                    },

                    colors: [success, warning, danger],
                };

                let chart = new ApexCharts(element, options);
                chart.render();
            }
            return {
                init: function (total_success, total_pending, total_cancle) {
                    setChartService(total_success, total_pending, total_cancle)
                }
            }
        }();

        let ChartAccount = function () {

            let setChartAccount = function (...series) {
                let element = document.querySelector('#chart_accounts');
                $(element).empty();
                let options = {
                    series: series,
                    chart: {
                        width: 400,
                        type: 'donut',
                    },
                    labels: [
                        'Đã bán',
                        'Sai mật khẩu',
                    ],
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        showAlways: true,
                                        label: 'Đơn',
                                        show: true,
                                        formatter: function (w) {
                                            return w.globals.seriesTotals.reduce((a, b) => {
                                                return a + b;
                                            }, 0);
                                        }
                                    },
                                }
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " Đơn"
                            }
                        }
                    },
                    colors: [success, danger],
                };

                let chart = new ApexCharts(element, options);
                chart.render();
            }
            return {
                init: function (...series) {
                    setChartAccount(...series);
                }
            }
        }()

        function format_money(n, d) {
            x = ('' + n).length -1, p = Math.pow, d = p(10, d)
            x -= x % 3
            return Math.round(n * d / p(10, x)) / d + " kMBTPE"[x / 3]
        }
    </script>

    <script>
        $('.parrent').on('click', function (e) {
            var id = $(this).attr('rel');

            $(".children_" + id).toggle();

        });
    </script>
    <script>
        new Swiper('.swiper-tablist', {
            observer: true,
            observeParents: true,
            freeMode: true,
            spaceBetween: 12,
            breakpoints: {
                328: {
                    slidesPerView: 1.75,
                },
                768: {
                    slidesPerView: 3,
                },
                1200: {
                    slidesPerView: 5,
                }
            }
        })
    </script>
@endsection
