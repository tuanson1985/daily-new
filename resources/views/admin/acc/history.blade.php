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
                    {{--ID--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" name="id" placeholder="{{__('ID')}}" value="{{ $_GET['id']??null }}">
                        </div>
                    </div>
                    {{--title--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" name="title" value="{{ $_GET['title']??null }}" placeholder="{{__('Tên tài khoản')}}">
                        </div>
                    </div>
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

                    {{--group_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <select class="form-control datatable-input datatable-input-select selectpicker" name="group_id" title="-- {{__('Tất cả danh mục')}} --">
                                <option value=''>-- Không chọn --</option>
                                @include('admin.acc.widget.category-select', ['data' => $properties, 'selected' => [$_GET['group_id']??null], 'stSpecial' => ''])
                            </select>

                        </div>
                    </div>
                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <select class="form-control datatable-input datatable-input-select selectpicker" name="status" title="-- Trạng thái --">
                                <option value=''>-- Tất cả --</option>
                                @foreach(config('etc.acc.status') as $key => $name)
                                    @if(($_GET['status']??'') === '0')
                                        <option value="{{ $key }}" {{ $key == 0? 'selected': null }}>{{ $name }}</option>
                                    @else
                                        <option value="{{ $key }}" {{ $key > 0 && ($_GET['status']??null) == $key? 'selected': null }}>{{ $name }}</option>
                                    @endif
                                @endforeach
                            </select>

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
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasPermissionTo('acc-history-export'))
                            @if(!empty($_GET['started_at']))
                            <a class="btn btn-success" href="{{ url()->current() }}?{{ http_build_query(array_merge($_GET, ['export' => 1])) }}">
                                <i class="far fa-file-excel icon-md"></i>
                                Xuất Excel
                            </a>
                            @endif
                            <span class="text-warning">Chọn khoảng thời gian để có thể xuất file Excel</span>
                        @endif
                    </div>
                </div>
            </form>
            <!--begin: Search Form-->
            {!! $items->links('vendor.pagination.bootstrap-4') !!}
            <!--begin: Datatable-->
            <div class="table-responsive">
                @php
                    $hide_view_price =  auth()->user()->account_type == 1 && !auth()->user()->can('acc-history-price');
                    $hide_view_author =  auth()->user()->account_type == 1 && !auth()->user()->can('acc-history-author');
                @endphp
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Thời gian bán</th>
                            <th>ID</th>
                            <th>Tài khoản</th>
                            <th>Danh mục</th>
                            @if(auth()->user()->account_type == 1)
                            <th>Shop</th>
                            <th>Người mua</th>
                            <th>Giá bán</th>
                            @endif
                            @if(!$hide_view_price)
                            <th>Giá gốc</th>
                            @endif
                            @if(!$hide_view_author)
                            <th>CTV bán</th>
                            @endif
                            <th>CTV hưởng</th>
                            @if(auth()->user()->account_type == 1)
                            <th>Lợi nhuận</th>
                            @endif
                            <th>Trạng thái</th>
                            <th>Thời gian tạo</th>
                            <th>Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                            <td>{{ empty($item->published_at)? '': $item->published_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                {{ $item->id }}
                                @if(auth()->user()->hasRole('admin'))
                                <div><i>{{ \App\Library\Helpers::encodeItemID($item->id, $item->shop_id) }}</i></div>
                                @endif
                            </td>
                            <td>{{ $item->title }}</td>
                            <td>
                                @if(!empty($item->category->parent))
                                    <span class="badge badge-primary">{{ $item->category->parent->title }}</span>
                                @endif
                                @if(!empty($item->category))
                                    <span class="badge badge-success">{{ $item->category->title }}</span>
                                    <span class="badge badge-secondary">{{ config('etc.acc_property.type')[$item->category->display_type??1] }}</span>
                                @endif
                            </td>
                            @php
                                $txns_price = $item->txns_order->price??0;
                                $txns_amount = $item->status == 0? $item->acc_txns->sortByDesc('id')->where('is_add', 1)->where('is_refund', 0)->first()->amount??0: 0;
                            @endphp
                            @if(auth()->user()->account_type == 1)
                            <td>{{ $item->shop->domain??null }}</td>
                            <td>{{ $item->customer->username??null }}</td>
                            <td>{{ number_format($txns_price, 0, ',', '.') }}</td>
                            @endif
                            @if(!$hide_view_price)
                            <td>{{ number_format($item->price??0, 0, ',', '.') }}</td>
                            @endif
                            @if(!$hide_view_author)
                            <td>{{ $item->author->username??null }}</td>
                            @endif
                            <td>{{ number_format($txns_amount, 0, ',', '.') }}</td>
                            @if(auth()->user()->account_type == 1)
                            <td>{{ $txns_amount > 0 ? number_format($txns_price-$txns_amount, 0, ',', '.'): 0 }}</td>
                            @endif
                            <td class="text-{{ $item->status == 0? 'success': ($item->status == 12? 'info': 'danger') }}">
                                {{ config('etc.acc.status')[$item->status]??$item->status }}
                                @if(in_array($item->status, [2, 3, 6]))
                                    <a href="{{ route("admin.acc.edit",[empty($item->category->display_type)? 1: $item->category->display_type, $item->id]) }}?check_login=1" class="btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-primary" title="Check lại thông tin"><i class="fa fa-sync-alt"></i></a>
                                @endif
                            </td>

                            <td>{{ empty($item->created_at)? '': $item->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                @if( (auth()->user()->hasRole('admin') || auth()->user()->hasPermissionTo('acc-refund')) && $item->status === 0)
                                    <a href='javascript:void(0)' data-toggle="modal" data-target="#refundModal" rel="{{ $item->id }}" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-danger btn-refund mr-2' title="Hoàn tiền"><i class="la la-refresh"></i></a>
                                @elseif($item->status === 13 && Auth::user()->can('nick-view-order-refund'))
                                    <a class="m-portlet__nav-link btn m-btn m-btn--hover-info m-btn--icon m-btn--icon-only m-btn--pill" href="{{ route('admin.acc.history.show',$item->id) }}"><i class="la la-eye"></i></a>
                                @endif
                                @if(auth()->user()->account_type == 1 && $txns_price-$txns_amount > 0)
                                    <a href="{{ route('admin.activity-log.index', ['description' => "#{$item->id}"]) }}" target="_blank" class="btn btn-sm btn-icon btn-hover-text-white btn-hover-bg-warning mr-2" title="Log hoạt động">
                                        <img src="/assets/backend/themes/media/svg/icons/Devices/Diagnostics.svg" alt="">
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!--end: Datatable-->
            {!! $items->links('vendor.pagination.bootstrap-4') !!}
        </div>
    </div>
    <!-- refund item Modal -->
    <div class="modal fade" id="refundModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.acc.edit',[1, 0]),'class'=>'form-horizontal','id'=>'form-refund','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value=""/>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Lý do hoàn</span>
                            </div>
                            <input type="text" name="desc" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Trạng thái acc</span>
                            </div>
                            <select class="form-control" name="status">
                                @foreach(config('etc.acc.status') as $key => $item)
                                    @if(in_array($key, [4,5]))
                                    <option value="{{ $key }}">{{ $item }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning m-btn m-btn--custom btn-submit-custom" name="submit" value="refund_nick_only" data-form="form-refund">Chỉ đổi trạng thái nick</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-custom" name="submit" value="refund" data-form="form-refund">{{__('Hoàn tiền')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection

@section('styles')
@endsection
@section('scripts')
    <script>
        $('#refundModal').on('show.bs.modal', function(e) {
            //get data-id attribute of the clicked element
            var id = $(e.relatedTarget).attr('rel')
            $('#refundModal [name="id"]').attr('value', id);
        });
    </script>
@endsection
