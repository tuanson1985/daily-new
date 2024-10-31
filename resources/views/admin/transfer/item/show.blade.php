{{-- Extends layout --}}
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

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__('Chi tiết đơn hàng')}} <i class="mr-2"></i>
                        </h3>
                    </div>

                </div>

                <div class="card-body">
                    <span class="m-section__sub">
                        <h3 style="color: #000">#{{$data->id}} - NAP {{config('module.transfer.partner_key')}} {{$data->id}}</h3>
                    </span>
                    <div>
                        @if ($data->status == 0)
                            <span class="label label-pill label-inline label-center mr-2 label-danger"> Thất bại </span>
                        @elseif($data->status == 1)
                            <span class="label label-pill label-inline label-center mr-2  label-success"> Số tiền đúng </span>
                        @elseif($data->status == 2)
                            <span class="label label-pill label-inline label-center mr-2 label-warning"> Đang chờ </span>
                        @elseif($data->status == 3)
                            <span class="label label-pill label-inline label-center mr-2  label-info"> Số tiền sai </span>
                        @endif
                    </div>
                    <div class="m-separator m-separator--dashed" style="border-bottom: 1px dashed #ebedf2;height: 0;margin: 20px 0;"></div>
                    <span class="m-section__sub">
                        <h3>Thông tin</h3>
                    </span>
                    <table class="table">
                        <thead class="thead-default">
                            <tr>
                                <th class="th-index">
                                    #
                                </th>
                                <th class="th-name">
                                    Người dùng:
                                </th>
                                <th class="th-value">
                                    <span style="font-weight:bold;color:#000"> {{$data->author->fullname_display}} </span>
                                </th>
                            </tr>
                            <tr>
                                <th class="th-index">
                                    #
                                </th>
                                <th class="th-name">
                                    Thời gian:
                                </th>
                                <th class="th-value">
                                    <span> {{$data->created_at->format("H:i d-m-y")}} </span>
                                </th>
                            </tr>
                            <tr>
                                <th class="th-index">
                                    #
                                </th>
                                <th class="th-name">
                                    Số tiền:
                                </th>
                                <th class="th-value">
                                    <span> {{number_format($data->price)}} VND </span>
                                </th>
                            </tr>
                            <tr>
                                <th class="th-index">
                                    #
                                </th>
                                <th class="th-name">
                                    Ngân hàng:
                                </th>
                                <th class="th-value">
                                    <span> {{$data->bank->title}} </span>
                                </th>
                            </tr>
                            <tr>
                                <th class="th-index">
                                #
                                </th>
                                <th class="th-name">
                                    Chủ tài khoản:
                                </th>
                                <th class="th-value">
                                    <span> {{$data->bank->params->account_name}} </span>
                                </th>
                            </tr>
                            <tr>
                                <th class="th-index">
                                #
                                </th>
                                <th class="th-name">
                                    Số tài khoản:
                                </th>
                                <th class="th-value">
                                    <span> {{$data->bank->params->number_account}} </span>
                                </th>
                            </tr>
                            <tr>
                                <th class="th-index">
                                #
                                </th>
                                <th class="th-name">
                                    Nội dung:
                                </th>
                                <th class="th-value">
                                    <span> NAP PZ {{$data->id}} </span>
                                </th>
                            </tr>
                        </thead>
                    </table>
                    @if ($data->status == 2)
                    {{Form::open(array('url'=>array('admin-1102/transfer/update-order/'.$data->id),'class'=>'m-form','method'=>'POST'))}}
                        <div class="m-separator m-separator--dashed" style="height: 0;margin: 20px 0;"></div>
                        <div class="form-group" style="margin-bottom:20px">
                            <div class="col-3 col-md-3">
                                <span class="m-section__sub">
                                    <h3>Cập nhật đơn hàng</h3>
                                </span>
                                {{Form::select('status',config('module.'.$module.'.status'),old('status', isset($data) ? $data->status : null),array('class'=>'form-control','id' => 'select-status'))}}
                        </div>
                        <div class="form-group" id="input-money" style="margin-top:20px;display:none">
                            <div class="col-3 col-md-3">
                                <span class="m-section__sub">
                                    <h3>Số tiền thực</h3>
                                </span>
                                <input type="text" id="price" name="price" value="" placeholder="Số tiền thực" class="form-control input-price">
                                                        </div>
                        </div>
                        <div class="form-group" style="margin-bottom:20px">
                            <div style="padding: 0.65rem 1rem;">
                                <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-toggle="modal" data-target="#updateModal">
                                    <i class="ki ki-check icon-sm"></i>
                                    {{__('Cập nhật')}}
                                </button>
                            </div>
                        </div>
                        <div class="modal fade show" id="updateModal" aria-modal="true" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel"> Xác nhận thao tác</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <i aria-hidden="true" class="ki ki-close"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Bạn thực sự muốn cập nhật đơn hàng?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                        <button type="submit" class="btn btn-danger m-btn m-btn--custom btn-submit-custom" data-form="form-delete">Xác nhận </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}

                    @endif
                </div>
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
    $('#select-status').on('change',function(){
        val = $(this).val();
        if(val == 3){
            $('#input-money').css('display','block')
        }
        else{
            $('#input-money').css('display','none')
        }
    })
</script>
    
@endsection


