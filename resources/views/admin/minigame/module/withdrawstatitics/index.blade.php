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
                    {{--started_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="started_at" id="started_at" autocomplete="off"
                                   class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian bắt đầu')}}" data-toggle="datetimepicker" value="{{request('started_at')}}">

                        </div>
                    </div>

                    {{--ended_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Đến</span>
                            </div>
                            <input type="text" name="ended_at" id="ended_at" autocomplete="off"
                                   class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian kết thúc')}}" data-toggle="datetimepicker" value="{{request('ended_at')}}">

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
                            <a href="#" data-started-at="{{\Carbon\Carbon::now()->startOfMonth()->subMonth()->format('d/m/Y H:i:s')}}"  data-ended-at="{{\Carbon\Carbon::now()->endOfMonth()->subMonth()->format('d/m/Y H:i:s')}}" class="btn btn-info btn-filter-date">Tháng trước</a>
                        </div>
                    </div>
                </div>

            </form>
            <!--begin: Search Form-->

            <div id="table_main_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="dataTables_scroll">

                            <div class="dataTables_scrollBody" style="position: relative; overflow: auto; width: 100%;">
                                <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table_main" role="grid" aria-describedby="table_main_info" style="width: 988px;">
                                    <thead>
                                    <tr role="row " class="b-header">
                                        <th style="background-color: #01a6f5; color: #fff">Tên loại vật phẩm</th>
                                        <th style="background-color: #01a6f5; color: #fff">Tổng số lượt rút</th>
                                        <th style="background-color: #01a6f5; color: #fff">Tổng vật phẩm đã rút</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $total_withdraw=0;
                                        $total_item=0;
                                    @endphp
                                    @foreach($datatable as $itemCat)
                                        {{--begin header--}}
                                        <tr id="parrent{{$itemCat->id}}" class="parrentClass" data-id="{{$itemCat->id}}" style=" background-color: #e0ebf9;font-weight: bold" >
                                            <td class="parrent" rel="{{$itemCat->id}}" style="cursor: pointer">
                                                {{$itemCat->title}}
                                            </td>
                                            @php
                                                $gametypeorder = $itemCat->gametypeorder;
                                            @endphp

                                            @if($gametypeorder!=null)
                                                @if(request('started_at')!=null)
                                                    @php
                                                        $gametypeorder = $gametypeorder->where('created_at', '>=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', request('started_at')));
                                                    @endphp
                                                @endif
                                                @if(request('ended_at')!=null)
                                                    @php
                                                        $gametypeorder = $gametypeorder->where('created_at', '<=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', request('ended_at')));
                                                    @endphp
                                                @endif
                                                @if(session('shop_id'))
                                                    @php
                                                        $gametypeorder = $gametypeorder->where('shop_id',session('shop_id'));
                                                    @endphp
                                                @endif
                                                <td>
                                                    {{number_format($gametypeorder->count())}}
                                                </td>
                                                <td>
                                                    {{number_format($gametypeorder->sum("price"))}}
                                                </td>
                                                @php
                                                    $total_withdraw=$total_withdraw+$gametypeorder->count();
                                                    $total_item=$total_item+$gametypeorder->sum("price");
                                                @endphp
                                            @else
                                                <td>0</td><td>0</td>
                                            @endif
                                        </tr>
                                    @endforeach

                                    <tr style="background-color: #abe7ed;font-weight: bold">

                                        <td> Tổng cộng tất cả</td>
                                        <td>
                                            {{number_format($total_withdraw)}}
                                        </td>
                                        <td>
                                            {{number_format($total_item)}}
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {

            $('#kt_reset').on('click', function (e) {
                e.preventDefault();
                $('.datatable-input').each(function () {
                    $(this).val('');
                });
                $('#kt_search').click();
            });

            $('.btn-filter-date').on('click',function (e) {
                e.preventDefault();
                console.log('fdsafads')
                var startedAt=$(this).data('started-at');
                var endeddAt=$(this).data('ended-at');

                $('#started_at').val(startedAt);
                $('#ended_at').val(endeddAt);
                $('#kt_search').click();
            });
        });
    </script>

@endsection
