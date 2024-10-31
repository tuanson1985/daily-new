{{-- Extends layout --}}
@extends('admin._layouts.master')


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

                    {{--group_id--}}
                    @if(config('module.'.$module.'.key') != 'article' )
                        <div class="form-group col-12 col-sm-6 col-lg-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                </div>

                                <select id="rolse_id"
                                        class="form-control datatable-input datatable-input-select selectpicker" data-live-search="true"
                                        title="-- {{__('Tất cả danh mục')}} --">
                                    @if( !empty(old('parent_id')) )
                                        {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,old('parent_id')) !!}
                                    @else
                                        <?php $itSelect = [] ?>
                                        {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,$itSelect) !!}
                                    @endif
                                </select>

                            </div>
                        </div>
                    @else
                        <div class="form-group col-12 col-sm-6 col-lg-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                </div>

                                <select id="group_id"
                                        class="form-control datatable-input" data-live-search="true"
                                        title="-- {{__('Tất cả danh mục')}} --">
                                    <option value="" selected="selected">-- Tất cả danh mục --</option>
                                    @if( !empty(old('parent_id')) )
                                        {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,old('parent_id')) !!}
                                    @else
                                        <?php $itSelect = [] ?>
                                        {!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,$itSelect) !!}
                                    @endif
                                </select>

                            </div>
                        </div>
                    @endif



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

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="author_id"
                                   placeholder="{{__('Tác giả')}}">
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

    @if($module == 'article')
        <div class="modal fade" id="zipModal">

            <div class="modal-dialog">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.'.$module.'.zip',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'POST'))}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="timezone" class="form-control-label">{{ __('Chọn link dẫn cho bài viết') }}&nbsp;&nbsp;<span class="text-danger">(*)</span></label>
                                <select name="route_article" class="form-control select2 col-md-5 datatable-input kt_select2_3_nhom" style="width: 100%"
                                        data-actions-box="true" title="-- {{__('Chọn link dẫn cho bài viết')}} --">
                                    <option value="">Chọn link dẫn cho bài viết</option>
                                    <option value="0">Tin tức</option>
                                    <option value="1">Blog</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-success m-btn m-btn--custom">{{__('Zip')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="zipSettingModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.'.$module.'.zipsetting',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'POST'))}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{__('Bạn thực sự muốn zip?')}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-success m-btn m-btn--custom">{{__('Zip')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="zipModalV1">

            <div class="modal-dialog">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.'.$module.'.zipv1',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'POST'))}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="timezone" class="form-control-label">{{ __('Chọn link dẫn cho bài viết') }}&nbsp;&nbsp;<span class="text-danger">(*)</span></label>
                                <select name="route_article" class="form-control select2 col-md-5 datatable-input kt_select2_3_nhom" style="width: 100%"
                                        data-actions-box="true" title="-- {{__('Chọn link dẫn cho bài viết')}} --">
                                    <option value="">Chọn link dẫn cho bài viết</option>
                                    <option value="0">Tin tức</option>
                                    <option value="1">Blog</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-success m-btn m-btn--custom">{{__('Zip')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="zipSettingModalV1">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.'.$module.'.zipsettingv1',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'POST'))}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{__('Bạn thực sự muốn zip?')}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-success m-btn m-btn--custom">{{__('Zip')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="switchImage">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.'.$module.'.switchimage',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'POST'))}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{__('Bạn thực sự muốn thay link youtube cho bài viết?')}}
                            </div>
                        </div>
                        <div class="row" style="padding-top: 24px">
                            <div class="col-md-12">
                                <input type="text" name="youtube_link" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-success m-btn m-btn--custom">{{__('Switch')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    @endif

    <!-- duplicate item Modal -->
    @if($module == 'article' || $module == 'advertise')
        <div class="modal fade" id="duplicateModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.'.$module.'.clone'),'class'=>'form-horizontal','id'=>'form-duplicate','method'=>'POST'))}}
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h3 style="font-size: 16px;padding-bottom: 16px">Chọn shop cần clone:</h3>
                        <select name="shop_access[]" multiple="multiple" title="Chọn shop cần clone" class="form-control select2 col-md-5"  data-placeholder="{{__('Hoặc chọn shop')}}" id="kt_select2_3" style="width: 100%">
                            @foreach($client as $key => $item)
                                <option value="{{ $item->id }}">{{ $item->domain }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" class="id" value=""/>
                        <input type="hidden" name="cate" class="cate" value=""/>
                        <input type="hidden" name="module" class="module" value="{{ $module }}"/>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                        <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-custom" style="background: #1bc5bd;border: none" data-form="form-duplicate">{{__('Clone')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    @endif

    <input type="hidden"  class="setting_zip" value="{{ $setting_zip }}">
    <input type="hidden" class="setting_zipv2" value="{{ $setting_zipv2 }}">
@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')

    <script>

        $('.kt_select2_3_nhom').select2({
            placeholder: "Chưa chọn nhóm shop"
        });

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
                    @if($module == 'article' && session('shop_id') && !isset($setting_zip))
                    "order": [[1, "desc"]],
                    @else

                        @endif
                    ajax: {
                        url: '{{url()->current()}}' + '?ajax=1',
                        type: 'GET',
                        data: function (d) {

                            d.id = $('#id').val();
                            d.title = $('#title').val();
                            d.group_id = $('#group_id').val();
                            d.status = $('#status').val();
                            d.position = $('#position').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.author_id = $('#author_id').val();

                        }
                    },

                    buttons: [
                        {
                            text: '<i class="fas fa-plus-circle icon-md"></i> Swicht youtube',
                            action : function(e) {
                                e.preventDefault();

                                $('#switchImage').modal('toggle');

                            }
                        },
                        // {
                        //     text: '<i class="fas fa-plus-circle icon-md"></i> Z-Setting V1',
                        //     action : function(e) {
                        //         e.preventDefault();
                        //
                        //         $('#zipSettingModalV1').modal('toggle');
                        //
                        //     }
                        // },
                        //
                        // {
                        //     text: '<i class="fas fa-plus-circle icon-md"></i> Z-Article V1',
                        //     action : function(e) {
                        //         e.preventDefault();
                        //
                        //         $('#zipModalV1').modal('toggle');
                        //     }
                        // },
                        // {
                        //     text: '<i class="fas fa-plus-circle icon-md"></i> Z-Setting',
                        //     action : function(e) {
                        //         e.preventDefault();
                        //
                        //         $('#zipSettingModal').modal('toggle');
                        //
                        //     }
                        // },
                        //
                        // {
                        //     text: '<i class="fas fa-plus-circle icon-md"></i> Z-Article ',
                        //     action : function(e) {
                        //         e.preventDefault();
                        //
                        //         $('#zipModal').modal('toggle');
                        //     }
                        // },
                        {
                            "extend": 'excelHtml5',
                            "text": ' <i class="far fa-file-excel icon-md"></i> {{__('Xuất excel')}} ',
                            "action": newexportaction,
                        },
                            @if($module == 'advertise')

                        {
                            text: '<i class="fas fa-plus-circle icon-md"></i> Nhân bản quảng cáo',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var allcate = '';
                                var total = datatable.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                                    return;
                                }

                                datatable.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {
                                    if ($(elem).is(':checked')) {
                                        var sale = $(elem).attr('rel');
                                        var cate =  $(elem).data('cate');

                                        if (allSelected == '') {
                                            allSelected = sale;
                                            allcate = cate;
                                        }else{
                                            allSelected = allSelected + ',' + sale;
                                            allcate = allcate + ',' + cate;
                                        }
                                    }
                                })
                                $('#duplicateModal').modal('toggle');
                                $('#duplicateModal .id').attr('value', allSelected);
                                $('#duplicateModal .cate').attr('value', allcate);
                            }
                        },
                            @endif
                            @if($module == 'article')

                        {
                            text: '<i class="fas fa-plus-circle icon-md"></i> Nhân bản bài viết',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var allcate = '';
                                var total = datatable.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    alert("Vui lòng chọn dòng để thực hiện thao tác");
                                    return;
                                }

                                datatable.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {
                                    if ($(elem).is(':checked')) {
                                        var sale = $(elem).attr('rel');
                                        var cate =  $(elem).data('cate');

                                        if (allSelected == '') {
                                            allSelected = sale;
                                            allcate = cate;
                                        }else{
                                            allSelected = allSelected + ',' + sale;
                                            allcate = allSelected + ',' + cate;
                                        }
                                    }
                                })
                                $('#duplicateModal').modal('toggle');
                                $('#duplicateModal .id').attr('value', allSelected);
                                $('#duplicateModal .cate').attr('value', allcate);
                            }
                        },
                            @endif
                        {
                            text: '<i class="m-nav__link-icon la la-trash"></i> Xóa ',
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
                        {data: 'title', title: 'Tiêu đề'},

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
                                        temp += "<span data-id=\"" + value.id +"\" class=\"label label-pill label-inline label-center mr-2  label-primary \">" + value.title + "</span><br />";
                                    } else {
                                        temp += "<span data-id=\"" + value.id +"\" class=\"label label-pill label-inline label-center mr-2  label-success \">" + value.title + "</span><br />";
                                    }

                                    // console.log( value.title);
                                });
                                return temp;
                            }
                        },

                            @if($module == 'article')
                        {
                            data: 'author', title: '{{__('Tác giả')}}',
                            render: function (data, type, row) {
                                if (row.author){
                                    return "<span class=\"label label-pill label-inline label-center mr-2  label-primary \">" + row.author.username + "</span>";
                                }else {
                                    return "";
                                }


                            }
                        },
                            @else
                        {data: 'locale', title: '{{__('Ngôn ngữ')}}'},
                            @endif
                        {data: 'image',title:'{{__('Hình ảnh')}}', orderable: false, searchable: false,
                            render: function ( data, type, row ) {
                                if(row.image=="" || row.image==null){

                                    return  "<img class=\"image-item\" src=\"/assets/backend/themes/images/empty-photo.jpg\" style=\"max-width: 40px;max-height: 40px\">";
                                }
                                else{
                                    var time_published_at = row.published_at;
                                    time_published_at = new Date(time_published_at);
                                    time_published_at = time_published_at.getTime();
                                    var c_time = row.created_at;
                                    c_time = new Date(c_time);
                                    c_time = c_time.getTime();

                                    if (time_published_at == c_time){

                                        return  "<img class=\"image-item\" src=\""+row.image+"\" style=\"max-width: 40px;max-height: 40px\">";

                                    }else{

                                        let text = row.image;
                                        let index = text.indexOf('cdn.upanh.info')*1  > -1;

                                        if (index){
                                            return  "<img class=\"image-item\" src=\""+row.image+"\" style=\"max-width: 40px;max-height: 40px\">";
                                        }else{
                                            return  "<img class=\"image-item\" src=\""+MEDIA_URL+row.image+"\" style=\"max-width: 40px;max-height: 40px\">";
                                        }

                                    }

                                }
                            }
                        },

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
                            @if(config('module.'.$module.'.key') == 'article')
                        {
                            data: 'shop', title: '{{__('Shop')}}',
                            render: function (data, type, row) {

                                if (row.shop) {
                                    var temp = "<span class='badge badge-success' target='_blank'    >" + row.shop.domain + "</span>";
                                } else {
                                    var temp = "";
                                }
                                // if( row.shop == null ||  row.shop == '' ||  row.shop == undefined){
                                //     var temp = "<a href=\"" + ROOT_DOMAIN +"/"+ row.slug + "\" title=\""+row.title+"\"  target='_blank'    >" + ROOT_DOMAIN + "</a>";
                                //     return temp;
                                // }else {
                                //     if( row.shop.domain == null ||  row.shop.domain == '' ||  row.shop.domain == undefined){
                                //         var temp = "<a href=\"" + ROOT_DOMAIN +"/"+ row.slug + "\" title=\""+row.title+"\"  target='_blank'    >" + ROOT_DOMAIN + "</a>";
                                //         return temp;
                                //     }
                                //     else {
                                //
                                //         var display_type = row.display_type;
                                //         if (display_type && display_type == 1){
                                //             var temp = "<a class='badge badge-success' href=\"https://"+ row.shop.domain + '/blog/' + row.slug + "\" title=\""+row.title+"\"  target='_blank'    >" + row.shop.domain + "</a>";
                                //
                                //         }else {
                                //             var temp = "<a class='badge badge-success' href=\"https://"+ row.shop.domain + '/tin-tuc/' + row.slug + "\" title=\""+row.title+"\"  target='_blank'    >" + row.shop.domain + "</a>";
                                //
                                //         }
                                //         return temp;
                                //     }
                                // }
                                return temp;
                            }
                        },
                            @else
                        {data: 'order', title: '{{__('Thứ tự')}}'},
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

                            @endif
                            @if(isset($setting_zip))
                        {
                            data: 'published_at', title: '{{__('Thời gian')}}',
                            render: function (data, type, row) {
                                var time_published_at = row.published_at;
                                time_published_at = new Date(time_published_at);
                                time_published_at = time_published_at.getTime();
                                var c_time = "01/01/1970 08:00:00";
                                c_time = new Date(c_time);
                                c_time = c_time.getTime();

                                if (time_published_at == c_time){

                                    return row.created_at;
                                }else{

                                    return row.published_at;
                                }

                            }
                        },
                            {{--{data: 'published_at', title: '{{__('Thời gian')}}'},--}}
                            @else
                        {data: 'created_at', title: '{{__('Thời gian')}}'},
                            @endif

                        { data: 'action',title:'Thao tác', orderable: false, searchable: false}

                    ],
                    "drawCallback": function (settings) {
                        // $('.buttons-excel').remove()
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

@endsection
