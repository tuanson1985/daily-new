{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
        <div class="btn-group">
            {{--btn thêm mới dạng popup--}}
            {{--<button type="button"  data-url="{{route("admin.article-category.create")}}" class="btn btn-success font-weight-bolder loadModal_toggle">--}}
            {{--    <i class="fas fa-plus-circle icon-md"></i>--}}
            {{--    {{__('Thêm mới')}}--}}
            {{--</button>--}}

            <div class="btn-group">
                <a href="{{route('admin.'.$module.'.create')}}" type="button"  class="btn btn-success font-weight-bolder">
                    <i class="fas fa-plus-circle icon-md"></i>
                    @if(session('shop_id'))
                    {{__('Thêm mới')}}
                    @else
                        {{__('Thêm mới mặc định')}}
                    @endif
                </a>
            </div>
        </div>
    </div>
@endsection

{{-- Content --}}
@section('content')

    <div class="card card-custom card-sticky" id="kt_page_sticky_card">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">
                    {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                </h3>
            </div>
            <div class="card-toolbar"></div>
            @if($module == 'menu-profile')
            <div class="row" style="margin-top: 12px">
                <div class="col-6">
                    <button class="btn btn-primary btn-dongbo">Minigame</button>
                </div>
                <div class="col-6">
                    <button class="btn btn-primary btn-nick">Url nick</button>
                </div>
            </div>
            @elseif($module == 'article-category')
                <div class="row" style="margin-top: 12px">
                    <div class="col-12">
                        <button class="btn btn-primary btn-swich">swich route</button>
                    </div>
                </div>
            @endif
        </div>

        <div class="card-body">

            <div class="row">
                <div class="col-sm-8">
                    <div class="well">
                        <div class="lead text-right">
                            <div class="" style="float: right">
                                <a href="#" class="btn btn-warning m-btn clone_selected">
                                    {{__('Clone menu')}}
                                </a>
                                <a href="#" id="nestable-menu-action" data-action="collapse-all" class="btn btn-info m-btn">
                                    {{__('Thu gọn')}}
                                </a>
                                <a href="#" id="nestable-menu-checkall" data-action="0"  class="btn btn-primary m-btn">
                                    {{__('Chọn tất cả')}}
                                </a>
                                <a  href="#" class="btn btn-danger m-btn  delete_selected"  >
                                    {{__('Xóa mục đã chọn')}}
                                </a>
                            </div>
                            <p class="success-indicator" style="display:none; margin-right: 15px;float: left;color: #34bfa3;font-size: 14px">
                                <span class="glyphicon glyphicon-ok"></span>   {{__('Danh mục đã được cập nhật !')}}
                            </p>

                        </div>
                        <div class="" style="clear: both"></div>
                        <div class="dd" id="nestable">
                            {!! $data !!}
                        </div>
                        {{ Form::close() }}

                    </div>
                </div>
                <div class="col-sm-4 d-none d-sm-block">
                    <div class="well">
                        <div class="m-demo-icon">
                            <i class="flaticon-light icon-lg"></i> {{__('Kéo thả để sắp xếp danh mục')}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- loadModal create_edit  Modal -->
    <div class="modal fade" id="loadModal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <!-- delete item Modal -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.'.$module.'.destroy',0),'class'=>'form-horizontal','method'=>'DELETE'))}}
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
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom">{{__('Xóa')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

{{--    clone menu --}}
    @if($module == 'menu-category' || $module == 'menu-profile' || $module == 'menu-transaction' || $module == 'article-category')
    <div class="modal fade" id="cloneModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.'.$module.'.duplicate',0),'class'=>'form-horizontal','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Chọn shop cần clone menu')}}</h5>
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
{{--                    @if (auth()->user()->account_type == 1)--}}
{{--                        @if (isset($client) && count($client) > 0)--}}
{{--                            <div class="dropdown">--}}
{{--                                <div class="topbar-item">--}}
{{--                                    <div class="dropdown bootstrap-select form-control datatable-input datatable-input-select">--}}
{{--                                        <select required name="shop_clone" class="form-control datatable-input datatable-input-select selectpicker select-client" data-live-search="true" title=" {{\Session::has('shop_name') ? \Session::get('shop_id') .' - '. \Session::get('shop_name') : '-- Tất cả shop --'}} " tabindex="null">--}}
{{--                                            <option class="bs-title-option" value="">Tất cả shop</option>--}}
{{--                                            @foreach ($client as $key => $item)--}}
{{--                                                <option value="{{$item->id}}">{{$item->id}} - {{$item->domain}}</option>--}}
{{--                                            @endforeach--}}
{{--                                        </select>--}}
{{--                                        <div class="dropdown-menu" style="max-height: 241px; overflow: hidden; min-height: 58px;">--}}
{{--                                            <div class="bs-searchbox">--}}
{{--                                                <input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-1" aria-autocomplete="list">--}}
{{--                                            </div>--}}
{{--                                            <div class="inner show" role="listbox" id="bs-select-1" tabindex="-1" style="max-height: 171px; overflow-y: auto; min-height: 0px;">--}}
{{--                                                <ul class="dropdown-menu inner show" role="presentation" style="margin-top: 0px; margin-bottom: 0px;">--}}
{{--                                                    <li><a role="option" class="dropdown-item" id="bs-select-1-0" tabindex="0"><span class="text">Tất cả shop</span></a></li>--}}
{{--                                                    @foreach ($client as $key => $item)--}}
{{--                                                        <li><a role="option" class="dropdown-item" id="bs-select-1-0" tabindex="0"><span class="text">{{$item->id}} - {{$item->domain}}</span></a></li>--}}
{{--                                                    @endforeach--}}
{{--                                                </ul>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <style>--}}
{{--                                @media (min-width: 992px){--}}
{{--                                    :not(.input-group) > .bootstrap-select.form-control:not([class*="col-"]){--}}
{{--                                        min-width: 200px !important;--}}
{{--                                    }--}}
{{--                                }--}}
{{--                            </style>--}}
{{--                        @endif--}}
{{--                    @endif--}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                    <button type="submit" class="btn btn-warning m-btn m-btn--custom">{{__('Clone')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    @endif
    @if($module == 'menu-profile')
    <div class="modal fade" id="switchImage">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.'.$module.'.switchurl',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn đổi url minigame?')}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                    <button type="submit" class="btn btn-success m-btn m-btn--custom">{{__('Switch')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="switchNick">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.'.$module.'.switchurlnick',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn đổi url lịch sử mua nick?')}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                    <button type="submit" class="btn btn-success m-btn m-btn--custom">{{__('Switch')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    @elseif($module == 'article-category')
        @if(session('shop_id'))
            <div class="modal fade" id="switchRoute">
                <div class="modal-dialog">
                    <div class="modal-content">
                        {{Form::open(array('route'=>array('admin.'.$module.'.switchrouter',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'POST'))}}
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            @php
                                $name = '';
                                $name_old = '';
                                if ($url == ''){
                                    $name = '/blog';
                                    $name_old = '/tin-tuc';
                                }else{
                                    if ($url == '/blog'){
                                        $name_old = '/blog';
                                        $name = '/tin-tuc';
                                    }else{
                                        $name = '/blog';
                                    $name_old = '/tin-tuc';
                                    }
                                }

                            @endphp


                            URL shop đang là <span style="font-weight: 700;font-size: 20;color: red">{{ $name_old }}</span> bạn muốn chuyển sang <span style="font-weight: 700;font-size: 20;color: red">{{ $name }}</span> không?
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
    @endif
@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')

    <script>



        //edit button
        $('.loadModal_toggle,.edit_toggle').each(function (index, elem) {
            $(elem).click(function (e) {

                e.preventDefault();
                $('#loadModal .modal-content').empty();
                $('#loadModal .modal-content').load($(this).data("url"),function(){
                    $('#loadModal').modal({show:true});
                    $("#kt_select2_2, #kt_select2_2_validate").select2();
                });
            });
        });

        //delete button
        $('.delete_toggle').each(function (index, elem) {
            $(elem).click(function (e) {

                e.preventDefault();
                $('#deleteModal .id').attr('value', $(elem).attr('rel'));
                $('#deleteModal').modal('toggle');
            });
        });
        //delete button all
        $('.delete_selected').click(function (e) {
            e.preventDefault();
            var id_delete = '';
            var total = $("#nestable .nested-list-content input[type=checkbox]:checked").length;
            if(total>0){
                $("#nestable input[type=checkbox]").each(function (index, elem) {
                    if ($(elem).is(':checked')) {
                        id_delete = id_delete + $(elem).attr('rel');
                        if (index !== total - 1) {
                            id_delete = id_delete + ',';
                        }
                    }
                });
                $('#deleteModal .id').attr('value', id_delete);
                $('#deleteModal').modal('toggle');
            }
            else{
                alert('{{__('Vui lòng chọn dữ liệu cần xóa')}}');
            }

        });


        //delete button all
        $('.clone_selected').click(function (e) {
            e.preventDefault();
            var id_delete = '';
            var total = $("#nestable .nested-list-content input[type=checkbox]:checked").length;
            if(total>0){
                $("#nestable input[type=checkbox]").each(function (index, elem) {
                    if ($(elem).is(':checked')) {
                        id_delete = id_delete + $(elem).attr('rel');
                        id_delete = id_delete + ',';
                        if (index !== total - 1) {

                        }
                    }
                });

                console.log(id_delete);
                $('#cloneModal .id').attr('value', id_delete);
                $('#cloneModal').modal('toggle');
            }
            else{
                alert('{{__('Vui lòng chọn dữ liệu cần clone')}}');
            }

        });

        //end delete button all

        // datatable.on("click", "#btnCheckAll", function () {
        //     $(".ckb_item input[type='checkbox']").prop('checked', this.checked).change();
        // })
        $("#nestable-menu-checkall").click(function(e) {
            e.preventDefault();
            action =$(this).attr('data-action');
            if (action == 1) {
                $(this).text('Chọn tất cả');
                $(this).attr('data-action',0);
                $(".nested-list-content .m-checkbox input[type='checkbox']").prop('checked', false).change();
            }
            else{
                $(this).text('Bỏ chọn tất cả');
                $(this).attr('data-action',1);
                $(".nested-list-content  .m-checkbox input[type='checkbox']").prop('checked', true).change();
            }

        });



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
        //nestable action
        $('#nestable-menu-action').on('click', function(e)
        {
            action =$(this).attr('data-action');
            if (action === 'expand-all') {


                $(this).text('Thu gọn');
                $(this).attr('data-action','collapse-all');
                //thực hiện thao tác expand-all
                $('.dd').nestable('expandAll');
            }
            else{
                $(this).text('Mở rộng');
                $(this).attr('data-action','expand-all');
                //thực hiện thao tác collapse-all
                $('.dd').nestable('collapseAll');
            }

        });
        //end nestable action

        $("#nestable input[type='checkbox']").change(function () {

            //click children
            $(this).closest('.dd-item').find("input[type='checkbox']").prop('checked', this.checked);
            var is_checked = $(this).is(':checked');


            $("#nestable input[type='checkbox']").each(function (index, elem) {

                if ($(elem).is(':checked')) {
                    return false;
                }
            });
        });

        jQuery(document).ready(function () {

            $('.btn-dongbo').click(function (e) {
                e.preventDefault();
                $('#switchImage').modal('show');
            });

            $('.btn-nick').click(function (e) {
                e.preventDefault();
                $('#switchNick').modal('show');
            });

            $('.btn-swich').click(function (e) {
                e.preventDefault();
                $('#switchRoute').modal('show');
            });

        });

    </script>


@endsection
