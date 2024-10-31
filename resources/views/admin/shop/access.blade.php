{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.shop.index')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>


        @if(Auth::user()->can('client-access-list'))
        <div class="btn-group">
            <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
                <i class="ki ki-check icon-sm"></i>
                {{__('Cập nhật')}}

            </button>
            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split btn-submit-dropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <ul class="nav nav-hover flex-column">
                    <li class="nav-item">
                        <button  class="nav-link btn-submit-custom" data-form="formMain">
                            <i class="nav-icon flaticon2-reload"></i>
                            <span class="ml-2">
                                {{__('Cập nhật & tiếp tục')}}
                            </span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        @endif
    </div>
@endsection

{{-- Content --}}
@section('content')


    {{Form::open(array('route'=>array('admin.shop.access',$id),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__($page_breadcrumbs[1]['title']??"")}} <i class="mr-2"></i>
                        </h3>
                    </div>

                </div>

                <div class="card-body">

                    <div class="mt-10 mb-10">
                        <a href="#" id="btnToggleTree" class="btn btn-info m-btn" data-open="1">Thu gọn</a>
                        <a href="#" id="btnSelectAll" class="btn btn-success m-btn">Chọn tất cả</a>
                        <a href="#" id="btnDeselectAll" class="btn btn-danger m-btn">Bỏ tất cả</a>

                    </div>


                    <div id="kt_tree_3" class="tree-demo">
                    </div>

                    <input type="hidden" id="permission_ids" name="permission_ids" value="{{implode (",",old('permission_ids', $cat_selected??[]) )}}">
                    <style>
                        a[aria-level="1"] {
                            font-weight: bold;
                            font-size: 14px;
                            color: #716aca !important;
                        }
                    </style>

                </div>
            </div>
        </div>

    </div>
    {{ Form::close() }}

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')
    <script>
        "use strict";

        function init_discount(){
            $('.discount-live').each(function(){
                var el = $(this);
                el.find('.ratio-result').text(100 - (el.find('.ratio-val').val() > 0? el.find('.ratio-val').val(): 0))
            });
        }

        jQuery(document).ready(function () {

            init_discount();
            $(".ratio-val").inputmask({
                groupSeparator: ",",
                radixPoint: ".",
                alias: "numeric",
                placeholder: "0",
                autoGroup: true,
                min:0,
                max:100
            });
            $('.ratio-val').keyup(function(){
                var el = $(this);
                var resetClass = el.hasClass('custom-val')? '.general-val': '.custom-val';
                if (el.hasClass('custom-val')) {
                    el.parents('.discount-group').find(resetClass).val('').parents('.discount-live').find('.ratio-result').text(100);
                }else{
                    el.parents('.discount-group').find(resetClass).val(el.val()).parents('.discount-live').find('.ratio-result').text(100 - (el.val() > 0? el.val(): 0));
                }
                el.parents('.discount-live').find('.ratio-result').text(100 - (el.val() > 0? el.val(): 0))
            });


            //btn submit form
            $('.btn-submit-custom').click(function (e) {
                e.preventDefault();
                var btn = this;
                if (confirm('Vui Lòng Kiểm Tra Phần Đã Cấu Hình 1 lần nữa . Để tránh xảy ra lỗi set nhầm . Cảm ơn')) {
                    $(".btn-submit-custom").each(function (index, value) {
                        KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                    });
                    $('.btn-submit-dropdown').prop('disabled', true);
                    //gắn thêm hành động close khi submit
                    $('#submit-close').val($(btn).data('submit-close'));
                    var formSubmit = $('#' + $(btn).data('form'));
                    formSubmit.submit();
                }
            });
            // var jsondata = [
            //
            //
            //     {"id":"15","parent":"#","text": "Child 1"},
            //     { "id": "ajson3", "parent": "ajson3", "text": "Child 1"},
            //     { "id": "ajson4", "parent": "ajson2", "text": "Child 2"},
            // ];

            var jsondata={!! $categoryJson !!};

            @if(Auth::user()->can('client-access-list'))
            $('#kt_tree_3').jstree({
                "plugins": ["wholerow", "checkbox", "types","search"],
                "core": {
                    "dblclick_toggle" : false,
                    "themes": {
                        "responsive": false,
                        "icons":false,
                        "dots": true,
                    },
                    "data": jsondata
                },
                // checkbox: {
                //     tie_selection : false,
                //     whole_node : false
                // },
                "types": {
                    "default": {
                        "icon": "fa fa-folder text-warning"
                    },
                    "file": {
                        "icon": "fa fa-file  text-warning"
                    }
                },

            }).bind("loaded.jstree", function (e, data) {
                // var perSelected=$('#permission_ids').val();

                // var arrPer = perSelected.split(",");
                // $.each(arrPer, function( index, value ) {
                //     $('#kt_tree_3').jstree("select_node", value, true);
                // });
                $('body').on('click', '.jstree-link', function(e){
                    e.preventDefault();
                    $(this).parent().find('.jstree-checkbox').trigger('click');
                    window.open($(this).attr('href'));
                });

            }).on('changed.jstree', function (e, data) {
                var i, j, r = [];
                for(i = 0, j = data.selected.length; i < j; i++) {
                    r.push(data.instance.get_node(data.selected[i]).id);
                }
                $('#permission_ids').val(r.join(','));
            });
            @else
            $('#kt_tree_3').jstree({
                "plugins": ["wholerow", "types","search"],
                "core": {
                    "dblclick_toggle" : false,
                    "themes": {
                        "responsive": false,
                        "icons":false,
                        "dots": true,
                    },
                    "data": jsondata
                },
                // checkbox: {
                //     tie_selection : false,
                //     whole_node : false
                // },
                "types": {
                    "default": {
                        "icon": "fa fa-folder text-warning"
                    },
                    "file": {
                        "icon": "fa fa-file  text-warning"
                    }
                },

            }).bind("loaded.jstree", function (e, data) {
                // var perSelected=$('#permission_ids').val();

                // var arrPer = perSelected.split(",");
                // $.each(arrPer, function( index, value ) {
                //     $('#kt_tree_3').jstree("select_node", value, true);
                // });
                $('body').on('click', '.jstree-link', function(e){
                    window.location.href = $(this).attr('href');
                });

            }).on('changed.jstree', function (e, data) {
                var i, j, r = [];
                for(i = 0, j = data.selected.length; i < j; i++) {
                    r.push(data.instance.get_node(data.selected[i]).id);
                }
                $('#permission_ids').val(r.join(','));
            });
            @endif
            $( "#btnDeselectAll").click(function(e) {
                e.preventDefault();
                $("#kt_tree_3").jstree().deselect_all(true);
                $("#permission_ids").val('');
            });

            $( "#btnDeselectAll").click(function(e) {
                e.preventDefault();
                $("#kt_tree_3").jstree().uncheck_all(true);
            });

            $( "#btnSelectAll").click(function(e) {
                e.preventDefault();
                $("#kt_tree_3").jstree().check_all(true);
            });
            $( "#btnToggleTree").click(function(e) {
                var isOpen=$(this).data('open');

                if(isOpen){

                    $("#kt_tree_3").jstree('close_all');
                    $(this).data('open',0);
                    $(this).text('{{__('Mở rộng')}}');
                }
                else{
                    $("#kt_tree_3").jstree('open_all');
                    $(this).data('open',1);
                    $(this).text('{{__('Thu gọn')}}');
                }
            });

        });



    </script>

    <script>
        // jQuery(document).ready(function () {
        //     var vl=$('#kt_card_0')
        //
        // })

        $('.kt_card_custom').each(function (idx, elm) {
            var card= new KTCard($(elm).attr('id'));
        });

    </script>


@endsection


