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
                            @if(Auth::user()->can('dashboard-export-excel'))
                                <li class="nav-item swiper-slide">
                                    <a class="nav-link border nav-dashboard-export-excel d-flex flex-grow-1 rounded flex-column align-items-center active"
                                       data-toggle="pill" href="#tab-dashboard-export-excel">
                                        <i class="menu-icon flaticon-multimedia-3 font-weight-bold" style="font-size: 34px"></i>
                                        <span class="nav-text font-size-lg py-2 font-weight-bolder text-center">Export excel</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <!--end::Nav Tabs-->
                </div>
            </div>
            @if(Auth::user()->can('dashboard-export-excel'))
                <div class="card gutter-b border-0 sticky-custom" style="--top:136px;--top-mobile:66px" id="card-filter-export">
                    <div class="card-body p-5">
                        <div class="row">
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
                            {{--finished_started_at--}}
                            {{--                        <div class="form-group col-12 col-sm-6 col-lg-3">--}}
                            {{--                            <div class="input-group">--}}
                            {{--                                <select id="choice_type" class="form-control">--}}
                            {{--                                    <option selected value="2">Xuất all loại shop</option>--}}
                            {{--                                    <option value="0">Xuất Shop nhà</option>--}}
                            {{--                                    <option value="1">Xuất Shop khách</option>--}}
                            {{--                                </select>--}}
                            {{--                            </div>--}}
                            {{--                        </div>--}}

                            <div class="form-group col-12 col-sm-6 col-lg-3">
                                <div class="input-group">
                                    <select id="choice_status" class="form-control">
                                        <option selected value="1">Trạng thái hoàn tất</option>
                                        <option value="2">Xuất All trạng thái</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-6">
                                <div class="btn-group m-btn-group" role="group" aria-label="...">
                                    <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date" >Hôm nay</a>
                                    <a href="#" data-started-at="{{\Carbon\Carbon::yesterday()->startOfDay()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::yesterday()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Hôm qua</a>
                                    <a href="#" data-started-at="{{\Carbon\Carbon::today()->subDays(7)->startOfDay()->format('d/m/Y H:i:s')}}" data-ended-at="{{\Carbon\Carbon::today()->endOfDay()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">7 Ngày Qua</a>
                                    <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng này</a>
                                    <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->startOfMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->endOfMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng trước</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
        <!--begin::Nav Content-->
            <div class="tab-content m-0 p-0">
                @if(Auth::user()->can('dashboard-export-excel'))
                    <div class="tab-pane active" id="tab-dashboard-export-excel" role="tabpanel">
                        <form class="mb-10" action="{{route('admin.dashboard.export-excel')}}" method="POST">
                            @csrf
                            <input type="hidden" name="started_at" id="started_at">
                            <input type="hidden" name="ended_at" id="ended_at">
                            <input type="hidden" value="2" name="type" id="type">
                            <input type="hidden" value="1" name="status" id="status">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card card-custom gutter-b card-stretch">
                                        <!--begin::Beader-->
                                        <div class="card-header py-5">
                                            <h3 class="card-title font-weight-bolder">Export excel</h3>
                                        </div>
                                        <!--end::Header-->
                                        <!--begin::Body-->
                                        <div class="card-body p-0 d-flex flex-column">
                                            <div class="row p-5">
                                                @if(Auth::user()->can('dashboard-export-excel-service'))
                                                    <div class="col-auto">
                                                        <button class="btn btn-danger btn-secondary--icon" value="service" name="module" type="submit">
                                                    <span>
                                                        <i class="flaticon-folder-2"></i>
                                                        <span>SERVICE</span>
                                                    </span>
                                                        </button>
                                                    </div>
                                                @endif
                                                @if(Auth::user()->can('dashboard-export-excel-service-auto'))
                                                    <div class="col-auto">
                                                        <button class="btn btn-danger btn-secondary--icon" value="service-auto" name="module" type="submit">
                                                    <span>
                                                        <i class="flaticon-folder-2"></i>
                                                        <span>SERVICE AUTO</span>
                                                    </span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
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

    <!-- Trọng viết script từ đây đổ xuống -->
    <script type="text/javascript">


        $('#choice_type').on('change', function (e) {
            let val = $(this).val();
            $("#type").val(val);
        })

        $('#choice_status').on('change', function (e) {
            let val = $(this).val();
            $("#status").val(val);
        })

        $('.nav-tab-point-of-game').on('click', function (e) {
            if (!$("#card-filter-export").hasClass("d-none")) {
                $("#card-filter-export").addClass('d-none');
            }
            $("#card-filter").removeClass('d-none');
        })

        $('.nav-report-use').on('click', function (e) {
            if (!$("#card-filter-export").hasClass("d-none")) {
                $("#card-filter-export").addClass('d-none');
            }
            $("#card-filter").removeClass('d-none');
        })

        $('.nav-tab-point-of-sale').on('click', function (e) {
            if (!$("#card-filter-export").hasClass("d-none")) {
                $("#card-filter-export").addClass('d-none');
            }
            $("#card-filter").removeClass('d-none');
        })

        $('.nav-widget-tab-1').on('click', function (e) {
            if (!$("#card-filter-export").hasClass("d-none")) {
                $("#card-filter-export").addClass('d-none');
            }

            $("#card-filter").removeClass('d-none');
        })
        $('.nav-dashboard-export-excel').on('click', function (e) {
            if (!$("#card-filter").hasClass("d-none")) {
                $("#card-filter").addClass('d-none');
            }

            $("#card-filter-export").removeClass('d-none');
        })
        $('.btn-filter-date').click(function (e) {
            e.preventDefault();
            var startedAt=$(this).data('started-at');
            var endeddAt=$(this).data('ended-at');

            $('#finished_started_at').val(startedAt);
            $('#finished_ended_at').val(endeddAt);


            $('#started_at').val(startedAt);
            $('#ended_at').val(endeddAt);
        });

        $(document).ready(function() {
            $('#finished_ended_at').on('change.datetimepicker', function(e) {
                var finishedEndedAtValue = $(this).val();
                $('#ended_at').val(finishedEndedAtValue);
            });
            $('#finished_started_at').on('change.datetimepicker', function(e) {
                var finishedStartedAtValue = $(this).val();
                $('#started_at').val(finishedStartedAtValue);
            });
        });

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
