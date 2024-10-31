{{-- Extends layout --}}
@extends('admin._layouts.master')

{{-- Content --}}
@section('content')

<div class="row">
    @if($category_access->count())
    <div class="col-lg-6">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3>Chiết khấu dịch vụ bán nick của bạn</h3>
                </div>
            </div>
            <div class="card-body">
                <div id="table_main_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="dataTables_scroll">

                                <div class="dataTables_scrollBody" style="position: relative; overflow: auto; width: 100%;">
                                    <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table_main" role="grid" aria-describedby="table_main_info" style="width: 988px;">
                                        <thead>
                                        <tr role="row " class="b-header">
                                            <th style="background-color: #01a6f5; color: #fff">#</th>
                                            <th style="background-color: #01a6f5; color: #fff">Danh mục</th>
                                            <th style="background-color: #01a6f5; color: #fff">Khoảng giá</th>
                                            <th style="background-color: #01a6f5; color: #fff">Bạn nhận</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($category_access as $key => $access)
                                            {{--begin header--}}
                                            <tr id="parrent{{$access->group_id}}" class="parrentClass" data-id="{{$access->group_id}}" style=" background-color: #e0ebf9;font-weight: bold" >
                                                <td class="parrent" rel="{{$access->group_id}}" style="cursor: pointer">
                                                    <i class="flaticon-plus" style="font-size: 12px"></i>
                                                    {{$key+1}}
                                                </td>
                                                <td>
                                                    {{$access->acc_category->title}}
                                                </td>
                                                <td>Lớn hơn <b>0</b> hoặc bằng <b>1.000.000</b></td>
                                                <td>{{ 100 - $access->ratio['1000000'] }}%</td>
                                            </tr>
                                            <?php $over = 0; ?>
                                            @foreach($access->ratio as $step => $value)
                                                @if($step > 0)
                                                    <tr class="children_{{$access->group_id}}" data-id="{{$access->group_id}}" style="display: none">
                                                        <td></td>
                                                        <td></td>
                                                        <td>Lớn hơn <b>{{ number_format($over, 0, ',', '.') }}</b> hoặc bằng <b>{{ number_format($step, 0, ',', '.') }}</b></td>
                                                        <td>{{ 100 - $value }}%</td>
                                                    </tr>
                                                    <?php $over = $step; ?>
                                                @elseif($step == 'over')
                                                    <tr class="children_{{$access->group_id}}" data-id="{{$access->group_id}}" style="display: none">
                                                        <td></td>
                                                        <td></td>
                                                        <td>Lớn hơn <b>{{ number_format($over, 0, ',', '.') }}</b></td>
                                                        <td>{{ 100 - $value }}%</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach 
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@role('admin')
@php
$year = Carbon\Carbon::now()->year;
$month = Carbon\Carbon::now()->month;
@endphp
<div class="row">
    <div class="col-lg-6">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3>Phân loại nhóm điểm bán</h3>
                </div>
            </div>
            <div class="card-body">
                <div id="classify_shop_group" class="d-flex justify-content-center"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3>Tăng trưởng điểm bán</h3>
                </div>
                <div class="card-toolbar">
                    <form action="" method="POST" class="d-flex justify-content-center">
                        <div class="example-tools">
                            <select class="form-control form-control-sm" name="year" id="growth_shop_year">
                                <option value="{{$year}}">{{$year}}</option>
                                <option value="{{$year - 1}}">{{$year - 1}}</option>
                                <option value="{{$year - 2}}">{{$year - 2}}</option>
                                <option value="{{$year - 3}}">{{$year - 3}}</option>
                                <option value="{{$year - 4}}">{{$year - 4}}</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div id="growth_shop"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3>Tăng trưởng thành viên</h3>
                </div>
                <div class="card-toolbar">
                    <form action="" method="POST" class="d-flex justify-content-center">
                        <div class="example-tools">
                            <select class="form-control form-control-sm" name="year" id="growth_user_year">
                                <option value="{{$year}}">{{$year}}</option>
                                <option value="{{$year - 1}}">{{$year - 1}}</option>
                                <option value="{{$year - 2}}">{{$year - 2}}</option>
                                <option value="{{$year - 3}}">{{$year - 3}}</option>
                                <option value="{{$year - 4}}">{{$year - 4}}</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div id="growth_user"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3>Tăng trưởng cộng tác viên</h3>
                </div>
                <div class="card-toolbar">
                    <div class="example-tools justify-content-center">
                        <select class="form-control form-control-sm" name="year" id="growth_ctv_year">
                            <option value="{{$year}}">{{$year}}</option>
                            <option value="{{$year - 1}}">{{$year - 1}}</option>
                            <option value="{{$year - 2}}">{{$year - 2}}</option>
                            <option value="{{$year - 3}}">{{$year - 3}}</option>
                            <option value="{{$year - 4}}">{{$year - 4}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="growth_ctv"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3>Thống kê nạp thẻ</h3>
                </div>
                <div class="card-toolbar">
                    <form action="{{route('admin.growth.export.charge')}}" method="POST" class="d-flex justify-content-center">
                        {{ csrf_field() }}
                        <div class="example-tools justify-content-center mr-3">
                            <select class="form-control form-control-sm" name="year" id="topup_card_year">
                                <option value="{{$year}}">{{$year}}</option>
                                <option value="{{$year - 1}}">{{$year - 1}}</option>
                                <option value="{{$year - 2}}">{{$year - 2}}</option>
                                <option value="{{$year - 3}}">{{$year - 3}}</option>
                                <option value="{{$year - 4}}">{{$year - 4}}</option>
                            </select>
                        </div>
                        <div class="example-tools justify-content-center">
                            <select class="form-control form-control-sm" name="month" id="topup_card_month">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}" @if ($i == $month)
                                        selected
                                    @endif
                                    > Tháng {{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        {{-- <div class="example-tools">
                            <button type="submit" class="btn btn-light-primary font-weight-bold ml-2 form-control form-control-sm">Xuất</button>
                        </div> --}}
                    </form>
                    </div>
            </div>
            <div class="card-body">
                <div id="topup_card"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3>Thống kê chi tiết nạp thẻ</h3>
                </div>
                <div class="card-toolbar">
                    <form action="" method="POST" class="d-flex justify-content-center">
                        {{ csrf_field() }}
                        <div class="example-tools justify-content-center mr-3">
                            <select class="form-control form-control-sm" name="year" id="charge-report-details-year">
                                <option value="{{$year}}">{{$year}}</option>
                                <option value="{{$year - 1}}">{{$year - 1}}</option>
                                <option value="{{$year - 2}}">{{$year - 2}}</option>
                                <option value="{{$year - 3}}">{{$year - 3}}</option>
                                <option value="{{$year - 4}}">{{$year - 4}}</option>
                            </select>
                        </div>
                        <div class="example-tools justify-content-center">
                            <select class="form-control form-control-sm" name="month" id="charge-report-details-month">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}" @if ($i == $month)
                                        selected
                                    @endif
                                    > Tháng {{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                 <!--begin: Datatable-->
                <table class="table table-bordered table-hover table-checkable " id="kt_datatable">
                </table>
                <!--end: Datatable-->
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3>Thống kê mua thẻ</h3>
                </div>
                <div class="card-toolbar">
                    <form action="{{route('admin.growth.export.store-card')}}" method="POST" class="d-flex justify-content-center">
                        {{ csrf_field() }}
                        <div class="example-tools justify-content-center mr-3">
                            <select class="form-control form-control-sm" name="year" id="store_card_year">
                                <option value="{{$year}}">{{$year}}</option>
                                <option value="{{$year - 1}}">{{$year - 1}}</option>
                                <option value="{{$year - 2}}">{{$year - 2}}</option>
                                <option value="{{$year - 3}}">{{$year - 3}}</option>
                                <option value="{{$year - 4}}">{{$year - 4}}</option>
                            </select>
                        </div>
                        <div class="example-tools justify-content-center">
                            <select class="form-control form-control-sm" name="month" id="store_card_month">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}" @if ($i == $month)
                                        selected
                                    @endif
                                    > Tháng {{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        {{-- <div class="example-tools">
                            <button type="submit" class="btn btn-light-primary font-weight-bold ml-2 form-control form-control-sm">Xuất</button>
                        </div> --}}
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div id="store_card"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3>Thống kê chi tiết mua thẻ</h3>
                </div>
                <div class="card-toolbar">
                    <form action="{{route('admin.growth.export.store-card')}}" method="POST" class="d-flex justify-content-center">
                        {{ csrf_field() }}
                        <div class="example-tools justify-content-center mr-3">
                            <select class="form-control form-control-sm" name="year" id="store-card-report-details-year">
                                <option value="{{$year}}">{{$year}}</option>
                                <option value="{{$year - 1}}">{{$year - 1}}</option>
                                <option value="{{$year - 2}}">{{$year - 2}}</option>
                                <option value="{{$year - 3}}">{{$year - 3}}</option>
                                <option value="{{$year - 4}}">{{$year - 4}}</option>
                            </select>
                        </div>
                        <div class="example-tools justify-content-center">
                            <select class="form-control form-control-sm" name="month" id="store-card-report-details-month">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}" @if ($i == $month)
                                        selected
                                    @endif
                                    > Tháng {{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                  <!--begin: Datatable-->
                  <table class="table table-bordered table-hover table-checkable " id="kt_datatable_charge_store_card">
                </table>
                <!--end: Datatable-->
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3>Thống kê nạp Ví - ATM tự động</h3>
                </div>
                <div class="card-toolbar">
                    <form action="{{route('admin.growth.export.store-card')}}" method="POST" class="d-flex justify-content-center">
                        {{ csrf_field() }}
                        <div class="example-tools justify-content-center mr-3">
                            <select class="form-control form-control-sm" name="year" id="tranfer_year">
                                <option value="{{$year}}">{{$year}}</option>
                                <option value="{{$year - 1}}">{{$year - 1}}</option>
                                <option value="{{$year - 2}}">{{$year - 2}}</option>
                                <option value="{{$year - 3}}">{{$year - 3}}</option>
                                <option value="{{$year - 4}}">{{$year - 4}}</option>
                            </select>
                        </div>
                        <div class="example-tools justify-content-center">
                            <select class="form-control form-control-sm" name="month" id="tranfer_month">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}" @if ($i == $month)
                                        selected
                                    @endif
                                    > Tháng {{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        {{-- <div class="example-tools">
                            <button type="submit" class="btn btn-light-primary font-weight-bold ml-2 form-control form-control-sm">Xuất</button>
                        </div> --}}
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div id="chart_tranfer"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h3>Thống kê chi tiết nạp Ví - ATM tự động</h3>
                </div>
                <div class="card-toolbar">
                    <form action="" method="POST" class="d-flex justify-content-center">
                        {{ csrf_field() }}
                        <div class="example-tools justify-content-center mr-3">
                            <select class="form-control form-control-sm" name="year" id="tranfer-report-details-year">
                                <option value="{{$year}}">{{$year}}</option>
                                <option value="{{$year - 1}}">{{$year - 1}}</option>
                                <option value="{{$year - 2}}">{{$year - 2}}</option>
                                <option value="{{$year - 3}}">{{$year - 3}}</option>
                                <option value="{{$year - 4}}">{{$year - 4}}</option>
                            </select>
                        </div>
                        <div class="example-tools justify-content-center">
                            <select class="form-control form-control-sm" name="month" id="tranfer-report-details-month">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}" @if ($i == $month)
                                        selected
                                    @endif
                                    > Tháng {{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                    <!--begin: Datatable-->
                    <table class="table table-bordered table-hover table-checkable " id="kt_datatable_tranfer">
                    </table>
                    <!--end: Datatable-->
            </div>
        </div>
    </div>
</div>

@section('styles')

@endsection
@endrole
@endsection
{{-- Scripts Section --}}
@section('scripts')
    @role('admin')
    <script>
        const primary = '#6993FF';
        const success = '#1BC5BD';
        const info = '#8950FC';
        const warning = '#FFA800';
        const danger = '#F64E60';
        const drak = '#181c32';
        var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };
        // setup biểu đồ tăng trường thành viên
        var ChartsGrowthUser = function () {
            var _user = function (categories,growth) {
                const apexChart = "#growth_user";
                var options = {
                    series: [{
                        name: "Thành viên",
                        data: growth
                    }],
                    chart: {
                        height: 340,
                        type: 'line',
                        zoom: {
                            enabled: false
                        }
                    },
                    dataLabels: { 	
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight'
                    },
                    grid: {
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    xaxis: {
                        categories:categories,
                    },
                    colors: [success]
                };

                var chart = new ApexCharts(document.querySelector(apexChart), options);
                chart.render();
            }
            return {
                init: function (categories,growth) {
                    _user(categories,growth);
                }
            };
        }();
        // gọi data tăng trưởng thành viên
        function GrowthUser(year){
            $.ajax({
                type: "GET",
                url: "{{route('admin.growth.user')}}",
                data:{
                    year:year
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if(data.success == true){
                        var categories = data.data['growth_month'];
                        categories = $.map(categories, function(value, index) {
                            return [value];
                        });
                        var growth = data.data['growth_user'];
                        growth = $.map(growth, function(value, index) {
                            return [value];
                        });
                        ChartsGrowthUser.init(categories,growth);
                    }
                    else{
                        alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                    }
                },
                error: function (data) {
                    alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                },
                complete: function (data) {
                    
                }
            });
        }
        var ChartsGrowthCTV = function () {
            var _ctv = function (categories,growth) {
                const apexChart = "#growth_ctv";
                var options = {
                    series: [{
                        name: "Cộng tác viên",
                        data: growth
                    }],
                    chart: {
                        height: 340,
                        type: 'line',
                        zoom: {
                            enabled: false
                        }
                    },
                    dataLabels: { 	
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight'
                    },
                    grid: {
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    xaxis: {
                        categories:categories,
                    },
                    colors: [info]
                };

                var chart = new ApexCharts(document.querySelector(apexChart), options);
                chart.render();
            }
            return {
                init: function (categories,growth) {
                    _ctv(categories,growth);
                }
            };
        }();
        // gọi data tăng trưởng idol
        function GrowthCTV(year){
            $.ajax({
                type: "GET",
                url: "{{route('admin.growth.ctv')}}",
                data:{
                    year:year
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if(data.success == true){
                        var categories = data.data['growth_month'];
                        categories = $.map(categories, function(value, index) {
                            return [value];
                        });
                        var growth = data.data['growth_ctv'];
                        growth = $.map(growth, function(value, index) {
                            return [value];
                        });
                        ChartsGrowthCTV.init(categories,growth);
                    }
                    else{
                        alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                    }
                },
                error: function (data) {
                    alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                },
                complete: function (data) {
                    
                }
            });
        }
        // setup biểu đồ thống kê thành viên
        var ChartsClassifyhUser = function () {
            var classify_user = function (idol,pedding_idol,user,user_block,user_qtv) {
            const apexChart = "#classify_user";
            var options = {
                series: [idol,pedding_idol,user,user_block,user_qtv],
                chart: {
                    width: 555,
                    type: 'pie',
                },
                labels: ['Idol', 'Chờ duyệt Idol', 'Thành viên', 'Thành viên bị khóa', 'QTV'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 350
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                colors: [primary, warning ,success, danger, info]
            };

            var chart = new ApexCharts(document.querySelector(apexChart), options);
            chart.render();
	    }
        return {
                init: function (idol,pedding_idol,user,user_block,user_qtv) {
                    classify_user(idol,pedding_idol,user,user_block,user_qtv);
                }
            };

        }();
          // gọi data thống kê thành viên
        function ClassifyUser(){
            $.ajax({
                type: "GET",
                url: "{{route('admin.classify.user')}}",
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if(data.success == true){
                        var idol = data.data.idol;
                        var pedding_idol = data.data.pedding_idol;
                        var user = data.data.user;
                        var user_block = data.data.user_block;
                        var user_qtv = data.data.user_qtv;
                        ChartsClassifyhUser.init(idol,pedding_idol,user,user_block,user_qtv)
                    }
                    else{
                        alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                    }
                },
                error: function (data) {
                    alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                },
                complete: function (data) {
                    
                }
            });
        }
        //setup biểu đồ nạp thẻ
        var ChartsTopupCard = function () {
            var topup_card = function (growth_card_fail,growth_card_susscess,growth_card_pendding,growth_day) {
            const apexChart = "#topup_card";
            var options = {
                series: [
                    {
                        name: 'Thẻ sai',
                        data: growth_card_fail
                    },
                    {
                        name: 'Thẻ đúng',
                        data: growth_card_susscess
                    },
                    {
                        name: 'Đang chờ',
                        data: growth_card_pendding
                    },
                ],
                chart: {
                    height: 430,
                    type: 'area'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: growth_day
                },
                colors: [danger,success,warning]
            };

            var chart = new ApexCharts(document.querySelector(apexChart), options);
            chart.render();
        }
        return {
                init: function (growth_card_fail,growth_card_susscess,growth_card_pendding,growth_day) {
                    topup_card(growth_card_fail,growth_card_susscess,growth_card_pendding,growth_day);
                }
            };

        }();
        function TopupCard(year,month){
            $.ajax({
                type: "GET",
                url: "{{route('admin.growth.topup-card')}}",
                data:{
                    year:year,
                    month:month
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if(data.success == true){
                        var growth_card_fail = data.data.growth_card_fail;
                        growth_card_fail = $.map(growth_card_fail, function(value, index) {
                            return [value];
                        });
                        var growth_card_susscess = data.data.growth_card_susscess;
                        growth_card_susscess = $.map(growth_card_susscess, function(value, index) {
                            return [value];
                        });
                        var growth_card_pendding = data.data.growth_card_pendding;
                        growth_card_pendding = $.map(growth_card_pendding, function(value, index) {
                            return [value];
                        });
                        var growth_day = data.data.growth_day;
                        growth_day = $.map(growth_day, function(value, index) {
                            return [value];
                        });
                        ChartsTopupCard.init(growth_card_fail,growth_card_susscess,growth_card_pendding,growth_day);
                    }
                    else{
                        alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                    }
                },
                error: function (data) {
                    alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                },
                complete: function (data) {
                    
                }
            });
        }
        //setup biểu đồ mua thẻ
        var ChartsStoreCard = function () {
            var store_card = function (growth_fail,growth_susscess,growth_pendding,growth_day) {
            const apexChart = "#store_card";
            var options = {
                series: [
                    {
                        name: 'Thất bại',
                        data: growth_fail
                    },
                    {
                        name: 'Thành công',
                        data: growth_susscess
                    },
                    {
                        name: 'Đang chờ',
                        data: growth_pendding
                    },
                ],
                chart: {
                    height: 430,
                    type: 'area'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: growth_day
                },
                colors: [danger,success,warning]
            };

            var chart = new ApexCharts(document.querySelector(apexChart), options);
            chart.render();
        }
        return {
                init: function (growth_fail,growth_susscess,growth_pendding,growth_day) {
                    store_card(growth_fail,growth_susscess,growth_pendding,growth_day);
                }
            };

        }();
        function StoreCard(year,month){
            $.ajax({
                type: "GET",
                url: "{{route('admin.growth.store-card')}}",
                data:{
                    year:year,
                    month:month
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if(data.success == true){
                        var growth_fail = data.data.growth_fail;
                        growth_fail = $.map(growth_fail, function(value, index) {
                            return [value];
                        });
                        var growth_susscess = data.data.growth_susscess;
                        growth_susscess = $.map(growth_susscess, function(value, index) {
                            return [value];
                        });
                        var growth_pendding = data.data.growth_pendding;
                        growth_pendding = $.map(growth_pendding, function(value, index) {
                            return [value];
                        });
                        var growth_day = data.data.growth_day;
                        growth_day = $.map(growth_day, function(value, index) {
                            return [value];
                        });
                        ChartsStoreCard.init(growth_fail,growth_susscess,growth_pendding,growth_day);
                    }
                    else{
                        alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                    }
                },
                error: function (data) {
                    alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                },
                complete: function (data) {
                    
                }
            });
        }
        // setup biểu đồ nạp tiền qua tài khoản ngân hàng
        var ChartsTopupBank = function () {
            var topup_bank = function (growth_fail,growth_susscess,growth_pendding,data_cancelled,growth_day) {
            const apexChart = "#topup_bank";
            var options = {
                series: [
                    {
                        name: 'Thất bại',
                        data: growth_fail
                    },
                    {
                        name: 'Thành công',
                        data: growth_susscess
                    },
                    {
                        name: 'Đang chờ thanh toán',
                        data: growth_pendding
                    },
                    {
                        name: 'Đã hủy',
                        data: data_cancelled
                    },
                ],
                chart: {
                    height: 350,
                    type: 'area'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: growth_day
                },
                colors: [danger,success,warning,primary]
            };

            var chart = new ApexCharts(document.querySelector(apexChart), options);
            chart.render();
        }
        return {
                init: function (growth_fail,growth_susscess,growth_pendding,data_cancelled,growth_day) {
                    topup_bank(growth_fail,growth_susscess,growth_pendding,data_cancelled,growth_day);
                }
            };

        }();
        function TopupBank(year,month){
            $.ajax({
                type: "GET",
                url: "{{route('admin.growth.topup-bank')}}",
                data:{
                    year:year,
                    month:month
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if(data.success == true){
                        var growth_fail = data.data.growth_fail;
                        growth_fail = $.map(growth_fail, function(value, index) {
                            return [value];
                        });
                        var growth_susscess = data.data.growth_susscess;
                        growth_susscess = $.map(growth_susscess, function(value, index) {
                            return [value];
                        });
                        var growth_pendding = data.data.growth_pendding;
                        growth_pendding = $.map(growth_pendding, function(value, index) {
                            return [value];
                        });
                        var growth_cancelled = data.data.growth_cancelled;
                        growth_cancelled = $.map(growth_cancelled, function(value, index) {
                            return [value];
                        });
                        var growth_day = data.data.growth_day;
                        growth_day = $.map(growth_day, function(value, index) {
                            return [value];
                        });
                        ChartsTopupBank.init(growth_fail,growth_susscess,growth_pendding,growth_cancelled,growth_day);
                    }
                    else{
                        alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                    }
                },
                error: function (data) {
                    alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                },
                complete: function (data) {
                    
                }
            });
        }
        // setup biểu đồ nạp ví atm
        var ChartsTranfer = function () {
            var charts_tranfer = function (growth_fail,growth_susscess,growth_pendding,growth_day) {
            const apexChart = "#chart_tranfer";
            var options = {
                series: [
                    {
                        name: 'Thất bại',
                        data: growth_fail
                    },
                    {
                        name: 'Thành công',
                        data: growth_susscess
                    },
                    {
                        name: 'Đang chờ',
                        data: growth_pendding
                    },
                ],
                chart: {
                    height: 430,
                    type: 'area'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: growth_day
                },
                colors: [danger,success,warning]
            };

            var chart = new ApexCharts(document.querySelector(apexChart), options);
            chart.render();
        }
        return {
                init: function (growth_fail,growth_susscess,growth_pendding,growth_day) {
                    charts_tranfer(growth_fail,growth_susscess,growth_pendding,growth_day);
                }
            };

        }();
        function Tranfer(year,month){
            $.ajax({
                type: "GET",
                url: "{{route('admin.growth.tranfer')}}",
                data:{
                    year:year,
                    month:month
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if(data.success == true){
                        var growth_fail = data.data.growth_fail;
                        growth_fail = $.map(growth_fail, function(value, index) {
                            return [value];
                        });
                        var growth_susscess = data.data.growth_susscess;
                        growth_susscess = $.map(growth_susscess, function(value, index) {
                            return [value];
                        });
                        var growth_pendding = data.data.growth_pendding;
                        growth_pendding = $.map(growth_pendding, function(value, index) {
                            return [value];
                        });
                        var growth_day = data.data.growth_day;
                        growth_day = $.map(growth_day, function(value, index) {
                            return [value];
                        });
                        ChartsTranfer.init(growth_fail,growth_susscess,growth_pendding,growth_day);
                    }
                    else{
                        alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                    }
                },
                error: function (data) {
                    alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                },
                complete: function (data) {
                    
                }
            });
        }





        // setup biểu đồ thống kê shop và nhóm shop
        var ChartsClassifyhShopGroup = function () {
            var classify_shop_group = function (shop_count,title) {
            const apexChart = "#classify_shop_group";
            var options = {
                series: shop_count,
                chart: {
                    width: 555,
                    type: 'pie',
                },
                labels: title,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 350
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
            };

            var chart = new ApexCharts(document.querySelector(apexChart), options);
            chart.render();
	    }
        return {
                init: function (shop_count,title) {
                    classify_shop_group(shop_count,title);
                }
            };
        }();
          // gọi data thống kê điểm bán, nhóm điểm bán
        function ClassifyShopGroup(){
            $.ajax({
                type: "GET",
                url: "{{route('admin.classify.shop-group')}}",
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if(data.success == true){
                        var shop_count = data.data.shop_count;
                        var title = data.data.title;
                        ChartsClassifyhShopGroup.init(shop_count,title)
                    }
                    else{
                        alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                    }
                },
                error: function (data) {
                    alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                },
                complete: function (data) {
                    
                }
            });
        }


         // setup biểu đồ tăng trưởng điểm bán
         var ChartsGrowthShop = function () {
            var _shop = function (categories,growth) {
                const apexChart = "#growth_shop";
                var options = {
                    series: [{
                        name: "Điểm bán",
                        data: growth
                    }],
                    chart: {
                        height: 220,
                        type: 'line',
                        zoom: {
                            enabled: false
                        }
                    },
                    dataLabels: { 	
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight'
                    },
                    grid: {
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    xaxis: {
                        categories:categories,
                    },
                    colors: [primary]
                };

                var chart = new ApexCharts(document.querySelector(apexChart), options);
                chart.render();
            }
            return {
                init: function (categories,growth) {
                    _shop(categories,growth);
                }
            };
        }();
        // gọi data tăng trưởng thành viên
        function GrowthShop(year){
            $.ajax({
                type: "GET",
                url: "{{route('admin.growth.shop')}}",
                data:{
                    year:year
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if(data.success == true){
                        var categories = data.data['growth_month'];
                        categories = $.map(categories, function(value, index) {
                            return [value];
                        });
                        var growth = data.data['growth_shop'];
                        growth = $.map(growth, function(value, index) {
                            return [value];
                        });
                        ChartsGrowthShop.init(categories,growth);
                    }
                    else{
                        alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                    }
                },
                error: function (data) {
                    alert('Có lỗi xảy ra vui lòng liên hệ Admin để xử lý');
                        return false;
                },
                complete: function (data) {
                    
                }
            });
        }

        var KTDatatablesDataSourceAjaxServer = function () {
            var initTable1 = function () {
                // begin first table
                datatable = $('#kt_datatable').DataTable({
                    responsive: true,
                    lengthMenu: [20, 50, 100, 200,500,1000],
                    pageLength: 20,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    sScrollY: "250px",
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "order": [[1, "desc"]],
                    ajax: {
                        url: '{{route("admin.chart.report-charge")}}' + '?ajax=1',
                        type: 'GET',
                        data: function (d) {
                            d.month = $('#charge-report-details-month').val();
                            d.year = $('#charge-report-details-year').val();
                        }
                    },
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
                        {data: 'id', title: 'ID'},
                        {data: 'shop_title', title: 'Điểm bán'},
                        {
                            data: 'total_record', title: '{{__('Tổng số GD')}}',
                            render: function (data, type, row) {
                                return "<b>"+row.total_record+"</b>";
                            }
                        },
                        {
                            data: 'total_record_success', title: '{{__('GD Thành công')}}',
                            render: function (data, type, row) {
                                return "<b style='color:#1BC5BD'>"+row.total_record_success+"</b>";
                            }
                        },
                        {
                            data: 'total_record_pendding', title: '{{__('GD Đang chờ')}}',
                            render: function (data, type, row) {
                                return "<b style='color:#FFA800'>"+row.total_record_pendding+"</b>";
                            }
                        },
                        {
                            data: 'total_record_error', title: '{{__('GD Thất bại')}}',
                            render: function (data, type, row) {
                                return "<b style='color:#F64E60'>"+row.total_record_error+"</b>";
                            }
                        },
                        {
                            data: 'total_amount', title: '{{__('Tổng thực nhận')}}',
                            render: function (data, type, row) {
                                return "<b style='color:#181C32'>"+row.total_amount+"</b>";
                            }
                        },
                    ],
                    "drawCallback": function (settings) {
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
                        datatable.column(i).search(val ? val : '', false, false);
                    });
                    datatable.table().draw();
                });
            };
            return {
                init: function () {
                    initTable1();
                },

            };
        }();
        var KTDatatablesDataSourceAjaxServerReportStoreCard = function () {
            var initTable2 = function () {
                // begin first table
                datatable = $('#kt_datatable_charge_store_card').DataTable({
                    responsive: true,
                    lengthMenu: [20, 50, 100, 200,500,1000],
                    pageLength: 20,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    sScrollY: "250px",
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "order": [[1, "desc"]],
                    ajax: {
                        url: '{{route("admin.chart.report-store-card")}}' + '?ajax=1',
                        type: 'GET',
                        data: function (d) {
                            d.month = $('#store-card-report-details-month').val();
                            d.year = $('#store-card-report-details-year').val();
                        }
                    },
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
                        {data: 'id', title: 'ID'},
                        {data: 'shop_title', title: 'Điểm bán'},
                        {
                            data: 'total_record', title: '{{__('Tổng số GD')}}',
                            render: function (data, type, row) {
                                return "<b>"+row.total_record+"</b>";
                            }
                        },
                        {
                            data: 'total_record_success', title: '{{__('GD Thành công')}}',
                            render: function (data, type, row) {
                                return "<b style='color:#1BC5BD'>"+row.total_record_success+"</b>";
                            }
                        },
                        {
                            data: 'total_record_pendding', title: '{{__('GD Đang chờ')}}',
                            render: function (data, type, row) {
                                return "<b style='color:#FFA800'>"+row.total_record_pendding+"</b>";
                            }
                        },
                        {
                            data: 'total_record_error', title: '{{__('GD Thất bại')}}',
                            render: function (data, type, row) {
                                return "<b style='color:#F64E60'>"+row.total_record_error+"</b>";
                            }
                        },
                        {
                            data: 'total_amount', title: '{{__('Tổng thực nhận')}}',
                            render: function (data, type, row) {
                                return "<b style='color:#181C32'>"+row.total_amount+"</b>";
                            }
                        },
                    ],
                    "drawCallback": function (settings) {
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
                        datatable.column(i).search(val ? val : '', false, false);
                    });
                    datatable.table().draw();
                });
            };
            return {
                init: function () {
                    initTable2();
                },

            };
        }();
        var KTDatatablesDataSourceAjaxServerReportTranfer = function () {
            var initTable3 = function () {
                // begin first table
                datatable = $('#kt_datatable_tranfer').DataTable({
                    responsive: true,
                    lengthMenu: [20, 50, 100, 200,500,1000],
                    pageLength: 20,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    sScrollY: "250px",
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "order": [[1, "desc"]],
                    ajax: {
                        url: '{{route("admin.chart.report-tranfer")}}' + '?ajax=1',
                        type: 'GET',
                        data: function (d) {
                            d.month = $('#tranfer-report-details-month').val();
                            d.year = $('#tranfer-report-details-year').val();
                        }
                    },
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
                        {data: 'id', title: 'ID'},
                        {data: 'shop_title', title: 'Điểm bán'},
                        {data: 'ratio_atm', title: 'Chiết khấu'},
                        {
                            data: 'total_record', title: '{{__('Tổng số GD')}}',
                            render: function (data, type, row) {
                                return "<b>"+row.total_record+"</b>";
                            }
                        },
                        {
                            data: 'total_amount', title: '{{__('Tổng tiền chuyển khoản')}}',
                            render: function (data, type, row) {
                                return "<b style='color:#181C32'>"+row.total_amount+"</b>";
                            }
                        },
                        {
                            data: 'total_real_received_price', title: '{{__('Tổng thực nhận thành viên')}}',
                            render: function (data, type, row) {
                                return "<b style='color:#181C32'>"+row.total_real_received_price+"</b>";
                            }
                        },
                    ],
                    "drawCallback": function (settings) {
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
                        datatable.column(i).search(val ? val : '', false, false);
                    });
                    datatable.table().draw();
                });
            };
            return {
                init: function () {
                    initTable3();
                },

            };
        }();

        function newexportaction(e, dt, button, config) {
            $(button).text("Đang tải...");
            $(button).prop('disabled', true);
            var self = this;
            var oldStart = dt.settings()[0]._iDisplayStart;
            dt.one('preXhr', function (e, s, data) {
                data.start = 0;
                data.length = 2147483647;
                dt.one('preDraw', function (e, settings) {
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
                    $(button).text("Xuất excel");
                    $(button).prop('disabled', false);
                    return false;
                });
            });
            dt.ajax.reload();
        };
        jQuery(document).ready(function () {
            ClassifyShopGroup();
            GrowthShop();
            $('body').on('change','#growth_shop_year',function(){
                year = $(this).val();
                $("#growth_shop").fadeOut(700,function(){
                    $( "#growth_shop" ).load(window.location.href + " #growth_shop" );
                    GrowthShop(Number(year))
                    setTimeout(function(){
                        $("#growth_shop").fadeIn(700);
                     }, 2000);
                });
            })
            GrowthUser();
            $('body').on('change','#growth_user_year',function(){
                year = $(this).val();
                $("#growth_user").fadeOut(700,function(){
                    $( "#growth_user" ).load(window.location.href + " #growth_user" );
                    GrowthUser(Number(year))
                    setTimeout(function(){
                        $("#growth_user").fadeIn(700);
                     }, 2000);
                });
            })
            GrowthCTV();
            $('body').on('change','#growth_ctv_year',function(){
                year = $(this).val();
                $("#growth_idol").fadeOut(700,function(){
                    $( "#growth_idol" ).load(window.location.href + " #growth_idol" );
                    GrowthCTV(Number(year))
                    setTimeout(function(){
                        $("#growth_idol").fadeIn(700);
                     }, 2000);
                });
            })
            TopupCard();
            $('body').on('change','#topup_card_year',function(){
                year = $(this).val();
                month = $('#topup_card_month').val();
                $("#topup_card").fadeOut(700,function(){
                    $( "#topup_card" ).load(window.location.href + " #topup_card" );
                    TopupCard(Number(year),Number(month))
                    setTimeout(function(){
                        $("#topup_card").fadeIn(700);
                     }, 4000);
                });
            })
            $('body').on('change','#topup_card_month',function(){
                month = $(this).val();
                year = $('#topup_card_year').val();
                $("#topup_card").fadeOut(700,function(){
                    $( "#topup_card" ).load(window.location.href + " #topup_card" );
                    TopupCard(Number(year),Number(month))
                    setTimeout(function(){
                        $("#topup_card").fadeIn(700);
                     }, 4000);
                });
            })
            KTDatatablesDataSourceAjaxServer.init();
            $('body').on('change','#charge-report-details-year',function(){
                $("#kt_datatable").DataTable().ajax.reload();
            })
            $('body').on('change','#charge-report-details-month',function(){
                $("#kt_datatable").DataTable().ajax.reload();
            })
            StoreCard();
            $('body').on('change','#store_card_year',function(){
                year = $(this).val();
                month = $('#store_card_month').val();
                $("#store_card").fadeOut(700,function(){
                    $( "#store_card" ).load(window.location.href + " #store_card" );
                    StoreCard(Number(year),Number(month))
                    setTimeout(function(){
                        $("#store_card").fadeIn(700);
                     }, 4000);
                });
            })
            $('body').on('change','#store_card_month',function(){
                month = $(this).val();
                year = $('#store_card_year').val();
                $("#store_card").fadeOut(700,function(){
                    $( "#store_card" ).load(window.location.href + " #store_card" );
                    StoreCard(Number(year),Number(month))
                    setTimeout(function(){
                        $("#store_card").fadeIn(700);
                     }, 4000);
                });
            })
            KTDatatablesDataSourceAjaxServerReportStoreCard.init();
            $('body').on('change','#store-card-report-details-year',function(){
                $("#kt_datatable_charge_store_card").DataTable().ajax.reload();
            })
            $('body').on('change','#store-card-report-details-month',function(){
                $("#kt_datatable_charge_store_card").DataTable().ajax.reload();
            })
            Tranfer();
            $('body').on('change','#tranfer_year',function(){
                year = $(this).val();
                month = $('#tranfer_month').val();
                $("#chart_tranfer").fadeOut(700,function(){
                    $( "#chart_tranfer" ).load(window.location.href + " #topup_card" );
                    Tranfer(Number(year),Number(month))
                    setTimeout(function(){
                        $("#chart_tranfer").fadeIn(700);
                     }, 4000);
                });
            })
            $('body').on('change','#tranfer_month',function(){
                month = $(this).val();
                year = $('#tranfer_year').val();
                $("#chart_tranfer").fadeOut(700,function(){
                    $( "#chart_tranfer" ).load(window.location.href + " #topup_card" );
                    Tranfer(Number(year),Number(month))
                    setTimeout(function(){
                        $("#chart_tranfer").fadeIn(700);
                     }, 4000);
                });
            })
            KTDatatablesDataSourceAjaxServerReportTranfer.init();
            $('body').on('change','#tranfer-report-details-year',function(){
                $("#kt_datatable_tranfer").DataTable().ajax.reload();
            })
            $('body').on('change','#tranfer-report-details-month',function(){
                $("#kt_datatable_tranfer").DataTable().ajax.reload();
            })
        });
    </script>
    @endrole
    <script type="text/javascript">
        $('.parrent').on('click', function(e){
            var id= $(this).attr('rel');

            $(".children_"+id).toggle();

        });
    </script>
@endsection