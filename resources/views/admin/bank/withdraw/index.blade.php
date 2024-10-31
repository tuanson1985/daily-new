@extends('admin._layouts.master')
@section('action_area')
    
@endsection

{{-- Content --}}
@section('content')



    {{Form::open(array('route'=>array('admin.withdraw.store'),'method'=>'POST','enctype'=>"multipart/form-data",'class'=>"m-form m-form--state"))}}

    <input type="hidden" name="submit-close" id="submit-close">

    <div class="row">
        <div class="col-lg-9">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                        </h3>
                    </div>

                </div>

                <div class="card-body">

                    <div class="text-center">
                        <h2 class="c-font-bold c-font-28">{{Auth::user()->username}}</h2>
                        <h2 class="c-font-22">{{Auth::user()->email}}</h2>
                        <h2 class="c-font-22 text-danger">{{currency_format(Auth::user()->balance)}} VNĐ</h2>
                    </div>

                    <div class="form-group m-form__group row">
                        <label class="col-sm-3 col-form-label">Ngân hàng/Ví đã tạo</label>
                        <div class="col-sm-7">
                            <select name="bank_account_id" id="bank_account_id" class="form-control ">
                                <option value="" name="bank_account_id">-- Chọn tài khoản ngân hàng/Ví nhận tiền --</option>
                                @forelse ($data as $item)
                                    @if($item->bank->bank_type==0)

                                        <option value="{{$item->id}}">{{$item->holder_name}} - {{$item->account_number}} - {{$item->bank->title}} </option>
                                    @else
                                        <option value="{{$item->id}}">{{$item->account_vi}} - {{$item->bank->title}}</option>
                                    @endif
                                @empty
                                @endforelse

                            </select>
                        </div>
                    </div>

                    <div class="block-load-info">

                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-7">
                            <button id="btn-confirm" disabled type="submit" class="btn btn-success font-weight-bolder btn-submit-custom">
									<span>
										<i class="la la-check"></i>
										<span>Xác nhận</span>
									</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{--Thông tin khác--}}


@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')


    <script src="/assets/backend/themes/plugins/custom/html-sortable/jquery.sortable.js"></script>


    <script>

        jQuery(document).ready(function () {

            $('#bank_account_id').on('change', function (e) {

                var bank_account_id = this.value;
                if(bank_account_id!=""){
                    $.get('{{route('admin.withdraw.load-info')}}' + "?id=" + bank_account_id,

                        function (data) {

                            $('.block-load-info').html(data);
                            $('#btn-confirm').prop("disabled", false); // Element(s) are now enabled.

                        })
                        .done(function () {
                        })
                        .fail(function () {
                            alert('Không tìm thấy thông tin tài khoản đã lưu');
                        })
                }
                else{
                    $('.block-load-info').html("");
                    $('#btn-confirm').prop("disabled", true); // Element(s) are now enabled.
                }

            });
        });
    </script>

@endsection


