{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">

        <div class="btn-group">
            <a type="button"  class="btn btn-success font-weight-bolder btnadd">
                <i class="fas fa-plus-circle icon-md"></i>
                {{__('Thêm mới')}}
            </a>
        </div>
    </div>
@endsection

{{-- Content --}}
@section('content')

    <div class="card card-custom" id="kt_page_sticky_card">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">
                    {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                    <span style="border-radius: 100px;border: 1px solid #f1a417;padding: 8px;background: #f1a417;">{{ $migame_total??0 }}</span>
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
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="id" placeholder="{{__('ID')}}">
                        </div>
                    </div>
                    {{--title--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="title"
                                   placeholder="{{__('Tiêu đề')}}">
                        </div>
                    </div>

                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select class="form-control" name="status" id="status">
                                <option value="">--- Chọn trạng thái ---</option>
                                <option value="0"> Ngừng hoạt động </option>
                                <option value="1"> Hoạt động </option>
                            </select>
                        </div>
                    </div>

                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select class="form-control" name="shop_group" id="shop_group">
                                <option value="">--- Chọn nhóm vận hành ---</option>
                                @foreach($shop_group as $nvh)
                                    <option value="{{ $nvh->id }}">{{ $nvh->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{--position--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i
                                    class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('position',[''=>'-- '.__('Tất cả loại minigame').' --']+config('module.minigame.minigame_type'),old('position', isset($data) ? $data->position : null),array('id'=>'position','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>


                    {{--game_type--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i
                                    class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('game_type',[''=>'-- '.__('Tất cả loại vật phẩm').' --']+config('module.minigame.game_type'),old('game_type', isset($data) ? $data->game_type : null),array('id'=>'game_type','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>

                    {{--valuefrom--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="valuefrom"
                                   placeholder="{{__('Giá lượt quay (từ)')}}">
                        </div>
                    </div>
                    {{--valueto--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="valueto"
                                   placeholder="{{__('Giá lượt quay (đến)')}}">
                        </div>
                    </div>

                    {{--status--}}

                    <div class="form-group col-12 col-sm-6 col-lg-3 data_value_item">
                        {{--                        @include('admin.minigame.module.category.value_item')--}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
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

                    @if(auth()->user()->hasRole('admin') && session('shop_id'))
                    <div class="col-auto" style="margin-left: auto">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#contentModal">Lấy content</button>
                    </div>
                    @endif
                </div>
            </form>
            <!--begin: Search Form-->

            <!--begin: Datatable-->
            <table class="table table-bordered table-hover table-checkable " id="kt_datatable">
            </table>
            <!--end: Datatable-->
        </div>
    </div>

    <input type="text" style="display: none" value="{{$module}}" id="module_group">


    {{---------------all modal controll-------}}

    <!-- list item Modal -->
    <!-- <div class="modal fade" id="listItemModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                @include('admin.module.group.additem')
        </div>
    </div>
</div> -->


    <!-- delete item Modal -->
    <div class="modal fade" id="contentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.'.$module.'.convert-content',0),'class'=>'form-horizontal','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn lấy lại content?')}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                    <button type="submit" class="btn btn-success m-btn">{{__('Xác nhận')}}</button>
                </div>
                {{ Form::close() }}
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

    <div class="modal fade" id="modal-replication">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.'.$module.'.replication'),'class'=>'form-horizontal','id'=>'','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title m-auto" id="exampleModalLabel"> {{__('Xác nhận nhân bản')}}</h5>
                    <button type="button" class="close position-absolute" style="right: 24px" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body r_data-title">
                    {{__('Bạn thực sự muốn nhân bản?')}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="replicationid" class="replication-id">
                    <input type="hidden" name="c_group" class="c_group" value="0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom b-custom">{{__('Nhân bản')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    @foreach($groups as $group)
        <div class="modal fade bd-example-modal-lg dsShopCreated" id="dsShopCreated_{{ $group->id }}">
            <div class="modal-dialog" style="max-width: 766px">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: none">
                        <h5 class="modal-title m-auto" id="exampleModalLabel" style="font-weight: 700;text-transform: uppercase"> {{__('Chọn điểm bán cần chỉnh sửa')}}</h5>
                        <button type="button" class="close position-absolute" style="right: 24px" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body ds-shop-body">
                        <div class="row marginauto">
                            <!--begin: Search Form-->
                            <div class="col-md-12">
                                <form class="mb-10">
                                    <div class="row">
                                        {{--ID--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                </div>
                                                <input type="text" class="form-control datatable-input_add-shop-created_{{ $group->id }}" id="id_{{ $group->id }}" placeholder="{{__('ID')}}">
                                            </div>
                                        </div>
                                        {{--title--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                </div>
                                                <input type="text" class="form-control datatable-input_add-shop-created_{{ $group->id }}" id="domain_{{ $group->id }}"
                                                       placeholder="{{__('Shop')}}">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button class="btn btn-primary btn-primary--icon" id="kt_search_add-shop-created_{{ $group->id }}">
                                            <span>
                                                <i class="la la-search"></i>
                                                <span>Tìm kiếm</span>
                                            </span>
                                            </button>&#160;&#160;
                                            <button class="btn btn-secondary btn-secondary--icon" id="kt_reset_add-shop-created_{{ $group->id }}">
                                            <span>
                                                <i class="la la-close"></i>
                                                <span>Reset</span>
                                            </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!--begin: Search Form-->
                        </div>

                        <div class="row marginauto ds-shop-search pt-lg-4 pt-3">
                            <div class="col-md-12 left-right scroll-default kt_datatable_addwebits_created_pont">

                                <table class="table table-bordered table-hover table-checkable kt_datatable_addwebits_created" id="kt_datatable_addwebits_created_{{ $group->id }}">
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <a href="" class="btn btn-primary btn-chinhsua_{{ $group->id }}">{{__('Chỉnh sửa')}}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-lg dsShopCreated" id="dsShopCreated_show_{{ $group->id }}">
            <div class="modal-dialog" style="max-width: 766px">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: none">
                        <h5 class="modal-title m-auto" id="exampleModalLabel" style="font-weight: 700;text-transform: uppercase"> {{__('Danh sách điểm bán')}}</h5>
                        <button type="button" class="close position-absolute" style="right: 24px" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body ds-shop-body">
                        <div class="row marginauto">
                            <!--begin: Search Form-->
                            <div class="col-md-12">
                                <form class="mb-10">
                                    <div class="row">
                                        {{--ID--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                </div>
                                                <input type="text" class="form-control datatable-input_add-shop-created_show_{{ $group->id }}" id="id_show_{{ $group->id }}" placeholder="{{__('ID')}}">
                                            </div>
                                        </div>
                                        {{--title--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                </div>
                                                <input type="text" class="form-control datatable-input_add-shop-created_show_{{ $group->id }}" id="domain_show_{{ $group->id }}"
                                                       placeholder="{{__('Shop')}}">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button class="btn btn-primary btn-primary--icon" id="kt_search_add-shop-created_show_{{ $group->id }}">
                                            <span>
                                                <i class="la la-search"></i>
                                                <span>Tìm kiếm</span>
                                            </span>
                                            </button>&#160;&#160;
                                            <button class="btn btn-secondary btn-secondary--icon" id="kt_reset_add-shop-created_show_{{ $group->id }}">
                                            <span>
                                                <i class="la la-close"></i>
                                                <span>Reset</span>
                                            </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!--begin: Search Form-->
                        </div>

                        <div class="row marginauto ds-shop-search pt-lg-4 pt-3">
                            <div class="col-md-12 left-right scroll-default kt_datatable_addwebits_created_pont">

                                <table class="table table-bordered table-hover table-checkable kt_datatable_addwebits_created" id="kt_datatable_addwebits_created_show_{{ $group->id }}">
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        {{--                    <a href="" class="btn btn-primary btn-chinhsua_{{ $group->id }}">{{__('Chỉnh sửa')}}</a>--}}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-lg r_dsShopCreated" id="r_dsShopCreated_{{ $group->id }}">
            <div class="modal-dialog" style="max-width: 766px">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: none">
                        <h5 class="modal-title m-auto" style="font-weight: 700;text-transform: uppercase"> {{__('Chọn bộ thông tin nhân bản')}}</h5>
                        <button type="button" class="close position-absolute" style="right: 24px" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body ds-shop-body">
                        <div class="row marginauto">
                            <!--begin: Search Form-->
                            <div class="col-md-12">
                                <form class="mb-10">
                                    <div class="row">
                                        {{--ID--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                </div>
                                                <input type="text" class="form-control r_datatable-input_add-shop-created_{{ $group->id }}" id="r_id_{{ $group->id }}" placeholder="{{__('ID')}}">
                                            </div>
                                        </div>
                                        {{--title--}}
                                        <div class="form-group col-12 col-sm-6 col-lg-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                </div>
                                                <input type="text" class="form-control r_datatable-input_add-shop-created_{{ $group->id }}" id="r_domain_{{ $group->id }}"
                                                       placeholder="{{__('Shop')}}">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button class="btn btn-primary btn-primary--icon" id="r_kt_search_add-shop-created_{{ $group->id }}">
                                            <span>
                                                <i class="la la-search"></i>
                                                <span>Tìm kiếm</span>
                                            </span>
                                            </button>&#160;&#160;
                                            <button class="btn btn-secondary btn-secondary--icon" id="r_kt_reset_add-shop-created_{{ $group->id }}">
                                            <span>
                                                <i class="la la-close"></i>
                                                <span>Reset</span>
                                            </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!--begin: Search Form-->
                        </div>

                        <div class="row marginauto ds-shop-search pt-lg-4 pt-3">
                            <div class="col-md-12 left-right scroll-default kt_datatable_addwebits_created_pont">

                                <table class="table table-bordered table-hover table-checkable r_kt_datatable_addwebits_created" id="r_kt_datatable_addwebits_created_{{ $group->id }}">
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{Form::open(array('route'=>array('admin.'.$module.'.replication'),'class'=>'form-horizontal','id'=>'','method'=>'POST'))}}
                        <input type="hidden" name="replicationid" class="replication-id" value="">
                        <input type="hidden" name="cr_shop" class="cr_shop" value="">
                        <input type="hidden" name="c_group" class="c_group" value="1">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-primary">{{__('Nhân bản')}}</button>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <input type="hidden" name="shopurl" class="shopurl" value="{{ $shopurl??null }}">
    <input type="hidden" name="cr_id" class="cr_id" value="{{ session('shop_id')??null }}">
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
                    dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                            <'row'<'col-sm-12'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
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
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.game_type = $('#game_type').val();
                            d.valueitem = $('#kt_select2_service').val();
                            d.valuefrom = $('#valuefrom').val();
                            d.valueto = $('#valueto').val();
                            d.shop_group = $('#shop_group').val();

                        }
                    },
                    buttons: [

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
                        @if(session('shop_id'))
                        {
                            data: 'id', title: '{{__('ID custom')}}',
                            render: function (data, type, row) {
                                return row.customs[0].id;
                            }
                        },
                        {data: 'customs', title: '{{__('Tiêu đề custom')}}',
                            render: function (data, type, row) {
                                let shopurl = $('.shopurl').val();
                                if (shopurl){
                                    let cr_id = $('.cr_id').val();
                                    if (cr_id){
                                        if (cr_id == row.customs[0].shop_id && row.customs[0].status == 1){
                                            return "<a target='_blank' href='{{$shopurl}}/minigame-"+row.customs[0].slug+"'>"+row.customs[0].title+"</a>";
                                        }else {
                                            return row.customs[0].title;
                                        }
                                    }else {
                                        return row.title;
                                    }
                                }else {
                                    return row.title;
                                }
                            }
                        },
                        {data: 'image', title: 'Hình ảnh custom'},
                        @else
                        {data: 'id', title: 'ID gốc'},
                        {data: 'title', title: 'Tiêu đề gốc'},
                        {data: 'image', title: 'Hình ảnh gốc'},
                        @endif

                        {data: 'position', title: '{{__('Loại minigame')}}',
                            render: function (data, type, row) {
                                if (row.position == 'rubywheel') {
                                    return "{{config('module.minigame.minigame_type.rubywheel')}}";
                                } else if (row.position == 'flip') {
                                    return "{{config('module.minigame.minigame_type.flip')}}";
                                } else if (row.position == 'slotmachine') {
                                    return "{{config('module.minigame.minigame_type.slotmachine')}}";
                                } else if (row.position == 'slotmachine5') {
                                    return "{{config('module.minigame.minigame_type.slotmachine5')}}";
                                } else if (row.position == 'squarewheel') {
                                    return "{{config('module.minigame.minigame_type.squarewheel')}}";
                                } else if (row.position == 'smashwheel') {
                                    return "{{config('module.minigame.minigame_type.smashwheel')}}";
                                } else {
                                    return "{{config('module.minigame.minigame_type.rubywheel')}}";
                                }
                            }
                        },
                        {data: 'params', title: '{{__('Loại vật phẩm')}}',
                            render: function (data, type, row) {
                                if (row.params.game_type && row.params){
                                    if (row.params.game_type == 1){
                                        return "{{config('module.minigame.game_type.1')}}";
                                    }else if (row.params.game_type == 2){
                                        return "{{config('module.minigame.game_type.2')}}";
                                    }else if (row.params.game_type == 3){
                                        return "{{config('module.minigame.game_type.3')}}";
                                    }else if (row.params.game_type == 4){
                                        return "{{config('module.minigame.game_type.4')}}";
                                    }else if (row.params.game_type == 5){
                                        return "{{config('module.minigame.game_type.5')}}";
                                    }else if (row.params.game_type == 6){
                                        return "{{config('module.minigame.game_type.6')}}";
                                    }else if (row.params.game_type == 7){
                                        return "{{config('module.minigame.game_type.7')}}";
                                    }else if (row.params.game_type == 8){
                                        return "{{config('module.minigame.game_type.8')}}";
                                    }else if (row.params.game_type == 9){
                                        return "{{config('module.minigame.game_type.9')}}";
                                    }else if (row.params.game_type == 10){
                                        return "{{config('module.minigame.game_type.10')}}";
                                    }else if (row.params.game_type == 11){
                                        return "{{config('module.minigame.game_type.11')}}";
                                    }else if (row.params.game_type == 12){
                                        return "{{config('module.minigame.game_type.12')}}";
                                    }else if (row.params.game_type == 13){
                                        return "{{config('module.minigame.game_type.13')}}";
                                    }else if (row.params.game_type == 14){
                                        return "{{config('module.minigame.game_type.14')}}";
                                    }
                                }else {
                                    return "";
                                }
                            }
                        },
                        {
                            data: 'price', title: '{{__('Giá')}}',
                            render: function (data, type, row) {
                                let price = row.price;
                                price = price.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
                                price = price.split('').reverse().join('').replace(/^[\.]/,'');
                                return price + " đ";
                            }
                        },
                            @if(session('shop_id'))
                        {
                            data: 'count_shop', title: '{{__('SL Điểm bán được phân phối')}}',
                            render: function (data, type, row) {

                                if (row.customs && row.customs[0].group && row.customs[0].group.count_shop_custom > 0){
                                    var index = 0;
                                    $.each(row.customs[0].group.customs,function(keyc1,valuec1){
                                        if (valuec1.status == 1) {
                                            index = index + 1;
                                        }
                                    });
                                    if (index == 0){
                                        return '<span  class="label label-pill label-inline label-center label-danger " style="padding: 6px 24px">0/' + row.customs[0].group.count_shop_custom + '</span>';
                                    }else if (index < row.count_shop){
                                        return '<span  class="label label-pill label-inline label-center label-warning" style="padding: 6px 24px">' + index + '/' + row.customs[0].group.count_shop_custom + '</span>';
                                    }else {
                                        return '<span  class="label label-pill label-inline label-center label-success " style="padding: 6px 24px">' + index + '/' + row.customs[0].group.count_shop_custom + '</span>';
                                    }
                                }else{
                                    return  0;
                                }
                            }
                        },
                        {
                            data: 'customs', title: '{{__('Trạng thái')}}',
                            render: function (data, type, row) {
                                if(row.customs[0]!=undefined){
                                    if (row.customs[0].status == 1) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.minigame.status.1')}}" + "</span>";
                                    } else if (row.customs[0].status == 2) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.minigame.status.2')}}" + "</span>";
                                    } else {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.minigame.status.0')}}" + "</span>";
                                    }
                                }else{
                                    if (row.status == 1) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.minigame.status.1')}}" + "</span>";
                                    } else if (row.status == 2) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.minigame.status.2')}}" + "</span>";
                                    } else {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.minigame.status.0')}}" + "</span>";
                                    }
                                }
                            }
                        },
                            @else
                        {
                            data: 'count_shop', title: '{{__('SL Điểm bán được phân phối')}}',
                            render: function (data, type, row) {
                                if (row.count_shop > 0){
                                    var index = 0;
                                    $.each(row.customs,function(keyc1,valuec1){
                                        if (valuec1.status == 1) {
                                            index = index + 1;
                                        }
                                    });
                                    if (index == 0){
                                        return '<span  class="label label-pill label-inline label-center label-danger " style="padding: 6px 24px">0/' + row.count_shop + '</span>';
                                    }else if (index < row.count_shop){
                                        return '<span  class="label label-pill label-inline label-center label-warning" style="padding: 6px 24px">' + index + '/' + row.count_shop + '</span>';
                                    }else {
                                        return '<span  class="label label-pill label-inline label-center label-success " style="padding: 6px 24px">' + index + '/' + row.count_shop + '</span>';
                                    }
                                }else{
                                    return  0;
                                }
                            }
                        },
                        @endif
                        @if(session('shop_id'))
                        {data: 'meta', title: 'Nhóm vận hành'},
                        @endif
                        { data: 'action',title:'Thao tác', orderable: false, searchable: false}
                    ],
                    "drawCallback": function (settings) {
                        var v_index = 0
                        $('#kt_datatable_wrapper .dataTables_length').each(function () {
                            v_index = v_index + 1
                        })
                        if (v_index == 2){
                            $('#kt_datatable_wrapper .dataTables_length').last().remove();
                        }
                        $('#kt_datatable_wrapper #kt_datatable_paginate').remove();
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
                    // data_value_item
                    var game_type = $('#game_type').val();

                    if (game_type){

                        var value_item = $('#kt_select2_service').val();
                        console.log(value_item);
                        $.ajax({
                            url: '{{ route('admin.minigame-value-item.index') }}',
                            datatype:'json',
                            data:{
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                game_type: game_type,
                                value_item: value_item,
                            },
                            type: 'get',
                            success: function (data) {
                                if(data.status == 1){
                                    $(".data_value_item").empty().html('');
                                    $(".data_value_item").empty().html(data.data);

                                    $(".kt_select2_service").select2();
                                }else{
                                    // $(".render_category").empty().html('');
                                    // var html = `<div class="dd scroll-default data_danhmuc_show" id="nestablev3">
                                    //     Chưa có thông tin
                                    // </div>`;
                                    // $(".render_category").empty().html(html);
                                }
                            }
                        })
                    }else {
                        $(".data_value_item").empty().html('');
                    }

                });
                $('#kt_reset').on('click', function (e) {
                    e.preventDefault();
                    $('.datatable-input').each(function () {
                        $(this).val('');
                        datatable.column($(this).data('col-index')).search('', false, false);
                    });
                    datatable.table().draw();

                    $(".data_value_item").empty().html('');
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
                //function update field
                datatable.on("change", ".update_field", function (e) {
                    e.preventDefault();
                    var field=$(this).data('field');
                    var id=$(this).data('id');
                    if(id==''){
                        return;
                    }
                    var required=$(this).data('required');
                    var value=$(this).val();
                    $.ajax({
                        type: "POST",
                        url: '{{route('admin.minigame-category.updatefieldcat')}}',
                        data: {
                            '_token':'{{csrf_token()}}',
                            'field':field,
                            'id':id,
                            'value':value,
                            'required' :required
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
                                toast(data.message, 'error');
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
                    // $(button).text("Xuất excel");
                    $(button).prop('disabled', false);
                    return false;
                });
            });
            // Requery the server with the new one-time export settings
            dt.ajax.reload();
        };
        //Funtion web ready state
        jQuery(document).ready(function () {
            $('.btnadd').click(function(){
                //if('{{session('shop_id')}}' != ''){
                location.href="{{route('admin.'.$module.'.create')}}";
                // }else{
                //     $('#select-client').focus();
                //     alert('vui lòng chọn shop!');
                // }
            })
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
        });
    </script>

    <script>
        jQuery(document).ready(function () {

            $(".kt_select2_service").select2();

            const host = window.location.hostname;
            $('#txtSearch').donetyping(function() {
                var find=$(this).val()
                var module = $('input#module_group').val();
                var url ='/admin-1102/'+module+'/search';
                if(find == null || find == "" || find == undefined){
                    $('.nav-search-in-value').css('display','none');
                    return false
                }
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        find:find
                    }, // serializes the form's elements.
                    beforeSend: function (xhr) {
                    },
                    success: function (data) {
                        $('.nav-search-in-value').css('display','block');
                        $('#result-search').html(data);
                    },
                    error: function (data) {
                        alert("Không kết nối được với máy chủ");
                    },
                    complete: function (data) {
                    }
                });
                //$(this).val() // get the current value of the input field.
            }, 400);
            $(document).on('click','.btn-modal-replication',function(e) {
                let count = $(this).data('count');
                if (parseInt(count) > 0){
                    let id = $(this).data('id');
                    $('.replication-id').val(id);
                    $('#r_dsShopCreated_'+ id +'').modal('show');
                    if ($('#r_kt_datatable_addwebits_created_' + id + '').hasClass('test_addclass')){
                    }else {
                        $('#r_kt_datatable_addwebits_created_' + id + '').addClass('test_addclass');
                        r_datatableaddcreated = $('#r_kt_datatable_addwebits_created_' + id + '').DataTable({
                            responsive: true,
                            dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                        <'row'<'col-sm-12'tr>>
                    <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                            lengthMenu: [5, 10, 20,50,100],
                            pageLength: 10,
                            language: {
                                'lengthMenu': 'Display _MENU_',
                            },
                            searchDelay: 500,
                            processing: true,
                            serverSide: true,
                            "order": [[1, "desc"]],
                            ajax: {
                                url: '{{url()->current()}}' + '?ajax=1&shop=2&id_group='+ id +'',
                                type: 'GET',
                                data: function (d) {
                                    d.id = $('#r_id_' + id + '').val();
                                    d.domain = $('#r_domain_' + id + '').val();
                                    d.status = $('#r_status_' + id + '').val();
                                    d.position = $('#r_position_' + id + '').val();
                                    d.started_at = $('#r_started_at_' + id + '').val();
                                    d.ended_at = $('#r_ended_at_' + id + '').val();
                                    d.group_shop = $('#r_group_shop_' + id + '').val();
                                }
                            },
                            buttons: [
                            ],
                            columns: [
                                {
                                    data: null,
                                    title: '<label class="radio radio-lg radio-outline"></label>',
                                    orderable: false,
                                    searchable: false,
                                    width: "20px",
                                    class: "ckb_item",
                                    render: function (data, type, row) {
                                        return '<label for="radio_' + row.id + '" class="radio radio-lg radio-outline radio-item"><input class="cr_radio" type="radio" data-id="' + row.id + '" name="radio_shop" rel="' + row.id + '" id="radio_' + row.id + '">&nbsp<span></span></label>';
                                    }
                                },
                                {
                                    data: 'domain', title: '{{__('Tên hiển thị')}}',
                                    render: function (data, type, row) {
                                        return `<a class="" href='javascript:void(0)'>` + row.title + `</a>`;
                                    }
                                },
                                {
                                    data: 'image', title: '{{__('Ảnh hiển thị')}}',
                                    render: function (data, type, row) {
                                        return  "<img class=\"image-item\" src=\""+row.image+"\" style=\"max-width: 90px;max-height: 90px\">";
                                    }
                                },
                                {
                                    data: 'domain', title: '{{__('Điểm bán')}}',
                                    render: function (data, type, row) {
                                        return `<a class="" href='javascript:void(0)'>` + row.shop.domain + `</a>`;
                                    }
                                },
                                {
                                    data: 'status', title: '{{__('Trạng thái')}}',
                                    render: function (data, type, row) {
                                        if (row.status == 1) {
                                            return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.minigame.status.1')}}" + "</span>";
                                        } else if (row.status == 2) {
                                            return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.minigame.status.2')}}" + "</span>";
                                        } else {
                                            return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.minigame.status.0')}}" + "</span>";
                                        }
                                    }
                                },
                            ],
                            "drawCallback": function (settings) {
                                var g_id = $('.replication-id').val();
                                var v_index = 0
                                $('#r_kt_datatable_addwebits_created_' + g_id + '_wrapper .dataTables_length').each(function () {
                                    v_index = v_index + 1
                                })
                                if (v_index == 2){
                                    $('#r_kt_datatable_addwebits_created_' + g_id + '_wrapper .dataTables_length').last().remove();
                                }
                                $('#r_kt_datatable_addwebits_created_' + g_id + '_wrapper #r_kt_datatable_addwebits_created_' + g_id + '_paginate').remove();
                            }
                        });
                        var filter = function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            datatable.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                        };
                        $('#r_kt_search_add-shop-created_' + id + '').on('click', function (e) {
                            e.preventDefault();
                            var params = {};
                            $('.r_datatable-input_add-shop-created_' + id + '').each(function () {
                                var i = $(this).data('col-index');
                                if (params[i]) {
                                    params[i] += '|' + $(this).val();
                                } else {
                                    params[i] = $(this).val();
                                }
                            });
                            $.each(params, function (i, val) {
                                // apply search params to datatable
                                r_datatableaddcreated.column(i).search(val ? val : '', false, false);
                            });
                            r_datatableaddcreated.table().draw();
                        });
                        $('#r_kt_reset_add-shop-created_' + id + '').on('click', function (e) {
                            e.preventDefault();
                            $('.r_datatable-input_add-shop-created_' + id + '').each(function () {
                                $(this).val('');
                                r_datatableaddcreated.column($(this).data('col-index')).search('', false, false);
                            });
                            r_datatableaddcreated.table().draw();
                        });
                        r_datatableaddcreated.on("click", "#r_btnCheckAllAddShop", function () {
                            $("#r_kt_datatable_addwebits_created_" + id + " .ckb_item input[type='checkbox']").prop('checked', this.checked).change();
                        })
                        r_datatableaddcreated.on("change", ".ckb_item input[type='checkbox']", function () {
                            if (this.checked) {
                                var currTr = $(this).closest("tr");
                                r_datatableaddcreated.rows(currTr).select();
                            } else {
                                var currTr = $(this).closest("tr");
                                r_datatableaddcreated.rows(currTr).deselect();
                            }
                        });
                    }
                }else{
                    let id = $(this).data('id');
                    $('.replication-id').val(id);
                    let title = $(this).data('title');
                    let html = 'Bạn muốn nhân bản ' + title;
                    $('.r_data-title').html('');
                    $('.r_data-title').html(html);
                    $('#modal-replication').modal('show');
                }
            })
            $(document).on('click','.cr_radio',function(e){
                var cr_id = $(this).data('id');
                $('.cr_shop').val(cr_id);
            })
            $(document).on('click','.pcr_radio',function(e){
                var pcr_route = $(this).data('route');
                var pcr_id = $(this).data('id');
                console.log('.btn-chinhsua_' + pcr_id + '')
                $('.btn-chinhsua_' + pcr_id + '').attr('href',pcr_route);
            })
            $(document).on('click','.btn-show-item',function(e){
                e.preventDefault();
                var module = $('input#module_group').val();
                var url = '/admin-1102/'+module+'/show-item';
                $('#result-search .rs-item').remove();
                $('#txtSearch').val('');
                var id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        id:id
                    },
                    beforeSend: function (xhr) {
                    },
                    success: function (data) {
                        if(data.status == 1){
                            console.log(data.id)
                            $('#listItemModal .modal-body #id-group').val(data.id);
                            let html = '';
                            let parent_id = 0;
                            if(data.data.length > 0){
                                html += '<ol class="dd-list">';
                                $.each(data.data,function(key,value){
                                    if (value.item != null ){
                                        if (value.item.status == 1){
                                            html += '<li class="dd-item nested-list-item remove__group' + value.id + '" data-order="' + value.order + '" data-id="' + value.id + '">';
                                            html += '<div class="dd-handle nested-list-handle">';
                                            html += "<span class='la la-arrows-alt'></span>";
                                            html += '</div>';
                                            html += '<div class="nested-list-content">';
                                            html += '<div class="m-checkbox">';
                                            html += '<label class="checkbox checkbox-outline">';
                                            html += '<input  type="checkbox" rel="' + value.id + '" class="children_of_' + value.id + '">';
                                            html += '<span></span>';
                                            html += value.item.title;
                                            html += '</label>';
                                            html += '</div>';
                                            html += '<div class="btnControll">';
                                            html += '<a href="#" class="btn btn-sm btn-danger delete_toggle btn-delete-item" data-id="'+value.id+'">';
                                            html += 'Xóa';
                                            html += '</a>';
                                            html += '</div>';
                                            html += '</div>';
                                            html += "</li>";
                                        }
                                    }
                                })
                                html += '</ol>';
                            }
                            else{
                                html += '<tr>';
                                html += 'Không có dữ liệu';
                                html += '</tr>';
                            }
                            $('#listItemModal #nestable').html(html);
                            $('#listItemModal').modal('show');
                        }
                    },
                    error: function (data) {
                        alert("Không kết nối được với máy chủ");
                    },
                    complete: function (data) {
                    }
                });
            })
//nestable
            $(function () {
                $('.dd').nestable({
                    dropCallback: function (details) {
                        var order = new Array();
                        $("li[data-id='" + details.destId + "']").find('ol:first').children().each(function (index, elem) {
                            order[index] = $(elem).attr('data-id');
                        });
                        if (order.length === 0) {
                            var rootOrder = new Array();
                            $("#nestable > ol > li").each(function (index, elem) {
                                rootOrder[index] = $(elem).attr('data-id');
                            });
                        }
                        $.post('{{route('admin.'.$module.'.order')}}',
                            {
                                _token:'{{ csrf_token() }}',
                                source: details.sourceId,
                                destination: details.destId,
                                order: JSON.stringify(order),
                                rootOrder: JSON.stringify(rootOrder)
                            },
                            function (data) {
                                // console.log('data '+data);
                            })
                            .done(function () {
                                $(".success-indicator").fadeIn(100).delay(1000).fadeOut();
                            })
                            .fail(function () {
                            })
                            .always(function () {
                            });
                    }
                });
            });
            $(document).on("click",".btnAppend",function(e) {
                e.preventDefault();
                var module = $('input#module_group').val();
                var url = '/admin-1102/'+module+'/update-item';
                var group_id =  $('#listItemModal .modal-body #id-group').val();
                var id= $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        group_id:group_id,
                        id:id
                    },
                    beforeSend: function (xhr) {
                    },
                    success: function (data) {
                        if(data.status == 1){
                            // console.log(data.data.item.title)
                            let html = "";
                            html += '<li class="dd-item nested-list-item remove__group' + data.data.id + '" data-order="' + data.data.order + '" data-id="' + data.data.id + '">';
                            html += '<div class="dd-handle nested-list-handle">';
                            html += "<span class='la la-arrows-alt'></span>";
                            html += '</div>';
                            html += '<div class="nested-list-content">';
                            html += '<div class="m-checkbox">';
                            html += '<label class="checkbox checkbox-outline">';
                            html += '<input  type="checkbox" rel="' + data.data.id + '" class="children_of_' + data.data.id + '">';
                            html += '<span></span>';
                            html += data.data.item.title;
                            html += '</label>';
                            html += '</div>';
                            html += '<div class="btnControll">';
                            html += '<a href="#" class="btn btn-sm btn-danger  delete_toggle btn-delete-item" data-id="' + data.data.id + '">';
                            html += 'Xóa';
                            html += '</a>';
                            html += '</div>';
                            html += '</div>';
                            html += "</li>";
                            $('#listItemModal #nestable .dd-list').append(html);
                        }
                        else{
                            alert(data.message);
                        }
                    },
                    error: function (data) {
                        alert("Không kết nối được với máy chủ");
                    },
                    complete: function (data) {
                    }
                });
            });
            $(document).on('click','.btn-delete-item',function(e){
                e.preventDefault();
                var module = $('input#module_group').val();
                var url = '/admin-1102/'+module+'/delete-item';
                var id = $(this).data('id');
                // var group_id = $(this).data('group');
                var confirm = confirmFunction();
                if(confirm == false){
                    return false;
                }
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        _token: '{{ csrf_token() }}',
                        // group_id:group_id,
                        id:id
                    },
                    beforeSend: function (xhr) {
                    },
                    success: function (data) {
                        if(data.status == 1){
                            $('#nestable .remove__group'+id).remove();
                        }
                        else{
                            alert(data.message);
                        }
                    },
                    error: function (data) {
                        alert("Không kết nối được với máy chủ");
                    },
                    complete: function (data) {
                    }
                });
            });
            $('body').on('click','.add-webits-created',function(){
                let route = $(this).data('route');
                let id = $(this).data('id');
                $('#dsShopCreated_'+ id +'').modal('show');
                if ($('#kt_datatable_addwebits_created_' + id + '').hasClass('test_addclass')){
                }else {
                    $('#kt_datatable_addwebits_created_' + id + '').addClass('test_addclass');
                    datatableaddcreated = $('#kt_datatable_addwebits_created_' + id + '').DataTable({
                        responsive: true,
                        dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                        <'row'<'col-sm-12'tr>>
                    <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                        lengthMenu: [5, 10, 20,50,100],
                        pageLength: 10,
                        language: {
                            'lengthMenu': 'Display _MENU_',
                        },
                        searchDelay: 500,
                        processing: true,
                        serverSide: true,
                        "order": [[1, "desc"]],
                        ajax: {
                            url: '{{url()->current()}}' + '?ajax=1&shop=1&id_group='+ id +'',
                            type: 'GET',
                            data: function (d) {
                                d.id = $('#id_' + id + '').val();
                                d.domain = $('#domain_' + id + '').val();
                                d.status = $('#status_' + id + '').val();
                                d.position = $('#position_' + id + '').val();
                                d.started_at = $('#started_at_' + id + '').val();
                                d.ended_at = $('#ended_at_' + id + '').val();
                                d.group_shop = $('#group_shop_' + id + '').val();
                            }
                        },
                        buttons: [
                        ],
                        columns: [
                            {
                                data: null,
                                title: '<label class="radio radio-lg radio-outline"></label>',
                                orderable: false,
                                searchable: false,
                                width: "20px",
                                class: "ckb_item",
                                render: function (data, type, row) {
                                    return '<label for="pcr_radio_' + row.id + '" class="radio radio-lg radio-outline p_radio-item"><input data-route="' + route + '&shop_id=' + row.shop.id + '" class="pcr_radio" type="radio" data-id="' + row.group_id + '" name="pcr_radio_shop" rel="' + row.id + '" id="pcr_radio_' + row.id + '">&nbsp<span></span></label>';
                                }
                            },
                            {
                                data: 'domain', title: '{{__('Tên hiển thị')}}',
                                render: function (data, type, row) {
                                    return `<a class="" href='javascript:void(0)'>` + row.title + `</a>`;
                                }
                            },
                            {
                                data: 'image', title: '{{__('Ảnh hiển thị')}}',
                                render: function (data, type, row) {
                                    return  "<img class=\"image-item\" src=\""+row.image+"\" style=\"max-width: 90px;max-height: 90px\">";
                                }
                            },
                            {
                                data: 'domain', title: '{{__('Điểm bán')}}',
                                render: function (data, type, row) {
                                    return `<a class="" href='javascript:void(0)'>` + row.shop.domain + `</a>`;
                                }
                            },
                            {
                                data: 'status', title: '{{__('Trạng thái')}}',
                                render: function (data, type, row) {
                                    if (row.status == 1) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.minigame.status.1')}}" + "</span>";
                                    } else if (row.status == 2) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.minigame.status.2')}}" + "</span>";
                                    } else {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.minigame.status.0')}}" + "</span>";
                                    }
                                }
                            },
                        ],
                        "drawCallback": function (settings) {
                            var v_index = 0
                            $('#kt_datatable_addwebits_created_' + id + '_wrapper .dataTables_length').each(function () {
                                v_index = v_index + 1
                            })
                            if (v_index == 2){
                                $('#kt_datatable_addwebits_created_' + id + '_wrapper .dataTables_length').last().remove();
                            }
                            $('#kt_datatable_addwebits_created_' + id + '_wrapper #kt_datatable_addwebits_created_' + id + '_paginate').remove();
                        }
                    });
                    var filter = function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        datatable.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                    };
                    $('#kt_search_add-shop-created_' + id + '').on('click', function (e) {
                        e.preventDefault();
                        var params = {};
                        $('.datatable-input_add-shop-created_' + id + '').each(function () {
                            var i = $(this).data('col-index');
                            if (params[i]) {
                                params[i] += '|' + $(this).val();
                            } else {
                                params[i] = $(this).val();
                            }
                        });
                        $.each(params, function (i, val) {
                            // apply search params to datatable
                            datatableaddcreated.column(i).search(val ? val : '', false, false);
                        });
                        datatableaddcreated.table().draw();
                    });
                    $('#kt_reset_add-shop-created_' + id + '').on('click', function (e) {
                        e.preventDefault();
                        $('.datatable-input_add-shop-created_' + id + '').each(function () {
                            $(this).val('');
                            datatableaddcreated.column($(this).data('col-index')).search('', false, false);
                        });
                        datatableaddcreated.table().draw();
                    });
                    datatableaddcreated.on("click", "#btnCheckAllAddShop", function () {
                        $("#kt_datatable_addwebits_created_" + id + " .ckb_item input[type='checkbox']").prop('checked', this.checked).change();
                    })
                    datatableaddcreated.on("change", ".ckb_item input[type='checkbox']", function () {
                        if (this.checked) {
                            var currTr = $(this).closest("tr");
                            datatableaddcreated.rows(currTr).select();
                        } else {
                            var currTr = $(this).closest("tr");
                            datatableaddcreated.rows(currTr).deselect();
                        }
                    });
                }
            })
            $('body').on('click','.add-webits-created-show',function(){
                let route = $(this).data('route');
                let id = $(this).data('id');
                $('#dsShopCreated_show_'+ id +'').modal('show');
                if ($('#kt_datatable_addwebits_created_show_' + id + '').hasClass('test_addclass')){
                }else {
                    $('#kt_datatable_addwebits_created_show_' + id + '').addClass('test_addclass');
                    datatableaddcreated = $('#kt_datatable_addwebits_created_show_' + id + '').DataTable({
                        responsive: true,
                        dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                        <'row'<'col-sm-12'tr>>
                    <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                        lengthMenu: [5, 10, 20,50,100],
                        pageLength: 10,
                        language: {
                            'lengthMenu': 'Display _MENU_',
                        },
                        searchDelay: 500,
                        processing: true,
                        serverSide: true,
                        "order": [[1, "desc"]],
                        ajax: {
                            url: '{{url()->current()}}' + '?ajax=1&shop=3&id_group='+ id +'',
                            type: 'GET',
                            data: function (d) {
                                d.id = $('#id_' + id + '').val();
                                d.domain = $('#domain_show_' + id + '').val();
                                d.status = $('#status_show_' + id + '').val();
                                d.position = $('#position_show_' + id + '').val();
                                d.started_at = $('#started_at_show_' + id + '').val();
                                d.ended_at = $('#ended_at_show_' + id + '').val();
                                d.group_shop = $('#group_shop_show_' + id + '').val();
                            }
                        },
                        buttons: [
                        ],
                        columns: [
                            {
                                data: 'domain', title: '{{__('Tên hiển thị')}}',
                                render: function (data, type, row) {
                                    return `<a class="" href='javascript:void(0)'>` + row.title + `</a>`;
                                }
                            },
                            {
                                data: 'image', title: '{{__('Ảnh hiển thị')}}',
                                render: function (data, type, row) {
                                    return  "<img class=\"image-item\" src=\""+row.image+"\" style=\"max-width: 90px;max-height: 90px\">";
                                }
                            },
                            {
                                data: 'domain', title: '{{__('Điểm bán')}}',
                                render: function (data, type, row) {
                                    return `<a class="" href='javascript:void(0)'>` + row.shop.domain + `</a>`;
                                }
                            },
                            {
                                data: 'status', title: '{{__('Trạng thái')}}',
                                render: function (data, type, row) {
                                    if (row.status == 1) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.minigame.status.1')}}" + "</span>";
                                    } else if (row.status == 2) {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.minigame.status.2')}}" + "</span>";
                                    } else {
                                        return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.minigame.status.0')}}" + "</span>";
                                    }
                                }
                            },
                        ],
                        "drawCallback": function (settings) {
                            var v_index = 0
                            $('#kt_datatable_addwebits_created_show_' + id + '_wrapper .dataTables_length').each(function () {
                                v_index = v_index + 1
                            })
                            if (v_index == 2){
                                $('#kt_datatable_addwebits_created_show_' + id + '_wrapper .dataTables_length').last().remove();
                            }
                            $('#kt_datatable_addwebits_created_show_' + id + '_wrapper #kt_datatable_addwebits_created_show_' + id + '_paginate').remove();
                        }
                    });
                    var filter = function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        datatable.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                    };
                    $('#kt_search_add-shop-created_show_' + id + '').on('click', function (e) {
                        e.preventDefault();
                        var params = {};
                        $('.datatable-input_add-shop-created_show_' + id + '').each(function () {
                            var i = $(this).data('col-index');
                            if (params[i]) {
                                params[i] += '|' + $(this).val();
                            } else {
                                params[i] = $(this).val();
                            }
                        });
                        $.each(params, function (i, val) {
                            // apply search params to datatable
                            datatableaddcreated.column(i).search(val ? val : '', false, false);
                        });
                        datatableaddcreated.table().draw();
                    });
                    $('#kt_reset_add-shop-created_show_' + id + '').on('click', function (e) {
                        e.preventDefault();
                        $('.datatable-input_add-shop-created_show_' + id + '').each(function () {
                            $(this).val('');
                            datatableaddcreated.column($(this).data('col-index')).search('', false, false);
                        });
                        datatableaddcreated.table().draw();
                    });
                    datatableaddcreated.on("click", "#btnCheckAllAddShop", function () {
                        $("#kt_datatable_addwebits_created_show_" + id + " .ckb_item input[type='checkbox']").prop('checked', this.checked).change();
                    })
                    datatableaddcreated.on("change", ".ckb_item input[type='checkbox']", function () {
                        if (this.checked) {
                            var currTr = $(this).closest("tr");
                            datatableaddcreated.rows(currTr).select();
                        } else {
                            var currTr = $(this).closest("tr");
                            datatableaddcreated.rows(currTr).deselect();
                        }
                    });
                }
            })
            function confirmFunction(){
                var txt;
                var r = confirm("Bạn có muốn xóa ?");
                if (r == true) {
                    return true;
                } else {
                    return false;
                }
            }
        });
    </script>

    <link href="/assets/backend/assets/css/replication.css?v={{time()}}" rel="stylesheet" type="text/css"/>

    <style>
        .r_dsShopCreated thead {
            position: sticky;
            top: 0px;
            z-index: 11;
            background: #fff;
        }
        .dsShopCreated thead {
            position: sticky;
            top: -4px;
            z-index: 11;
            background: #fff;
        }
    </style>
@endsection
