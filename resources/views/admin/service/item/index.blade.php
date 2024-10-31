{{-- Extends layout --}}
@extends('admin._layouts.master')

@section('styles')
    <link href="/assets/backend/assets/css/replication_shop.css?v={{time()}}" rel="stylesheet" type="text/css"/>
    <link href="/assets/backend/assets/css/service.css?v={{time()}}" rel="stylesheet" type="text/css"/>
@endsection

@section('action_area')
    <div class="d-flex align-items-center text-right">

        <div class="btn-group">
            <a href="{{route('admin.'.$module.'.create')}}" type="button"  class="btn btn-success font-weight-bolder">
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
                </h3>
            </div>
            <div class="card-toolbar"></div>

        </div>

        <div class="card-body">
            <!--begin: Search Form-->
            <form class="mb-10" action="{{route('admin.service.export-excel')}}" id="form-export-excel" method="POST">
                @csrf
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

                    {{--group_id--}}

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>

                            <select id="id_group"
                                    class="form-control datatable-input datatable-input-select selectpicker" data-live-search="true"
                                    title="-- {{__('Tất cả danh mục')}} --">
                                <option value="">--- Chọn danh mục ---</option>
                                @if( !empty(old('parent_id')) )
                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,old('parent_id')) !!}
                                @else
                                    <?php $itSelect = [] ?>
                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,$itSelect) !!}
                                @endif
                            </select>

                        </div>
                    </div>


                    {{--shop_id--}}
                    {{--                    <div class="form-group col-12 col-sm-6 col-lg-3">--}}
                    {{--                        <div class="input-group">--}}
                    {{--                            <div class="input-group-prepend">--}}
                    {{--                                <span class="input-group-text"><i--}}
                    {{--                                        class="la la-calendar-check-o glyphicon-th"></i></span>--}}
                    {{--                            </div>--}}
                    {{--                            {{Form::select('shop_id',[''=>'-- Tất cả trạng shop --']+$shop,old('shop_id', isset($data) ? $data->shop_id : null),array('id'=>'shop_id','class'=>'form-control datatable-input',))}}--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}


                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            {{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.language-nation.status'),old('status', isset($data) ? $data->status : null),array('id'=>'status','class'=>'form-control datatable-input',))}}
                        </div>
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

                    {{--started_at--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="started_at" id="started_at" autocomplete="off"
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
                            <input type="text" name="ended_at" id="ended_at" autocomplete="off"
                                   class="form-control datatable-input   datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian kết thúc')}}" data-toggle="datetimepicker">

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
                        @if(Auth::user()->can('service-export-excel-attribute') || Auth::user()->id == 28)
                            <button class="btn btn-danger btn-secondary--icon" value="1" name="export_excel" type="submit">
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

            <!--begin: Datatable-->
            <table class="table table-bordered table-hover table-checkable " id="kt_datatable">
            </table>
            <!--end: Datatable-->
        </div>
    </div>


    {{---------------all modal controll-------}}

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

    <!-- updateConfigModal Modal -->
    <div class="modal fade" id="updateConfigModal">
        <div class="modal-dialog  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="text-center modal-title label-service-selected" ></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body replication" style="padding: 0">

                        <ul class="nav nav-tabs" role="tablist">

                            <li class="nav-item nav-item-replication updateByShop">
                                <a class="nav-link show active" data-toggle="tab" href="#system" role="tab" aria-selected="true">
                                    <span class="nav-text">Shop</span>
                                </a>
                            </li>

                            <li class="nav-item nav-item-replication updateByGroupShop">
                                <a class="nav-link" data-toggle="tab" href="#expenses" role="tab" aria-selected="false">
                                    <span class="nav-text">Nhóm shop</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content tab-content-replication">
                            {{--                    Theo từng shop        --}}
                            <div class="tab-pane show active" id="system" role="tabpanel">
                                <div class="row marginauto blook-row">
                                    <div class="col-md-12 left-right">

                                        <div class="row marginauto blook-item-row">
                                            <div class="col-md-12 left-right blook-item-title">
                                                <span>Danh sách shop</span>
                                            </div>
                                            <div class="col-md-12 left-right blook-item-body">

                                                <!--begin: Search Form-->
                                                <form class="mb-10">
                                                    <div class="row">

                                                        <div class="form-group col-12 col-sm-6 col-lg-6">
                                                            <div class="input-group">
                                                                <select name="shop_access[]" multiple="multiple" title="Chọn shop cần clone" class="form-control select2 col-md-5 datatable-input-shop shop_access"  data-placeholder="{{__('Hoặc chọn shop')}}" id="kt_select2_3" style="width: 100%">
                                                                    @foreach($shopSearch as $key => $item)
                                                                        <option value="{{ $item->id }}">{{ $item->domain }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <button class="btn btn-primary btn-primary--icon" id="kt_search_shop">
                                                    <span>
                                                        <i class="la la-search"></i>
                                                        <span>Tìm kiếm</span>
                                                    </span>
                                                            </button>&#160;&#160;
                                                            <button class="btn btn-secondary btn-secondary--icon" id="kt_reset_shop">
                                                    <span>
                                                        <i class="la la-close"></i>
                                                        <span>Reset</span>
                                                    </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <!--begin: Search Form-->

                                                <!--begin: Datatable-->
                                                <table class="table table-bordered table-hover table-checkable " id="kt_datatable_shop">
                                                </table>
                                                <!--end: Datatable-->

                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            {{--                    Theo nhóm shop     --}}
                            <div class="tab-pane" id="expenses" role="tabpanel">
                                <div class="row marginauto blook-row">
                                    <div class="col-md-12 left-right">
                                        {{--                                    Block 1                     --}}
                                        <div class="row marginauto blook-item-row">
                                            <div class="col-md-12 left-right blook-item-title">
                                                <span>Danh sách nhóm shop</span>
                                            </div>
                                            <div class="col-md-12 left-right blook-item-body">

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
                                                                <input type="text" class="form-control datatable-input-group-shop" id="id_group_shop" placeholder="{{__('ID')}}">
                                                            </div>
                                                        </div>
                                                        {{--title--}}
                                                        <div class="form-group col-12 col-sm-6 col-lg-3">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i
                                                                            class="la la-calendar-check-o glyphicon-th"></i></span>
                                                                </div>
                                                                <input type="text" class="form-control datatable-input-group-shop" id="domain_group_shop"
                                                                       placeholder="{{__('Tên group shop')}}">
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <button class="btn btn-primary btn-primary--icon" id="kt_search_group_shop">
                                                                <span>
                                                                    <i class="la la-search"></i>
                                                                    <span>Tìm kiếm</span>
                                                                </span>
                                                            </button>&#160;&#160;
                                                            <button class="btn btn-secondary btn-secondary--icon" id="kt_reset_group_shop">
                                                                <span>
                                                                    <i class="la la-close"></i>
                                                                    <span>Reset</span>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <!--begin: Search Form-->
                                                <!--begin: Datatable-->
                                                <table class="table table-bordered table-hover table-checkable " id="kt_datatable_group_shop">
                                                </table>
                                                <!--end: Datatable-->

                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

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

    <!-- cập nhật item Modal -->
    <div class="modal fade" id="confirmUpdateModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service.post-sync-update-config'),'style'=>'','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-weight: 500;font-size: 18px"> {{__('Danh sách các shop cập nhật.')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row marginauto c-table">
                        <div class="col-md-12 left-right">
                            <div class="body-box-loadding result-amount-loadding">
                                <div class="d-flex justify-content-center">
                                    <span class="pulser"></span>
                                </div>
                            </div>
                            <table class="table table-hover" style="background: ghostwhite;border-radius: 8px;margin-bottom: 0">
                                <thead>
                                <tr>
                                    <th scope="col">STT</th>
                                    <th scope="col">Tên shop</th>
                                    <th scope="col">Update cổng Sms</th>
                                </tr>
                                </thead>
                                <tbody class="data-shop">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="item_id" class="item_id" value=""/>
                    <input type="hidden" name="shop_id" class="shop_id">
                    <input type="hidden" name="shop_id_update_with_gate" class="shop_id_update_with_gate">
                    <input type="hidden" name="group_shop" class="group_shop" value="0"/>
                    <button type="submit" class="btn btn-info mr-auto btn-back-updateConfigModal ">{{__('Quay lại')}}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                    <button type="submit" class="btn btn-success">{{__('Cập nhật cấu hình')}}</button>

                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- cập nhật item Modal -->
    <div class="modal fade" id="confirmRemoveSyncModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service.post-remove-sync-config'),'style'=>'','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-weight: 500;font-size: 18px"> {{__('Gỡ bỏ phân phối')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row marginauto c-table">
                        <div class="col-md-12 left-right">
                            <div class="body-box-loadding result-amount-loadding">
                                <div class="d-flex justify-content-center">
                                    <span class="pulser"></span>
                                </div>
                            </div>
                            <table class="table table-hover" style="background: ghostwhite;border-radius: 8px;margin-bottom: 0">
                                <thead>
                                <tr>
                                    <th scope="col">STT</th>
                                    <th scope="col">Tên shop</th>
                                    <th scope="col">Gõ bỏ phân phối</th>
                                </tr>
                                </thead>
                                <tbody class="data-shop">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="item_id" class="item_id" value=""/>
                    <input type="hidden" name="shop_id" class="shop_id">
                    <input type="hidden" name="group_shop" class="group_shop" value="0"/>
                    <button type="submit" class="btn btn-info mr-auto btn-back-updateConfigModal ">{{__('Quay lại')}}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                    <button type="submit" class="btn btn-danger"><i class="la la-trash "></i>{{__('Gỡ bỏ phân phối')}}</button>
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
                            d.id_group = $('#id_group').val();
                            d.position = $('#position').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                        }
                    },

                    buttons: [

                        {
                            text: '<i class="m-nav__link-icon la la-trash"></i> Xóa đã chọn ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatable.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                                    return;
                                }

                                datatable.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {
                                    if ($(elem).is(':checked')) {
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                        }
                                    }
                                })
                                $('#deleteModal').modal('toggle');
                                $('#deleteModal .id').attr('value', allSelected);

                            }
                        },
                        {
                            "extend": 'excelHtml5',
                            "text": ' <i class="far fa-file-excel icon-md"></i> {{__('Xuất excel')}} ',
                            "action": newexportaction,
                        },





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

                        {data: 'id', title: 'ID'},
                        {
                            data: 'title', title: '{{__('Tiêu đề')}}',
                            render: function (data, type, row) {
                                var domainCurrent='{{session()->get('shop_name')}}'+"";

                                if(domainCurrent!=""){
                                    var temp = "<a href=\"https://"+ domainCurrent + '/dich-vu/' + row.slug + "\" title=\""+row.title+"\"  target='_blank'    >" + row.title + "</a>";
                                }
                                else{
                                    var temp = "<a href=\"" + '/dich-vu/' + row.slug + "\" title=\""+row.title+"\"  target='_blank'    >" + row.title + "</a>";
                                }
                                return temp;
                            }
                        },
                        {
                            data: 'daily', title: '{{__('Cổng dịch vụ')}}',
                            render: function (data, type, row) {
                                if (row.daily){
                                    return row.daily;
                                }
                                return '';
                            }
                        },
                            @if(!empty(config('module.'.$module.'.position')))

                        {data: 'position',title:'{{__('Vị trí')}}', orderable: false, searchable: false,

                            render: function ( data, type, row ) {
                                var arrConfig= {!! json_encode(config('module.'.$module.'.position')) !!}

                                    return arrConfig[row.position]??"";

                            }
                        },
                            @endif

                        {
                            data: "groups", title: '{{__('Danh mục')}}', orderable: false,
                            render: function (data, type, row) {
                                var temp = "";
                                $.each(row.groups, function (index, value) {
                                    if (value.name == 'admin') {
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-primary \">" + value.title + "</span><br />";
                                    } else {
                                        temp += "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + value.title + "</span><br />";
                                    }

                                    // console.log( value.title);
                                });
                                return temp;
                            }
                        },


                        {{--{data: 'locale', title: '{{__('Ngôn ngữ')}}'},--}}

                        {{--{data: 'image',title:'{{__('Hình ảnh')}}', orderable: false, searchable: false,--}}
                        {{--    render: function ( data, type, row ) {--}}
                        {{--        if(row.image=="" || row.image==null){--}}

                        {{--            return  "<img class=\"image-item\" src=\"/assets/backend/themes/images/empty-photo.jpg\" style=\"max-width: 40px;max-height: 40px\">";--}}
                        {{--        }--}}
                        {{--        else{--}}
                        {{--            return  "<img class=\"image-item\" src=\""+row.image+"\" style=\"max-width: 40px;max-height: 40px\">";--}}
                        {{--        }--}}
                        {{--    }--}}
                        {{--},--}}
                        {{--{data: 'order', title: '{{__('Thứ tự')}}'},--}}
                        {
                            data: 'status', title: '{{__('Trạng thái')}}',
                            render: function (data, type, row) {

                                if (row.status == 1) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + "{{config('module.'.$module.'.status.1')}}" + "</span>";
                                } else if (row.status == 2) {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-warning \">" + "{{config('module.'.$module.'.status.2')}}" + "</span>";
                                } else {
                                    return "<span class=\"label label-pill label-inline label-center mr-2 label-danger \">" + "{{config('module.'.$module.'.status.0')}}" + "</span>";
                                }

                            }
                        },

                        {data: 'created_at', title: '{{__('Thời gian')}}'},
                        { data: 'action',title:'Thao tác', orderable: false, searchable: false}

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
                        // apply search params to datatable
                        datatable.column(i).search(val ? val : '', false, false);
                    });
                    datatable.table().draw();
                });

                $('#kt_reset').on('click', function (e) {
                    e.preventDefault();
                    $('.datatable-input').each(function () {
                        $(this).val('');
                        datatable.column($(this).data('col-index')).search('', false, false);
                    });

                    $("#id_group").val('').trigger('change')


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


            $('body').on('click', '.updateconfig_toggle', function(e) {
                e.preventDefault();
                var id = $(this).attr('rel')
                $('#updateConfigModal .id').attr('value', id);
                $('#updateConfigModal .label-service-selected').html("Dịch vụ: <b>#"+id + " - "+ $(this).data('title')+"</b>");
                $('#updateConfigModal_title').val($(this).data('title'));
                $('#updateConfigModal_id').val(id);

                updateByType();
            });

            $('body').on('click', '.updateByShop', function(e) {
                e.preventDefault();
                updateByType();
            });

            $('body').on('click', '.updateByGroupShop', function(e) {
                e.preventDefault();
                updateByType();
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

        ////////////////////////handle for shop ////////////////////////
        var datatableShop=null;
        var datatableGroupShop=null;

        function updateByType(){
            if(datatableShop==null){

                // begin first table
                datatableShop = $('#kt_datatable_shop').DataTable({
                    responsive: true,
                    dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                             <'row'<'col-sm-12 scroll-default'tr>>
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
                        url: '{{route('admin.service.get-shop-update-config')}}' + '?ajax=1&filter_type=shop',
                        type: 'GET',
                        data: function (d) {
                            d.shop_access = $('.shop_access').val();
                            d.item_id = $('#updateConfigModal .id').val();
                        }
                    },

                    buttons: [
                            @if(Auth::user()->can('service-remove-sync-config'))
                        {
                            text: '<i class="la la-trash "></i> Gỡ bỏ phân phối ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatableShop.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    toast("Vui lòng chọn shop để thực hiện thao tác", 'error');
                                    return;
                                }
                                $('.data-shop').html('');
                                let c_index = 0;
                                let r_id;
                                datatableShop.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {

                                    if ($(elem).is(':checked')) {
                                        c_index = c_index + 1;
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                        }

                                        let c_domain = $(elem).data('domain');
                                        let c_id = $(elem).data('id');


                                        let c_update_gate_sms = $('.row_update_gate_sms_id_'+c_id).is(':checked');

                                        if (c_index > 1){
                                            r_id += ',' + c_id;
                                        }else {
                                            r_id = c_id;
                                        }
                                        var html = `   <tr>
                                                        <th scope="row">${c_index}</th>
                                                        <td>${c_domain}</td>
                                                        <td><b class=\"text-success\">Có</b></td>

                                                    </tr>`;

                                        $('.data-shop').append(html);
                                    }
                                });

                                $('#updateConfigModal').modal('toggle');
                                $('#confirmRemoveSyncModal').modal('toggle');
                                $('#confirmRemoveSyncModal .item_id').attr('value', $('#updateConfigModal .id').val());
                                $('#confirmRemoveSyncModal .shop_id').attr('value', allSelected);
                                $('#confirmRemoveSyncModal .group_shop').attr('value', 0);

                            }
                        },
                            @endif
                            @if(Auth::user()->can('service-sync-update-config'))
                        {
                            text: '<i class="fas fa-plus-circle icon-md"></i> Cập nhật đã chọn ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatableShop.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    toast("Vui lòng chọn shop để thực hiện thao tác", 'error');
                                    return;
                                }
                                $('.data-shop').html('');
                                let c_index = 0;
                                let r_domain;
                                let r_id;
                                let shop_id_update_with_gate;

                                datatableShop.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {

                                    if ($(elem).is(':checked')) {
                                        c_index = c_index + 1;
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                        }

                                        let c_domain = $(elem).data('domain');
                                        let c_id = $(elem).data('id');


                                        let c_update_gate_sms = $('.row_update_gate_sms_id_'+c_id).is(':checked');

                                        if (c_index > 1){
                                            r_id += ',' + c_id;
                                            if(c_update_gate_sms==true){
                                                shop_id_update_with_gate += ',' + c_id;
                                            }

                                        }else {
                                            r_id = c_id;
                                            if(c_update_gate_sms==true){
                                                shop_id_update_with_gate =c_id;
                                            }
                                        }
                                        var html = `   <tr>
                                                        <th scope="row">${c_index}</th>
                                                        <td>${c_domain}</td>
                                                        <td>${c_update_gate_sms==true?"<b class=\"text-success\">Có</b>":"<b class=\"text-danger\">Không</b>"}</td>

                                                    </tr>`;

                                        $('.data-shop').append(html);
                                    }
                                });

                                $('#updateConfigModal').modal('toggle');
                                $('#confirmUpdateModal').modal('toggle');
                                $('#confirmUpdateModal .group_shop').attr('value', 0);
                                $('#confirmUpdateModal .item_id').attr('value', $('#updateConfigModal .id').val());
                                $('#confirmUpdateModal .shop_id').attr('value', allSelected);
                                $('#confirmUpdateModal .shop_id_update_with_gate').attr('value', shop_id_update_with_gate);

                            }
                        }
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

                                return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input type="checkbox" data-id="' + row.shop.id  + '" data-domain="' + row.shop.domain + '" rel="' + row.shop.id + '" >&nbsp<span></span></label>';

                            }
                        },
                        {
                            data: 'shop.id', title: '{{__('ID')}}',
                            render: function (data, type, row) {

                                return row.shop.id??"";
                            }
                        },
                        {
                            data: 'shop.domain', title: '{{__('Tên shop')}}',
                            render: function (data, type, row) {

                                return "<a href=\"https://"+ (row.shop.domain??"") + "\" target='_blank'    >" + (row.shop.domain??"") + "</a>";
                            }
                        },

                        {
                            data: null,
                            title: '<label class="checkbox checkbox-lg checkbox-outline"><input type="checkbox" id="btnCheckAllUpdateGateSms">&nbsp<span></span>&nbspUpdate cổng Sms</label>',
                            orderable: false,
                            searchable: false,
                            class: "ckb_item_update_gate_sms",
                            render: function (data, type, row) {

                                return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input type="checkbox" class="' +'row_update_gate_sms_id_'+ row.shop.id + '" data-id="' + row.shop.id  + '" data-domain="' + row.shop.domain + '" rel="' + row.shop.id + '" >&nbsp<span></span></label>';

                            }
                        },
                    ],
                    "drawCallback": function (settings) {
                        $('#kt_datatable_shop_paginate').remove();
                        $('#kt_datatable_shop_length').remove();
                    }

                });

                var filter = function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    datatableShop.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                };

                $('#kt_search_shop').on('click', function (e) {
                    e.preventDefault();

                    var params = {};
                    $('.datatable-input-shop').each(function () {
                        var i = $(this).data('col-index');
                        if (params[i]) {
                            params[i] += '|' + $(this).val();
                        } else {
                            params[i] = $(this).val();
                        }
                    });

                    $.each(params, function (i, val) {
                        // apply search params to datatable
                        datatableShop.column(i).search(val ? val : '', false, false);
                    });
                    datatableShop.table().draw();
                });

                $('#kt_reset_shop').on('click', function (e) {
                    e.preventDefault();
                    $('.datatable-input-shop').each(function () {
                        $(this).val('');
                        datatableShop.column($(this).data('col-index')).search('', false, false);
                    });
                    datatableShop.table().draw();
                });

                datatableShop.on("click", "#btnCheckAll", function () {
                    $("#kt_datatable_shop .ckb_item input[type='checkbox']").prop('checked', this.checked).change();

                })
                datatableShop.on("click", "#btnCheckAllUpdateGateSms", function () {
                    $("#kt_datatable_shop .ckb_item_update_gate_sms  input[type='checkbox']").prop('checked', this.checked).change();
                })
                datatableShop.on("change", "#kt_reset_shop .ckb_item input[type='checkbox']", function () {

                    if (this.checked) {
                        var currTr = $(this).closest("tr");
                        datatableShop.rows(currTr).select();
                    } else {
                        var currTr = $(this).closest("tr");
                        datatableShop.rows(currTr).deselect();
                    }
                });
            }
            else
            {
                datatableShop.draw();
            }

            if(datatableGroupShop==null){

                datatableGroupShop = $('#kt_datatable_group_shop').DataTable({
                    paging: true,
                    destroy: true,
                    responsive: true,
                    dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                             <'row'<'col-sm-12 scroll-default'tr>>
                       <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    lengthMenu: [5, 10, 20, 50,100,500,1000],
                    pageLength: 20,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "order": [[1, "desc"]],
                    ajax: {
                        url: '{{route('admin.service.get-shop-update-config')}}' + '?ajax=1&filter_type=group_shop',
                        type: 'GET',
                        data: function (d) {
                            d.shop_access = $('.shop_access').val();
                            d.item_id = $('#updateConfigModal .id').val();
                            d.id = $('.id_group_shop').val();
                            d.title = $('.domain_group_shop').val();
                        }
                    },
                    buttons: [
                            @if(Auth::user()->can('service-remove-sync-config'))
                        {
                            text: '<i class="la la-trash "></i> Gỡ bỏ phân phối ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatableGroupShop.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    toast("Vui lòng chọn shop để thực hiện thao tác", 'error');
                                    return;
                                }
                                $('.data-shop').html('');
                                let c_index = 0;
                                let r_id;
                                datatableGroupShop.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {

                                    if ($(elem).is(':checked')) {
                                        c_index = c_index + 1;
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                        }

                                        let c_domain = $(elem).data('domain');
                                        let c_id = $(elem).data('id');


                                        let c_update_gate_sms = $('.row_update_gate_sms_id_'+c_id).is(':checked');

                                        if (c_index > 1){
                                            r_id += ',' + c_id;
                                        }else {
                                            r_id = c_id;
                                        }
                                        var html = `   <tr>
                                                        <th scope="row">${c_index}</th>
                                                        <td>${c_domain}</td>
                                                        <td><b class=\"text-success\">Có</b></td>

                                                    </tr>`;

                                        $('.data-shop').append(html);
                                    }
                                });

                                $('#updateConfigModal').modal('toggle');
                                $('#confirmRemoveSyncModal').modal('toggle');
                                $('#confirmRemoveSyncModal .item_id').attr('value', $('#updateConfigModal .id').val());
                                $('#confirmRemoveSyncModal .shop_id').attr('value', allSelected);
                                $('#confirmRemoveSyncModal .group_shop').attr('value', 1);

                            }
                        },
                            @endif
                            @if(Auth::user()->can('service-sync-update-config'))
                        {
                            text: '<i class="fas fa-plus-circle icon-md"></i> Cập nhật đã chọn ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatableGroupShop.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    toast("Vui lòng chọn shop để thực hiện thao tác", 'error');
                                    return;
                                }
                                $('.data-shop').html('');
                                let c_index = 0;
                                let r_domain;
                                let r_id;
                                let shop_id_update_with_gate;

                                datatableGroupShop.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {

                                    if ($(elem).is(':checked')) {
                                        c_index = c_index + 1;
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                        }

                                        let c_domain = $(elem).data('domain');
                                        let c_id = $(elem).data('id');


                                        let c_update_gate_sms = $('.row_update_gate_group_sms_id_'+c_id).is(':checked');

                                        if (c_index > 1){
                                            r_id += ',' + c_id;
                                            if(c_update_gate_sms==true){
                                                shop_id_update_with_gate += ',' + c_id;
                                            }

                                        }else {
                                            r_id = c_id;
                                            if(c_update_gate_sms==true){
                                                shop_id_update_with_gate =c_id;
                                            }
                                        }
                                        var html = `   <tr>
                                                        <th scope="row">${c_index}</th>
                                                        <td>${c_domain}</td>
                                                        <td>${c_update_gate_sms==true?"<b class=\"text-success\">Có</b>":"<b class=\"text-danger\">Không</b>"}</td>

                                                    </tr>`;

                                        $('.data-shop').append(html);
                                    }
                                });

                                $('#updateConfigModal').modal('toggle');
                                $('#confirmUpdateModal').modal('toggle');
                                $('#confirmUpdateModal .item_id').attr('value', $('#updateConfigModal .id').val());
                                $('#confirmUpdateModal .shop_id').attr('value', allSelected);
                                $('#confirmUpdateModal .shop_id_update_with_gate').attr('value', shop_id_update_with_gate);
                                $('#confirmUpdateModal .group_shop').attr('value', 1);
                            }
                        }
                        @endif

                    ],
                    columns: [
                        {
                            data: null,
                            title: '<label class="checkbox checkbox-lg checkbox-outline"><input type="checkbox" id="btnCheckAll3">&nbsp<span></span></label>',
                            orderable: false,
                            searchable: false,
                            width: "20px",
                            class: "ckb_item",
                            render: function (data, type, row) {

                                return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item label_checkbox_item_group_shop"><input type="checkbox" data-id="' + row.id  + '" data-domain="' + row.title + '" rel="' + row.id + '" >&nbsp<span></span></label>';

                            }
                        },
                        {data: 'id', title: 'ID'},
                        {
                            data: 'title', title: '{{__('Tên nhóm điểm bán')}}',
                            render: function (data, type, row) {
                                return row.title;
                            }
                        },
                        {
                            data: 'count', title: '{{__('Số lượng điểm bán đang cấu hình')}}',
                            render: function (data, type, row) {
                                return row.count;
                            }
                        },
                        {
                            data: null,
                            title: '<label class="checkbox checkbox-lg checkbox-outline"><input type="checkbox" id="btnCheckAllUpdateGateGroupSms">&nbsp<span></span>&nbspUpdate cổng Sms</label>',
                            orderable: false,
                            searchable: false,
                            class: "ckb_item_update_gate_group_sms",
                            render: function (data, type, row) {

                                return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input type="checkbox" class="' +'row_update_gate_group_sms_id_'+ row.id + '" data-id="' + row.id  + '" data-domain="' + row.title + '" rel="' + row.id + '" >&nbsp<span></span></label>';

                            }
                        }
                    ],
                    "drawCallback": function (settings) {
                        $('#kt_datatable_group_shop_paginate').remove();
                        $('#kt_datatable_group_shop_length').remove();
                    }
                });
                var filter = function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    datatableGroupShop.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                };
                $('#kt_search_group_shop').on('click', function (e) {
                    e.preventDefault();
                    var params = {};
                    $('.datatable-input-group-shop').each(function () {
                        var i = $(this).data('col-index');
                        if (params[i]) {
                            params[i] += '|' + $(this).val();
                        } else {
                            params[i] = $(this).val();
                        }
                    });
                    $.each(params, function (i, val) {
                        // apply search params to datatable
                        datatableGroupShop.column(i).search(val ? val : '', false, false);
                    });
                    datatableGroupShop.table().draw();
                });
                $('#kt_reset_group_shop').on('click', function (e) {
                    e.preventDefault();
                    $('.datatable-input-group-shop').each(function () {
                        $(this).val('');
                        datatableGroupShop.column($(this).data('col-index')).search('', false, false);
                    });
                    datatableGroupShop.table().draw();
                });
                datatableGroupShop.on("click", "#btnCheckAll3", function () {
                    $(".ckb_item .label_checkbox_item_group_shop input[type='checkbox']").prop('checked', this.checked).change();
                })

                datatableGroupShop.on("click", "#btnCheckAllUpdateGateGroupSms", function () {
                    $("#kt_datatable_group_shop .ckb_item_update_gate_group_sms  input[type='checkbox']").prop('checked', this.checked).change();
                })
                datatableGroupShop.on("change", ".ckb_item .label_checkbox_item_group_shop input[type='checkbox']", function () {
                    if (this.checked) {
                        var currTr = $(this).closest("tr");
                        datatableGroupShop.rows(currTr).select();
                    } else {
                        var currTr = $(this).closest("tr");
                        datatableGroupShop.rows(currTr).deselect();
                    }
                });
                //function update field
                datatableGroupShop.on("change", ".update_field", function (e) {
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
            }
            else
            {
                datatableGroupShop.draw();
            }
        }


        $('.btn-back-updateConfigModal').click(function (e) {
            e.preventDefault();
            $('#confirmUpdateModal').modal('hide');
            $('#confirmRemoveSyncModal').modal('hide');
            $('#updateConfigModal').modal('show');
        });

    </script>



@endsection
