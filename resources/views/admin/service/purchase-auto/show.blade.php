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
                                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist">
                                    <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link active" data-toggle="tab" href="#t_info" role="tab">
                                            <i class="la la-info-circle"></i>Thông tin
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="t_info">
                                    <div class="m-section">
                                        <span class="m-section__sub mt-5" >
                                            <h3 style="margin: 25px 0">
                                                #{{$datatable->item_ref->id??""}} {{$datatable->item_ref->title??""}} @if($datatable->request_id_customer!="")-  Mã giao dịch SMS:{{$datatable->request_id_customer}} @endif
                                            </h3>
                                        </span>
                                        <div class="mt-5">
                                            <span class="label label-pill label-inline label-center mr-2  label-danger">{{$datatable->item_ref->title}}</span>
                                            @if($datatable->status==0)
                                                <span class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase-auto.status.0')}}</span>
                                            @elseif($datatable->status==1)
                                                <span class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase-auto.status.1')}}</span>
                                            @elseif($datatable->status==2)
                                                <span class="label label-pill label-inline label-center mr-2  label-info">{{config('module.service-purchase-auto.status.2')}}</span>
                                            @elseif($datatable->status==3)
                                                <span class="label label-pill label-inline label-center mr-2  label-brand">{{config('module.service-purchase-auto.status.3')}}</span>
                                            @elseif($datatable->status==4)
                                                <span class="label label-pill label-inline label-center mr-2  label-success">{{config('module.service-purchase-auto.status.4')}}
                                            @endif

                                        </div>

                                        @if(Auth::user()->can('service-purchase-show-author'))
                                        <div class="m-separator m-separator--dashed"></div>
                                        <h3>Thành viên yêu cầu</h3>
                                        <div class="" style="margin: 15px 0">
                                            <a href="#" class="m-section__sub" style="font-size: 16px">{{ $datatable->author->username??'' }}</a>
                                        </div>
                                        @endif

                                        <div class="m-separator m-separator--dashed"></div>
                                        <span class="m-section__sub">
                                            <h3>Công việc</h3>
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
                                                $workname= $datatable->order_detail->where('module','service-workname');
                                            @endphp
                                            @if(!empty($workname) && count($workname)>0)
                                                @foreach( $workname as $index=> $aWorkName)
                                                    <tr>
                                                        <td>{{$index}}</td>
                                                        <td>{{$aWorkName->title}}</td>
                                                        <td>{{ currency_format($aWorkName->unit_price) }} VNĐ</td>
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
                                                    {{ currency_format($datatable->price) }} VNĐ
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
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
                                                $param_service_access= json_decode(isset($service_access->params)?$service_access->params:"");
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

                                                            @if(((isset($param_service_access->{'display_info_role'}) && in_array($datatable->item_ref->id,(array)$param_service_access->{'display_info_role'})) || ($datatable->processor->id??"" == Auth::guard()->user()->id && $datatable->status!=3) )
                                                            || Auth::user()->can('service-purchase-auto-show-link'))

                                                                @if($send_type[$index]==4)
                                                                    <a href="{{\App\Library\Helpers::DecodeJson('customer_data'.$index,$datatable->params)}}" target="_blank"><img src="{{\App\Library\Helpers::DecodeJson('customer_data'.$index,$datatable->params)}}" alt="" style="max-width: 100px;max-height: 100px;"></a>
                                                                @else

                                                                    {{\App\Library\Helpers::DecodeJson('customer_data'.$index,$datatable->params)}}
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

                                        <div class="row align-items-right">
                                            @if($datatable->status==1 || $datatable->status==2)
                                                <div class="col-sm-12 m--align-right">
                                                    <button class="btn btn-brand" data-toggle="modal" href="#edit_info">Chỉnh sửa thông tin</button>
                                                </div>
                                                <div class="modal fade" id="edit_info" tabindex="-1" role="basic" aria-hidden="true">
                                                    <div style="text-align:initial;" class="modal-dialog">
                                                        <div class="modal-content">
                                                            {{Form::open(array('route'=>array('admin.service-purchase-auto.edit-info',$datatable->id),'class'=>'m-form','method'=>'post'))}}
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Chỉnh sửa thông tin</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                            </div>
                                                            <div class="modal-body">

                                                                @php
                                                                    $send_name= \App\Library\Helpers::DecodeJson('send_name',$datatable->item_ref->params);
                                                                    $send_type= \App\Library\Helpers::DecodeJson('send_type',$datatable->item_ref->params);
                                                                @endphp
                                                                @if(!empty($send_name)&& count($send_name)>0)
                                                                    @for ($i = 0; $i < count($send_name); $i++)
                                                                        @if($send_name[$i]!=null)
                                                                            <div class="row m-form__group">
                                                                                <label class="col-sm-4 col-lg-4 col-form-label">{{$send_name[$i]}}:</label>
                                                                                {{--check trường của sendname--}}
                                                                                @if($send_type[$i]==1 || $send_type[$i]==2||$send_type[$i]==3||$send_type[$i]==8)

                                                                                    <div class="col-sm-8 col-lg-8">
                                                                                        <div class="input-group">
                                                                                            <input value="{{\App\Library\Helpers::DecodeJson('customer_data'.$i,$datatable->params)}}" type="text" class="form-control m-input m-input--air" name="customer_data{{$i}}" required="">
                                                                                        </div>
                                                                                    </div>

                                                                                @elseif($send_type[$i]==4)
                                                                                    <div class="col-sm-8 col-lg-8">
                                                                                        <div class="input-group">
                                                                                            <input type="file" required accept="image/*" class="form-control" name="customer_data{{$i}}" placeholder="{{$send_name[$i]}}">
                                                                                        </div>
                                                                                    </div>


                                                                                @elseif($send_type[$i]==5)

                                                                                    <div class="col-sm-8 col-lg-8">
                                                                                        <div class="input-group">
                                                                                            <input type="password" value="{{\App\Library\Helpers::DecodeJson('customer_data'.$i,$datatable->params)}}" required class="form-control" name="customer_data{{$i}}" placeholder="{{$send_name[$i]}}">
                                                                                        </div>
                                                                                    </div>

                                                                                @elseif($send_type[$i]==6)
                                                                                    @php
                                                                                        $send_data=\App\Library\Helpers::DecodeJson('send_data'.$i,$data->params);
                                                                                    @endphp
                                                                                    <div class="col-sm-8 col-lg-8">
                                                                                        <select name="customer_data{{$i}}" required class="mb-15 control-label bb">
                                                                                            @if(!empty($send_data))
                                                                                                @for ($sn = 0; $sn < count($send_data); $sn++)
                                                                                                    <option value="{{$sn}}" {{\App\Library\Helpers::DecodeJson('customer_data'.$i,$data->params)==$sn?"selected":""}} >{{$send_data[$sn]}}</option>
                                                                                                @endfor
                                                                                            @endif
                                                                                        </select>

                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endif
                                                                    @endfor
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal">Hủy bỏ</button>
                                                                <button type="submit" class="btn btn-primary m-btn m-btn--air">Cập nhật</button>
                                                            </div>
                                                            {{Form::close()}}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="m-separator m-separator--dashed"></div>
                                        <span class="m-section__sub"></span>

                                        <div class="m-separator m-separator--dashed"></div>
                                        <span class="m-section__sub">
                                            <h3>Tiến độ</h3>
                                        </span>

                                        <div id="table_main_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="dataTables_scroll">

                                                        <div class="dataTables_scrollBody" style="position: relative; overflow: auto; width: 100%;">
                                                            <table class="table table-striped- table-bordered table-hover table-checkable  no-footer dtr-inline" id="table_main" role="grid" aria-describedby="table_main_info" >
                                                                <thead>
                                                                <tr>
                                                                    <th class="th-index">
                                                                        #
                                                                    </th>
                                                                    <th class="">
                                                                        Người thao tác
                                                                    </th>
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
                                                                    $workflow = $datatable->order_detail->where('module','service-workflow');
                                                                @endphp

                                                                @if(!empty($workflow) && count($workflow)>0)
                                                                    @foreach( $workflow as $index=> $aWorkFlow)
                                                                        <tr>
                                                                            <td>{{$index+1}}</td>
                                                                            <td>{{$aWorkFlow->author->username??""}}</td>
                                                                            <td>{{ \App\Library\Helpers::FormatDateTime("d/m/Y H:i:s",$aWorkFlow->created_at) }}</td>
                                                                            <td>
                                                                                @if($aWorkFlow->status==0)
                                                                                    <span class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase-auto.status.0')}}</span>
                                                                                @elseif($aWorkFlow->status==1)
                                                                                    <span class="label label-pill label-inline label-center mr-2  label-warning">{{config('module.service-purchase-auto.status.1')}}</span>
                                                                                @elseif($aWorkFlow->status==2)
                                                                                    <span class="label label-pill label-inline label-center mr-2  label-info">{{config('module.service-purchase-auto.status.2')}}</span>
                                                                                @elseif($aWorkFlow->status==3)
                                                                                    <span class="label label-pill label-inline label-center mr-2  label-brand">{{config('module.service-purchase-auto.status.3')}}</span>
                                                                                @elseif($aWorkFlow->status==4)
                                                                                    <span class="label label-pill label-inline label-center mr-2  label-success">{{config('module.service-purchase-auto.status.4')}}</span>
                                                                                @elseif($aWorkFlow->status==5)
                                                                                    <span class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase-auto.status.5')}}</span>
                                                                                @elseif($aWorkFlow->status==6)
                                                                                    <span class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase-auto.status.6')}}</span>
                                                                                @elseif($aWorkFlow->status==7)
                                                                                    <span class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase-auto.status.7')}}</span>
                                                                                @elseif($aWorkFlow->status==9)
                                                                                    <span class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase-auto.status.9')}}</span>
                                                                                @elseif($aWorkFlow->status==77)
                                                                                    <span class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase-auto.status.77')}}</span>
                                                                                @elseif($aWorkFlow->status==85)
                                                                                    Ảnh kết quả bot
                                                                                @elseif($aWorkFlow->status==88)
                                                                                    <span class="label label-pill label-inline label-center mr-2  label-danger">{{config('module.service-purchase-auto.status.88')}}</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($aWorkFlow->status==85)
                                                                                    @if(isset($aWorkFlow->content))
                                                                                        <div style="background-image: url('{{ $aWorkFlow->content }}'); background-size: 400px 280px; background-repeat: no-repeat;height: 280px">

                                                                                        </div>
                                                                                    @endif
                                                                                @else
                                                                                {{$aWorkFlow->content}}
                                                                                @endif
                                                                            </td>
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

                                                    @if(Auth::user()->can('service-purchase-auto-success'))
                                                        @if($datatable->status!=6 && $datatable->status!=77 && $datatable->status!=88)
                                                            <button class="btn btn-success" type="button" data-toggle="modal" href="#successModal">
                                                                Tích hoàn thành
                                                            </button>
                                                        @endif
                                                    @endif
                                                    @if(Auth::user()->can('service-purchase-auto-lost-item'))
                                                        @if($datatable->status==6)
                                                            <button class="btn btn-success" type="button" data-toggle="modal" href="#lostItemRefundModal">
                                                                Xử lý đơn mất item có hoàn tiền
                                                            </button>
                                                            <button class="btn btn-danger" type="button" data-toggle="modal" href="#lostItemNoRefundModal">
                                                                Xử lý đơn mất item không hoàn tiền
                                                            </button>

                                                        @endif
                                                    @endif

                                                    @if(Auth::user()->can('service-purchase-auto-delete'))
                                                        @if($datatable->gate_id == 1)
                                                            @if($datatable->status==1 || $datatable->status==2 || $datatable->status==7 || $datatable->status==9 )
                                                                <button type="button" data-toggle="modal" href="#deleteModal" class="btn btn-danger">Từ chối</button>
                                                            @endif
                                                        @endif
                                                    @endif

                                                    @if(Auth::user()->can('service-purchase-edit-pengiriman') && ($datatable->idkey == "roblox_buygamepass" || $datatable->idkey == "roblox_buyserver") && ($datatable->status== 4 || $datatable->status== 10))
                                                        <button type="button" class="btn btn-success" data-toggle="modal"
                                                                href="#pengirimanModal">
                                                            Thay đổi thông tin lô hàng
                                                        </button>

                                                    @endif

                                                        <div class="modal fade" id="deleteModal" tabindex="-1" role="basic" aria-hidden="true">
                                                        <div style="text-align:initial;" class="modal-dialog">
                                                            <div class="modal-content">
                                                                {{Form::open(array('route'=>array('admin.service-purchase-auto.destroy',$datatable->id),'class'=>'m-form','method'=>'DELETE'))}}
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Từ chối yêu cầu dịch vụ</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row m-form__group">
                                                                        <label class="col-sm-4 col-lg-4 col-form-label">Lỗi thuộc về:</label>
                                                                        <div class="col-sm-8 col-lg-8">
                                                                            <div class="input-group">
                                                                                {{Form::select('mistake_by',array(''=>'-- Không chọn --')+config('module.service-purchase-auto.mistake_by'),Request::get('mistake_by'),array('required'=>'','class'=>'form-control m-input m-input--air'))}}

                                                                            </div>
                                                                        </div>


                                                                    </div>
                                                                    <div class="row m-form__group">
                                                                        <label class="col-sm-4 col-lg-4 col-form-label">Nội dung:</label>
                                                                        <div class="col-sm-8 col-lg-8">
                                                                            <div class="input-group">
                                                                                <textarea style="min-height:100px;" type="text" class="form-control m-input m-input--air" name="note" placeholder="Nội dung ít nhất 10 ký tự"></textarea>
                                                                            </div>
                                                                        </div>


                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal">Đóng</button>
                                                                    <button type="submit" class="btn btn-primary m-btn m-btn--air">Xác nhận</button>
                                                                </div>
                                                                {{ Form::close() }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="receptionModal" tabindex="-1" role="basic" aria-hidden="true">
                                                        <div style="text-align:initial;" class="modal-dialog">
                                                            <div class="modal-content">
                                                                {{Form::open(array('route'=>array('admin.service-purchase.reception',$datatable->id),'class'=>'m-form','method'=>'POST'))}}
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Tiếp nhận yêu cầu dịch vụ</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Bạn muốn tiếp nhận yêu cầu dịch vụ này?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal">Đóng</button>
                                                                    <button type="submit" class="btn btn-primary m-btn m-btn--air">Xác nhận</button>
                                                                </div>
                                                                {{ Form::close() }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="completedModal" tabindex="-1" role="basic" aria-hidden="true">
                                                        <div style="text-align:initial;" class="modal-dialog">
                                                            <div class="modal-content">
                                                                {{Form::open(array('route'=>array('admin.service-purchase.completed',$datatable->id),'class'=>'m-form','method'=>'POST'))}}
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Hoàn tất yêu cầu dịch vụ</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Bạn muốn Hoàn tất yêu cầu dịch vụ này?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal">Đóng</button>
                                                                    <button type="submit" class="btn btn-primary m-btn m-btn--air">Xác nhận</button>
                                                                </div>
                                                                {{ Form::close() }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="successModal" tabindex="-1" role="basic" aria-hidden="true">
                                                        <div style="text-align:initial;" class="modal-dialog">
                                                            <div class="modal-content">
                                                                {{Form::open(array('route'=>array('admin.service-purchase-auto.success',$datatable->id),'class'=>'m-form','method'=>'POST'))}}
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Tích thành công yêu cầu dịch vụ</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Bạn muốn tích thành công yêu cầu dịch vụ này?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal">Đóng</button>
                                                                    <button type="submit" class="btn btn-primary m-btn m-btn--air btton_successModal" >Xác nhận</button>
                                                                </div>
                                                                {{ Form::close() }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="lostItemRefundModal" tabindex="-1" role="basic" aria-hidden="true">
                                                        <div style="text-align:initial;" class="modal-dialog">
                                                            <div class="modal-content">
                                                                {{Form::open(array('route'=>array('admin.service-purchase-auto.lostitem',$datatable->id),'class'=>'m-form','method'=>'POST'))}}
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Bạn muốn xử lý mất item Hoàn tiền cho khách?</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="text-success">Bạn muốn xử lý CÓ HOÀN TIỀN cho khách?</p>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="is_refund" value="1">
                                                                    <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal">Đóng</button>
                                                                    <button type="submit" class="btn btn-primary m-btn m-btn--air">Xác nhận</button>
                                                                </div>
                                                                {{ Form::close() }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="lostItemNoRefundModal" tabindex="-1" role="basic" aria-hidden="true">
                                                        <div style="text-align:initial;" class="modal-dialog">
                                                            <div class="modal-content">
                                                                {{Form::open(array('route'=>array('admin.service-purchase-auto.lostitem',$datatable->id),'class'=>'m-form','method'=>'POST'))}}
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Bạn muốn xử lý mất item?</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="text-danger"> Bạn muốn xử lý KHÔNG HOÀN TIỀN cho khách?</p>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="is_refund" value="0">
                                                                    <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal">Đóng</button>
                                                                    <button type="submit" class="btn btn-primary m-btn m-btn--air">Xác nhận</button>
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

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{ Form::close() }}


    <div class="modal fade" id="pengirimanModal" tabindex="-1"
         role="basic" aria-hidden="true">
        <div style="text-align:initial;" class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.service-purchase-auto.pengiriman'),'class'=>'m-form','method'=>'POST'))}}
                <input type="hidden" name="pengiriman_id" value="{{ $datatable->id }}">
                <div class="modal-header">
                    <h4 class="modal-title">Chỉnh sửa thông tin lô hàng</h4>
                    <button type="button" class="close"
                            data-dismiss="modal"
                            aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    @php
                        $value_id_pengiriman = null;
                        if ($datatable->pengiriman_detail){
                            $value_id_pengiriman = $datatable->pengiriman_detail->title??'';
                        }else{
                            if (isset($datatable->roblox_order)){
                                $roblox_order = $datatable->roblox_order;
                                if (isset($roblox_order->bot)){
                                    $value_id_pengiriman = $roblox_order->bot->id_pengiriman??'';
                                }
                            }
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

            $('.btton_successModal').click(function (e) {
                $('#successModal').modal('hide');
            })
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
                var elem_id=$(this).prop('id');
                var height=$(this).data('height');
                height=height!=""?height:150;
                var startupMode= $(this).data('startup-mode');
                if(startupMode=="source"){
                    startupMode="source";
                }
                else{
                    startupMode="wysiwyg";
                }

                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                    height:height,
                    startupMode:startupMode,
                } );
            });
            $('.ckeditor-basic').each(function () {
                var elem_id=$(this).prop('id');
                var height=$(this).data('height');
                height=height!=""?height:150;
                CKEDITOR.replace(elem_id, {
                    filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                    filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                    filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                    filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                    filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                    filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                    height:height,
                    removeButtons: 'Source',
                } );
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
                            elemThumb.attr("src", MEDIA_URL+url);
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
                            allFiles.forEach( function( file, i ) {
                                chosenFiles += file.get('url');
                                if (i != len - 1) {
                                    chosenFiles += "|";
                                }

                                elemBoxSort.append(`<div class="image-preview-box">
                                            <img src="${MEDIA_URL+file.get('url')}" alt="" data-input="${file.get( 'url' )}">
                                            <a rel="8" class="btn btn-xs  btn-icon btn-danger btn_delete_image" data-toggle="modal" data-target="#deleteModal"><i class="la la-close"></i></a>
                                        </div>`);
                            });
                            var allImageChoose=parent.find(".image-preview-box img");
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
                                var allImageChoose=parent.find(".image-preview-box img");

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
                                var allImageChoose=parent.find(".image-preview-box img");
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
                var allImageChoose=parent.find(".image-preview-box img");

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
                var allImageChoose=parent.find(".image-preview-box img");
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
            var elem_id=$(this).prop('id');
            var height=$(this).data('height');
            height=height!=""?height:150;
            var startupMode= $(this).data('startup-mode');
            if(startupMode=="source"){
                startupMode="source";
            }
            else{
                startupMode="wysiwyg";
            }

            CKEDITOR.replace(elem_id, {
                filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                height:height,
                startupMode:startupMode,
            } );
            CKEDITOR.on('instanceReady', function(ev) {
                var editor = ev.editor;
                editor.dataProcessor.htmlFilter.addRules({
                    elements : {
                        a : function( element ) {
                            if ( !element.attributes.rel ){
                                //gets content's a href values
                                var url = element.attributes.href;

                                //extract host names from URLs (IE safe)
                                var parser = document.createElement('a');
                                parser.href = url;

                                var hostname = parser.hostname;
                                if ( hostname !== window.location.host) {
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
            var elem_id=$(this).prop('id');
            var height=$(this).data('height');
            height=height!=""?height:150;
            CKEDITOR.replace(elem_id, {
                filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                height:height,
                removeButtons: 'Source',
            } );

            CKEDITOR.on('instanceReady', function(ev) {
                var editor = ev.editor;
                editor.dataProcessor.htmlFilter.addRules({
                    elements : {
                        a : function( element ) {
                            if ( !element.attributes.rel ){
                                //gets content's a href values
                                var url = element.attributes.href;

                                //extract host names from URLs (IE safe)
                                var parser = document.createElement('a');
                                parser.href = url;

                                var hostname = parser.hostname;
                                if ( hostname !== window.location.host) {
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


