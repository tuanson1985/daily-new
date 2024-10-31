{{Form::open(array('route'=>array('admin.telecom.set-value',$data->id),'method'=>'POST','enctype'=>"multipart/form-data"))}}


<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"> {{__('Chỉnh sửa mệnh giá - '.$shop->domain)}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <i aria-hidden="true" class="ki ki-close"></i>
    </button>
</div>


<div class="modal-body">

    {{-- title --}}
    <div class="form-group {{ $errors->has('title')? 'has-danger':'' }}">
        <label class="form-control-label">Nhà mạng</label>
        <input type="text" class="form-control" name="title" readonly
               value="{{old('title', isset($data) ? $data->title : null)}}" autofocus="true">
        @if($errors->has('title'))
            <div class="form-control-feedback">{{ $errors->first('title') }}</div>
        @endif
    </div>
    {{-- key --}}
    <div class="form-group {{ $errors->has('key')? 'has-danger':'' }}">
        <label class="form-control-label">Key</label>
        <input type="text" class="form-control" name="key" readonly
               value="{{old('key', isset($data) ? $data->key : null)}}">
        @if($errors->has('key'))
            <div class="form-control-feedback">{{ $errors->first('key') }}</div>
        @endif
    </div>
    <div class="dataTables_scrollBody" style=" overflow: auto; width: 100%;">
        <table class="table table-bordered table-list">
            <thead>
            <tr>
                <th>Mệnh giá</th>
                <th class="text-success">C.K <br/> Đúng mệnh giá (%)</th>
                <th class="text-danger">C.K  <br/>Sai mệnh giá (%)</th>
                <th class="text-success">C.K (Đ.lý) <br/> Đúng mệnh giá (%)</th>
                <th class="text-danger">C.K (Đ.lý) <br/> Sai mệnh giá (%)</th>
                <th style="min-width: 120px">Trạng thái</th>
            </tr>
            </thead>
            <tbody>

            @if(!is_null($data_telecom_value) && count($data_telecom_value)>0)

                @foreach($data_telecom_value as $aData)

                    <tr>
                        <td><input type="text" class="form-control" name="amount[]"  value="{{$aData->amount}}"></td>
                        <td><input type="text" class="form-control" maxlength="4" name="ratio_true_amount[]"  value="{{number_format($aData->ratio_true_amount,1)+0}}"></td>
                        <td><input type="text" class="form-control" maxlength="4" name="ratio_false_amount[]"  value="{{number_format($aData->ratio_false_amount,1)+0}}"></td>
                        <td><input type="text" class="form-control" maxlength="4" name="agency_ratio_true_amount[]"  value="{{number_format($aData->agency_ratio_true_amount,1)+0}}"></td>
                        <td><input type="text" class="form-control" maxlength="4" name="agency_ratio_false_amount[]"  value="{{number_format($aData->agency_ratio_false_amount,1)+0}}"></td>
                        <td>
                            <select class="form-control" style="min-width: 30px;" name="status[]">
                                <option value="0" {{$aData->status==0?"selected":""}} >Tắt</option>
                                <option value="1" {{$aData->status==1?"selected":""}}>Bật</option>
                            </select>
                        </td>
                    </tr>

                @endforeach
            @endif

            </tbody>
            <tfoot>
            <tr>
                <td colspan="6">
                    <button type="button" class="btn btn-primary btn-block add-row">Thêm</button>
                </td>
            </tr>
            </tfoot>
        </table>

    </div>



    {{-- password2 --}}
    <div class="form-group {{ $errors->has('password2')? 'has-danger':'' }}">
        <label class="form-control-label">{{__('Mật khẩu cấp 2')}}</label>
        <input type="password" class="form-control" name="password2" value="">
        @if($errors->has('password2'))
            <div class="form-control-feedback">{{ $errors->first('password2') }}</div>
        @endif
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
    <button type="submit" class="btn btn-success m-btn m-btn--custom m-btn--icon">
        @if(isset($data))
            Chỉnh sửa
        @else
            Thêm mới
        @endif
    </button>
</div>
{{ Form::close() }}
<script>

    $(document).ready(function () {

        $(".add-row").click(function () {
            var markup = '<tr><td><input type="text" class="form-control" name="amount[]"  value=""/></td><td><input type="text" class="form-control" name="ratio_true_amount[]" maxlength="4"/></td><td><input type="text" class="form-control" name="ratio_false_amount[]"  maxlength="4" /></td><td><input type="text" class="form-control" name="agency_ratio_true_amount[]"  maxlength="4" /></td><td><input type="text" class="form-control" name="agency_ratio_false_amount[]" maxlength="4" /></td><td><select class="form-control" style="min-width: 30px;" name="status[]"><option value="1">Bật</option><option value="0">Tắt</option></select></td></tr>';
            $(".table-list tbody").append(markup);
        });

    });
</script>

