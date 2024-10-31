@extends('admin._layouts.master')
@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('frontend.'.$module.'.index')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>
    </div>
@endsection

{{-- Content --}}
@section('content')

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

                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="tab-content">

                                <div class="tab-pane active" id="t_info" role="tabpanel">
                                    <div class="m-section">
                                        <span class="m-section__sub mt-5">

                                            <h3 style="margin: 25px 0">#{{$datatable->item_ref->id??""}} {{$datatable->item_ref->title??""}}</h3>
                                            <h3 style="margin: 25px 0"><i>#{{$datatable->item_ref->id??""}} {{$datatable->item_ref->title??""}}</i></h3>

                                        </span>
                                        <div class="mt-5">
                                            <span
                                                class="label label-pill label-inline label-center mr-2  label-danger">{{$datatable->item_ref->title}}</span>

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


                                        <div class="m-separator m-separator--dashed"></div>
{{--                                        @if(auth('frontend')->user()->can('service-purchase-view-workname-author'))--}}
                                        <h3>Thành viên yêu cầu</h3>
                                        <div class="" style="margin: 15px 0">
                                            <a href="#" class="m-section__sub"
                                               style="font-size: 16px">
                                                @php
                                                    $author = str_replace('tt_', '', $datatable->author->username);
                                                @endphp
                                                {{ $author }}
                                            </a>
                                        </div>
{{--                                        @endif--}}
                                        @php
                                            $filter_type=\App\Library\Helpers::DecodeJson('filter_type',$datatable->item_ref->params);
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
                                                    if ($datatable->module == 'withdraw-service-item'){
                                                            $workname=$datatable->order_detail->where('module','withdraw-service-workname');
                                                        }else{
                                                            $workname=$datatable->order_detail->where('module','service-workname');
                                                        }
                                                @endphp
                                                @if(!empty($workname) && count($workname)>0)
                                                    @foreach( $workname as $index=> $aWorkName)
                                                        <tr>

                                                            <td>{{$index}}</td>
                                                            <td>{{$aWorkName->title}}</td>
                                                            <td>
                                                                @if($datatable->module == 'withdraw-service-item')
                                                                    {{currency_format($aWorkName->unit_price)}} ROBUX
                                                                @else
                                                                    {{currency_format($aWorkName->unit_price)}} VNĐ
                                                                @endif

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
                                                        @if($datatable->module == 'withdraw-service-item')
                                                            {{currency_format($datatable->price)}} ROBUX
                                                        @else
                                                            {{currency_format($datatable->price)}} VNĐ
                                                        @endif

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            {{--nếu là dạng khách thì check quyền xem giá gốc của đơn khách order--}}
                                        @else

                                            {{--nếu có quyền xem giá tiền của khách hàng order thì mới được xem --}}
{{--                                            @if(auth('frontend')->user()->can('service-purchase-view-workname-customer'))--}}

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
                                                        if ($datatable->module == 'withdraw-service-item'){
                                                                $workname=$datatable->order_detail->where('module','withdraw-service-workname');
                                                            }else{
                                                                $workname=$datatable->order_detail->where('module','service-workname');
                                                            }
                                                    @endphp
                                                    @if(!empty($workname) && count($workname)>0)
                                                        @foreach( $workname as $index=> $aWorkName)
                                                            <tr>

                                                                <td>{{$index}}</td>
                                                                <td>{{$aWorkName->title}}</td>
                                                                <td>
                                                                    @if($datatable->module == 'withdraw-service-item')
                                                                        {{currency_format($aWorkName->unit_price)}}
                                                                        ROBUX
                                                                    @else
                                                                        {{currency_format($aWorkName->unit_price)}} VNĐ
                                                                    @endif

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
                                                            @if($datatable->module == 'withdraw-service-item')
                                                                {{currency_format($datatable->price)}} ROBUX
                                                            @else
                                                                {{currency_format($datatable->price)}} VNĐ
                                                            @endif

                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
{{--                                            @endif--}}
                                        @endif


                                        @if($filter_type==3||$filter_type==4||$filter_type==5||$filter_type==6)

{{--                                            @if(auth('frontend')->user()->can('service-purchase-view-workname-for-ctv'))--}}
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
                                                    if ($datatable->module == 'withdraw-service-item'){
                                                        $workname=$datatable->order_detail->where('module','withdraw-service-workname');
                                                    }else{
                                                        $workname=$datatable->order_detail->where('module','service-workname');
                                                    }
                                                        $totalPriceCTV=0;
                                                @endphp
                                                @if(!empty($workname) && count($workname)>0)
                                                    @foreach( $workname as $index=> $aWorkName)

                                                        <tr>

                                                            <td>{{$index}}</td>
                                                            <td>{{$aWorkName->title}}</td>
                                                            <td>
                                                                @if($datatable->module == 'withdraw-service-item')
                                                                    {{currency_format($aWorkName->unit_price_ctv)}}
                                                                    ROBUX
                                                                @else
                                                                    {{currency_format($aWorkName->unit_price_ctv)}} VNĐ
                                                                @endif

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

                                                        @if($datatable->module == 'withdraw-service-item')
                                                            {{currency_format($totalPriceCTV)}} ROBUX
                                                        @else
                                                            {{currency_format($totalPriceCTV)}} VNĐ
                                                        @endif

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
{{--                                            @endif--}}

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

                                                $send_name=\App\Library\Helpers::DecodeJson('send_name',$datatable->item_ref->params);
                                                $send_type=\App\Library\Helpers::DecodeJson('send_type',$datatable->item_ref->params);
                                                $server_data=\App\Library\Helpers::DecodeJson('server_data',$datatable->item_ref->params);
                                                $param_service_access=json_decode(isset($service_access->params)?$service_access->params:"");
                                            @endphp


                                            @if(\App\Library\Helpers::DecodeJson('server_mode',$datatable->item_ref->params)==1)
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
                                                        @if(\App\Library\Helpers::DecodeJson('server_mode',$datatable->item_ref->params)==1)
                                                            <td> {{$index+1+1}} </td>
                                                        @else
                                                            <td> {{$index+1}} </td>
                                                        @endif

                                                        <td> {{$aSendName}} </td>
                                                        <td>
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
                                                                    $params = json_decode($datatable->params);
                                                                @endphp
                                                                {{--@dd($params)--}}
                                                                {{ html_entity_decode($params->{'customer_data'.$index}) }}

                                                            @endif
{{--                                                            @if((isset($param_service_access->{'display_info_role'}) && in_array(($datatable->item_ref->id??""),(array)$param_service_access->{'display_info_role'})) || (($datatable->processor->id??"") == auth('frontend')->user()->user()->id && $datatable->status!=3) )--}}

{{--                                                                --}}

{{--                                                            @else--}}
{{--                                                                **********--}}
{{--                                                            @endif--}}
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
{{--                                                                    @if(auth('frontend')->user()->can('service-purchase-view-workname-author'))--}}
                                                                    <th class="">
                                                                        Người thao tác
                                                                    </th>
{{--                                                                    @endif--}}
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
                                                                    if ($datatable->module == 'withdraw-service-item'){
                                                                        $workflow=$datatable->order_detail->where('module','withdraw-service-workflow');
                                                                    }else{
                                                                        $workflow=$datatable->order_detail->where('module','service-workflow');
                                                                    }
                                                                @endphp

                                                                @if(!empty($workflow) && count($workflow)>0)
                                                                    @foreach( $workflow as $index=> $aWorkFlow)
                                                                        <tr>
                                                                            <td>{{$index+1}}</td>
{{--                                                                            @if(auth('frontend')->user()->can('service-purchase-view-workname-author'))--}}
                                                                            <td>
                                                                                @php
                                                                                    $aWorkFlow_author = str_replace('tt_', '', $aWorkFlow->author->username??'');
                                                                                @endphp
                                                                                {{ $aWorkFlow_author }}
                                                                            </td>
{{--                                                                            @endif--}}
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
                                                                            <td>{{$aWorkFlow->content}}</td>
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

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>
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

@endsection

