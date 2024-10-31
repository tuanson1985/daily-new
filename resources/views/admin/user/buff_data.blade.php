{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.user.index')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>



        <div class="btn-group">
            <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain" data-submit-close="1">
                <i class="ki ki-check icon-sm"></i>
                @if(isset($data))
                    {{__('Cập nhật')}}
                @else
                    {{__('Thêm mới')}}
                @endif

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
                                 @if(isset($data))
                                    {{__('Cập nhật & tiếp tục')}}
                                @else
                                    {{__('Thêm mới & tiếp tục')}}
                                @endif
                            </span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

{{-- Content --}}
@section('content')
{{Form::open(array('route'=>array('admin.user.buff',$data->id),'method'=>'POST','id'=>'formMain','enctype'=>"multipart/form-data" , 'files' => true))}}
    <div class="row">
        <div class="col-lg-9">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                            {{__('IDOL')}} : {{$data->fullname_display}} <i class="mr-2"></i>
                        </h3>
                    </div>

                </div>

                <div class="card-body">
                    <input type="hidden" name="submit-close" id="submit-close">


                    <div class="form-group row">
                        {{--buff_rating--}}
                        <div class="col-12 col-md-6">
                            <label for="buff_rating">{{ __('Chỉ số sao rating')}} <span style="color: red">({{ __('Định dạng số - sẽ được cộng thêm với dữ liệu rating thật của Idol')}})</span></label>
                            <input type="text" name="buff_rating"
                                   value="{{ old('buff_rating', isset($meta['buff_rating']) ? $meta['buff_rating'] : null) }}"
                                   placeholder="{{ __('Rating') }}" autocomplete="off"
                                   class="form-control {{ $errors->has('buff_rating') ? ' is-invalid' : '' }}">
                            @if ($errors->has('buff_rating'))
                                <span class="form-text text-danger">{{ $errors->first('buff_rating') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        {{--buff_follow--}}
                        <div class="col-12 col-md-6">
                            <label for="buff_follow">{{ __('Chỉ số người theo dõi')}}  <span style="color: red">({{ __('Định dạng số - sẽ được cộng thêm với dữ liệu theo dõi thật của Idol')}})</span></label>
                            <input type="text" name="buff_follow"
                                value="{{ old('buff_follow', isset($meta['buff_follow']) ? $meta['buff_follow'] : null) }}"
                                placeholder="{{ __('Follow') }}" autocomplete="off"
                                class="form-control {{ $errors->has('buff_follow') ? ' is-invalid' : '' }}">
                            @if ($errors->has('buff_follow'))
                                <span class="form-text text-danger">{{ $errors->first('buff_follow') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        {{--buff_topdonate--}}
                        <div class="col-12 col-md-12">
                            <label for="buff_follow">{{ __('Chỉ số top donate')}}  <span style="color: red">({{ __('Tối đa 10 user, sẽ được cộng dồn với dữ liệu top thật')}})</span></label>
                            <table class="table table-bordered table-list">
                                <thead>
                                <tr>
                                    <th>Ảnh đại diện</th>
                                    <th class="text-success">Tên user donate</th>
                                    <th class="text-danger">Số tiền donate</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if (isset($meta['buff_donate']))
                                        @php
                                            $buff_donate = json_decode($meta['buff_donate']);
                                        @endphp

                                        @foreach ($buff_donate as $key => $item)
                                            <tr>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-8 col-md-8">
                                                            {{Form::select('buff_donate[image_user][]',config('module.user.image_fake'),old('buff_donate[image_user][]'),array('class'=>'form-control select-image','data-id'=>$key))}}
                                                        </div>
                                                        <div class="col-4 col-md-4">
                                                            <img width="40px" class="image-{{$key}}" height="40px" src="{{$item->image_user}}" alt="">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><input type="text" class="form-control" name="buff_donate[name_user][]"  value="{{$item->name_user}}"></td>
                                                <td><input type="text" class="form-control" name="buff_donate[amount_user][]"  value="{{$item->amount_user}}"></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <div class="row">
                                                    <div class="col-8 col-md-8">
                                                        {{Form::select('buff_donate[image_user][]',config('module.user.image_fake'),old('buff_donate[image_user][]'),array('class'=>'form-control select-image','data-id'=>time()))}}
                                                    </div>
                                                    <div class="col-4 col-md-4">
                                                        <img width="40px" class="image-{{time()}}" height="40px" src="{{config('module.user.image_fake.0')}}" alt="">
                                                    </div>
                                                </div>
                                            </td>
                                            <td><input type="text" class="form-control" name="buff_donate[name_user][]"  value=""></td>
                                            <td><input type="text" class="form-control" name="buff_donate[amount_user][]"  value=""></td>
                                        </tr>
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
                    </div>
                    <div class="div-image-fake" style="display: none">
                        @foreach (config('module.user.image_fake') as $key => $item)
                            <input type="text" value="{{$item}}" id="image-fake-{{$key}}">
                        @endforeach
                    </div>
                    {{-----frames_avatar------}}
                    <div class="form-group  {{ $errors->has('frames_avatar') ? ' text-danger' : '' }} ">
                        <div class="row">
                            {{-----image------}}
                            <div class="col-12 col-md-4">
                                <label for="frames_avatar">{{ __('Khung avatar') }}:</label>
                                <div class="">
                                    <div class="fileinput ck-parent" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px">

                                            @if(old('frames_avatar', isset($meta['frames_avatar']) ? $meta['frames_avatar'] : null)!="")
                                                <img class="ck-thumb" src="{{ old('frames_avatar', isset($meta['frames_avatar']) ? config('module.media.url').$meta['frames_avatar'] : null) }}">
                                            @else
                                                <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                            @endif

{{--                                            <input class="ck-input" type="hidden" name="frames_avatar" value="{{ old('frames_avatar', isset($data) ? $data->image : null) }}">--}}
                                            <input class="ck-input" type="hidden" name="frames_avatar" value=" {{ old('frames_avatar', isset($meta['frames_avatar']) ? $meta['frames_avatar'] : null) }}">


                                        </div>
                                        <div>
                                            <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                            <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                        </div>
                                    </div>
                                    @if ($errors->has('frames_avatar'))
                                        <span class="form-text text-danger">{{ $errors->first('frames_avatar') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-----frames_armorial------}}
                    <div class="form-group  {{ $errors->has('frames_armorial') ? ' text-danger' : '' }} ">
                        <div class="row">
                            {{-----image------}}
                            <div class="col-12 col-md-4">
                                <label for="frames_armorial">{{ __('Huy hiệu idol') }}:</label>
                                <div class="">
                                    <div class="fileinput ck-parent" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px">

                                            @if(old('frames_armorial', isset($meta['frames_armorial']) ? $meta['frames_armorial'] : null)!="")
{{--                                                <img class="ck-thumb" src="{{ old('image', isset($data) ? $data->image : null) }}">--}}
                                                <img class="ck-thumb" src="{{ old('frames_armorial', isset($meta['frames_armorial']) ? config('module.media.url').$meta['frames_armorial'] : null) }}">
                                            @else
                                                <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                            @endif
                                            <input class="ck-input" type="hidden" name="frames_armorial" value="{{ old('frames_armorial', isset($meta['frames_armorial']) ? $meta['frames_armorial'] : null) }}">

                                        </div>
                                        <div>
                                            <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                            <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                        </div>
                                    </div>
                                    @if ($errors->has('frames_armorial'))
                                        <span class="form-text text-danger">{{ $errors->first('frames_armorial') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">
                             {{__('Hiệu ứng')}}<i class="mr-2"></i>
                        </h3>
                    </div>
                </div>

                <div class="card-body">
                    {{-- effect_profile --}}
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="effect_profile" class="form-control-label">{{ __('Hiệu ứng trang profile') }}</label>
                            {{Form::select('effect_profile',[''=>'-- '.__('Không chọn').' --']+config('module.user.effect_profile'),old('effect_profile', isset($meta['effect_profile']) ? $meta['effect_profile'] : null),array('class'=>'form-control'))}}
                            @if($errors->has('effect_profile'))
                                <div class="form-control-feedback">{{ $errors->first('effect_profile') }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- odp_active --}}
                    {{-- <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <label for="status" class="form-control-label">{{ __('Kích hoạt ODP') }}</label>
                            {{Form::select('odp_active',[0 =>'Không', 1=>'Có'],old('odp_active', isset($data) ? $data->odp_active : null),array('class'=>'form-control'))}}
                            @if($errors->has('odp_active'))
                                <div class="form-control-feedback">{{ $errors->first('odp_active') }}</div>
                            @endif
                        </div>
                    </div> --}}

                    {{-- created_at --}}
                    <div class="form-group row">
                       <div class="col-12 col-md-12">
                           <label>{{ __('Ngày cập nhật') }}</label>
                           <div class="input-group">
                               <input type="text" class="form-control  datetimepicker-input datetimepicker-default"
                                      value="{{date('d/m/Y H:i:s') }}"
                                      placeholder="{{ __('Ngày tạo') }}" autocomplete="off"
                                      data-toggle="datetimepicker">

                               <div class="input-group-append">
                                   <span class="input-group-text"><i class="la la-calendar"></i></span>
                               </div>
                           </div>
                           @if($errors->has('created_at'))
                               <div class="form-control-feedback">{{ $errors->first('created_at') }}</div>
                           @endif
                       </div>
                    </div>


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

        jQuery(document).ready(function () {

        });

        $(document).ready(function () {

            // Demo 6
            $('.datetimepicker-default').datetimepicker({
                useCurrent: true,
                autoclose: true,
                format: "DD/MM/YYYY HH:mm:ss"
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

        });

    </script>


    <script>

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

    </script>

<script>

    $(document).ready(function () {
        $(".add-row").click(function () {
            var id =  Date.now();
            var markup = '<tr><td><div class="row"> <div class="col-8 col-md-8"><select class="form-control select-image" name="buff_donate[image_user][]" data-id="'+id+'">@foreach(config('module.user.image_fake') as $key => $cat) <option value="{{$key}}">{{$cat}}</option> @endforeach</select></div> <div class="col-4 col-md-4"><img class="image-'+id+'" width="40px" height="40px" src="{{config('module.user.image_fake.0')}}" alt=""></div></div></td><td><input type="text" class="form-control" name="buff_donate[name_user][]"/></td><td><input type="text" class="form-control" name="buff_donate[amount_user][]"/></td></tr>';
            $(".table-list tbody").append(markup);
        });

        $('body').on('change','.select-image',function(){
            var ele_id = $(this).data('id');
            var val = $(this).val();
            var img = $('#image-fake-'+val).val();
            $('.image-'+ele_id).attr('src',img);
        })
    });
</script>



@endsection


