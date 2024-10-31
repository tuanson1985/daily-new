<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="loadingServer" style="display: none;position: fixed;top: 0;left: 0;width: 100%;height: 100%;z-index: 999999;background: rgba(0,0,0,0.5);text-align: center">
    <button class="buttonload">
        <i class="fa fa-refresh fa-spin" style="color: #fff"></i>&nbsp;&nbsp;&nbsp;Đang đồng bộ server, Vui lòng chờ...
    </button>

    <style>
        /* Style buttons */
        .buttonload {
            margin-top: 50px;
            background-color: #04AA6D; /* Green background */
            border: none; /* Remove borders */
            color: white; /* White text */
            padding: 12px 16px; /* Some padding */
            font-size: 16px /* Set a font size */
        }
    </style>
</div>
{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
        <div class="btn-group">
            <a href="javascript:updateServer(0)" type="button"  class="btn btn-success font-weight-bolder">
                <i class="la la-refresh"></i>
                {{__('Cập nhật Shop')}}
            </a>
        </div>&nbsp;&nbsp;
        <div class="btn-group">
            <a href="{{route('admin.'.$module.'.create')}}" type="button"  class="btn btn-success font-weight-bolder">
                <i class="fas fa-plus-circle icon-md"></i>
                {{__('Thêm mới')}}
            </a>
        </div>
    </div>
@endsection

@section('content')

<div class="card card-custom" id="kt_page_sticky_card">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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
				<!-- <div class="form-group col-12 col-sm-6 col-lg-3">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i
									class="la la-calendar-check-o glyphicon-th"></i></span>
						</div>
						<input type="text" class="form-control datatable-input" id="id" placeholder="{{__('ID')}}">
					</div>
				</div> -->
				{{--title--}}
				<div class="form-group col-12 col-sm-6 col-lg-3">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i
									class="la la-calendar-check-o glyphicon-th"></i></span>
						</div>
						<input type="text" class="form-control datatable-input" id="title"
							   placeholder="{{__('Tên server')}}">
					</div>
				</div>
                {{--shop_name--}}
                <div class="form-group col-12 col-sm-6 col-lg-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i
                                    class="la la-calendar-check-o glyphicon-th"></i></span>
                        </div>
                        <input type="text" class="form-control datatable-input" id="shop_name"
                               placeholder="{{__('Tên shop')}}">
                    </div>
                </div>

                    {{--status--}}
                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i
                                    class="la la-calendar-check-o glyphicon-th"></i></span>
                            </div>
                            <select name="server_category_id" class="form-control datatable-input" id="server_category_id" style="">
                                <option value="">-- {{__('Không chọn danh mục')}} --</option>
                                @if( !empty(old('server_category_id')) )
                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCatalog,old('server_category_id')) !!}
                                @else
                                    <?php $itSelect = [] ?>
                                    {!!\App\Library\Helpers::buildMenuDropdownList($dataCatalog,$itSelect) !!}
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="formLoadArrNcc form-group col-12 col-sm-6 col-lg-3"></div>

{{--                    --}}{{--status--}}
{{--                    <div class="form-group col-12 col-sm-6 col-lg-3">--}}
{{--                        <div class="input-group">--}}
{{--                            <div class="input-group-prepend">--}}
{{--                            <span class="input-group-text"><i--}}
{{--                                    class="la la-calendar-check-o glyphicon-th"></i></span>--}}
{{--                            </div>--}}
{{--                            <select name="type_category_id" id="type_category_id" class="form-control datatable-input">--}}
{{--                                <option value="">--Tất cả các mảng server--</option>--}}
{{--                                @if(isset($dataType) && count($dataType)>0)--}}
{{--                                    @foreach($dataType as $item)--}}
{{--                                        <option value="{{$item->id}}">{{$item->title}}</option>--}}
{{--                                    @endforeach--}}
{{--                                @endif--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}


                {{--status--}}
                <div class="form-group col-12 col-sm-6 col-lg-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i
                                    class="la la-calendar-check-o glyphicon-th"></i></span>
                        </div>
                        <select name="parrent_id" id="parrent_id" class="form-control datatable-input">
                            <option value="">--Tất cả nhà phát hành--</option>
                            @if(isset($dataCategory) && count($dataCategory)>0)
                                @foreach($dataCategory as $item)
                                    <option value="{{$item->id}}">{{$item->title}}</option>
                                @endforeach
                            @endif
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
						{{Form::select('status',[''=>'-- Tất cả trạng thái --']+config('module.server.status'),old('status', isset($data) ? $data->status : 1),array('id'=>'status','class'=>'form-control datatable-input',))}}
					</div>
				</div>

                    <div class="form-group col-12 col-sm-6 col-lg-3">
                        <div class="input-group">
                            <input type="text" placeholder="Nhập Ip, List IP" class="tags-input" value="" data-role="tagsinput" id="ipaddress" name="ipaddress"/>

                            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
                            <link href="{{asset('assets/backend/assets/css/bootstrap-tagsinput.css')}}" rel="stylesheet" type="text/css"/>
                            <script type="text/javascript" src="{{asset('assets/backend/assets/js/bootstrap-tagsinput.min.js')}}"/>
                            <script>
                                $('input').tagsinput({
                                    trimValue: true,
                                    confirmKeys: [13, 32]
                                });
                            </script>
                            <style>
                                .bootstrap-tagsinput input{padding: 3px 6px!important;}
                                .bootstrap-tagsinput .label{display: inline;
                                    padding: .2em .6em .3em;
                                    font-size: 75%;
                                    font-weight: 700;
                                    line-height: 1;
                                    color: #fff;
                                    text-align: center;
                                    white-space: nowrap;
                                    vertical-align: baseline;
                                    border-radius: .25em;}
                                .tags-input {
                                    max-width: 100%;
                                    line-height: 22px;
                                    overflow-y: scroll;
                                    overflow-x: scroll;
                                    height: 65px;
                                    cursor: text;
                                }
                            </style>
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
        <div class="totalMoney"></div>
		<table class="table table-bordered table-hover table-checkable " id="kt_datatable">
		</table>
	</div>
</div>
 <!-- set value Modal -->
 <div class="modal fade" id="setValueModal"  role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
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

<!-- delete item Modal -->
<div class="modal fade" id="infoModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel"> {{__('Ghi chú')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="content-modal"></div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" class="id" value=""/>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')

    <script>
        "use strict";

        function updateServer(id){
            $(".loadingServer").show();
            $.ajax({
                type: "POST",
                url: "{{route('admin.server.update-server')}}",
                data: {
                    '_token':'{{csrf_token()}}',
                    'id':id
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    $(".loadingServer").hide();
                    if (data.status == 1) {
                        $(".loadingServer").hide();
                        toast('{{__('Cập nhật thành công, Tải lại trang để xem cập nhật')}}');
                        //KTDatatablesDataSourceAjaxServer.init();
                    } else {

                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    }
                },
                error: function (data) {
                    $(".loadingServer").hide();
                    toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                },
                complete: function (data) {

                }
            });
        }


        function formatNumber(n) {
            return n.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }

        function getTotalPrice(){
            const server_name = $("#title").val();
            const shop_name = $("#shop_name").val();
            const ipaddress = $("#ipaddress").val();
            const status = $("#status").val();
            const type_category_id = $("#type_category_id").val();
            const server_category_id = $("#server_category_id").val();
            const parrent_id = $("#parrent_id").val();
            $.ajax({
                type: "POST",
                url: '{{route('admin.server_gettotal_price')}}',
                data: {
                    '_token':'{{csrf_token()}}',
                    'server_name':server_name,
                    'shop_name':shop_name,
                    'ipaddress':ipaddress,
                    'server_category_id':server_category_id,
                    'type_category_id':type_category_id,
                    'parrent_id':parrent_id,
                    'status':status,
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {

                    if (data.success) {
                        $(".totalPrice").html("(Tổng: "+formatNumber(data.total.toString())+")")
                    } else {
                        $(".totalPrice").html("(Tổng: Errors)")
                    }
                },
                error: function (data) {
                    $(".totalPrice").html("(Tổng: Errors)")
                },
                complete: function (data) {

                }
            });
        };


        //func loadAttribute for Theme
        function loadSubCateServer(){
            let server_category_id = $("#server_category_id").val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/server/loadSubCateServerIndex',
                data: {
                    "server_category_id":server_category_id
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if (data.status == "SUCCESS") {
                        $(".formLoadArrNcc").html(data.htmlAttribute);
                    } else {
                        toast(data.msg, 'error');
                        $(".formLoadArrNcc").html("");
                    }
                },
                error: function (data) {
                    toast('{{__('Không thể load mảng')}}', 'error');
                },
                complete: function (data) {
                    //KTUtil.btnRelease(btn);
                }
            });
        };

        var datatable;
        var KTDatatablesDataSourceAjaxServer = function () {
            var initTable1 = function () {


                // begin first table
                datatable = $('#kt_datatable').DataTable({
                    responsive: true,
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
                            d.ipaddress = $('#ipaddress').val();
                            d.status = $('#status').val();
                            d.parrent_id = $('#parrent_id').val();
                            d.shop_name = $('#shop_name').val();
                            d.server_category_id = $('#server_category_id').val();
                            d.type_category_id = $('#type_category_id').val();
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
                            data: 'cateName', title: '{{__('Nhà cung cấp')}}',
                            render: function (data, type, row) {
                                const myStringObj = row.shop_name != null ? row.shop_name.replace(/&quot;/ig,'"') : "";
                                let shop_active =0;
                                let shop_unactive =0;
                                if(myStringObj.length > 0){
                                    let myArray_shopName =[];
                                    if(JSON.parse(myStringObj).shop_name != null != undefined && JSON.parse(myStringObj).shop_name != null) {
                                        myArray_shopName = JSON.parse(myStringObj).shop_name;
                                    }
                                    let  myArray_shopStatus = [];
                                    if(JSON.parse(myStringObj).shop_status != undefined && JSON.parse(myStringObj).shop_status != null) {
                                        myArray_shopStatus = JSON.parse(myStringObj).shop_status;
                                    }
                                    if(myArray_shopName.length > 0 && myArray_shopStatus.length > 0) {
                                        for (let i = 0; i < myArray_shopName.length; i++) {
                                            if(myArray_shopName[i] != null && myArray_shopStatus[i] != null && myArray_shopStatus[i] == 1){
                                                shop_active += 1;
                                            }
                                            else if(myArray_shopName[i] != null && myArray_shopStatus[i] != null && myArray_shopStatus[i] == 0){
                                                shop_unactive += 1;
                                            }
                                        }
                                    }
                                }

                                if(row.lst_shop_of_shop.length > 0){
                                    const lstShop = JSON.parse(row.lst_shop_of_shop);
                                    if(lstShop.length > 0){
                                        let botnet = "";
                                        for (let j = 0; j < lstShop.length; j++) {
                                            //console.log(lstShop[j]["domain"])
                                            if (lstShop[j]["domain"] != null) {
                                               if(lstShop[j]["status"] != null && lstShop[j]["status"] == 1)
                                               {
                                                   shop_active += 1;
                                               }
                                               else{
                                                   shop_unactive += 1;
                                                   //inactive
                                               }
                                            }
                                        }
                                    }
                                }


                                if(row.cateName.length > 0){
                                    return "<a href='server-category/"+row.parrent_id+"/edit'>"+row.cateName+"</a><br/><span class='shop_count' style='font-style: italic;'>- "+shop_active+" shop hoạt động<br/>- "+shop_unactive+" shop ngưng hoạt động</span>";
                                }
                                else{
                                    return "<a href='javascript://'>Chưa chọn nhà cung cấp</a><br/><span class='shop_count' style='font-style: italic;'>- "+shop_active+" shop hoạt động<br/>- "+shop_unactive+" shop ngưng hoạt động</span>";
                                }

                            }
                        },
                        {
                            data: 'catalogName', title: '{{__('Danh mục Server')}}',
                            render: function (data, type, row) {
                                if(row.catalogName.length > 0){
                                    return "<a href='server-catalog/"+row.server_category_id+"/edit'>"+row.catalogName+"</a>";
                                }
                                else{
                                    return "<a href='javascript://'>Chưa chọn danh mục</a>";
                                }
                            }
                        },
                        {
                            data: 'typeName', title: '{{__('Mảng Server')}}',
                            render: function (data, type, row) {
                                if(row.typeName.length > 0){
                                    return "<a href='server-type/"+row.type_category_id+"/edit'>"+row.typeName+"</a>";
                                }
                                else{
                                    return "<a href='javascript://'>Chưa chọn mảng</a>";
                                }
                            }
                        },
                        //{
                        //    data: 'title', title: '{{__('Tên Server')}}',
                        //    render: function (data, type, row) {
                       //          return row.title;
                       //     }
                       // },
                        {data: 'ipaddress', title: '{{__('Địa chỉ IP')}}'},
                        {
                            data: 'price', title: '{{__('Giá')}}<label class="totalPrice" style="display: block;font-weight: bold;"></label>',
                            render: function (data, type, row) {
                                return "<input class='update_field update_field_"+row.id+"' data-field='bonus_from' data-required='0' data-id='"+row.id+"' type='text' value='" + (row.price!=null?  formatNumber(row.price) :'0') + "' style='width:70px'>";
                            }
                        },
                        {data: 'register_date', title: 'Ngày đăng ký'},
                        {
                            data: 'check_express', title: '{{__('Ngày hết hạn')}}',
                            render: function (data, type, row) {
                                let html = "";
                                let btnpur = "";
                                if(row.check_express > 7){
                                    html = "<span style='color:blue'>"+row.ended_at+"</span>";
                                }
                                else{
                                    if(row.purchase_link != null && row.purchase_link.length > 1){
                                        btnpur = "<br/><a href='"+row.purchase_link+"' title='Gia hạn ngay' target='_blank'><span class='label label-pill label-inline label-center mr-2  label-success'>Gia hạn ngay</span></a>";
                                    }else{
                                        btnpur = "<br/><a href='javascript://' title='Chưa cập nhật link gia hạn'><span class='label label-pill label-inline label-center mr-2  label-success'>Gia hạn ngay</span></a>";
                                    }
                                    html = "<span style='color:red'>"+row.ended_at+"</span>"+btnpur;
                                }
                                return html;
                            }
                        },
                        {
                            data: 'shop_name', title: '{{__('Shop')}}',
                            render: function (data, type, row) {
                                const statusSelect = $("#status").val();
                                if((row.shop_name == null || row.shop_name.length < 1) && row.lst_shop_of_shop.length < 1)
                                {
                                    return "Chưa có shop nào!";
                                }
                                else{
                                    let html = "";
                                    let countR = 0;
                                    html += '<div class="lstWeb" style="max-height: 200px;overflow: auto;">';
                                    const current_dt = new Date();
                                    const current_dt_cr = new Date();
                                    current_dt.setDate(current_dt.getDate() + 7);
                                    if(row.shop_name != null && row.shop_name.length > 0) {

                                        const myStringObj = row.shop_name.replace(/&quot;/ig, '"');

                                        if (myStringObj.length > 0) {
                                            let myArray_shopName = [];
                                            if (JSON.parse(myStringObj).shop_name != null != undefined && JSON.parse(myStringObj).shop_name != null) {
                                                myArray_shopName = JSON.parse(myStringObj).shop_name;
                                            }

                                            let myArray_shopStatus = [];
                                            if (JSON.parse(myStringObj).shop_status != undefined && JSON.parse(myStringObj).shop_status != null) {
                                                myArray_shopStatus = JSON.parse(myStringObj).shop_status;
                                            }
                                            let myArray_shopexpired = [];
                                            if (JSON.parse(myStringObj).ended_date != undefined && JSON.parse(myStringObj).ended_date != null) {
                                                myArray_shopexpired = JSON.parse(myStringObj).ended_date;
                                            }
                                            let myArray_shoplink = [];
                                            if (JSON.parse(myStringObj).shop_link != undefined && JSON.parse(myStringObj).shop_link != null) {
                                                myArray_shoplink = JSON.parse(myStringObj).shop_link;
                                            }

                                            if (myArray_shopName.length > 0 && myArray_shopStatus.length > 0) {

                                                for (let i = 0; i < myArray_shopName.length; i++) {
                                                    if (myArray_shopName[i] != null) {

                                                        countR += 1;
                                                        const ex_date = new Date(myArray_shopexpired[i]);
                                                        let btnep = "";
                                                        if (myArray_shopName[i].includes("http") || myArray_shopName[i].includes("https") || myArray_shopName[i].includes("www")) {
                                                            if (myArray_shopStatus[i] != null && myArray_shopStatus[i] == 1) {
                                                                if (ex_date != null && ex_date != undefined && ex_date >= current_dt) {
                                                                    if (myArray_shoplink[i] != null && myArray_shoplink[i].length > 1) {
                                                                        btnep = '&nbsp;&nbsp;<a href="' + myArray_shoplink[i] + '" target="_blank"  title="Sắp hết hạn, gia hạn ngay"><i style="color:#ff0000" class="fa fa-handshake-slash"></i></a>';
                                                                    }
                                                                    html += '<a title="' + (ex_date != null && ex_date != undefined && ex_date >= current_dt_cr ? "Tên miền đã hết hạn. Gia hạn ngay" : "Tên miền sắp hết hạn. Gia hạn ngay") + '" style="color:red" target="_blank" href="' + myArray_shopName[i] + '">' + myArray_shopName[i] + '</a>&nbsp;<i title="Đang hoạt động" class="fa fa-check" style="color:#00a651"></i>' + btnep + '<br/>';
                                                                } else {
                                                                    html += '<a target="_blank" href="' + myArray_shopName[i] + '">' + myArray_shopName[i] + '</a>&nbsp;<i  title="Đang hoạt động"  class="fa fa-check" style="color:#00a651"></i><br/>';
                                                                }
                                                            } else {
                                                                if (statusSelect != 1) {
                                                                    html += '<a title="Tên miền ngưng hoạt động" style="color: red;text-decoration: line-through;" target="_blank" href="' + myArray_shopName[i] + '">' + myArray_shopName[i] + '</a>&nbsp;<i  title="Ngưng hoạt động"  class="fa fa-times" style="color:#f00"></i><br/>';
                                                                }
                                                            }
                                                        } else {
                                                            console.log(current_dt_cr);
                                                            console.log(current_dt_cr);
                                                            console.log(myArray_shopName);
                                                            if (myArray_shopStatus[i] != null && myArray_shopStatus[i] == 1) {

                                                                if (ex_date != null && ex_date != undefined && ex_date >= current_dt) {
                                                                    if (myArray_shoplink[i] != null && myArray_shoplink[i].length > 1) {
                                                                        btnep = '&nbsp;&nbsp;<a href="' + myArray_shoplink[i] + '"  target="_blank" title="Sắp hết hạn, gia hạn ngay"><i style="color:#ff0000" class="fa fa-handshake-slash"></i></a>';
                                                                    }
                                                                    html += '<a title="' + (ex_date != null && ex_date != undefined && ex_date < current_dt_cr ? "Tên miền đã hết hạn. Gia hạn ngay" : "Tên miền sắp hết hạn. Gia hạn ngay") + '" style="color:red" target="_blank" href="' + myArray_shopName[i] + '">' + myArray_shopName[i] + '</a>&nbsp;<i  title="Đang hoạt động"  class="fa fa-check" style="color:#00a651"></i>' + btnep + '<br/>';
                                                                } else {
                                                                    html += '<a target="_blank" href="http://' + myArray_shopName[i] + '">' + myArray_shopName[i] + '</a>&nbsp;<i  title="Đang hoạt động"  class="fa fa-check" style="color:#00a651"></i><br/>';
                                                                }
                                                            } else {
                                                                if (statusSelect != 1) {
                                                                    html += '<a title="Tên miền ngưng hoạt động" style="color: red;text-decoration: line-through;"  target="_blank" href="http://' + myArray_shopName[i] + '">' + myArray_shopName[i] + '</a>&nbsp;<i  title="Ngưng hoạt động"  class="fa fa-times" style="color:#f00"></i><br/>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if(row.lst_shop_of_shop.length > 0){
                                        const lstShop = JSON.parse(row.lst_shop_of_shop);
                                        if(lstShop.length > 0){
                                            let botnet = "";
                                            for (let j = 0; j < lstShop.length; j++) {

                                                if (lstShop[j]["domain"] != null) {
                                                    countR += 1;
                                                    const ex_date = new Date(lstShop[j]["ended_at"]);

                                                    if (lstShop[j]["domain"].includes("http") || lstShop[j]["domain"].includes("https") || lstShop[j]["domain"].includes("www")) {
                                                        if (lstShop[j]["status"] != null && lstShop[j]["domain"] == 1) {
                                                            if (ex_date != null && ex_date != undefined && ex_date >= current_dt) {
                                                                if (lstShop[j]["url"] != null && lstShop[j]["url"].length > 1) {
                                                                    botnet = '&nbsp;&nbsp;<a href="' + lstShop[j]["url"] + '" target="_blank"  title="Sắp hết hạn, gia hạn ngay"><i style="color:#ff0000" class="fa fa-handshake-slash"></i></a>';
                                                                }
                                                                html += '<a title="' + (ex_date != null && ex_date != undefined && ex_date >= current_dt_cr ? "Tên miền đã hết hạn. Gia hạn ngay" : "Tên miền sắp hết hạn. Gia hạn ngay") + '" style="color:red" target="_blank" href="' + lstShop[j]["domain"] + '">' + lstShop[j]["domain"] + '</a>&nbsp;<i title="Đang hoạt động" class="fa fa-check" style="color:#00a651"></i>' + botnet + '<br/>';
                                                            } else {
                                                                html += '<a target="_blank" href="' + lstShop[j]["domain"] + '">' + lstShop[j]["domain"] + '</a>&nbsp;<i  title="Đang hoạt động"  class="fa fa-check" style="color:#00a651"></i><br/>';
                                                            }
                                                        } else {
                                                            if (statusSelect != 1) {
                                                                html += '<a title="Tên miền ngưng hoạt động" style="color: red;text-decoration: line-through;" target="_blank" href="' + lstShop[j]["domain"] + '">' + lstShop[j]["domain"] + '</a>&nbsp;<i  title="Ngưng hoạt động"  class="fa fa-times" style="color:#f00"></i><br/>';
                                                            }
                                                        }
                                                    } else {
                                                        if (lstShop[j]["status"] != null && lstShop[j]["status"] == 1) {
                                                            if (ex_date != null && ex_date != undefined && ex_date >= current_dt) {
                                                                if (lstShop[j]["url"] != null && lstShop[j]["url"].length > 1) {
                                                                    botnet = '&nbsp;&nbsp;<a href="' + lstShop[j]["url"] + '"  target="_blank" title="Sắp hết hạn, gia hạn ngay"><i style="color:#ff0000" class="fa fa-handshake-slash"></i></a>';
                                                                }
                                                                html += '<a title="' + (ex_date != null && ex_date != undefined && ex_date >= current_dt_cr ? "Tên miền đã hết hạn. Gia hạn ngay" : "Tên miền sắp hết hạn. Gia hạn ngay") + '" style="color:red" target="_blank" href="' + lstShop[j]["domain"] + '">' + lstShop[j]["domain"] + '</a>&nbsp;<i  title="Đang hoạt động"  class="fa fa-check" style="color:#00a651"></i>' + botnet + '<br/>';
                                                            } else {
                                                                html += '<a target="_blank" href="http://' + lstShop[j]["domain"] + '">' + lstShop[j]["domain"] + '</a>&nbsp;<i  title="Đang hoạt động"  class="fa fa-check" style="color:#00a651"></i><br/>';
                                                            }
                                                        } else {
                                                           // console.log(statusSelect)
                                                            if (statusSelect != 1) {
                                                                html += '<a title="Tên miền ngưng hoạt động" style="color: red;text-decoration: line-through;"  target="_blank" href="http://' + lstShop[j]["domain"] + '">' + lstShop[j]["domain"] + '</a>&nbsp;<i  title="Ngưng hoạt động"  class="fa fa-times" style="color:#f00"></i><br/>';
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if (countR == 0) {
                                        html += "Chưa có shop nào!";
                                    }
                                    html += "</div>";
                                    return html;
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
                    const params = {};
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
                    getTotalPrice();
                });

                $('#server_category_id').on('change',function (){
                    loadSubCateServer();
                })

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

                datatable.on("keyup", ".update_field", function (e) {
                    e.preventDefault();
                    var valu = $(this).val().replace(",","");
                    $(this).val(formatNumber(valu));
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
                datatable.on("focus",".update_field",function(e){
                    e.preventDefault();
                    var id = $(this).attr('data-id');
                    $(".btn_update").hide();
                     $(".btn_update_"+id).show();
                });


                // datatable.on("focusout",".update_field",function(e){
                //     e.preventDefault();
                //     var id = $(this).attr('data-id');
                //     $(".btn_update").hide();
                //      $(".btn_update_"+id).hide();
                // })
                //function update field
                datatable.on("click", ".btn_update", function (e) {
                    e.preventDefault();
                    var id = $(this).attr("data-id");
                    //var id=$(this).data('id');
                    var value=$(".update_field_"+id).val().replace(",","");
                    $.ajax({
                        type: "POST",
                        url: '{{route('admin.server_updatefield')}}',
                        data: {
                            '_token':'{{csrf_token()}}',
                            'id':id,
                            'value':value
                        },
                        beforeSend: function (xhr) {

                        },
                        success: function (data) {

                            if (data.success) {
                                toast(data.message);
                                getTotalPrice();
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
                init: function () {
                    initTable1();
                    getTotalPrice();
                    loadSubCateServer();
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
                    $(button).text("Xuất excel");
                    $(button).prop('disabled', false);
                    return false;
                });
            });
            dt.ajax.reload();
        };
        jQuery(document).ready(function () {
            KTDatatablesDataSourceAjaxServer.init();

            $('.datetimepicker-default').datetimepicker({
                format: 'DD/MM/YYYY HH:mm:00',
                useCurrent: true,
                autoclose: true

            });

            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).attr('rel')
                $('#deleteModal .id').attr('value', id);
            });

            $('#infoModal').on('show.bs.modal', function(e) {
                const id = $(e.relatedTarget).attr('data-id');
                const content = $(e.relatedTarget).attr('data-content');
                $("#infoModal .id").val(id);
                $("#infoModal .content-modal").html(content);
            });

            $('body').on('click', '.setvalue_toggle', function(e) {

                e.preventDefault();
                $('#setValueModal .modal-content').empty();
                $('#setValueModal .modal-content').load($(this).attr("href"),function(){
                    $('#setValueModal').modal({show:true});
                });


            })
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
