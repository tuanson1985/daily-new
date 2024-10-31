<div class="modal-header" style="border: none;padding-bottom: 0px">

    <ul style="width: 100%" class="nav nav-tabs  m-tabs-line m-tabs-line--2x m-tabs-line--success" role="tablist">
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link {{ isset($data)?"":"active show"}} {{old('bank_id', isset($data) ? $data->bank->bank_type : null)==0?"active show":""}}" data-toggle="tab" href="#m_tabs_6_1_{{ isset($data) ? $data->bank_id : null}}" role="tab">Ngân hàng</a>
        </li>

        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link  {{old('bank_id', isset($data) ? $data->bank->bank_type : null)==1?"active show":""}}" data-toggle="tab" href="#m_tabs_6_3_{{ isset($data) ? $data->bank_id : null}}" role="tab">Ví điện tử</a>
        </li>
    </ul>

    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <i aria-hidden="true" class="ki ki-close"></i>
    </button>
</div>


<div class="tab-content">

    <div class="tab-pane {{ isset($data)?"":"active show"}} {{old('bank_id', isset($data) ? $data->bank->bank_type : null)==0?"active show":""}}" id="m_tabs_6_1_{{ isset($data) ? $data->bank_id : null}}" role="tabpanel">
        @if(isset($data))
            {{Form::open(array('route'=>array('admin.bank-account.update',$data->id),'method'=>'PUT','enctype'=>"multipart/form-data" , 'files' => true))}}
        @else
            {{Form::open(array('route'=>array('admin.bank-account.store'),'method'=>'POST','enctype'=>"multipart/form-data"))}}
        @endif
        <div class="modal-body">

            <input type="hidden" name="bank_type" value="0">
            {{--bank_id--}}
            <div class="form-group {{ $errors->has('bank_id')? 'has-danger':'' }}">
                <label class="form-control-label">Ngân hàng:</label>
                {{Form::select('bank_id',$bank_type_0,old('bank_id', isset($data) ? $data->bank_id : null),array('class'=>'form-control col-md-4'))}}
                @if($errors->has('bank_id'))
                    <span class="form-control-feedback">{{ $errors->first('bank_id') }}</span>
                @endif
            </div>

            {{-- holder_name --}}
            <div class="form-group {{ $errors->has('holder_name')? 'has-danger':'' }}">
                <label class="form-control-label">Chủ tài khoản:</label>
                <input type="text" class="form-control" name="holder_name" value="{{old('holder_name', isset($data) ? $data->holder_name : null)}}" required autofocus="true">
                @if($errors->has('holder_name'))
                    <div class="form-control-feedback">{{ $errors->first('holder_name') }}</div>
                @endif
            </div>

            {{--account_number--}}
            <div class="form-group {{ $errors->has('account_number')? 'has-danger':'' }}">
                <label class="form-control-label">Số tài khoản:</label>
                <input type="text" class="form-control" name="account_number" value="{{old('account_number', isset($data) ? $data->account_number : null)}}" required >
                @if($errors->has('account_number'))
                    <div class="form-control-feedback">{{ $errors->first('account_number') }}</div>
                @endif
            </div>
            {{--brand--}}
            <div class="form-group {{ $errors->has('brand')? 'has-danger':'' }}">
                <label class="form-control-label">Chi nhánh:</label>
                <input type="text" class="form-control" name="brand" value="{{old('brand', isset($data) ? $data->brand : null)}}" required>
                @if($errors->has('brand'))
                    <div class="form-control-feedback">{{ $errors->first('brand') }}</div>
                @endif
            </div>


        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-success m-btn m-btn--custom m-btn--icon">
                @if(isset($data))
                    Chỉnh sửa ngân hàng
                @else
                    Thêm ngân hàng
                @endif
            </button>
        </div>
        {{ Form::close() }}
    </div>

    <div class="tab-pane {{old('bank_id', isset($data) ? $data->bank->bank_type : null)==1?"active show":""}}" id="m_tabs_6_3_{{ isset($data) ? $data->bank_id : null}}" role="tabpanel">
        @if(isset($data))
            {{Form::open(array('route'=>array('admin.bank-account.update',$data->id),'method'=>'PUT','enctype'=>"multipart/form-data" , 'files' => true))}}
        @else
            {{Form::open(array('route'=>array('admin.bank-account.store'),'method'=>'POST','enctype'=>"multipart/form-data"))}}
        @endif
        <div class="modal-body">
            <input type="hidden" name="bank_type" value="1">
            {{--bank_id--}}
            <div class="form-group {{ $errors->has('bank_id')? 'has-danger':'' }}">
                <label class="form-control-label">Ví điện tử:</label>
                {{Form::select('bank_id',$bank_type_1,old('bank_id', isset($data) ? $data->bank_id : null),array('class'=>'form-control col-md-4'))}}
                @if($errors->has('bank_id'))
                    <span class="form-control-feedback">{{ $errors->first('bank_id') }}</span>
                @endif
            </div>
            {{--account_vi--}}
            <div class="form-group {{ $errors->has('account_vi')? 'has-danger':'' }}">
                <label class="form-control-label">Tài khoản ví:</label>
                <input type="text" class="form-control" name="account_vi" value="{{old('account_vi', isset($data) ? $data->account_vi : null)}}" required >
                @if($errors->has('account_vi'))
                    <div class="form-control-feedback">{{ $errors->first('account_vi') }}</div>
                @endif
            </div>
            {{--account_vi_confirmation--}}
            <div class="form-group {{ $errors->has('account_vi_confirmation')? 'has-danger':'' }}">
                <label class="form-control-label">Nhập lại tài khoản ví:</label>
                <input type="text" class="form-control" name="account_vi_confirmation" value="{{old('account_vi_confirmation', isset($data) ? $data->account_vi_confirmation : null)}}" required >
                @if($errors->has('account_vi_confirmation'))
                    <div class="form-control-feedback">{{ $errors->first('account_vi_confirmation') }}</div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-success m-btn m-btn--custom m-btn--icon">
                @if(isset($data))
                    Chỉnh sửa ví điện tử
                @else
                    Thêm ví điện tử
                @endif
            </button>
        </div>
        {{ Form::close() }}
    </div>
</div>


{{ Form::close() }}
<script>
    $(document).ready(function () {
        //file input upload file
        $('.fileinput').fileinput();
        $(".attribute-box input[type='checkbox']").change(function () {

            //click children
            $(this).closest('li').find("input[type='checkbox']").prop('checked', this.checked);
            var is_checked = $(this).is(':checked');

        });

    });


    jQuery(document).ready(function ($) {
        for (name in CKEDITOR.instances) {
            CKEDITOR.instances[name].destroy(true);
        }
        $('.ckeditor_post').each(function () {
            CKEDITOR.replace($(this).attr('id'));
        });
    })


    $('.btn-edit-tut').on('click', function (e) {

        $(".tut_area").toggle();

    });
</script>

