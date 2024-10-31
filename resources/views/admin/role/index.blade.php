{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
        <div class="btn-group">
            <button type="button"  data-url="{{route("admin.role.create")}}" class="btn btn-success font-weight-bolder loadModal_toggle">
                <i class="fas fa-plus-circle icon-md"></i>
                {{__('Thêm mới')}}
            </button>
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
        </div>

        <div class="card-body">

            <div class="row">
                <div class="col-sm-8">
                    <div class="well">
                        <div class="lead text-right">
                            <div class="" style="float: right">
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
                            {!! $datatable !!}
                        </div>
                        {{ Form::close() }}

                    </div>
                </div>
                <div class="col-sm-4 d-none d-sm-block">
                    <div class="well">
                        <div class="m-demo-icon">
                            <i class="flaticon-light icon-lg"></i> {{__('Kéo thả để sắp xếp danh mục')}}
                        </div>
                        <br>
                        <div class="well-content" style="margin-left: 15px;display:none">
                            <div class="well-content-title">Mô tả nhóm quyền: <span></span> </div>
                            <div class="well-content-main">
                                ấcdc
                            </div>
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
                {{Form::open(array('route'=>array('admin.role.destroy',0),'class'=>'form-horizontal','method'=>'DELETE'))}}
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
                    $("#kt_select2_3, #kt_select2_3_validate").select2();
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

                    $.post('{{route('admin.role.order')}}',
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

                    return;
                }
            });
        });

      

    </script>


@endsection
