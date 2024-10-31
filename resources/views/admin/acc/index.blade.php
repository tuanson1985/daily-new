{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">

        <div class="btn-group">
            @if($type == 1)
            <div class="btn-group">
                <button type="button" class="btn btn-success font-weight-bolder dropdown-toggle" data-toggle="dropdown">
                    <i class="ki ki-excel icon-sm"></i>
                    Up bằng file
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    @foreach($properties as $group)
                    <h6 class="dropdown-header">{{ $group->title }}</h6>
                        @foreach($group->childs as $category)
                            @if(in_array($category->position, config('etc.acc_property.up_by_excel')))
                            <a href="{{route('admin.acc.edit', [$type, 0])}}?excel=1&category={{ $category->id }}" type="button"  class="dropdown-item">
                                + {{ $category->title }}
                            </a>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
            <a href="{{route('admin.acc.edit', [$type, 0])}}?target=1" type="button"  class="btn btn-info font-weight-bolder">
                <i class="fas fa-plus-circle icon-md"></i>
                Thêm acc auto
            </a>
            @endif
            <a href="{{route('admin.acc.edit', [$type, 0])}}" type="button"  class="btn btn-success font-weight-bolder">
                <i class="fas fa-plus-circle icon-md"></i>
                {{__('Thêm mới')}}
            </a>
        </div>
    </div>



@endsection

{{-- Styles Section --}}
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
                                   placeholder="{{__('Tên tài khoản')}}">
                        </div>
                    </div>
                    {{--author--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="la la-user glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="author" placeholder="{{__('Tên người bán')}}">
                        </div>
                    </div>
                    {{--keyword--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="la la-key glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="seo_title" placeholder="{{__('keyword')}}">
                        </div>
                    </div>

                    {{--group_id--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>

                            <select class="form-control datatable-input datatable-input-select selectpicker" id="group_id" data-live-search="true" title="-- {{__('Tất cả danh mục')}} --">
                                <option value=''>-- Không chọn --</option>
                                @include('admin.acc.widget.category-select', ['data' => $properties, 'selected' => old('parent_id')??[], 'stSpecial' => ''])
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
                            {{Form::select('status', $status,old('status', $_GET['status']??null),array('id'=>'status','class'=>'form-control datatable-input',))}}
                        </div>
                    </div>

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
                    </div>
                </div>
            </form>
            <!--begin: Search Form-->
            <form method="GET" action="{{ route("admin.acc.edit",[1, 0]) }}">
                <div class="row">
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Keyword</span>
                            </div>
                            <input type="text" name="keyword" autocomplete="off" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <button type="submit" class="btn btn-danger" name="delete_keyword" value="1" onclick="return confirm('Bạn đã chắc chắn xoá!')">Xoá acc theo Keyword</button>
                    </div>
                </div>
            </form>
            <!--begin: Datatable-->
            <table class="table table-bordered table-hover table-checkable " id="kt_datatable">

            </table>
            <!--end: Datatable-->
        </div>


    </div>

    <!-- status item Modal -->
    <div class="modal fade" id="statusModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>['admin.acc.quick'],'class'=>'form-horizontal','id'=>'form-status','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title"> {{__('Xác nhận đổi trạng thái')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value=""/>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Lý do đổi</span>
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
                                    @if(in_array($key, [1,4,5,7,9]))
                                    <option value="{{ $key }}">{{ $item }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-custom" name="submit" value="status" data-form="form-refund">{{__('Đổi trạng thái')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- refund item Modal -->
    <div class="modal fade" id="refundModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.acc.edit',[$type, 0]),'class'=>'form-horizontal','id'=>'form-refund','method'=>'POST'))}}
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-custom" name="submit" value="refund" data-form="form-refund">{{__('Hoàn tiền')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- delete item Modal -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.acc.edit',[$type, 0]),'class'=>'form-horizontal','id'=>'form-delete','method'=>'DELETE'))}}
                <div class="modal-header">
                    <h5 class="modal-title"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        {{__('Bạn thực sự muốn xóa?')}}
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" name="force" type="checkbox" value="1" id="force_delete">
                        <label class="form-check-label" for="force_delete">Xoá vĩnh viễn</label>
                    </div>
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
    <div class="modal fade" id="reUpModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=> ['admin.acc.quick'],'class'=>'form-horizontal','id'=>'form-refresh-old','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        {{__('Bạn thực sự muốn cập nhật?')}}
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="refresh_old" value="1"/>
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-custom" data-form="form-delete">{{__('OK')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>




@endsection

<div class="container__clonev1">
    <div class="page-wrapper iframe-hidden">
        {{--                <a class="links-list-link" href="http://www.weather.gov/">Weather.gov</a>--}}
    </div>


    <div class="iframe-modal">
        <div class="iframe-header">
            <h2></h2>
            <button class="close-iframe">Close</button>
        </div>
        <div class="iframe-wrapper">
            <iframe class="iframe" src="" frameborder="0" sandbox="allow-same-origin allow-scripts">
            </iframe>
        </div>
    </div>
</div>

@section('styles')
    <style>
        .page-wrapper {
            position: relative;
            transition: filter 0.5s ease, opacity 0.5s ease;
        }
        .page-wrapper.iframe-shown {
            filter: blur(0.25rem);
            opacity: 0.5;
        }

        .page-title {
            font-size: 2rem;
            padding: 1rem;
            background: #DDD;
        }

        .links-list {
            width: 16rem;
            margin: 0 auto;
            padding: 1rem;
        }

        .links-list-item {
            padding: 1rem;
        }

        .links-list-link {
            display: block;
            padding: 1rem;
            background: #46F;
            color: #FFF;
            text-decoration: none;
            text-align: center;
            font-size: 1.5rem;
        }
        /*.links-list-link:hover {*/
        /*    background: #2b51ff;*/
        /*}*/
        .links-list-link:active {
            background: #0028dd;
        }

        .iframe-modal {
            display: flex;
            flex-direction: column;
            position: absolute;
            top: 2rem;
            right: 2rem;
            bottom: 2rem;
            left: 2rem;
            box-sizing: border-box;
            background: rgba(255, 255, 255, 0.75);
            transition: transform 0.5s ease, opacity 0.5s ease;
            box-shadow: 0 0.25rem 1rem 0 rgba(0, 0, 0, 0.5);
            transform: scale(0);
            opacity: 0;
        }
        .iframe-shown + .iframe-modal {
            transform: scale(1);
            opacity: 1;
        }

        .iframe-header {
            display: flex;
            background: #32c5d2;
            color: white;
            align-items: center;
        }
        .iframe-header h2 {
            flex-grow: 1;
            padding-left: 1rem;
        }

        .iframe-header .close-iframe {
            padding: 1rem;
            background: #32c5d2;
            color: white;
            font-size: 1.5rem;
            border: none;
            outline: none;
        }
        .iframe-header .close-iframe i{
            color: red;
            font-size: 24px;
        }
        .iframe-header .close-iframe:hover {
            opacity: 0.7;
        }
        .iframe-header .close-iframe:active {
            opacity: 1;
        }

        .iframe-wrapper {
            position: relative;
            flex-grow: 1;
            background: #FFFFFF;
            /*border: 4px solid #FFFFFF;*/
            border-top: none;
        }

        .iframe {
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .container__clonev2{
            z-index: 9999999;
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
        }
    </style>
@endsection

{{-- Styles Section

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
                    // Pagination settings
                    //full dom i và lp
                    // dom: `
                    //         <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>
                    //         <'row'<'col-sm-12'tr>>
                    //         <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                    // read more: https://datatables.net/examples/basic_init/dom.html

                    dom: `<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7 dataTables_pager'Bp>>
                            <'row'<'col-sm-12'tr>>
                        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,

                    // dom: "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
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
                            d.group_id = $('#group_id').val();
                            d.status = $('#status').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                            d.author = $('#author').val();
                            d.seo_title = $('#seo_title').val();
                        }
                    },

                    buttons: [
                        {
                            text: '<i class="m-nav__link-icon la la-refresh"></i> Cập nhật lại nick cũ ',
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
                                $('#reUpModal').modal('toggle');
                                $('#reUpModal .id').attr('value', allSelected);

                            }
                        },
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

                        {
                            data: 'id', title: 'ID',
                            render: function (data, type, row) {
                                return row.id+'<div><i>'+row.randId+'</i></div>';
                            }
                        },
                        {
                            data: 'id', title: '{{__('Tên TK')}}',
                            render: function (data, type, row) {
                                // $('.links-list-link' + row.id +'').on("click", function (e) {
                                //     e.preventDefault();
                                //     let src = $(this).attr('href');
                                //     let title = $(this).html();
                                //     $(".iframe").attr("src", src);
                                //     $('.iframe-header h2').html(title);
                                //     $('.page-wrapper' + row.id +'').addClass('iframe-shown');
                                // });
                                //
                                // $('.close-iframe').on('click', function() {
                                //     $('.page-wrapper' + row.id +'').removeClass('iframe-shown');
                                // });
                                // return `<div class="page-wrapper${row.id} iframe-hidden">
                                //             <a class="links-list-link${row.id}" href="/${row.id}">${row.title}</a>
                                //         </div>
                                //
                                //         <div class="iframe-modal">
                                //             <div class="iframe-header">
                                //                 <h2>National Weather Service</h2>
                                //                 <button class="close-iframe">Close</button>
                                //             </div>
                                //             <div class="iframe-wrapper">
                                //                 <iframe class="iframe" src="/${row.id}" frameborder="0" sandbox="allow-same-origin allow-scripts">
                                //                 </iframe>
                                //             </div>
                                //         </div>`;

                                var temp = `<a class="onclickshowclone" href="javascript:void(0)" data-id="${row.id}" data-title="${row.title}"> ${row.title} </a>`;
                                @if(auth()->user()->hasRole('admin') && session('shop_name'))
                                    temp += "<a href=\"http://{{ session('shop_name') }}/acc/" + row.randId + "\" title=\""+row.title+"\" target='_blank'><i class='fas fa-eye'></i></a>";
                                @endif
                                return temp;
                            }
                        },
                        {
                            data: "category", title: '{{__('Danh mục')}}', orderable: false,
                            render: function (data, type, row) {
                                return "<span class=\"label label-pill label-inline label-center mr-2  label-success \">" + (row.category? row.category.title: row.parent_id) + "</span><br />";
                            }
                        },

                        {data: 'author', title:'Người bán',
                            render: function ( data, type, row ) {
                                return row.author;
                            }
                        },
                        {data: 'price_old',title:'Giá ảo',
                            render: function ( data, type, row ) {
                                var disable = row.status == 1 && row.category.display_type != 2? "": 'disabled';
                                return "<input class=\"form-control input-price quick-input\" name=\"price_old\" value=\""+row.price_old+"\" "+disable+">";
                            }
                        },

                        {data: 'price',title:'Giá gốc',
                            render: function ( data, type, row ) {
                                var disable= row.status == 1 && row.category.display_type != 2? "": 'disabled';
                                return "<input class=\"form-control input-price quick-input\" name=\"price\" value=\""+row.price+"\"  "+disable+">";
                            }
                        },
                        {
                            data: 'status', title: '{{__('Trạng thái')}}',
                            render: function (data, type, row) {
                                var status_class = {'0': 'success', '1': 'primary'}
                                var status = {!! json_encode(config('etc.acc.status'), true) !!}
                                var el = '';
                                if (row.status == 0){
                                    el += "<a href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#refundModal\" rel=\""+row.id+"\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-danger btn-refund mr-2' title=\"Hoàn tiền\"><i class=\"la la-refresh\"></i></a>";
                                }
                                el += "<span class=\"label label-pill label-inline label-center mr-2  label-"+(status_class[row.status]? status_class[row.status]: 'danger' )+" \">" + status[row.status] + "</span>";
                                if ([1,4,5].indexOf(row.status) > -1) {
                                    el += "<a href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#statusModal\" rel=\""+row.id+"\" class='btn btn-sm  btn-icon btn-hover-text-white btn-hover-bg-info btn-refund ml-2' title=\"Sửa trạng thái\"><i class=\"la la-edit\"></i></a>";
                                }
                                return el;
                            }
                        },
                        {data: 'created_at', title: '{{__('Thời gian tạo')}}'},
                        { data: 'action',title:'Thao tác', orderable: false, searchable: false}

                    ],
                    "drawCallback": function (settings) {
                        $(".input-price").inputmask({
                            groupSeparator: ".",
                            radixPoint: ",",
                            alias: "numeric",
                            placeholder: "0",
                            autoGroup: true,
                        });
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
                        var i = $(this).attr('col-index');
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

            $('#refundModal').on('show.bs.modal', function(e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#refundModal [name="id"]').attr('value', id);
            });

            $('#statusModal').on('show.bs.modal', function(e) {
                //get data-id attribute of the clicked element
                var id = $(e.relatedTarget).attr('rel')
                $('#statusModal [name="id"]').attr('value', id);
            });
            var prevent = false;
            $('.btn-submit-custom').click(function (e) {
                if (!prevent) {
                    prevent = true;
                    var btn = this;
                    // KTUtil.btnWait(btn, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                    //gắn thêm hành động close khi submit
                    $('#submit-close').val($(btn).data('submit-close'));
                    // $('#' + $(btn).data('form')).submit();
                }
            });

            $('body').on('click', '.quick-save', function(){
                var el = $(this);
                var params = { _token:'{{ csrf_token() }}', id: el.data('id')};
                el.parents('tr').find('.quick-input').each(function(){
                    if ($(this).attr('name')) {
                        params[$(this).attr('name')] = $(this).val();
                    }
                })
                $.post('{{route('admin.acc.quick')}}', params, function (data) {
                    if (data.success) {
                        toast('{{__('Cập nhật thành công')}}');
                    } else {
                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    }
                }).done(function () {
                }).fail(function () {
                    toast('{{__('Lỗi rồi.Vui lòng F5 và thử lại')}}', 'error');
                })
                .always(function () {
                });
            });

        });





    </script>

    <script>
        $(document).ready(function(){
            $(document).on('click', '.onclickshowclone',function(e){
                e.preventDefault();

                var id = $(this).data('id');
                var src = '/nickclone/' + id;
                var title = $(this).data('title');

                $(".iframe").attr("src", src);
                $('.iframe-header h2').html(title);
                $(".page-wrapper").addClass('iframe-shown');

                $('.container__clonev1').addClass('container__clonev2');

            })
            $(document).on('click', '.close-iframe',function(e){
                e.preventDefault();
                $(".page-wrapper").removeClass('iframe-shown');
                $('.container__clonev1').removeClass('container__clonev2');
            });
        })


        $(".links-list-link").on("click", function (e) {
            e.preventDefault();
            let src = $(this).attr('href');
            let title = $(this).html();
            $(".iframe").attr("src", src);
            $('.iframe-header h2').html(title);
            $(".page-wrapper").addClass('iframe-shown');
        });


    </script>
@endsection
