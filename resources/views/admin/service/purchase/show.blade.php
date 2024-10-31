@extends('admin._layouts.master')
@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.'.$module.'.index')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>
    </div>
@endsection

{{-- Content --}}
@section('content')

    @if(isset($datatable))
        {{Form::open(array('route'=>array('admin.'.$module.'.update',$datatable->id),'method'=>'PUT','id'=>'formMain','enctype'=>"multipart/form-data" , 'files' => true))}}
    @else
        {{Form::open(array('route'=>array('admin.'.$module.'.store'),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data"))}}
    @endif
    <input type="hidden" name="submit-close" id="submit-close">

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                        </h3>
                    </div>

                </div>

                <div class="card-body">
                    <div class="m-portlet m-portlet--tabs">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-tools">
                                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger"
                                    role="tablist">
                                    <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link active" data-toggle="tab" href="#t_info"
                                           role="tab">
                                            <i class="la la-info-circle"></i>Thông tin
                                        </a>
                                    </li>

                                    @if(isset($order_refund) && Auth::user()->can('service-purchase-view-order-refund'))

                                        <li class="nav-item m-tabs__item">
                                            <a class="nav-link m-tabs__link " data-toggle="tab" href="#t_refund"
                                               role="tab">
                                                <i class="la la-comment-o"></i>Yêu cầu hoàn tiền
                                            </a>
                                        </li>
                                    @endif

                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="t_info" role="tabpanel">
                                    <div class="m-section">
                                        <span class="m-section__sub mt-5">

                                            <h3 style="margin: 25px 0">#{{$itemconfig_ref->id??""}} {{$itemconfig_ref->title??""}}</h3>
                                            <h3 style="margin: 25px 0"><i>#{{$itemconfig_ref->id??""}} {{$itemconfig_ref->title??""}}</i></h3>

                                        </span>
                                        <div class="mt-5">
                                            <span
                                                class="label label-pill label-inline label-center mr-2  label-danger">{{$itemconfig_ref->title}}</span>

                                            @if($datatable->status==0)
                                                <span
                                                    class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase.status.0')}}</span>

                                            @elseif($datatable->status==1)
                                                <span
                                                    class="label label-pill label-inline label-center mr-2  label-warning">{{config('module.service-purchase.status.1')}}</span>
                                            @elseif($datatable->status==2)
                                                <span
                                                    class="label label-pill label-inline label-center mr-2  label-info">{{config('module.service-purchase.status.2')}}</span>
                                            @elseif($datatable->status==10)
                                                <span
                                                    class="label label-pill label-inline label-center mr-2  label-info">{{config('module.service-purchase.status.10')}}</span>
                                            @elseif($datatable->status==11)
                                                <span
                                                    class="label label-pill label-inline label-center mr-2  label-info">{{config('module.service-purchase.status.11')}}</span>
                                            @elseif($datatable->status==3)
                                                <span
                                                    class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase.status.3')}}</span>
                                            @elseif($datatable->status==4)
                                                <span
                                                    class="label label-pill label-inline label-center mr-2  label-success">{{config('module.service-purchase.status.4')}}</span>
                                            @endif

                                        </div>

                                        @php
                                            $filter_type =\App\Library\Helpers::DecodeJson('filter_type',$datatable->itemconfig_ref->params);
                                        @endphp

                                        <div class="m-separator m-separator--dashed"></div>
                                        @if(Auth::user()->can('service-purchase-view-workname-author'))
                                            <h3>Thành viên yêu cầu</h3>
                                            <div class="" style="margin: 15px 0">
                                                <a href="#" class="m-section__sub"
                                                   style="font-size: 16px">{{$author->username}}</a>
                                            </div>
                                        @endif
                                        @php
                                            $filter_type=\App\Library\Helpers::DecodeJson('filter_type',$itemconfig_ref->params);
                                        @endphp
                                        {{--nếu là dạng điền tiền thì show tiền + gói--}}
                                        @if($filter_type==7)

                                            <div class="m-separator m-separator--dashed"></div>
                                            <span class="m-section__sub">
                                                <h3>Công việc khách yêu cầu</h3>
                                            </span>
                                            <table class="table">
                                                <thead class="thead-default">
                                                <tr>
                                                    <th class="th-index">
                                                        #
                                                    </th>
                                                    <th class="th-name">
                                                        Tên
                                                    </th>
                                                    <th class="th-value">
                                                        Trị giá
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @php
                                                    $workname= $datatable->order_service_workname;
                                                @endphp
                                                @if(!empty($workname) && count($workname)>0)
                                                    @foreach( $workname as $index=> $aWorkName)
                                                        <tr>

                                                            <td>{{$index}}</td>
                                                            <td>{{$aWorkName->title}}</td>
                                                            <td>
                                                                {{currency_format($aWorkName->unit_price)}} VNĐ
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                <tr class="highligh-row">
                                                    <td>
                                                        #
                                                    </td>
                                                    <td>
                                                        Tổng tiền
                                                    </td>
                                                    <td>
                                                        {{currency_format($datatable->price)}} VNĐ
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            {{--nếu là dạng khách thì check quyền xem giá gốc của đơn khách order--}}
                                        @else

                                            {{--nếu có quyền xem giá tiền của khách hàng order thì mới được xem --}}
                                            @if(Auth::user()->can('service-purchase-view-workname-customer'))

                                                <div class="m-separator m-separator--dashed"></div>
                                                <span class="m-section__sub">
                                                <h3>Công việc khách yêu cầu</h3>
                                                </span>
                                                <table class="table">
                                                    <thead class="thead-default">
                                                    <tr>
                                                        <th class="th-index">
                                                            #
                                                        </th>
                                                        <th class="th-name">
                                                            Tên
                                                        </th>
                                                        <th class="th-value">
                                                            Trị giá
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @php
                                                        $workname=$datatable->order_service_workname;
                                                    @endphp
                                                    @if(!empty($workname) && count($workname)>0)
                                                        @foreach( $workname as $index=> $aWorkName)
                                                            <tr>

                                                                <td>{{$index}}</td>
                                                                <td>{{$aWorkName->title}}</td>
                                                                <td>
                                                                    {{currency_format($aWorkName->unit_price)}} VNĐ
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    <tr class="highligh-row">
                                                        <td>
                                                            #
                                                        </td>
                                                        <td>
                                                            Tổng tiền
                                                        </td>
                                                        <td>
                                                            {{currency_format($datatable->price)}} VNĐ
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            @endif
                                        @endif


                                        @if($filter_type==3||$filter_type==4||$filter_type==5||$filter_type==6)

                                            @if(Auth::user()->can('service-purchase-view-workname-for-ctv'))
                                                {{--Công việc CTV--}}
                                                <span class="m-section__sub">
                                            <h3>Công việc dành cho CTV</h3>
                                            </span>
                                                <table class="table">
                                                    <thead class="thead-default">
                                                    <tr>
                                                        <th class="th-index">
                                                            #
                                                        </th>
                                                        <th class="th-name">
                                                            Tên
                                                        </th>
                                                        <th class="th-value">
                                                            Trị giá
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @php
                                                        $workname=$datatable->order_service_workname;
                                                        $totalPriceCTV=0;
                                                    @endphp
                                                    @if(!empty($workname) && count($workname)>0)
                                                        @foreach( $workname as $index=> $aWorkName)

                                                            <tr>

                                                                <td>{{$index}}</td>
                                                                <td>{{$aWorkName->title}}</td>
                                                                <td>
                                                                    {{currency_format($aWorkName->unit_price_ctv)}} VNĐ
                                                                </td>
                                                                @php
                                                                    $totalPriceCTV=$totalPriceCTV+intval($aWorkName->unit_price_ctv)
                                                                @endphp
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    <tr class="highligh-row">
                                                        <td>
                                                            #
                                                        </td>
                                                        <td>
                                                            Tổng tiền
                                                        </td>
                                                        <td>

                                                            {{currency_format($totalPriceCTV)}} VNĐ

                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            @endif

                                        @endif
                                        {{--END Công việc CTV--}}
                                        <div class="m-separator m-separator--dashed"></div>

                                        <span class="m-section__sub">
                                            <h3>Thông tin đính kèm</h3>
                                        </span>
                                        <table class="table">
                                            <thead class="thead-default">
                                            <tr>
                                                <th class="th-index">
                                                    #
                                                </th>
                                                <th class="th-name">
                                                    Tên thông tin
                                                </th>
                                                <th class="th-value">
                                                    Nội dung
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                                $send_name=\App\Library\Helpers::DecodeJson('send_name',$itemconfig_ref->params);
                                                $send_type=\App\Library\Helpers::DecodeJson('send_type',$itemconfig_ref->params);
                                                $server_data=\App\Library\Helpers::DecodeJson('server_data',$itemconfig_ref->params);
                                                $param_service_access=json_decode(isset($service_access->params)?$service_access->params:"");
                                            @endphp


                                            @if(\App\Library\Helpers::DecodeJson('server_mode',$itemconfig_ref->params)==1)
                                                <tr>
                                                    <td>1</td>
                                                    <td> Server</td>
                                                    <td>
                                                        {{isset($server_data[$datatable->position])?$server_data[$datatable->position]:""}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(!empty($send_name)&& count($send_name)>0)
                                                @foreach( $send_name as $index=> $aSendName)

                                                    <tr>
                                                        @if(\App\Library\Helpers::DecodeJson('server_mode',$itemconfig_ref->params)==1)
                                                            <td> {{$index+1+1}} </td>
                                                        @else
                                                            <td> {{$index+1}} </td>
                                                        @endif

                                                        <td> {{$aSendName}} </td>
                                                        <td>
                                                            @if((isset($param_service_access->{'display_info_role'}) && in_array(($itemconfig_ref->id??""),(array)$param_service_access->{'display_info_role'})) || (($datatable->processor_id??"") == Auth::guard()->user()->id && $datatable->status!=3) )

                                                                @if($send_type[$index]==4)
                                                                    @php
                                                                        $image = $datatable->params->{'customer_data'.$index}??""
                                                                    @endphp
                                                                    <a href="{{ \App\Library\MediaHelpers::media($image) }}"
                                                                       target="_blank">

                                                                        <img
                                                                            src="{{ \App\Library\MediaHelpers::media($image) }}"
                                                                            alt=""
                                                                            style="max-width: 100px;max-height: 100px;">
                                                                    </a>
                                                                @else

                                                                    @php
                                                                        if ($filter_type == 5){
                                                                            $params = $datatable->params;
                                                                        }else{
                                                                            $params = json_decode($datatable->params);
                                                                        }

                                                                    @endphp
                                                                    {{--@dd($params)--}}
                                                                    @if(!empty($params->{'customer_data'.$index}))
                                                                        {{ html_entity_decode($params->{'customer_data'.$index}) }}
                                                                    @endif
                                                                @endif

                                                            @else
                                                                **********
                                                            @endif
                                                        </td>


                                                    </tr>

                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>

                                        <div class="m-separator m-separator--dashed"></div>
                                        <span class="m-section__sub">
                                            <h3>Tiến độ</h3>
                                        </span>

                                        <div id="table_main_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="dataTables_scroll">

                                                        <div class="dataTables_scrollBody"
                                                             style="position: relative; overflow: auto; width: 100%;">
                                                            <table
                                                                class=" table  table-striped- table-bordered table-hover table-checkable  no-footer dtr-inline"
                                                                id="table_main" role="grid"
                                                                aria-describedby="table_main_info">
                                                                <thead>
                                                                <tr>
                                                                    <th class="th-index">
                                                                        #
                                                                    </th>
                                                                    @if(Auth::user()->can('service-purchase-view-workname-author'))
                                                                        <th class="">
                                                                            Người thao tác
                                                                        </th>
                                                                    @endif
                                                                    <th class="">
                                                                        Thời gian
                                                                    </th>
                                                                    <th class="">
                                                                        Trạng thái
                                                                    </th>
                                                                    <th class="">
                                                                        Nội dung
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>

                                                                @php

                                                                    $workflow = $datatable->order_service_workflow;
                                                                @endphp

                                                                @if(!empty($workflow) && count($workflow)>0)
                                                                    @foreach( $workflow as $index=> $aWorkFlow)
                                                                        <tr>
                                                                            <td>{{$index+1}}</td>
                                                                            @if(Auth::user()->can('service-purchase-view-workname-author'))
                                                                                <td>{{$aWorkFlow->author->username??""}}</td>
                                                                            @endif
                                                                            <td>{{\App\Library\Helpers::FormatDateTime("d/m/Y H:i:s",$aWorkFlow->created_at)}}</td>
                                                                            <td>
                                                                                @if($aWorkFlow->status==0)
                                                                                    <span
                                                                                        class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase.status.0')}}</span>

                                                                                @elseif($aWorkFlow->status==1)
                                                                                    <span
                                                                                        class="label label-pill label-inline label-center mr-2  label-warning">{{config('module.service-purchase.status.1')}}</span>
                                                                                @elseif($aWorkFlow->status==2)
                                                                                    <span
                                                                                        class="label label-pill label-inline label-center mr-2  label-info">{{config('module.service-purchase.status.2')}}</span>
                                                                                @elseif($aWorkFlow->status==10)
                                                                                    <span
                                                                                        class="label label-pill label-inline label-center mr-2  label-success">{{config('module.service-purchase.status.10')}}</span>
                                                                                @elseif($aWorkFlow->status==11)
                                                                                    <span
                                                                                        class="label label-pill label-inline label-center mr-2  label-info">{{config('module.service-purchase.status.11')}}</span>
                                                                                @elseif($aWorkFlow->status==3)
                                                                                    <span
                                                                                        class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase.status.3')}}</span>
                                                                                @elseif($aWorkFlow->status==4)
                                                                                    <span
                                                                                        class="label label-pill label-inline label-center mr-2  label-success">{{config('module.service-purchase.status.4')}}</span>
                                                                                @elseif($aWorkFlow->status==12)
                                                                                    <span
                                                                                        class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase.status.12')}}</span>
                                                                                @elseif($aWorkFlow->status==5)
                                                                                    <span
                                                                                        class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase.status.5')}}</span>
                                                                                @endif


                                                                            </td>
                                                                            <td>{{$aWorkFlow->content??$aWorkFlow->title??''}}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                                </tbody>

                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="m-portlet__foot">
                                            <div class="row align-items-right">
                                                <div class="col-sm-12 m--align-right">

                                                    @if($datatable->gate_id==1)
                                                        @if($datatable->status==1)
                                                            <button type="button" data-toggle="modal"
                                                                    href="#deleteModal" class="btn btn-danger">Từ chối
                                                            </button>
                                                        @endif
                                                    @else

                                                        @if($datatable->status==1)

                                                            @if(!Auth::user()->can('service-reception-all'))

                                                                @if((isset($param_service_access->{'accept_role'}) && in_array($itemconfig_ref->id??null,(array)$param_service_access->{'accept_role'})) )

                                                                    @if(\App\Library\Helpers::DecodeJson('server_mode',$itemconfig_ref->params)==1)

                                                                        @if(isset($param_service_access->{'allow_server_'.($itemconfig_ref->id??null)}) && in_array($datatable->position,(array)$param_service_access->{'allow_server_'.($itemconfig_ref->id??null)}))
                                                                            <button type="button" data-toggle="modal"
                                                                                    href="#deleteModal"
                                                                                    class="btn btn-danger">Từ chối
                                                                            </button>
                                                                            <button type="button" data-toggle="modal"
                                                                                    href="#recpetionModal"
                                                                                    class="btn btn-success">Tiếp nhận
                                                                            </button>
                                                                        @else
                                                                            <button type="button" data-toggle="modal"
                                                                                    disabled class="btn btn-success">
                                                                                Không được tiếp nhận
                                                                            </button>
                                                                        @endif
                                                                    @else
                                                                        @if(\App\Library\Helpers::DecodeJson('filter_type',$itemconfig_ref->params) == 4 )
                                                                            @if(count($allow_attribute) > 0)
                                                                                @if (in_array($datatable->description,$allow_attribute))
                                                                                    <button type="button" data-toggle="modal"
                                                                                            href="#deleteModal"
                                                                                            class="btn btn-danger">Từ chối
                                                                                    </button>
                                                                                    <button type="button" data-toggle="modal"
                                                                                            href="#recpetionModal"
                                                                                            class="btn btn-success">Tiếp nhận
                                                                                    </button>
                                                                                @endif
                                                                            @else
                                                                                <button type="button" data-toggle="modal"
                                                                                        href="#deleteModal"
                                                                                        class="btn btn-danger">Từ chối
                                                                                </button>
                                                                                <button type="button" data-toggle="modal"
                                                                                        href="#recpetionModal"
                                                                                        class="btn btn-success">Tiếp nhận
                                                                                </button>
                                                                            @endif
                                                                        @else
                                                                            <button type="button" data-toggle="modal"
                                                                                    href="#recpetionModal"
                                                                                    class="btn btn-success">Tiếp nhận
                                                                            </button>
                                                                        @endif
                                                                    @endif
                                                                @else
                                                                    <button type="button" data-toggle="modal" disabled
                                                                            class="btn btn-success">Không được tiếp nhận
                                                                    </button>
                                                                @endif

                                                            @else
                                                                <button type="button" data-toggle="modal"
                                                                        href="#deleteModal" class="btn btn-danger">Từ
                                                                    chối
                                                                </button>
                                                                <button type="button" data-toggle="modal"
                                                                        href="#recpetionModal" class="btn btn-success">
                                                                    Tiếp nhận
                                                                </button>
                                                            @endif
                                                        @elseif($datatable->status==2)

                                                            @if(Auth::user()->can('service-reception-all'))
                                                                <button type="button" data-toggle="modal"
                                                                        href="#deleteModal" class="btn btn-danger">Hủy
                                                                    bỏ
                                                                </button>
                                                            @else
                                                                @if($datatable->processor_id==Auth::user()->id )
                                                                    <button type="button" data-toggle="modal"
                                                                            href="#deleteModal" class="btn btn-danger">
                                                                        Hủy bỏ
                                                                    </button>
                                                                @endif
                                                            @endif

                                                            @if($datatable->processor_id == Auth::guard()->user()->id)
                                                                <button class="btn btn-info" data-toggle="modal"
                                                                        href="#completedModal">
                                                                    Hoàn tất
                                                                </button>
                                                            @endif
                                                            @if($datatable->processor_id == Auth::guard()->user()->id && isset($roblox_order) && $roblox_order->status == 'recharge')
                                                                <button class="btn btn-success" data-toggle="modal"
                                                                        href="#rechangModal">
                                                                    Gọi lại
                                                                </button>
                                                            @endif
                                                        @elseif(Auth::user()->can('service-purchase-edit-pengiriman') && $datatable->idkey == "gamepass_roblox" && ($datatable->status== 4 || $datatable->status== 10))
                                                            <button class="btn btn-success" data-toggle="modal"
                                                                    href="#pengirimanModal">
                                                                Thay đổi thông tin lô hàng
                                                            </button>
                                                        @endif
                                                    @endif

                                                    <div class="modal fade" id="deleteModal" tabindex="-1" role="basic"
                                                         aria-hidden="true">
                                                        <div style="text-align:initial;" class="modal-dialog">
                                                            <div class="modal-content">
                                                                {{Form::open(array('route'=>array('admin.service-purchase.destroy',$datatable->id),'class'=>'m-form','method'=>'DELETE'))}}
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Từ chối yêu cầu dịch vụ</h4>
                                                                    <button type="button" class="close"
                                                                            data-dismiss="modal"
                                                                            aria-hidden="true"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row m-form__group">
                                                                        <label class="col-sm-4 col-lg-4 col-form-label">Lỗi
                                                                            thuộc về:</label>
                                                                        <div class="col-sm-8 col-lg-8">
                                                                            <div class="input-group">
                                                                                {{Form::select('mistake_by',array(''=>'-- Không chọn --')+config('module.service-purchase.mistake_by'),Request::get('mistake_by'),array('required'=>'','class'=>'form-control m-input m-input--air'))}}
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    @php
                                                                        $messages = [];
                                                                        if (!empty($itemconfig_ref->groups)){
                                                                            if(!empty($itemconfig_ref->groups[0])){
                                                                                $groups = $itemconfig_ref->groups[0];
                                                                                if (isset($groups->params_error)){
                                                                                    $messages = json_decode($groups->params_error);
                                                                                }
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    <div class="row m-form__group" style="padding-top: 12px">
                                                                        <label class="col-sm-4 col-lg-4 col-form-label">Nội
                                                                            dung:</label>
                                                                        <div class="col-sm-8 col-lg-8">
                                                                            <div class="input-group">
                                                                                <select required class="form-control" name="note">
                                                                                    <option value="">------ Chọn nội dung ------</option>
                                                                                    @foreach($messages??[] as $message)
                                                                                        <option value="{{ $message }}"> {{ $message }} </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>


                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button"
                                                                            class="btn btn-secondary btn-outline"
                                                                            data-dismiss="modal">Đóng
                                                                    </button>
                                                                    <button type="submit"
                                                                            class="btn btn-primary m-btn m-btn--air">Xác
                                                                        nhận
                                                                    </button>
                                                                </div>
                                                                {{ Form::close() }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="recpetionModal" tabindex="-1"
                                                         role="basic" aria-hidden="true">
                                                        <div style="text-align:initial;" class="modal-dialog">
                                                            <div class="modal-content">
                                                                {{Form::open(array('route'=>array('admin.service-purchase.reception',$datatable->id),'class'=>'m-form','method'=>'POST'))}}
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Tiếp nhận yêu cầu dịch
                                                                        vụ</h4>
                                                                    <button type="button" class="close"
                                                                            data-dismiss="modal"
                                                                            aria-hidden="true"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    @if($datatable->idkey == 'anime_defenders_auto')
                                                                        <div class="form-group col-12">
                                                                            <div class="row">
                                                                                <div class="col-md-12" style="padding-left: 0;padding-right: 0">
                                                                                    <label for="" style="font-weight: bold">Chọn bot cần xử lý:</label>
                                                                                </div>
                                                                                <select style="width: 100%" name="bot_id" data-placeholder="{{__('Chọn bot cần xử lý')}}" title="Chọn bot cần xử lý" id="status" class="form-control col-md-12">
                                                                                    <option value="">-- Chọn bot cần xử lý --</option>
                                                                                    @foreach($bots??[] as $key => $bot)
                                                                                        <option value="{{ $bot->id }}"> {{ $bot->acc }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        Bạn muốn tiếp nhận yêu cầu dịch vụ này?
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button"
                                                                            class="btn btn-secondary btn-outline"
                                                                            data-dismiss="modal">Đóng
                                                                    </button>
                                                                    <button type="submit"
                                                                            class="btn btn-primary m-btn m-btn--air">Xác
                                                                        nhận
                                                                    </button>
                                                                </div>
                                                                {{ Form::close() }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="pengirimanModal" tabindex="-1"
                                                             role="basic" aria-hidden="true">
                                                            <div style="text-align:initial;" class="modal-dialog">
                                                                <div class="modal-content">
                                                                    {{Form::open(array('route'=>array('admin.service-purchase.pengiriman',$datatable->id),'class'=>'m-form','method'=>'POST'))}}
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Chỉnh sửa thông tin lô hàng</h4>
                                                                        <button type="button" class="close"
                                                                                data-dismiss="modal"
                                                                                aria-hidden="true"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        @if($datatable->idkey == 'gamepass_roblox')
                                                                            @php
                                                                                $value_id_pengiriman = null;
                                                                                $value_account_pengiriman = null;
                                                                                if ($datatable->pengiriman_detail){
                                                                                    $value_id_pengiriman = $datatable->pengiriman_detail->title??'';
                                                                                    $value_account_pengiriman = $datatable->pengiriman_detail->description??'';
                                                                                }
                                                                            @endphp
                                                                            <div class="form-group col-12">
                                                                                <div class="row">
                                                                                    <div class="form-group col-md-12">
                                                                                        <label for="edit_id_pengiriman"> ID lô hàng:</label>
                                                                                        <input type="text"
                                                                                               required
                                                                                               class="form-control" id="edit_id_pengiriman" name="edit_id_pengiriman" value="{{ $value_id_pengiriman }}"  placeholder="{{__('ID lô hàng')}}"     >
                                                                                    </div>
                                                                                    <div class="form-group col-md-12">
                                                                                        <label for="edit_account_pengiriman"> Account lô hàng:</label>
                                                                                        <input type="text"
                                                                                               required
                                                                                               class="form-control" id="edit_account_pengiriman" name="edit_account_pengiriman" value="{{ $value_account_pengiriman??'' }}"   placeholder="{{__('Account lô hàng')}}">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @else
                                                                            Bạn muốn tiếp nhận yêu cầu dịch vụ này?
                                                                        @endif
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button"
                                                                                class="btn btn-secondary btn-outline"
                                                                                data-dismiss="modal">Đóng
                                                                        </button>
                                                                        <button type="submit"
                                                                                class="btn btn-primary m-btn m-btn--air">Xác
                                                                            nhận
                                                                        </button>
                                                                    </div>
                                                                    {{ Form::close() }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <div class="modal fade" id="completedModal" tabindex="-1"
                                                         role="basic" aria-hidden="true">
                                                        <div style="text-align:initial;" class="modal-dialog">
                                                            <div class="modal-content">
                                                                {{Form::open(array('route'=>array('admin.service-purchase.completed',$datatable->id),'class'=>'m-form','method'=>'POST'))}}
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Hoàn thành yêu cầu dịch
                                                                        vụ</h4>
                                                                    <button type="button" class="close"
                                                                            data-dismiss="modal"
                                                                            aria-hidden="true"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        @if(isset(Auth::user()->type_information_ctv) && Auth::user()->type_information_ctv == 1 && $datatable->idkey == "gamepass_roblox")
                                                                        <div class="form-group col-md-12">
                                                                            <label for="id_pengiriman"> ID lô hàng:</label>
                                                                            <input type="text"
                                                                                   required
                                                                                   class="form-control" id="id_pengiriman" name="id_pengiriman" value=""  placeholder="{{__('ID lô hàng')}}"     >
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <label for="account_pengiriman"> Account lô hàng:</label>
                                                                            <input type="text"
                                                                                   required
                                                                                   class="form-control" id="account_pengiriman" name="account_pengiriman" value=""   placeholder="{{__('Account lô hàng')}}">
                                                                        </div>
                                                                        @else
                                                                            Bạn muốn Hoàn tất yêu cầu dịch vụ này?
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button"
                                                                            class="btn btn-secondary btn-outline"
                                                                            data-dismiss="modal">Đóng
                                                                    </button>
                                                                    <button type="submit"
                                                                            class="btn btn-primary m-btn m-btn--air">Xác
                                                                        nhận
                                                                    </button>
                                                                </div>
                                                                {{ Form::close() }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="rechangModal" tabindex="-1"
                                                         role="basic" aria-hidden="true">
                                                        <div style="text-align:initial;" class="modal-dialog">
                                                            <div class="modal-content">
                                                                {{Form::open(array('route'=>array('admin.service-purchase.rechang',$datatable->id),'class'=>'m-form','method'=>'POST'))}}
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Gọi lại đơn hàng
                                                                        vụ</h4>
                                                                    <button type="button" class="close"
                                                                            data-dismiss="modal"
                                                                            aria-hidden="true"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    @if($datatable->idkey == 'anime_defenders_auto')
                                                                        <div class="form-group col-12">
                                                                            <div class="row">
                                                                                <div class="col-md-12" style="padding-left: 0;padding-right: 0">
                                                                                    <label for="" style="font-weight: bold">Chọn bot cần xử lý:</label>
                                                                                </div>
                                                                                <select style="width: 100%" name="bot_id" data-placeholder="{{__('Chọn bot cần xử lý')}}" title="Chọn bot cần xử lý" id="status" class="form-control col-md-12">
                                                                                    <option value="">-- Chọn bot cần xử lý --</option>
                                                                                    @foreach($bots??[] as $key => $bot)
                                                                                        <option value="{{ $bot->id }}"> {{ $bot->acc }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button"
                                                                            class="btn btn-secondary btn-outline"
                                                                            data-dismiss="modal">Đóng
                                                                    </button>
                                                                    <button type="submit"
                                                                            class="btn btn-primary m-btn m-btn--air">Xác
                                                                        nhận
                                                                    </button>
                                                                </div>
                                                                {{ Form::close() }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                @if(isset($order_refund) && Auth::user()->can('service-purchase-view-order-refund'))

                                    <div class="tab-pane " id="t_refund" role="tabpanel">
                                        <div class="m-section card-body">

                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label for="locale">
                                                        Yêu cầu hoàn tiền
                                                        @if($order_refund->status == 0)
                                                            <span class="badge badge-danger" style="margin-left: 12px">Đã hủy</span>
                                                        @elseif($order_refund->status == 1)
                                                            <span class="badge badge-success" style="margin-left: 12px">Thành công</span>
                                                        @elseif($order_refund->status == 3)
                                                            <span class="badge badge-dark" style="margin-left: 12px">Từ chối</span>
                                                        @elseif($order_refund->status == 2)
                                                            <span class="badge badge-warning" style="margin-left: 12px">Đang chờ xử lý</span>
                                                        @endif
                                                    </label>
                                                    <div class="card">
                                                        <div class="card-body p-3" style="min-height: 148px;">
                                                        <span class="form-text text-dark">
                                                            Nội dung:
                                                        </span>
                                                            <div class="text-warning mb-5">
                                                                {{ $order_refund->description }}
                                                            </div>
                                                            <span class="form-text text-dark mb-5">
                                                            Ảnh đính kèm:
                                                        </span>
                                                            @php
                                                                $image_customer = null;
                                                                if (isset($order_refund->content)){
                                                                    $params = json_decode($order_refund->content);
                                                                    if ($params->image_customer){
                                                                        $image_customer = $params->image_customer;
                                                                    }
                                                                }
                                                            @endphp

                                                            <div class="image-preview-box row">
                                                                @if(isset($image_customer))
                                                                    @foreach($image_customer as $image)
                                                                        <div class="col-md-3 col-sm-4 col-6 mb-2">
                                                                            <div class="border image-item" style="position: relative; min-height: 60px;">
                                                                                <img src="{{ \App\Library\MediaHelpers::media($image) }}" class="img-fluid">
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                            @if($order_refund->status == 2)
                                                                <div class="row mt-5">
                                                                    <div class="col-md-12">

                                                                        @if(Auth::user()->can('service-purchase-delete-order-refund'))
                                                                            <button class="btn btn-danger" data-toggle="modal"
                                                                                    href="#rejectRefundModal">Từ chối</button>
                                                                        @endif
                                                                        @if(Auth::user()->can('service-purchase-complete-order-refund'))
                                                                            <button class="btn btn-primary ml-5" data-toggle="modal"
                                                                                    href="#completedRefundModal">Đồng ý</button>
                                                                        @endif

                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{ Form::close() }}

    <div class="modal fade" id="rejectRefundModal" tabindex="-1"
         role="basic" aria-hidden="true">
        <div style="text-align:initial;" class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase.reject-refund',$datatable->id),'class'=>'m-form','method'=>'POST'))}}
                <div class="modal-header">
                    <h4 class="modal-title">Từ chối yêu cầu hoàn tiền</h4>
                    <button type="button" class="close"
                            data-dismiss="modal"
                            aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            Vui lòng nhập lý do từ chối yêu cầu hoàn tiền này.
                        </div>
                        <div class="col-md-12 mt-5">
                            <textarea class="form-control" name="note_refund" required>

                            </textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary btn-outline"
                            data-dismiss="modal">Đóng
                    </button>
                    <button type="submit" class="btn btn-primary m-btn m-btn--air">Xác
                        nhận
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="completedRefundModal" tabindex="-1"
         role="basic" aria-hidden="true">
        <div style="text-align:initial;" class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase.completed-refund',$datatable->id),'class'=>'m-form','method'=>'POST'))}}
                <div class="modal-header">
                    <h4 class="modal-title">Hoàn thành yêu cầu hoàn tiền
                        vụ</h4>
                    <button type="button" class="close"
                            data-dismiss="modal"
                            aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    Xác nhận hoàn thành yêu cầu hoàn tiền này?
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary btn-outline"
                            data-dismiss="modal">Đóng
                    </button>
                    <button type="submit"
                            class="btn btn-primary m-btn m-btn--air">Xác
                        nhận
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

@endsection

{{-- Styles Section --}}
@section('styles')
    <style>
        .th-index {
            width: 60px;
        }

        .th-name {
            width: 300px;
        }

        .edu-history-sec {
            float: left;
            width: 100%;
        }

        .edu-history {
            float: left;
            width: 100%;
            display: table;
            margin-bottom: 20px;
            position: relative;
        }

        .edu-history > i {
            display: table-cell;
            vertical-align: top;
            width: 70px;
            font-size: 50px;
            color: #fb236a;
            line-height: 60px;
        }

        .edu-hisinfo {
            display: table-cell;
            vertical-align: top;
        }

        .edu-hisinfo > h3 {
            float: left;
            width: 100%;
            font-family: Open Sans;
            font-size: 16px;
            color: #8b91dd;
            margin: 0;
            margin-top: 0px;
            margin-top: 10px;
        }

        .edu-hisinfo > i {
            float: left;
            width: 100%;
            font-style: normal;
            font-size: 14px;
            color: #888888;
            margin-top: 7px;
        }

        .edu-hisinfo > span {
            float: left;
            width: 100%;
            font-family: Open Sans;
            font-size: 16px;
            color: #202020;
            margin-top: 8px;
        }

        .edu-hisinfo > span i {
            font-size: 14px;
            color: #888888;
            font-style: normal;
            margin-left: 12px;
        }

        .edu-hisinfo > p {
            float: left;
            width: 100%;
            margin: 0;
            font-size: 14px;
            color: #888888;
            font-style: normal;
            line-height: 24px;
            margin-top: 10px;
        }

        .edu-history.style2 {
            margin: 0;
            padding-bottom: 20px;
            position: relative;
            padding-left: 40px;
            margin-bottom: 24px;
            padding-bottom: 0;
        }

        .edu-history.style2 > i {
            position: absolute;
            left: 0;
            top: 0;
            width: 16px;
            height: 16px;
            border: 2px solid #8b91dd;
            content: "";

            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            -ms-border-radius: 50%;
            -o-border-radius: 50%;
            border-radius: 50%;

        }

        .edu-history.style2 .edu-hisinfo > h3 {
            margin: 0;
        }

        .edu-history.style2::before {
            position: absolute;
            left: 7px;
            top: 20px;
            width: 2px;
            height: 100%;
            content: "";
            background: #e8ecec;
        }

        .edu-history.style2:last-child::before {
            display: none;
        }

        .edu-history.style2 .edu-hisinfo > h3 span {
            /*        color: #202020;*/
            margin-left: 10px;
        }

        .highligh-row {
            background-color: #ebedf2;
            font-weight: bold;
        }
    </style>
@endsection
{{-- Scripts Section --}}
@section('scripts')


    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>
    <script>


        "use strict";
        $(document).ready(function () {

            $('#status').select2();

            $('.form_delete_service').on('submit', function(event) {
                // Ngăn form submit mặc định
                event.preventDefault();

                // Tìm nút submit có class btn_delete_service
                var submitButton = $(this).find('.btn_modal_deleteModal');

                // Disable nút submit
                submitButton.prop('disabled', true);

                // Submit form sau khi đã disable nút submit
                this.submit();
            });
            //btn submit form
            $('.btn-submit-custom').click(function (e) {
                e.preventDefault();
                var btn = this;
                $(".btn-submit-custom").each(function (index, value) {
                    KTUtil.btnWait(this, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);
                });
                $('.btn-submit-dropdown').prop('disabled', true);
                //gắn thêm hành động close khi submit
                $('#submit-close').val($(btn).data('submit-close'));
                var formSubmit = $('#' + $(btn).data('form'));
                formSubmit.submit();
            });


            $('.ckeditor-source').each(function () {
                var elem_id = $(this).prop('id');
                var height = $(this).data('height');
                height = height != "" ? height : 150;
                var startupMode = $(this).data('startup-mode');
                if (startupMode == "source") {
                    startupMode = "source";
                } else {
                    startupMode = "wysiwyg";
                }

                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl: "{{ route('admin.ckfinder_browser') }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                    filebrowserUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                    height: height,
                    startupMode: startupMode,
                });
            });
            $('.ckeditor-basic').each(function () {
                var elem_id = $(this).prop('id');
                var height = $(this).data('height');
                height = height != "" ? height : 150;
                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl: "{{ route('admin.ckfinder_browser') }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                    filebrowserUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                    height: height,
                    removeButtons: 'Source',
                });
            });

            // Image choose item
            $(".ck-popup").click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.ck-parent');

                var elemThumb = parent.find('.ck-thumb');
                var elemInput = parent.find('.ck-input');
                var elemBtnRemove = parent.find('.ck-btn-remove');
                CKFinder.modal({
                    connectorPath: '{{route('admin.ckfinder_connector')}}',
                    resourceType: 'Images',
                    chooseFiles: true,

                    width: 900,
                    height: 600,
                    onInit: function (finder) {
                        finder.on('files:choose', function (evt) {
                            var file = evt.data.files.first();
                            var url = file.getUrl();
                            elemThumb.attr("src", MEDIA_URL + url);
                            elemInput.val(url);

                        });
                    }
                });
            });
            $(".ck-btn-remove").click(function (e) {
                e.preventDefault();

                var parent = $(this).closest('.ck-parent');

                var elemThumb = parent.find('.ck-thumb');
                var elemInput = parent.find('.ck-input');
                elemThumb.attr("src", "/assets/backend/themes/images/empty-photo.jpg");
                elemInput.val("");

            });

            // Image extenstion choose item
            $(".ck-popup-multiply").click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.ck-parent');
                var elemBoxSort = parent.find('.sortable');
                var elemInput = parent.find('.image_input_text');
                CKFinder.modal({
                    connectorPath: '{{route('admin.ckfinder_connector')}}',
                    resourceType: 'Images',
                    chooseFiles: true,
                    width: 900,

                    height: 600,
                    onInit: function (finder) {
                        finder.on('files:choose', function (evt) {
                            var allFiles = evt.data.files;

                            var chosenFiles = '';
                            var len = allFiles.length;
                            allFiles.forEach(function (file, i) {
                                chosenFiles += file.get('url');
                                if (i != len - 1) {
                                    chosenFiles += "|";
                                }

                                elemBoxSort.append(`<div class="image-preview-box">
                                            <img src="${MEDIA_URL + file.get('url')}" alt="" data-input="${file.get('url')}">
                                            <a rel="8" class="btn btn-xs  btn-icon btn-danger btn_delete_image" data-toggle="modal" data-target="#deleteModal"><i class="la la-close"></i></a>
                                        </div>`);
                            });
                            var allImageChoose = parent.find(".image-preview-box img");
                            var allPath = "";
                            var len = allImageChoose.length;
                            allImageChoose.each(function (index, obj) {
                                allPath += $(this).attr('data-input');

                                if (index != len - 1) {
                                    allPath += "|";
                                }
                            });
                            elemInput.val(allPath);

                            //set lại event cho các nút xóa đã được thêm
                            //remove image extension each item
                            $('.btn_delete_image').click(function (e) {

                                var parent = $(this).closest('.ck-parent');
                                var elemInput = parent.find('.image_input_text');
                                $(this).closest('.image-preview-box').remove();
                                var allImageChoose = parent.find(".image-preview-box img");

                                var allPath = "";
                                var len = allImageChoose.length;
                                allImageChoose.each(function (index, obj) {
                                    allPath += $(this).attr('src');

                                    if (index != len - 1) {
                                        allPath += "|";
                                    }
                                });
                                elemInput.val(allPath);
                            });
                            //khoi tao lại sortable sau khi append phần tử mới
                            $('.sortable').sortable().bind('sortupdate', function (e, ui) {

                                var parent = $(this).closest('.ck-parent');
                                var allImageChoose = parent.find(".image-preview-box img");
                                var elemInput = parent.find('.image_input_text');
                                var allPath = "";
                                var len = allImageChoose.length;
                                allImageChoose.each(function (index, obj) {
                                    allPath += $(this).attr('src');

                                    if (index != len - 1) {
                                        allPath += "|";
                                    }
                                });
                                elemInput.val(allPath);
                            });

                        });
                    }
                });
            });

            //remove image extension each item
            $('.btn_delete_image').click(function (e) {

                var parent = $(this).closest('.ck-parent');
                var elemInput = parent.find('.image_input_text');
                $(this).closest('.image-preview-box').remove();
                var allImageChoose = parent.find(".image-preview-box img");

                var allPath = "";
                var len = allImageChoose.length;
                allImageChoose.each(function (index, obj) {
                    allPath += $(this).attr('src');

                    if (index != len - 1) {
                        allPath += "|";
                    }
                });
                elemInput.val(allPath);
            });


            //khoi tao sortable
            $('.sortable').sortable().bind('sortupdate', function (e, ui) {

                var parent = $(this).closest('.ck-parent');
                var allImageChoose = parent.find(".image-preview-box img");
                var elemInput = parent.find('.image_input_text');
                var allPath = "";
                var len = allImageChoose.length;
                allImageChoose.each(function (index, obj) {
                    allPath += $(this).attr('src');

                    if (index != len - 1) {
                        allPath += "|";
                    }
                });
                elemInput.val(allPath);
            });


            //ckfinder for upload file
            $(".ck-popup-file").click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.ck-parent');


                var elemInput = parent.find('.ck-input');
                var elemBtnRemove = parent.find('.ck-btn-remove');
                CKFinder.modal({
                    connectorPath: '{{route('admin.ckfinder_connector')}}',
                    resourceType: 'Files',
                    chooseFiles: true,

                    width: 900,
                    height: 600,
                    onInit: function (finder) {
                        finder.on('files:choose', function (evt) {
                            var file = evt.data.files.first();
                            var url = file.getUrl();
                            elemInput.val(url);

                        });
                    }
                });
            });
        });


    </script>
    <script>


        $('.ckeditor-source').each(function () {
            var elem_id = $(this).prop('id');
            var height = $(this).data('height');
            height = height != "" ? height : 150;
            var startupMode = $(this).data('startup-mode');
            if (startupMode == "source") {
                startupMode = "source";
            } else {
                startupMode = "wysiwyg";
            }

            CKEDITOR.replace(elem_id, {
                filebrowserBrowseUrl: "{{ route('admin.ckfinder_browser') }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                filebrowserUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                height: height,
                startupMode: startupMode,
            });
            CKEDITOR.on('instanceReady', function (ev) {
                var editor = ev.editor;
                editor.dataProcessor.htmlFilter.addRules({
                    elements: {
                        a: function (element) {
                            if (!element.attributes.rel) {
                                //gets content's a href values
                                var url = element.attributes.href;

                                //extract host names from URLs (IE safe)
                                var parser = document.createElement('a');
                                parser.href = url;

                                var hostname = parser.hostname;
                                if (hostname !== window.location.host) {
                                    element.attributes.rel = 'nofollow';
                                    element.attributes.target = '_blank';
                                }
                            }
                        }
                    }
                });
            })
        });


        $('.ckeditor-basic').each(function () {
            var elem_id = $(this).prop('id');
            var height = $(this).data('height');
            height = height != "" ? height : 150;
            CKEDITOR.replace(elem_id, {
                filebrowserBrowseUrl: "{{ route('admin.ckfinder_browser') }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                filebrowserUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                height: height,
                removeButtons: 'Source',
            });

            CKEDITOR.on('instanceReady', function (ev) {
                var editor = ev.editor;
                editor.dataProcessor.htmlFilter.addRules({
                    elements: {
                        a: function (element) {
                            if (!element.attributes.rel) {
                                //gets content's a href values
                                var url = element.attributes.href;

                                //extract host names from URLs (IE safe)
                                var parser = document.createElement('a');
                                parser.href = url;

                                var hostname = parser.hostname;
                                if (hostname !== window.location.host) {
                                    element.attributes.rel = 'nofollow';
                                    element.attributes.target = '_blank';
                                }
                            }
                        }
                    }
                });
            })
        });


        // Image choose item
        $(".ck-popup").click(function (e) {
            e.preventDefault();
            var parent = $(this).closest('.ck-parent');

            var elemThumb = parent.find('.ck-thumb');
            var elemInput = parent.find('.ck-input');
            var elemBtnRemove = parent.find('.ck-btn-remove');
            CKFinder.modal({
                connectorPath: '{{route('admin.ckfinder_connector')}}',
                resourceType: 'Images',
                chooseFiles: true,

                width: 900,
                height: 600,
                onInit: function (finder) {
                    finder.on('files:choose', function (evt) {
                        var file = evt.data.files.first();
                        var url = file.getUrl();
                        elemThumb.attr("src", url);
                        elemInput.val(url);

                    });
                }
            });
        });
        $(".ck-btn-remove").click(function (e) {
            e.preventDefault();

            var parent = $(this).closest('.ck-parent');

            var elemThumb = parent.find('.ck-thumb');
            var elemInput = parent.find('.ck-input');
            elemThumb.attr("src", "/assets/backend/themes/images/empty-photo.jpg");
            elemInput.val("");
        });


        //ckfinder for upload file
        $(".ck-popup-file").click(function (e) {
            e.preventDefault();
            var parent = $(this).closest('.ck-parent');


            var elemInput = parent.find('.ck-input');
            var elemBtnRemove = parent.find('.ck-btn-remove');
            CKFinder.modal({
                connectorPath: '{{route('admin.ckfinder_connector')}}',
                resourceType: 'Files',
                chooseFiles: true,

                width: 900,
                height: 600,
                onInit: function (finder) {
                    finder.on('files:choose', function (evt) {
                        var file = evt.data.files.first();
                        var url = file.getUrl();
                        elemInput.val(url);

                    });
                }
            });
        });


    </script>
@endsection

