

<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"> Giao dịch #{{$datatable->id}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <i aria-hidden="true" class="ki ki-close"></i>
    </button>
</div>

<div class="modal-body">

    <table class="table table-striped">
        <tbody>
        <tr>
            <th colspan="2"><b>Thông tin giao dịch #{{$datatable->id}}</b></th>
        </tr>
        <tr>
            <td width="40%">Thời gian:</td>
            <td>{{\App\Library\Helpers::FormatDateTime('d/m/Y H:i:s',$datatable->created_at)}}</td>
        </tr>
        <tr>
            <td>Tài khoản:</td>
            <td>
                <a href="#" class="load-modal">
                    {{ $datatable->author->username??'' }}
                </a>
            </td>
        </tr>
        @if(isset($gametype->idkey))
            <tr>
                <td>{{ $gametype->idkey }}</td>
                <td>{{$datatable->idkey}}</td>
            </tr>
        @endif
        @if(isset($gametype->position))
            <tr>
                <td>{{ $gametype->position }}</td>
                <td>{{$datatable->title}}</td>
            </tr>
        @endif
        @php
            $parameters_3 = null;
            $parameters_4 = null;
            $parameters_5 = null;
            $g_parameters_3 = null;
            $g_parameters_4 = null;
            $g_parameters_5 = null;

            if ($datatable->payment_type != 11 && $datatable->payment_type != 12 && $datatable->payment_type != 13 && $datatable->payment_type != 14){
                if (isset($datatable) && $datatable->params){

                    $params = json_decode($datatable->params);
                    if (isset($params->parameters_3)){
                        $parameters_3 = $params->parameters_3;
                    }
                    if (isset($params->parameters_4)){
                        $parameters_4 = $params->parameters_4;
                    }
                    if (isset($params->parameters_5)){
                        $parameters_5 = $params->parameters_5;
                    }
                }
            }
            if ($gametype->parent_id != 11 && $datatable->parent_id != 12 && $datatable->parent_id != 13 && $datatable->parent_id != 14){
                if (isset($gametype) && $gametype->params){

                    $g_params = json_decode($gametype->params);
                    if (isset($g_params->parameters_3)){
                        $g_parameters_3 = $g_params->parameters_3;
                    }
                    if (isset($params->parameters_4)){
                        $g_parameters_4 = $g_params->parameters_4;
                    }
                    if (isset($params->parameters_5)){
                        $g_parameters_5 = $g_params->parameters_5;
                    }
                }
            }
        @endphp
        @if(isset($g_parameters_3))
        <tr>
            <td>{{ $g_parameters_3 }}</td>
            <td>{{ $parameters_3 }}</td>
        </tr>
        @endif
        @if(isset($g_parameters_4))
        <tr>
            <td>{{ $g_parameters_4 }}</td>
            <td>{{ $parameters_4 }}</td>
        </tr>
        @endif
        @if(isset($g_parameters_5))
        <tr>
            <td>{{ $g_parameters_5 }}</td>
            <td>{{ $parameters_5 }}</td>
        </tr>
        @endif
        <tr>
            <td>Trạng thái:</td>
            <td>

                @if($datatable->payment_type == 13 || $datatable->payment_type == 11 || $datatable->payment_type == 12 || $datatable->payment_type == 14)
                    @if($datatable->status == 0 || $datatable->status == 3 || $datatable->status == 5 || $datatable->status == 6 || $datatable->status == 77 || $datatable->status == 88)
                        Giao dịch thất bại
                    @elseif($datatable->status == 1 || $datatable->status == 9 || $datatable->status == 2)
                        Chờ xử lý
                    @elseif($datatable->status == 4)
                        Hoàn thành
                    @endif

                @else
                    {{config('module.minigame.withdraw_status.'.$datatable->status)}}
                @endif
            </td>
        </tr>
        @php
            $workname= $datatable->order_detail->where('module',config('module.minigame.module.withdraw-item'));
        @endphp
        @if(!empty($workname) && count($workname)>0)
        <tr>
            <td>Tiến độ:</td>

            <td>
                @foreach( $workname as $index=> $aWorkName)
                    {{ $index + 1 }} -
                    {{$aWorkName->content}}<br>
                @endforeach
            </td>
        </tr>
        @endif
        <tr>
            <td>Ghi chú:</td>
            <td>{{$datatable->description}}</td>
        </tr>

        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>

</div>


<script>
    $(document).ready(function () {
        $('.load-modal').each(function (index, elem) {
            $(elem).unbind().click(function (e) {
                e.preventDefault();
                e.preventDefault();
                var curModal = $('#LoadModal');
                curModal.find('.modal-content').html("<div class=\"loader\" style=\"text-align: center\"><img src=\"/assets/frontend/images/loader.gif\" style=\"width: 50px;height: 50px;\"></div>");
                curModal.modal('show').find('.modal-content').load($(elem).attr('rel'));
            });
        });
    });
</script>
