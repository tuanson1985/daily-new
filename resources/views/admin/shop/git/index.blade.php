{{-- Extends layout --}}
@extends('admin._layouts.master')


{{--@section('action_area')--}}
{{--    <div class="d-flex align-items-center text-right">--}}
{{--        <div class="btn-group">--}}
{{--            <a href="{{route('admin.'.$module.'.create')}}" type="button"  class="btn btn-success font-weight-bolder">--}}
{{--                <i class="fas fa-plus-circle icon-md"></i>--}}
{{--                {{__('Thêm mới')}}--}}
{{--            </a>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endsection--}}

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

        <div class="card-body replication">
            <ul class="nav nav-tabs" role="tablist">

                <li class="nav-item nav-item-replication">
                    <a class="nav-link show active" data-toggle="tab" href="#system" role="tab" aria-selected="true">
                        <span class="nav-text">Shop</span>
                    </a>
                </li>

                <li class="nav-item nav-item-replication c-group-shop">
                    <a class="nav-link" data-toggle="tab" href="#expenses" role="tab" aria-selected="false">
                        <span class="nav-text">Nhóm shop</span>
                    </a>
                </li>

                <li class="nav-item nav-item-replication c-theme">
                    <a class="nav-link" data-toggle="tab" href="#theme" role="tab" aria-selected="false">
                        <span class="nav-text">Theme</span>
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
                                                    <select name="shop_access[]" multiple="multiple" title="Chọn shop cần clone" class="form-control select2 col-md-5 datatable-input"  data-placeholder="{{__('Hoặc chọn shop')}}" id="kt_select2_3" style="width: 100%">
                                                        @foreach($client as $key => $item)
                                                            <option value="{{ $item->id }}">{{ $item->domain }}</option>
                                                        @endforeach
                                                    </select>
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
                                                    <input type="text" class="form-control datatable-input_group_shop" id="id_group_shop" placeholder="{{__('ID')}}">
                                                </div>
                                            </div>
                                            {{--title--}}
                                            <div class="form-group col-12 col-sm-6 col-lg-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control datatable-input_group_shop" id="domain_group_shop"
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
                                    <table class="table table-bordered table-hover table-checkable " id="kt_datatable_group">
                                    </table>
                                    <!--end: Datatable-->

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                {{--                    Theo theme     --}}
                <div class="tab-pane" id="theme" role="tabpanel">
                    <div class="row marginauto blook-row">
                        <div class="col-md-12 left-right">
                            {{--                                    Block 1                     --}}
                            <div class="row marginauto blook-item-row">
                                <div class="col-md-12 left-right blook-item-title">
                                    <span>Danh sách theme</span>
                                </div>
                                <div class="col-md-12 left-right blook-item-body">

                                    <table class="table table-bordered table-hover table-checkable " id="kt_datatable_theme">
                                    </table>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{---------------all modal controll-------}}
    <!-- cập nhật item Modal -->
    <div class="modal fade" id="gitpullModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {{--                {{Form::open(array('route'=>array('admin.'.$module.'.destroy',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'DELETE'))}}--}}
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
                                    <th scope="col">Server</th>
                                    <th scope="col">Message</th>
                                    <th scope="col">Trạng thái</th>
                                </tr>
                                </thead>
                                <tbody class="data-shop">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="gitPullShop" action="/admin/git-pull" method="POST">
                        {{csrf_field()}}
                        <input type="hidden" name="id" class="id" value=""/>
                        <input type="hidden" name="group_shop" class="group_shop" value="0"/>
                        <input type="hidden" name="r_domain" class="r_domain">
                        <input type="hidden" name="r_ip" class="r_ip">
                        <input type="hidden" name="r_id" class="r_id">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-danger">{{__('Cập nhật')}}</button>
                    </form>
                </div>
                {{--                {{ Form::close() }}--}}
            </div>
        </div>
    </div>



    <!-- cập nhật item Modal Group -->
    <div class="modal fade" id="gitpullModalGroup">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {{--                {{Form::open(array('route'=>array('admin.'.$module.'.destroy',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'DELETE'))}}--}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-weight: 500;font-size: 18px"> {{__('Danh sách group shop cập nhật.')}}</h5>
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
                                    <th scope="col">Server</th>
                                    <th scope="col">Message</th>
                                    <th scope="col">Trạng thái</th>
                                </tr>
                                </thead>
                                <tbody class="data-group-shop">


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="gitPullGroupShop" action="/admin/git-pull" method="POST">
                        {{csrf_field()}}
                        <input type="hidden" name="id" class="id" value=""/>
                        <input type="hidden" name="group_shop" class="group_shop" value="1"/>
                        <input type="hidden" name="r_domain" class="r_domain">
                        <input type="hidden" name="r_ip" class="r_ip">
                        <input type="hidden" name="r_group" class="r_group">
                        <input type="hidden" name="r_id" class="r_id">
                        <input type="hidden" name="r_gid" class="r_gid">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-danger">{{__('Cập nhật')}}</button>
                    </form>
                </div>
                {{--                {{ Form::close() }}--}}
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

        $('#gitPullShop').submit(function (e) {
            e.preventDefault();
            var formSubmit = $(this);
            var url = formSubmit.attr('action');
            var btnSubmit = formSubmit.find(':submit');
            btnSubmit.prop('disabled', true);

            $('#gitpullModal .body-box-loadding').css('display','block');
            $('.data-shop').html('');
            var html = '<tr><td colspan="8" style="height: 48px" class="text-left"><b></b></td></tr>';
            $('.data-shop').html(html);

            $.ajax({
                type: "POST",
                url: url,
                data: formSubmit.serialize(), // serializes the form's elements.
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    $('#gitpullModal .body-box-loadding').css('display','none');
                    $('.data-shop').html('');
                    if(data.status == 1){

                        let r_status = data.r_status;
                        let r_domain = data.r_domain;
                        r_domain = r_domain.split('|');
                        let r_ip = data.r_ip;
                        r_ip = r_ip.split('|');
                        let cg_index = 0;
                        var r_html = '';
                        var r_ketqua = data.r_ketqua;
                        for (let i =0;i < r_domain.length; i++){
                            var t_ketqua = r_ketqua[i];
                            var message = '';
                            if (t_ketqua){
                                message = t_ketqua;
                            }else {
                                message = 'Chưa phân quyền vào thư mục server';
                            }

                            cg_index = cg_index + 1
                            r_html += '<tr>';
                            r_html += '<th scope="row">' + cg_index + '</th>';
                            r_html += '<td>' + r_domain[i] + '</td>';
                            r_html += '<td><a href="" target="_blank">' + r_ip[i] + '</a></td>';
                            r_html += '<td>' + r_ketqua[i] + '</td>';
                            if (r_status[i] == 0){
                                r_html += '<td class="text-center justify-content-center"><img src="/assets/backend/images/c-remove.svg" alt=""></td>';
                            }else {
                                r_html += '<td class="text-center justify-content-center"><img src="/assets/backend/images/c-check.svg" alt=""></td>';
                            }

                            r_html += '</tr>';
                        }
                        $('.data-shop').html(r_html);

                        let r_message = data.r_message;



                    }else if(data.status == 2){
                        toastr.error('Đặt yêu cầu thuê thất bại.');
                    }else{
                        $('#openDH').modal('show');
                        $('#text__errors').html('<span class="text-danger pb-2" style="font-size: 14px">'+data.message+'</span>');
                        // alert(response.message);
                    }
                },
                error: function (response) {
                    if(response.status === 422 || response.status === 429) {
                        let errors = response.responseJSON.errors;

                        jQuery.each(errors, function(index, itemData) {

                            formSubmit.find('.notify-error').text(itemData[0]);
                            return false; // breaks
                        });
                    }else if(response.status === 0){
                        alert(response.message);
                        $('#text__errors').html('<span class="text-danger pb-2" style="font-size: 14px">'+response.message+'</span>');
                    }
                    else {
                        $('#text__errors').html('<span class="text-danger pb-2" style="font-size: 14px">'+'Kết nối với hệ thống thất bại.Xin vui lòng thử lại'+'</span>');
                    }
                },
                complete: function (data) {
                    // btnSubmit.text('Thuê ngay');
                    btnSubmit.prop('disabled', false);
                }
            })

        })

        $('#gitPullGroupShop').submit(function (e) {
            e.preventDefault();
            var formSubmit = $(this);
            var url = formSubmit.attr('action');
            var btnSubmit = formSubmit.find(':submit');
            btnSubmit.prop('disabled', true);

            $('#gitpullModalGroup .body-box-loadding').css('display','block');
            $('.data-group-shop').html('');
            var html = '<tr><td colspan="8" style="height: 48px" class="text-left"><b></b></td></tr>';
            $('.data-group-shop').html(html);

            $.ajax({
                type: "POST",
                url: url,
                data: formSubmit.serialize(), // serializes the form's elements.
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    $('#gitpullModalGroup .body-box-loadding').css('display','none');
                    $('.data-group-shop').html('');
                    if(data.status == 1){

                        console.log(data)
                        var r_domain;
                        let r_group = data.r_group;
                        r_group = r_group.split(',');

                        r_domain = data.r_domain;
                        r_domain = r_domain.split(',');

                        let r_ip = data.r_ip;
                        r_ip = r_ip.split(',');
                        let cg_index = 0;
                        let s_index = 0;
                        let rq_domain;
                        let rq_ip;
                        let r_status = data.r_status;
                        var r_html_group = '';
                        var r_html = '';
                        var r_ketqua = data.r_ketqua;
                        for (let i =0;i < r_group.length; i++){
                            var t_ketqua = r_ketqua[i];
                            var message = '';
                            if (t_ketqua){
                                message = t_ketqua;
                            }else {
                                message = 'Chưa phân quyền vào thư mục server';
                            }
                            r_html_group += '<tr><td colspan="8" class="text-left"><b>' + r_group[i] + '</b></td></tr>';
                            rq_domain = r_domain[i].split('|');
                            rq_ip = r_ip[i].split('|');
                            for (let i =0;i < rq_domain.length; i++){
                                cg_index = cg_index + 1
                                r_html_group += '<tr>';
                                r_html_group += '<th scope="row">' + cg_index + '</th>';
                                r_html_group += '<td>' + rq_domain[i] + '</td>';
                                r_html_group += '<td><a href="" target="_blank">' + rq_ip[i] + '</a></td>';
                                r_html_group += '<td>' + r_ketqua[i] + '</td>';
                                if (r_status[s_index] == 0){
                                    r_html_group += '<td class="text-center justify-content-center"><img src="/assets/backend/images/c-remove.svg" alt=""></td>';
                                }else {
                                    r_html_group += '<td class="text-center justify-content-center"><img src="/assets/backend/images/c-check.svg" alt=""></td>';
                                }

                                r_html_group += '</tr>';

                                s_index = s_index + 1;
                            }
                        }


                        $('.data-group-shop').html(r_html_group);

                        // let r_message = data.r_message;



                    }else if(data.status == 2){
                        toastr.error('Đặt yêu cầu thuê thất bại.');
                    }else{
                        $('#openDH').modal('show');
                        $('#text__errors').html('<span class="text-danger pb-2" style="font-size: 14px">'+data.message+'</span>');
                        // alert(response.message);
                    }
                },
                error: function (response) {
                    if(response.status === 422 || response.status === 429) {
                        let errors = response.responseJSON.errors;

                        jQuery.each(errors, function(index, itemData) {

                            formSubmit.find('.notify-error').text(itemData[0]);
                            return false; // breaks
                        });
                    }else if(response.status === 0){
                        alert(response.message);
                        $('#text__errors').html('<span class="text-danger pb-2" style="font-size: 14px">'+response.message+'</span>');
                    }
                    else {
                        $('#text__errors').html('<span class="text-danger pb-2" style="font-size: 14px">'+'Kết nối với hệ thống thất bại.Xin vui lòng thử lại'+'</span>');
                    }
                },
                complete: function (data) {
                    // btnSubmit.text('Thuê ngay');
                    btnSubmit.prop('disabled', false);
                }
            })

        })


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
                    lengthMenu: [20, 50, 70, 80, 90, 100, 200,500,1000],
                    pageLength: 20,
                    language: {
                        'lengthMenu': 'Display _MENU_',
                    },
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    "order": [[1, "desc"]],
                    ajax: {
                        url: '{{url()->current()}}' + '?ajax=1&shop_client=1&group_shop=0',
                        type: 'GET',
                        data: function (d) {

                            d.id = $('#id').val();
                            d.domain = $('#domain').val();
                            d.shop_access = $('#kt_select2_3').val();
                            d.status = $('#status').val();
                            d.position = $('#position').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                        }
                    },

                    buttons: [

                        {
                            text: '<i class="fas fa-plus-circle icon-md"></i> Cập nhật đã chọn ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatable.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    toast("Vui lòng chọn shop để thực hiện thao tác", 'error');
                                    return;
                                }
                                $('.data-shop').html('');
                                let c_index = 0;
                                let r_domain;
                                let r_ip;
                                let r_id;

                                datatable.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {
                                    if ($(elem).is(':checked')) {
                                        c_index = c_index + 1;
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected = allSelected + ',';
                                        }

                                        let c_domain = $(elem).data('domain');
                                        let c_ip = $(elem).data('server');
                                        let c_id = $(elem).data('id');

                                        if (c_index > 1){
                                            r_domain += '|' + c_domain;
                                            r_ip += '|' + c_ip;
                                            r_id += '|' + c_id;
                                        }else {
                                            r_domain = c_domain;
                                            r_ip = c_ip;
                                            r_id = c_id;
                                        }

                                        var html = `   <tr>
                                                    <th scope="row">${c_index}</th>
                                                    <td>${c_domain}</td>
                                                    <td><a href="" target="_blank">${c_ip}</a></td>
                                                    <td class="text-center justify-content-center"></td>
                                                </tr>`;

                                        $('.data-shop').append(html);
                                    }
                                });

                                $('#gitpullModal').modal('toggle');
                                $('#gitpullModal .id').attr('value', allSelected);
                                $('#gitpullModal .r_domain').attr('value', r_domain);
                                $('#gitpullModal .r_ip').attr('value', r_ip);
                                $('#gitpullModal .r_id').attr('value', r_id);
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

                                return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input type="checkbox" data-id="' + row.id + '" data-server="' + row.ip + '" data-domain="' + row.domain + '" rel="' + row.id + '" id="">&nbsp<span></span></label>';

                            }
                        },

                        {data: 'id', title: 'ID'},
                        {
                            data: 'domain', title: '{{__('Tên shop')}}',
                            render: function (data, type, row) {

                                return "<a href=\"https://"+ row.domain + "\" target='_blank'    >" + row.domain + "</a>";
                            }
                        },
                        { data: 'server',title:'Server', orderable: false, searchable: false},
                        {
                            data: 'group', title: '{{__('Nhóm')}}',
                            render: function (data, type, row) {
                                return row.group;
                            }
                        },
                        {data: 'update_git_at', title: '{{__('Thời gian cập nhật gần nhất')}}'},

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



            function UpdateStatusClient(id){
                $.ajax({
                    type: "POST",
                    url: "{{route('admin.shop.update-stt')}}",
                    data: {
                        '_token':'{{csrf_token()}}',
                        'id':id,
                    },
                    beforeSend: function (xhr) {

                    },
                    success: function (data) {

                        if (data.status == 1) {
                            toast(data.message);
                        }
                        else {
                            toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                            $("#kt_datatable").DataTable().ajax.reload();
                        }
                    },
                    error: function (data) {
                        if(data.status === 429) {
                            toast('{{__('Bạn đã thao tác quá nhiều lần, không thể cập nhật')}}', 'error');
                        }
                        else {
                            toast('{{__('Lỗi hệ thống, vui lòng liên hệ QTV để xử lý')}}', 'error');
                        }
                        $("#kt_datatable").DataTable().ajax.reload();

                    },
                    complete: function (data) {

                    }
                });
            }

            $('body').on('change','.btn-update-stt',function(e){
                e.preventDefault();
                var id = $(this).data('id');
                UpdateStatusClient(id);
            })
        });


    </script>

    {{--    XỬ LÝ AJAX DATATABLE--}}
    <script>
        "use strict";
        var edit_flg = false;
        var datatablegroup;
        var datatabletheme;

        $('body').on('click', '.c-group-shop', function(e) {
            e.preventDefault();

            if ($('#kt_datatable_group').hasClass('test_addclass')){

            }else {
                $('#kt_datatable_group').addClass('test_addclass');
                // begin first table
                datatablegroup = $('#kt_datatable_group').DataTable({
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
                        url: '{{url()->current()}}' + '?ajax=1&shop_client=0&group_shop=1',
                        type: 'GET',
                        data: function (d) {

                            d.id_group_shop = $('#id_group_shop').val();
                            d.domain_group_shop = $('#domain_group_shop').val();
                        }
                    },

                    buttons: [

                        {
                            text: '<i class="fas fa-plus-circle icon-md"></i> Cập nhật đã chọn ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatablegroup.$('.checkbox-item input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    toast("Vui lòng chọn nhóm shop để thực hiện thao tác", 'error');
                                    return;
                                }

                                $('.data-group-shop').html('');
                                let cg_index = 0;
                                let r_shop;
                                let r_ip;
                                let r_group;
                                let r_id;
                                let r_gid;
                                datatablegroup.$('.ckb_item input[type="checkbox"]').each(function (index, elem)  {
                                    if ($(elem).is(':checked')) {
                                        var html = '';
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected += ',' + allSelected ;
                                        }
                                        if (cg_index > 0){
                                            r_shop += ',' + $(elem).data('shop');
                                            r_ip += ',' + $(elem).data('ip');
                                            r_group += ',' + $(elem).data('title');
                                            r_id += ',' + $(elem).data('id');
                                            r_gid += ',' + $(elem).data('gid');
                                        }else {
                                            r_shop = $(elem).data('shop');
                                            r_ip = $(elem).data('ip');
                                            r_group = $(elem).data('title');
                                            r_id = $(elem).data('id');
                                            r_gid = $(elem).data('gid');
                                        }

                                        var cr_ip = $(elem).data('ip').split('|');
                                        var cr_shop = $(elem).data('shop').split('|');
                                        var title = $(elem).data('title');

                                        html += '<tr><td colspan="8" class="text-left"><b>' + title +'</b></td></tr>';

                                        for (let i =0;i < cr_shop.length; i++){
                                            cg_index = cg_index + 1
                                            html += '<tr>';
                                            html += '<th scope="row">' + cg_index + '</th>';
                                            html += '<td>' + cr_shop[i] + '</td>';
                                            html += '<td><a href="" target="_blank">' + cr_ip[i] + '</a></td>';
                                            html += '<td class="text-center justify-content-center"></td>';
                                            html += '</tr>';
                                        }

                                        $('.data-group-shop').append(html);

                                    }
                                });


                                $('#gitpullModalGroup').modal('toggle');
                                $('#gitpullModalGroup .id').attr('value', allSelected);
                                $('#gitpullModalGroup .r_domain').attr('value', r_shop);
                                $('#gitpullModalGroup .r_ip').attr('value', r_ip);
                                $('#gitpullModalGroup .r_group').attr('value', r_group);
                                $('#gitpullModalGroup .r_id').attr('value', r_id);
                                $('#gitpullModalGroup .r_gid').attr('value', r_gid);
                            }
                        },
                    ],
                    columns: [
                        {
                            data: null,
                            title: '<label class="checkbox checkbox-lg checkbox-outline"><input type="checkbox" id="btnCheckAllGroup">&nbsp<span></span></label>',
                            orderable: false,
                            searchable: false,
                            width: "20px",
                            class: "ckb_item ckb_item_group",
                            render: function (data, type, row) {

                                if (row.count > 0){
                                    let g_title;
                                    let g_ip;
                                    let g_id;
                                    $.each(row.shop,function(key,value){
                                        if (key > 0) {
                                            g_title += '|' + value.domain;
                                            g_id += '|' + value.id;
                                        } else {
                                            g_title = value.domain;
                                            g_id = value.id;
                                        }
                                    })

                                    return '<label class="checkbox checkbox-lg checkbox-outline checkbox-item"><input data-gid="' + g_id + '" data-id="' + row.id + '" data-ip="' + row.ip + '" data-shop="' + g_title + '" data-title="' + row.title +'" type="checkbox" rel="' + row.id + '" id="">&nbsp<span></span></label>';

                                }else {
                                    return '<label style="margin-left: 8px" class="checkbox checkbox-lg checkbox-outline checkbox-item"><img src="/assets/backend/images/c-remove.svg" alt=""></label>';
                                }

                            }
                        },

                        {data: 'id', title: 'ID'},
                        {
                            data: 'title', title: '{{__('Nhóm')}}',
                            render: function (data, type, row) {
                                return row.title;
                            }
                        },
                        {
                            data: 'count', title: '{{__('Số lượng shop')}}',
                            render: function (data, type, row) {
                                return row.count;
                            }
                        },
                        { data: 'description',title:'Mô tả'},
                        { data: 'status',title:'Trạng thái', orderable: false, searchable: false},
                    ],
                    "drawCallback": function (settings) {
                    }

                });

                var filter = function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    datatablegroup.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                };

                $('#kt_search_group_shop').on('click', function (e) {
                    e.preventDefault();
                    var params = {};
                    $('.datatable-input_group_shop').each(function () {
                        var i = $(this).data('col-index');
                        if (params[i]) {
                            params[i] += '|' + $(this).val();
                        } else {
                            params[i] = $(this).val();
                        }
                    });

                    $.each(params, function (i, val) {
                        // apply search params to datatable
                        datatablegroup.column(i).search(val ? val : '', false, false);
                    });
                    datatablegroup.table().draw();
                });

                $('#kt_reset_group_shop').on('click', function (e) {
                    e.preventDefault();
                    $('#kt_reset_group_shop .datatable-input_group_shop').each(function () {
                        $(this).val('');
                        datatablegroup.column($(this).data('col-index')).search('', false, false);
                    });
                    datatablegroup.table().draw();
                });

                datatablegroup.on("click", "#btnCheckAllGroup", function () {
                    $(".ckb_item_group input[type='checkbox']").prop('checked', this.checked).change();
                })

                datatablegroup.on("change", "#kt_reset_group_shop .ckb_item input[type='checkbox']", function () {
                    if (this.checked) {
                        var currTr = $(this).closest("tr");
                        datatablegroup.rows(currTr).select();
                    } else {
                        var currTr = $(this).closest("tr");
                        datatablegroup.rows(currTr).deselect();
                    }
                });

                //function update field
                datatablegroup.on("change", "#kt_reset_group_shop .update_field", function (e) {

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
        })

        $('body').on('click', '.c-theme', function(e) {
            e.preventDefault();

            if ($('#kt_datatable_theme').hasClass('test_addclass')){

            }else {
                $('#kt_datatable_theme').addClass('test_addclass');
                // begin first table
                datatabletheme = $('#kt_datatable_theme').DataTable({
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
                        url: '{{url()->current()}}' + '?ajax=1&shop_client=0&theme_id=1',
                        type: 'GET',
                        data: function (d) {

                            d.id_theme = $('#id_theme').val();
                            d.domain_theme = $('#domain_theme').val();
                        }
                    },

                    buttons: [

                        {
                            text: '<i class="fas fa-plus-circle icon-md"></i> Cập nhật đã chọn ',
                            action : function(e) {
                                e.preventDefault();
                                var allSelected = '';
                                var total = datatabletheme.$('.ckb_item_theme input[type="checkbox"]:checked').length;
                                if(total<=0){
                                    toast("Vui lòng chọn loại theme để thực hiện thao tác", 'error');
                                    return;
                                }

                                $('.data-group-shop').html('');
                                let cg_index = 0;
                                let r_shop;
                                let r_ip;
                                let r_group;
                                let r_id;
                                let r_gid;

                                datatabletheme.$('.ckb_item_theme input[type="checkbox"]').each(function (index, elem)  {
                                    if ($(elem).is(':checked')) {
                                        var html = '';
                                        allSelected = allSelected + $(elem).attr('rel');
                                        if (index !== total - 1) {
                                            allSelected += ',' + allSelected ;
                                        }
                                        if (cg_index > 0){
                                            r_shop += ',' + $(elem).data('shop');
                                            r_ip += ',' + $(elem).data('ip');
                                            r_group += ',' + $(elem).data('title');
                                            r_id += ',' + $(elem).data('id');
                                            r_gid += ',' + $(elem).data('gid');
                                        }else {
                                            r_shop = $(elem).data('shop');
                                            r_ip = $(elem).data('ip');
                                            r_group = $(elem).data('title');
                                            r_id = $(elem).data('id');
                                            r_gid = $(elem).data('gid');
                                        }

                                        var cr_ip = $(elem).data('ip').split('|');
                                        var cr_shop = $(elem).data('shop').split('|');
                                        var title = $(elem).data('title');

                                        html += '<tr><td colspan="8" class="text-left"><b>' + title +'</b></td></tr>';

                                        for (let i =0;i < cr_shop.length; i++){
                                            cg_index = cg_index + 1
                                            html += '<tr>';
                                            html += '<th scope="row">' + cg_index + '</th>';
                                            html += '<td>' + cr_shop[i] + '</td>';
                                            html += '<td><a href="" target="_blank">' + cr_ip[i] + '</a></td>';
                                            html += '<td class="text-center justify-content-center"></td>';
                                            html += '</tr>';
                                        }

                                        $('.data-group-shop').append(html);

                                    }
                                });


                                $('#gitpullModalGroup').modal('toggle');
                                $('#gitpullModalGroup .id').attr('value', allSelected);
                                $('#gitpullModalGroup .r_domain').attr('value', r_shop);
                                $('#gitpullModalGroup .r_ip').attr('value', r_ip);
                                $('#gitpullModalGroup .r_group').attr('value', r_group);
                                $('#gitpullModalGroup .r_id').attr('value', r_id);
                                $('#gitpullModalGroup .r_gid').attr('value', r_gid);
                            }
                        },
                    ],
                    columns: [
                        {
                            data: null,
                            title: '<label class="checkbox checkbox-lg checkbox-outline"><input type="checkbox" id="btnCheckAllGroup_theme">&nbsp<span></span></label>',
                            orderable: false,
                            searchable: false,
                            width: "20px",
                            class: "ckb_item ckb_item_group",
                            render: function (data, type, row) {

                                if (row.count > 0){
                                    let g_title;
                                    let g_ip;
                                    let g_id;
                                    $.each(row.themes,function(key,value){
                                        if (key > 0) {
                                            g_title += '|' + value.shop.domain;
                                            g_id += '|' + value.shop.id;
                                        } else {
                                            g_title = value.shop.domain;
                                            g_id = value.shop.id;
                                        }
                                    })

                                    return '<label class="checkbox checkbox-lg checkbox-outline ckb_item_theme"><input data-gid="' + g_id + '" data-id="' + row.id + '" data-ip="' + row.ip + '" data-shop="' + g_title + '" data-title="' + row.title +'" type="checkbox" rel="' + row.id + '" id="">&nbsp<span></span></label>';

                                }else {
                                    return '<label style="margin-left: 8px" class="checkbox checkbox-lg checkbox-outline checkbox-item"><img src="/assets/backend/images/c-remove.svg" alt=""></label>';
                                }

                            }
                        },

                        {data: 'id', title: 'ID'},
                        {
                            data: 'title', title: '{{__('Nhóm')}}',
                            render: function (data, type, row) {
                                return row.title;
                            }
                        },
                        {
                            data: 'count', title: '{{__('Số lượng shop')}}',
                            render: function (data, type, row) {
                                return row.count;
                            }
                        },
                        { data: 'description',title:'Mô tả'},
                        { data: 'status',title:'Trạng thái', orderable: false, searchable: false},
                    ],
                    "drawCallback": function (settings) {
                    }

                });

                var filter = function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    datatablegroup.column($(this).data('col-index')).search(val ? val : '', false, false).draw();
                };

                datatabletheme.on("click", "#btnCheckAllGroup_theme", function () {
                    $(".ckb_item_group input[type='checkbox']").prop('checked', this.checked).change();
                })

                datatabletheme.on("change", ".ckb_item_theme input[type='checkbox']", function () {
                    if (this.checked) {
                        var currTr = $(this).closest("tr");
                        datatabletheme.rows(currTr).select();
                    } else {
                        var currTr = $(this).closest("tr");
                        datatabletheme.rows(currTr).deselect();
                    }
                });

                //function update field
                datatabletheme.on("change", "#kt_datatable_theme .update_field", function (e) {

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
        })

    </script>
    <link href="/assets/backend/assets/css/replication_shop.css?v={{time()}}" rel="stylesheet" type="text/css"/>
@endsection

