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
            <div class="card-toolbar">

            </div>
        </div>

        <div class="card-body">
            <!--begin: Search Form-->
            <form class="mb-15">
                <div class="row mb-6">
                    <div class="col-lg-3 mb-lg-0 mb-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" placeholder="{{__('ID')}}"
                                   data-col-index="1">
                        </div>
                    </div>
                    <div class="col-lg-3 mb-lg-0 mb-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" placeholder="{{__('Tài khoản hoặc email')}}"
                                   data-col-index="2">
                        </div>
                    </div>

                    <div class="col-lg-3 mb-lg-0 mb-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select class="form-control datatable-input" data-col-index="3">
                                <option value="">-- {{__('Chọn method')}}--</option>
                                <option value="GET">{{__('GET')}}</option>
                                <option value="POST">{{__('POST')}}</option>
                                <option value="UPDATE">{{__('UPDATE')}}</option>
                                <option value="DELETE">{{__('DELETE')}}</option>
                            </select>

                        </div>
                    </div>

                    <div class="col-lg-3 mb-lg-0 mb-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="url" placeholder="{{__('URL')}}"
                                   >
                        </div>
                    </div>
                </div>
                <div class="row mb-8">

                    <div class="col-lg-3 mb-lg-0 mb-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" placeholder="{{__('IP')}}"
                                   data-col-index="5">
                        </div>
                    </div>

                    <div class="col-lg-3 mb-lg-0 mb-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" placeholder="{{__('User agent')}}"
                                   data-col-index="6">
                        </div>
                    </div>

                    <div class="col-lg-3 mb-lg-0 mb-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i
                                        class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <input type="text" class="form-control datatable-input" id="description" placeholder="{{__('Mô tả')}}" value="{{ $_GET['description']??null }}">
                        </div>
                    </div>

                    <div class="col-lg-3 mb-lg-0 mb-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Từ</span>
                            </div>
                            <input type="text" name="started_at" id="started_at" autocomplete="off"
                                   class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian bắt đầu')}}" data-toggle="datetimepicker">

                        </div>
                    </div>

                    <div class="col-lg-3 mb-lg-0 mb-6 " style="margin-top: 16px">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Đến</span>
                            </div>
                            <input type="text" name="ended_at" id="ended_at" autocomplete="off"
                                   class="form-control datatable-input  datetimepicker-input datetimepicker-default"
                                   placeholder="{{__('Thời gian kết thúc')}}" data-toggle="datetimepicker">
                        </div>
                    </div>


                </div>
                <div class="row mt-8">
                    <div class="col-lg-12">
                        <button class="btn btn-primary btn-primary--icon" id="kt_search">
                            <span>
                                <i class="la la-search"></i>
                                <span>Search</span>
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





@endsection

{{-- Styles Section --}}
@section('styles')
    <style>
        table {
            width: 100%;
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        td:nth-child(6) {
            white-space: nowrap;
            width: 100%; /* Extend the cell as much as possible */
            max-width: 0; /* Avoid resizing beyond table width */
            /*background-color: red !important;*/
        }
    </style>
@endsection
{{-- Scripts Section --}}
@section('scripts')

    {{--    <script src="{{ asset('assets/backend/themes/js/pages/widgets.js') }}" type="text/javascript"></script>--}}

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

                            d.description = $('#description').val();
                            d.url = $('#url').val();
                            d.started_at = $('#started_at').val();
                            d.ended_at = $('#ended_at').val();
                        }
                    },

                    buttons: [
                        {
                            "extend": 'excelHtml5',
                            "text": ' <i class="far fa-file-excel icon-md"></i> {{__('Xuất excel')}} ',
                            "action": newexportaction,


                        },

                    ],

                    columnDefs: [
                        {'max-width': '20%', targets: 4}
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
                            data: 'user_id', title: '{{__('Tài khoản')}} / {{__('Email')}}', name: 'user.username',
                            render: function (data, type, row) {

                                return "TK: "+(row.user !== null ? row.user.username : "") + "<br />Email: " +(row.user !== null ? row.user.email : "");

                            }

                        },

                        {data: 'method', title: '{{__('Method')}}'},
                        {data: 'description', title: '{{__('Mô tả')}}'},
                        {
                            data: 'url', title: '{{__('Url')}}',
                            render: function (data, type, row) {
                                // return '<a href=\"' + row.url + '\" target=\"blank\"  class=\"text-primary\" ' + 'title=\"' + row.url + '\"' + '\">' + row.url + '</a>';
                               return "<a href='"+row.url+"' title='"+row.url+"' target='blank' class='text-primary' >"+row.url+"</a>";
                            }
                        },
                        {data: 'ip', title: '{{__('IP')}}'},
                        {data: 'user_agent', title: 'User agent', width: "150px",},
                        {data: 'created_at', title: '{{__('Thời gian')}}'},
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


            };

            return {

                //main function to initiate the module
                init: function () {
                    initTable1();
                },

            };

        }();

        jQuery(document).ready(function () {
            KTDatatablesDataSourceAjaxServer.init();
        });

        $('.datetimepicker-default').datetimepicker({
            format: 'DD/MM/YYYY HH:mm:00',
            useCurrent: true,
            autoclose: true
        });



        // function newexportaction(e, dt, button, config) {
        //
        //
        //     $(button).text("Đang tải...");
        //     $(button).prop('disabled', true);
        //     alert('');
        // };


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


    </script>



@endsection
