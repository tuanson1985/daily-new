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
                                        $total_nickon=0;
                                        $total_nickoff=0;
                                    @endphp
                                    @foreach($datatable as $itemCat)
                                        {{--begin header--}}
                                        <tr id="parrent{{$itemCat->id}}" class="parrentClass" data-id="{{$itemCat->id}}" style=" background-color: #e0ebf9;font-weight: bold" >
                                            <td class="parrent" rel="{{$itemCat->id}}" style="cursor: pointer">
                                                <i class="flaticon-plus" style="font-size: 12px"></i>
                                                {{$itemCat->id}}
                                            </td>
                                            <td>
                                                {{$itemCat->title}}
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
                                        @if($itemCat->items!=null)
                                            @foreach($itemCat->items as $item)
                                            <tr class="children_{{$itemCat->id}}" data-id="{{$itemCat->id}}" style="display: none">
                                                <td>{{$item->id}}</td>
                                                <td>{{$item->title}}</td>
                                                @php
                                                    $minigame = $item->minigameorder;
                                                @endphp
                                                @if(request('started_at')!=null)
                                                    @php
                                                        $minigame = $minigame->where('created_at', '>=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', request('started_at')));
                                                    @endphp
                                                @endif                                                
                                                @if(request('ended_at')!=null)
                                                    @php
                                                        $minigame = $minigame->where('created_at', '<=', \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', request('ended_at')));
                                                    @endphp
                                                @endif
                                                <td>{{$minigame->sum("real_received_price")}}</td>
                                                <td>{{$minigame->count()}}</td>
                                                <td>{{$minigame->sum("price")}}</td>
                                                <td>{{$item->children->where('status',1)->count()}}</td>
                                                <td>{{$minigame->whereNotNull('acc_id')->count()}}</td>
                                            </tr>                                            
                                            @php
                                                $total_item=$total_item + $minigame->sum("real_received_price");
                                                $total_flip=$total_flip + $minigame->count();
                                                $total_money=$total_money + $minigame->sum("price");
                                                $total_nickon=$total_nickon + $item->children->where('status',1)->count();
                                                $total_nickoff=$total_nickoff + $minigame->whereNotNull('acc_id')->count();
                                            @endphp
                                            @endforeach
                                        @endif

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
                                            {{number_format($total_nickon)}}
                                        </td>
                                        <td>
                                            {{$total_nickoff}}
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

            $('.parrentClass').each(function(){
                $id = $(this).attr('data-id');
                var total_item=0;
                var total_money=0;
                var total_flip=0;
                var total_nickon=0;
                var total_nickoff=0;
                $('.children_'+$id).each(function(){
                    if($(this).find('td:eq(1)').text()==''){
                        $('#parrent'+$(this).attr('data-id')+' i').hide();
                        $('#parrent'+$(this).attr('data-id')).removeClass('parrent');
                        $(this).remove();
                    }else{
                        total_item=total_item+parseFloat($(this).find('td:eq(2)').text()==''?0:$(this).find('td:eq(2)').text());
                        total_money=total_money+parseFloat($(this).find('td:eq(4)').text()==''?0:$(this).find('td:eq(4)').text());
                        total_flip=total_flip+parseFloat($(this).find('td:eq(3)').text()==''?0:$(this).find('td:eq(3)').text());
                        total_nickon=total_nickon+parseFloat($(this).find('td:eq(5)').text()==''?0:$(this).find('td:eq(5)').text());
                        total_nickoff=total_nickoff+parseFloat($(this).find('td:eq(6)').text()==''?0:$(this).find('td:eq(6)').text());
                    }
                    
                    $(this).find('td:eq(2)').text($(this).find('td:eq(2)').text()==''?0:$(this).find('td:eq(2)').text().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    $(this).find('td:eq(3)').text($(this).find('td:eq(3)').text()==''?0:$(this).find('td:eq(3)').text().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    $(this).find('td:eq(4)').text($(this).find('td:eq(4)').text()==''?0:$(this).find('td:eq(4)').text().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    $(this).find('td:eq(5)').text($(this).find('td:eq(5)').text()==''?0:$(this).find('td:eq(5)').text().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    $(this).find('td:eq(6)').text($(this).find('td:eq(6)').text()==''?0:$(this).find('td:eq(6)').text().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                });
                $(this).find('td:eq(2)').text(total_item.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $(this).find('td:eq(3)').text(total_flip.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $(this).find('td:eq(4)').text(total_money.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $(this).find('td:eq(5)').text(total_nickon.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $(this).find('td:eq(6)').text(total_nickoff.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
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