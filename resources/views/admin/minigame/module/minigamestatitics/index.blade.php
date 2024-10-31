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
                        <button class="btn btn-danger m-btn m-btn--icon open_more" id="m_search">
                            <span>
                                <i class="la la-refresh"></i>
                                <span>Mở rộng</span>
                            </span>
                        </button>&#160;&#160;
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
                    <div class="col-md-6">
                        @if ( auth()->user()->can('store-card-export'))
                            <button class="btn btn-danger btn-secondary--icon" type="submit">
                        <span>
                            <i class="flaticon-folder-2"></i>
                            <span>Xuất Excel</span>
                        </span>
                            </button>
                        @endif
                    </div>
                </div>


            </form>
            <!--begin: Search Form-->

            {{--            @if(session('shop_id'))--}}
            <div id="table_main_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="dataTables_scroll">

                            <div class="dataTables_scrollBody" style="position: relative; overflow: auto; width: 100%;">
                                <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table_main" role="grid" aria-describedby="table_main_info" style="width: 988px;">
                                    <thead>
                                    <tr role="row " class="b-header">
                                        <th style="background-color: #01a6f5; color: #fff">ID danh mục</th>
                                        <th style="background-color: #01a6f5; color: #fff">Tên danh mục</th>
                                        <th style="background-color: #01a6f5; color: #fff">Tổng vật phẩm đã trúng</th>
                                        <th style="background-color: #01a6f5; color: #fff">Tổng lượt quay</th>
                                        <th style="background-color: #01a6f5; color: #fff">Tổng tiền đã quay</th>
                                        <th style="background-color: #01a6f5; color: #fff">Nick chưa trao</th>
                                        <th style="background-color: #01a6f5; color: #fff">Nick đã trao</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $total_item=0;
                                        $total_money=0;
                                        $total_flip=0;
                                        $total_nickoffall=0;
                                    @endphp
                                    @foreach($all as $datatable)
                                        <tr id="parrent{{$datatable['id']}}" class="parrentClass" data-id="{{$datatable['id']}}" style=" background-color: #c0e0ff;font-weight: bold;text-transform: uppercase;" >
                                            <td class="parrent" rel="{{$datatable['id']}}" style="cursor: pointer" colspan="2">
                                                <i class="fa fa-arrow-right" style="font-size: 12px; color: #000"></i>
                                                &nbsp;{{$datatable['title']}}
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>

                                        @foreach($datatable['data'] as $itemCat)

                                            {{--begin header--}}
                                            <tr class="children_{{$datatable['id']}}" data-id="{{$datatable['id']}}" style=" background-color: #e0ebf9;font-weight: bold;display: none">
                                                <td class="parrentp" rel="{{$itemCat->id}}" style="cursor: pointer">
                                                    <i class="flaticon-plus" style="font-size: 12px"></i>
                                                    {{$itemCat->id}}
                                                </td>
                                                <td>
                                                    {{ isset($itemCat->customs) && count($itemCat->customs) ?  $itemCat->customs[0]->title : $itemCat->title}}
                                                </td>
                                                @php
                                                    $gateorder = $itemCat->order_gate;
                                                @endphp

                                                @if(request('started_at')!=null)
                                                    @php
                                                        $gateorder = $gateorder->where('created_at', '>=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', request('started_at')));
                                                    @endphp
                                                @else
                                                    @php
                                                        $gateorder = $gateorder->where('created_at', '>=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->startOfDay()->format('d/m/Y H:i:s')));
                                                    @endphp
                                                @endif
                                                @if(request('ended_at')!=null)
                                                    @php
                                                        $gateorder = $gateorder->where('created_at', '<=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', request('ended_at')));
                                                    @endphp
                                                @else
                                                    @php
                                                        $gateorder = $gateorder->where('created_at', '<=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->endOfDay()->format('d/m/Y H:i:s')));
                                                    @endphp
                                                @endif
                                                @php
                                                    $real_received_price_order = $gateorder->sum("real_received_price");
                                                    $value_gif_bonus_order = $gateorder->sum("value_gif_bonus");
                                                    $total_vp_order = $real_received_price_order + $value_gif_bonus_order;
                                                @endphp
                                                <td>
                                                    {{number_format($total_vp_order)}}
                                                </td>
                                                <td>
                                                    {{number_format($gateorder->count())}}
                                                </td>
                                                <td>
                                                    {{number_format($gateorder->sum('price'))}}
                                                </td>
                                                @if($itemCat->items!=null)
                                                    @php
                                                        $total_nickoff = 0;
                                                    @endphp
                                                    @foreach($itemCat->items as $item)
                                                        @php
                                                            $minigame = $item->minigameorder;
                                                        @endphp
                                                        @if(request('started_at')!=null)
                                                            @php
                                                                $minigame = $minigame->where('created_at', '>=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', request('started_at')));
                                                            @endphp
                                                        @else
                                                            @php
                                                                $minigame = $minigame->where('created_at', '>=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->startOfDay()->format('d/m/Y H:i:s')));
                                                            @endphp
                                                        @endif
                                                        @if(request('ended_at')!=null)
                                                            @php
                                                                $minigame = $minigame->where('created_at', '<=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', request('ended_at')));
                                                            @endphp
                                                        @else
                                                            @php
                                                                $minigame = $minigame->where('created_at', '<=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->endOfDay()->format('d/m/Y H:i:s')));
                                                            @endphp
                                                        @endif
                                                        @php
                                                            $total_nickoff = $total_nickoff + $minigame->whereNotNull('acc_id')->count();
                                                        @endphp
                                                    @endforeach
                                                @endif
                                                <td></td>
                                                <td>
                                                    {{number_format($total_nickoff)}}
                                                </td>
                                            </tr>
                                            @if($itemCat->items!=null)
                                                @foreach($itemCat->items as $item)
                                                    <tr class="childrenp_{{$itemCat->id}}" style="display: none">
                                                        <td>{{$item->id}}</td>
                                                        <td>{{$item->title}}</td>
                                                        @php
                                                            $minigame = $item->minigameorder;
                                                        @endphp

                                                        @if(request('started_at')!=null)
                                                            @php
                                                                $minigame = $minigame->where('created_at', '>=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', request('started_at')));
                                                            @endphp
                                                        @else
                                                            @php
                                                                $minigame = $minigame->where('created_at', '>=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->startOfDay()->format('d/m/Y H:i:s')));
                                                            @endphp
                                                        @endif
                                                        @if(request('ended_at')!=null)
                                                            @php
                                                                $minigame = $minigame->where('created_at', '<=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', request('ended_at')));
                                                            @endphp
                                                        @else
                                                            @php
                                                                $minigame = $minigame->where('created_at', '<=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', \Carbon\Carbon::now()->endOfDay()->format('d/m/Y H:i:s')));
                                                            @endphp
                                                        @endif
                                                        @php
                                                            $real_received_price = $minigame->sum("real_received_price");
                                                            $value_gif_bonus = $minigame->sum("value_gif_bonus");
                                                            $total_vp = $real_received_price + $value_gif_bonus;
                                                        @endphp

                                                        <td>{{number_format($total_vp)}}</td>
                                                        <td>{{number_format($minigame->count())}}</td>
                                                        <td>{{number_format($minigame->sum("price"))}}</td>
                                                        <td></td>
                                                        <td>{{$minigame->whereNotNull('acc_id')->count()}}</td>
                                                    </tr>
                                                    @php
                                                        $total_item=$total_item + $minigame->sum("real_received_price");
                                                        $total_flip=$total_flip + $minigame->count();
                                                        $total_money=$total_money + $minigame->sum("price");
                                                        $total_nickoffall=$total_nickoffall + $minigame->whereNotNull('acc_id')->count();
                                                    @endphp
                                                @endforeach
                                            @endif

                                        @endforeach
                                    @endforeach
                                    <tr style="background-color: #abe7ed;font-weight: bold">

                                        <td colspan="2"> Tổng cộng tất cả</td>
                                        <td>
                                            {{number_format($total_item)}}
                                        </td>
                                        <td>
                                            {{$total_flip}}
                                        </td>
                                        <td>
                                            {{number_format($total_money)}}
                                        </td>
                                        <td>
                                            {{number_format($totalnickon)}}
                                        </td>
                                        <td>
                                            {{$total_nickoffall}}
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            {{--            @endif--}}

            {{--            <div id="table_main_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">--}}
            {{--                <div class="row">--}}
            {{--                    <div class="col-sm-12">--}}
            {{--                        <div class="dataTables_scroll">--}}

            {{--                            <div class="dataTables_scrollBody" style="position: relative; overflow: auto; width: 100%;">--}}
            {{--                                <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline" id="table_main" role="grid" aria-describedby="table_main_info" style="width: 988px;">--}}
            {{--                                    <thead>--}}
            {{--                                    <tr role="row " class="b-header">--}}
            {{--                                        <th style="background-color: #01a6f5; color: #fff">Loại game</th>--}}
            {{--                                        <th style="background-color: #01a6f5; color: #fff">Số lượt đã rút</th>--}}
            {{--                                        <th style="background-color: #01a6f5; color: #fff">Số vật phẩm đã rút</th>--}}
            {{--                                        <th style="background-color: #01a6f5; color: #fff">Số lượt chờ xử lý</th>--}}
            {{--                                        <th style="background-color: #01a6f5; color: #fff">Số vật phẩm chờ xử lý</th>--}}
            {{--                                        <th style="background-color: #01a6f5; color: #fff">Số vật phẩm còn lại</th>--}}
            {{--                                    </tr>--}}
            {{--                                    </thead>--}}
            {{--                                    <tbody>--}}
            {{--                                        @if(session('shop_id'))--}}
            {{--                                            @foreach(config('module.minigame.game_type') as $item =>$key)--}}
            {{--                                            <tr>--}}

            {{--                                                @php--}}
            {{--                                                    $data = $datatable_withdraw->where('parent_id', $item);--}}

            {{--                                                    $total_withdraw = 0;--}}
            {{--                                                    $price_withdraw = 0;--}}
            {{--                                                    $total_withdraw_service = 0;--}}
            {{--                                                    $price_withdraw_service = 0;--}}
            {{--                                                    $total_pending_withdraw = 0;--}}
            {{--                                                    $price_pending_withdraw = 0;--}}
            {{--                                                    $total_pending_withdraw_service = 0;--}}
            {{--                                                    $price_pending_withdraw_service = 0;--}}

            {{--                                                    foreach($datatable_withdraw as $with){--}}
            {{--                                                        if($with->parent_id == $item){--}}
            {{--                                                            $total_withdraw = $with->total_withdraw;--}}
            {{--                                                            $price_withdraw = $with->price_withdraw;--}}
            {{--                                                        }--}}
            {{--                                                    }--}}

            {{--                                                    foreach($datatable_withdraw_service as $with_service){--}}
            {{--                                                        if($with_service->parent_id == $item){--}}
            {{--                                                            $total_withdraw_service = $with_service->total_withdraw;--}}
            {{--                                                            $price_withdraw_service = $with_service->price_withdraw;--}}
            {{--                                                        }--}}
            {{--                                                    }--}}

            {{--                                                    foreach($datatable_pending_withdraw as $with_pending){--}}
            {{--                                                        if($with_pending->parent_id == $item){--}}
            {{--                                                            $total_pending_withdraw = $with_pending->total_withdraw;--}}
            {{--                                                            $price_pending_withdraw = $with_pending->price_withdraw;--}}
            {{--                                                        }--}}
            {{--                                                    }--}}

            {{--                                                    foreach($datatable_pending_withdraw_service as $with_pending_service){--}}
            {{--                                                        if($with_pending_service->parent_id == $item){--}}
            {{--                                                            $total_pending_withdraw_service = $with_pending_service->total_withdraw;--}}
            {{--                                                            $price_pending_withdraw_service = $with_pending_service->price_withdraw;--}}
            {{--                                                        }--}}
            {{--                                                    }--}}

            {{--                                                    $total_w = $total_withdraw + $total_withdraw_service;--}}
            {{--                                                    $total_p = $price_withdraw + $price_withdraw_service;--}}

            {{--                                                     $total_pending_w = $total_pending_withdraw + $total_pending_withdraw_service;--}}
            {{--                                                    $total_pending_p = $price_pending_withdraw + $price_pending_withdraw_service;--}}
            {{--                                                @endphp--}}
            {{--                                                <td>--}}
            {{--                                                   {{ $key }}--}}
            {{--                                                </td>--}}
            {{--                                                <td>--}}
            {{--                                                    {{ number_format($total_w) }}--}}
            {{--                                                </td>--}}
            {{--                                                <td>--}}
            {{--                                                    {{ number_format($total_p) }}--}}
            {{--                                                </td>--}}
            {{--                                                <td>--}}
            {{--                                                    {{ number_format($total_pending_w) }}--}}
            {{--                                                </td>--}}
            {{--                                                <td>--}}
            {{--                                                    {{ number_format($total_pending_p) }}--}}
            {{--                                                </td>--}}
            {{--                                                <td>--}}
            {{--                                                    @if($item == 11)--}}
            {{--                                                        {{number_format($user_item->xu_num)}}--}}
            {{--                                                    @elseif($item == 12)--}}
            {{--                                                        {{number_format($user_item->gem_num)}}--}}
            {{--                                                    @elseif($item == 13)--}}
            {{--                                                        {{number_format($user_item->robux_num)}}--}}
            {{--                                                    @elseif($item == 14)--}}
            {{--                                                        {{number_format($user_item->coin_num)}}--}}
            {{--                                                    @else--}}
            {{--                                                    {{number_format($user_item['ruby_num'.$item])}}--}}
            {{--                                                    @endif--}}
            {{--                                                </td>--}}
            {{--                                            </tr>--}}
            {{--                                            @endforeach--}}
            {{--                                        @else--}}
            {{--                                            <tr><td colspan="3" style="text-align: center; font-weight: bold;">{{__('Bạn chưa chọn shop để thống kê!')}}</td></tr>--}}
            {{--                                        @endif--}}
            {{--                                    </tbody>--}}
            {{--                                </table>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}

        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {

            $('.parrentClass').each(function(){
                // $(this).addClass('spinner-border');
                $id = $(this).attr('data-id');
                var total_item=0;
                var total_money=0;
                var total_flip=0;
                // var total_nickon=0;
                var total_nickoff=0;
                $('.children_'+$id).each(function(){
                    total_item=total_item+parseFloat($(this).find('td:eq(2)').text()==''?0:$(this).find('td:eq(2)').text().replace(',','').replace(',','').replace(',',''));
                    total_money=total_money+parseFloat($(this).find('td:eq(4)').text()==''?0:$(this).find('td:eq(4)').text().replace(',','').replace(',','').replace(',',''));
                    total_flip=total_flip+parseFloat($(this).find('td:eq(3)').text()==''?0:$(this).find('td:eq(3)').text().replace(',','').replace(',','').replace(',',''));
                    // total_nickon=total_nickon+parseFloat($(this).find('td:eq(5)').text()==''?0:$(this).find('td:eq(5)').text().replace(',','').replace(',','').replace(',',''));
                    total_nickoff=total_nickoff+parseFloat($(this).find('td:eq(6)').text()==''?0:$(this).find('td:eq(6)').text().replace(',','').replace(',','').replace(',',''));

                });
                $(this).find('td:eq(1)').text(total_item.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $(this).find('td:eq(2)').text(total_flip.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $(this).find('td:eq(3)').text(total_money.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                // $(this).find('td:eq(4)').text(total_nickon.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $(this).find('td:eq(5)').text(total_nickoff.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            })

            //delete button
            //triggered when modal is about to be shown
            $('#deleteModal').on('show.bs.modal', function (e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#deleteModal .id').attr('value', id);
            });


            $(".datetimepicker").datetimepicker({
                todayHighlight: !0,
                pickerPosition: "bottom-left",
                todayBtn: !0,
                autoclose: !0,
                format: "dd/mm/yyyy hh:ii:ss"
            });

            $('.parrent').on('click', function(e){
                var id= $(this).attr('rel');

                $(".children_"+id).toggle();

            });

            $('.parrentp').on('click', function(e){
                var id= $(this).attr('rel');

                $(".childrenp_"+id).toggle();

            });

            $('.open_more').on('click', function(e){
                e.preventDefault();
                if($(this).find('span span').html()=="Mở rộng"){
                    $(this).find('span span').html('Thu gọn');
                    $("[class*='children_']").show();
                }
                else{
                    $(this).find('span span').html('Mở rộng');
                    $("[class*='children_']").hide();
                }

            });

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
