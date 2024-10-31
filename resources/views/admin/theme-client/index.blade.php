{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')

@endsection

@section('content')
    @if(session('shop_id'))
        <div class="card card-custom" id="kt_page_sticky_card">
            <meta name="csrf-token" content="{{ csrf_token() }}" />
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">
                        {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                    </h3>
                </div>

            </div>


            <div class="card-body">

                <ul class="nav nav-tabs" role="tablist">

                    <li class="nav-item nav-item-replication">
                        <a class="nav-link show active nav_thong-tin-hien-thi" data-toggle="tab" href="#system" role="tab" aria-selected="true">
                            <span class="nav-text">CH theme </span>
                        </a>
                    </li>

                    <li class="nav-item nav-item-replication cpthanhtoan">
                        <a class="nav-link" data-toggle="tab" href="#expenses" role="tab" aria-selected="false">
                            <span class="nav-text">CH component</span>
                        </a>
                    </li>


                    <li class="nav-item nav-item-replication">
                        <a class="nav-link" data-toggle="tab" href="#desc-seo" role="tab" aria-selected="false">
                            <span class="nav-text">CH giao diện</span>
                        </a>
                    </li>

                    <li class="nav-item nav-item-replication">
                        <a class="nav-link" data-toggle="tab" href="#display-module" role="tab" aria-selected="false">
                            <span class="nav-text">CH module</span>
                        </a>
                    </li>

                    <li class="nav-item nav-item-replication">
                        <a class="nav-link" data-toggle="tab" href="#server-image" role="tab" aria-selected="false">
                            <span class="nav-text">CH server ảnh</span>
                        </a>
                    </li>

                    <li class="nav-item nav-item-replication">
                        <a class="nav-link" data-toggle="tab" href="#category-option" role="tab" aria-selected="false">
                            <span class="nav-text">Lựa chọn DM hiển thị</span>
                        </a>
                    </li>

                    <li class="nav-item nav-item-replication">
                        <a class="nav-link" data-toggle="tab" href="#server-api" role="tab" aria-selected="false">
                            <span class="nav-text">Chuyển link redireck 301</span>
                        </a>
                    </li>
                    {{--                    <li class="nav-item nav-item-replication">--}}
                    {{--                        <a class="nav-link" data-toggle="tab" href="#category-custom-option" role="tab" aria-selected="false">--}}
                    {{--                            <span class="nav-text">Lựa chọn DM custom</span>--}}
                    {{--                        </a>--}}
                    {{--                    </li>--}}

                    {{--                    <li class="nav-item nav-item-replication">--}}
                    {{--                        <a class="nav-link" data-toggle="tab" href="#choice-category-option" role="tab" aria-selected="false">--}}
                    {{--                            <span class="nav-text">Gom DM</span>--}}
                    {{--                        </a>--}}
                    {{--                    </li>--}}

                </ul>

                <div class="tab-content tab-content-replication">
                    <!-- Thông tin hiển thị -->
                    <div class="tab-pane show active" id="system" role="tabpanel">
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-12 left-right blook-item-title">
                                        <div class="row marginauto">
                                            <div class="col-4 p-0">
                                                <span>Chọn theme:</span>
                                            </div>
                                            <div class="col-auto pr-0" style="margin-left: auto">
                                                <div class="d-flex align-items-center text-right">
                                                    <div class="btn-group">
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-success font-weight-bolder btn-submit-custom" data-form="formMain">
                                                                <i class="ki ki-check icon-sm"></i>
                                                                {{__('Cập nhật')}}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 left-right blook-item-body">

                                        <form action="" id="formMain" method="post" class="form" enctype="multipart/form-data">
                                        @csrf
                                        <!--begin: Search Form-->
                                            @if(isset($theme))
                                                <div class="form-group m-form__group ">
                                                    {{--                                                <label for="sys_theme_config_theme">Chọn theme:</label>--}}
                                                    <div class="row" style="width: 100%;margin: 0 auto">
                                                        <div class="col-md-6 pl-0 pr-0" >
                                                            <select name="theme_id" class="form-control col-md-12" id="theme_id">
                                                                @if($themeclient)
                                                                    @foreach($theme as $index=>$item)
                                                                        @if($themeclient->theme_id == $item->id)
                                                                            <option selected value="{{$item->id}}">{{$item->title}}</option>
                                                                        @else
                                                                            <option value="{{$item->id}}">{{$item->title}}</option>
                                                                        @endif

                                                                    @endforeach
                                                                @else
                                                                    @foreach($theme as $index=>$item)
                                                                        <option value="{{$item->id}}">{{$item->title}}</option>
                                                                    @endforeach
                                                                @endif

                                                            </select>
                                                        </div>
                                                        @if(isset($key_theme))
                                                            <div class="col-auto" style="padding-left: 16px;margin-top: 8px">
                                                                <a class="onclickshowclone" href="http://review.nick.vn/" target="_blank" data-title="Theme" data-theme="{{ isset($themeclient) ? $themeclient->theme_id : '' }}">Xem thử</a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            @if(!isset($key_theme))
                                                <span style="color: #DA4343;">Shop chưa được chọn loại theme vui lòng chọn theme rồi cấu hình.</span>
                                            @endif
                                            @if(isset($key_theme))
                                                <div class="area_theme area_theme" style="">
                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="title_area" style="width: 100%"><h2 style="margin-bottom: 25px;font-size: 14px;font-weight: bold;color: #094c7d;text-decoration: underline;">Cấu hình nâng cao cho giao diện được chọn</h2></div>
                                                            <div id="loadDataAttribute" style="width: 100%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="expenses" role="tabpanel">
                        <div class="row marginauto blook-row" id="kt_page_sticky_card">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-12 left-right blook-item-title">
                                        <span>Sắp xếp thứ tự vị trí component trong theme</span>
                                        <br>
                                        <small style="color: red;font-size: 12px">(Lưu ý: Hiện tại tính năng chỉ áp dụng cho theme 1.0,3.0,5.0)</small>
                                    </div>
                                    <div class="col-md-12 left-right blook-item-body">

                                        <meta name="csrf-token" content="{{ csrf_token() }}" />
                                        <div class="card-header">
                                            @if(isset($key_theme))
                                                <div class="d-flex align-items-center text-right">
                                                    <div class="btn-group">
                                                        <div class="btn-group">
                                                            <div class="btn-group mr-4">
                                                                <button type="button" class="btn btn-primary font-weight-bolder btn-nhanban-custom" data-form="formMain">
                                                                    <i class="ki ki-check icon-sm"></i>
                                                                    {{__('Nhân bản')}}
                                                                </button>
                                                            </div>
                                                            <button type="button" class="btn btn-success font-weight-bolder openWidgetModal" data-form="formMain">
                                                                <i class="ki ki-check icon-sm"></i>
                                                                {{__('Cấu hình lại')}}
                                                            </button>
                                                            @if(!isset($data))

                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="well">
                                                        <div class="lead text-right">
                                                            <div class="" style="float: right">
                                                                {{--                                @if(($module == 'menu-category' || $module == 'menu-profile' || $module == 'menu-transaction' || $module == 'article-category')  && !session('shop_id'))--}}
                                                                {{--                                    <a href="#" class="btn btn-warning m-btn clone_selected">--}}
                                                                {{--                                        {{__('Clone menu')}}--}}
                                                                {{--                                    </a>--}}
                                                                {{--                                @endif--}}
                                                                <a href="#" id="nestable-menu-action" data-action="collapse-all" class="btn btn-info m-btn">
                                                                    {{__('Thu gọn')}}
                                                                </a>
                                                                <a href="#" id="nestable-menu-checkall" data-action="0"  class="btn btn-primary m-btn">
                                                                    {{__('Chọn tất cả')}}
                                                                </a>
                                                                <a  href="#" class="btn btn-success m-btn  delete_inselected"  >
                                                                    {{__('Active các mục đã chọn')}}
                                                                </a>
                                                                <a  href="#" class="btn btn-secondary m-btn  delete_selected"  >
                                                                    {{__('Inactive các mục đã chọn')}}
                                                                </a>
                                                            </div>
                                                            <p class="success-indicator" style="display:none; margin-right: 15px;float: left;color: #34bfa3;font-size: 14px">
                                                                <span class="glyphicon glyphicon-ok"></span>   {{__('Component đã được cập nhật !')}}
                                                            </p>

                                                        </div>
                                                        <div class="" style="clear: both"></div>
                                                        <div class="dd" id="nestable">
                                                            {!! $data !!}
                                                        </div>
                                                        {{ Form::close() }}

                                                    </div>
                                                </div>
                                                <div class="col-sm-4 d-none d-sm-block">
                                                    <div class="well">
                                                        <div class="m-demo-icon">
                                                            <i class="flaticon-light icon-lg"></i> {{__('Kéo thả để sắp xếp component')}}
                                                        </div>
                                                    </div>
                                                    <div class="well">
                                                        <p>1. Sắp xếp thứ tự vị trí component trong theme chỉ áp dụng cho theme 2.0 và 3.0</p>
                                                        <p>2. Khi chưa cấu hình component vui lòng bấm thêm mới(tạo mới shop có phần chọn theme nếu đã chọn theme thì component sẽ không phải tạo).</p>
                                                        <p>3. Danh sách component hiển thị tùy vào theme cấu hình. </p>
                                                        <p>4. Sử dụng swich để thay đổi trạng thái hiển thị tắt hoặc bật trên điểm bán.</p>
                                                        <p>5. Sử dụng kéo thả để sắp xếp thứ tự hiển thị component trên điểm bán.</p>
                                                        <p>6. Sử dụng kéo thả để sắp xếp thứ tự hiển thị component trên điểm bán.</p>
                                                        <p>7. Sử dụng sửa để thay đổi tên hiển thị component trên điểm bán.</p>
                                                        <p>8. Chọn hiển thị mua thẻ theo ver nếu cấu hình loại mua thẻ.</p>
                                                        <p>9. Nhân bản chọn điểm bán cần lấy thông tin về component (lấy component theo theme).</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="desc-seo" role="tabpanel">
                        {{Form::open(array('route'=>array('admin.theme-client.build.background'),'class'=>'form-horizontal','method'=>'POST'))}}
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-12 left-right blook-item-title">

                                        <div class="row marginauto">
                                            <div class="col-6 pl-0">
                                                <span>Background - border</span>
                                            </div>
                                            <div class="col-auto pr-0" style="margin-left: auto">
                                                <div class="d-flex align-items-center text-right">
                                                    <div class="btn-group">
                                                        <div class="btn-group">
                                                            <button type="submit" class="btn btn-success font-weight-bolder">
                                                                <i class="ki ki-check icon-sm"></i>
                                                                {{__('Cập nhật')}}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-12 left-right blook-item-body">

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Background</label><span style="margin-left: 4px;font-size: 13px">(Vui lòng chọn 1 trong 2,trong trường hợp trọn 2 sẽ ưu tiên Background ảnh):</span>
                                            <div class="row marginauto">
                                                <div class="col-md-6 p-0">
                                                    <div class="row marginauto">
                                                        <div class="col-md-12 p-0">
                                                            <label for="order" style="font-size: 13px">Background màu:</label>
                                                        </div>
                                                    </div>
                                                    <div class="row marginauto">
                                                        <div class="col-auto pl-0">
                                                            <input style="width: 120px" class="form-control " type="text" name="sys_theme_background_color" value="{{ setting('sys_theme_background_color') && setting('sys_theme_background_color') != '' ? setting('sys_theme_background_color'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor">
                                                        </div>
                                                        <div class="col-auto pr-0">
                                                            <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_background_color') && setting('sys_theme_background_color') != '' ? setting('sys_theme_background_color'): '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 p-0">
                                                    <div class="row marginauto">
                                                        <div class="col-md-12 p-0">
                                                            <label for="order" style="font-size: 13px">Background ảnh:</label>
                                                        </div>
                                                    </div>
                                                    <div class="row marginauto">
                                                        <div class="col-md-12">
                                                            <div class="">
                                                                <div class="fileinput ck-parent" data-provides="fileinput">
                                                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">

                                                                        @if(setting('sys_theme_background_image') && setting('sys_theme_background_image') != '')
                                                                            <img class="ck-thumb" src="{{ setting('sys_theme_background_image') && setting('sys_theme_background_image') != '' ? setting('sys_theme_background_image'): null }}">
                                                                        @else
                                                                            <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">
                                                                        @endif
                                                                        <input class="ck-input" type="hidden" name="sys_theme_background_image" value="{{ setting('sys_theme_background_image') && setting('sys_theme_background_image') != '' ? setting('sys_theme_background_image'): null }}">

                                                                    </div>
                                                                    <div>
                                                                        <a href="#" class="btn red fileinput-exists ck-popup "> {{__("Thay đổi")}} </a>
                                                                        <a href="#" class="btn red fileinput-exists ck-btn-remove" > {{__("Xóa")}} </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        {{-- màu chủ đạo--}}
                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Màu chủ đạo:</label>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_primary" value="{{ setting('sys_theme_color_primary') && setting('sys_theme_color_primary') != '' ? setting('sys_theme_color_primary'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor2">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker2" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_primary') && setting('sys_theme_color_primary') != '' ? setting('sys_theme_color_primary'): '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- màu chủ đạo dạng --}}
                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Màu chủ đạo (dạng mix 2 màu) (chỉ dùng theme robux 2):</label>
                                            <p class="text-danger">(*) Shop sẽ ưu tiên dạng mix 2 màu </p>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_primary_linear1" value="{{ setting('sys_theme_color_primary_linear1') && setting('sys_theme_color_primary_linear1') != '' ? setting('sys_theme_color_primary_linear1'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor9">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker9" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_primary_linear1') && setting('sys_theme_color_primary_linear1') != '' ? setting('sys_theme_color_primary_linear1'): '' }}">
                                                </div>
                                            </div>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_primary_linear2" value="{{ setting('sys_theme_color_primary_linear2') && setting('sys_theme_color_primary_linear2') != '' ? setting('sys_theme_color_primary_linear2'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor10">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker10" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_primary_linear2') && setting('sys_theme_color_primary_linear2') != '' ? setting('sys_theme_color_primary_linear2'): '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- màu nền danh mục--}}
                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Màu nền danh mục:</label>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_bg_card" value="{{ setting('sys_theme_color_bg_card') && setting('sys_theme_color_bg_card') != '' ? setting('sys_theme_color_bg_card'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor11">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker11" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_bg_card') && setting('sys_theme_color_bg_card') != '' ? setting('sys_theme_color_bg_card'): '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Màu viền form--}}
                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Màu viền form:</label>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_border_form" value="{{ setting('sys_theme_color_border_form') && setting('sys_theme_color_border_form') != '' ? setting('sys_theme_color_border_form'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor12">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker12" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_border_form') && setting('sys_theme_color_border_form') != '' ? setting('sys_theme_color_border_form'): '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Màu viền danh mục--}}
                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Màu viền danh mục:</label>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_border_card1" value="{{ setting('sys_theme_color_border_card1') && setting('sys_theme_color_border_card1') != '' ? setting('sys_theme_color_border_card1'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor13">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker13" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_border_card1') && setting('sys_theme_color_border_card1') != '' ? setting('sys_theme_color_border_card1'): '' }}">
                                                </div>
                                            </div>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_border_card2" value="{{ setting('sys_theme_color_border_card2') && setting('sys_theme_color_border_card2') != '' ? setting('sys_theme_color_border_card2'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor14">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker14" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_border_card2') && setting('sys_theme_color_border_card2') != '' ? setting('sys_theme_color_border_card2'): '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- màu chữ--}}
                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Màu chữ:</label>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_text" value="{{ setting('sys_theme_color_text') && setting('sys_theme_color_text') != '' ? setting('sys_theme_color_text'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor6">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker6" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_text') && setting('sys_theme_color_text') != '' ? setting('sys_theme_color_text'): '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- màu hover--}}
                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Màu hover:</label>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_hover" value="{{ setting('sys_theme_color_hover') && setting('sys_theme_color_hover') != '' ? setting('sys_theme_color_hover'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor3">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker3" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_hover') && setting('sys_theme_color_hover') != '' ? setting('sys_theme_color_hover'): '' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Màu click:</label>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_click" value="{{ setting('sys_theme_color_click') && setting('sys_theme_color_click') != '' ? setting('sys_theme_color_click'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor4">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker4" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_click') && setting('sys_theme_color_click') != '' ? setting('sys_theme_color_click'): '' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Màu disable:</label>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_disable" value="{{ setting('sys_theme_color_disable') && setting('sys_theme_color_disable') != '' ? setting('sys_theme_color_disable'): '' }}" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor5">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker5" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_disable') && setting('sys_theme_color_disable') != '' ? setting('sys_theme_color_disable'): '' }}">
                                                </div>
                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Màu tiêu đề danh mục trang chủ:</label>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_text_item" value="{{ setting('sys_theme_color_text_item') && setting('sys_theme_color_text_item') != '' ? setting('sys_theme_color_text_item'): '' }}" readonly="" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor7">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker7" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_text_item') && setting('sys_theme_color_text_item') != '' ? setting('sys_theme_color_text_item'): '' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Màu hover tiêu đề danh mục trang chủ:</label>
                                            <div class="row marginauto">
                                                <div class="col-auto pl-0">
                                                    <input style="width: 120px" class="form-control " type="text" name="sys_theme_color_text_item_hover" value="{{ setting('sys_theme_color_text_item_hover') && setting('sys_theme_color_text_item_hover') != '' ? setting('sys_theme_color_text_item_hover'): '' }}" readonly="" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" id="hexcolor8">
                                                </div>
                                                <div class="col-auto pr-0">
                                                    <input style="width: 65px;cursor: pointer;" class="form-control" type="color" id="colorpicker8" pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="{{ setting('sys_theme_color_text_item_hover') && setting('sys_theme_color_text_item_hover') != '' ? setting('sys_theme_color_text_item_hover'): '' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Border radius:</label>
                                            <input type="number" name="sys_theme_border_radius"
                                                   placeholder="{{ __('Border radius') }}"
                                                   class="form-control col-md-3" value="{{ setting('sys_theme_border_radius') && setting('sys_theme_border_radius') != '' ? setting('sys_theme_border_radius'): null }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Kích thước ảnh trang chủ (Width):</label>
                                            <input type="number" name="sys_theme_width_image"
                                                   placeholder="{{ __('Kích thước ảnh trang chủ (Width)') }}"
                                                   class="form-control col-md-3" value="{{ setting('sys_theme_width_image') && setting('sys_theme_width_image') != '' ? setting('sys_theme_width_image'): null }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Kích thước ảnh trang chủ (Height):</label>
                                            <input type="number" name="sys_theme_height_image"
                                                   placeholder="{{ __('Kích thước ảnh trang chủ (Height)') }}"
                                                   class="form-control col-md-3" value="{{ setting('sys_theme_height_image') && setting('sys_theme_height_image') != '' ? setting('sys_theme_height_image'): null }}">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div class="tab-pane" id="display-module" role="tabpanel">
                        {{Form::open(array('route'=>array('admin.theme-client.build.module'),'class'=>'form-horizontal','method'=>'POST'))}}
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-6 left-right blook-item-title">
                                        <span>Số lượng hàng hiển thị các module</span>
                                        <br>
                                        <small style="font-size: 12px">(Trong trường hợp không chọn hoặc chọn 1 các module hiển thị dưới dạng slider)</small>
                                    </div>
                                    <div class="col-auto pr-0" style="margin-left: auto">
                                        <div class="d-flex align-items-center text-right">
                                            <div class="btn-group">
                                                <div class="btn-group">
                                                    <button type="submit" class="btn btn-success font-weight-bolder">
                                                        <i class="ki ki-check icon-sm"></i>
                                                        {{__('Cập nhật')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 left-right blook-item-body">

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Minigame:</label>
                                            <input type="text" name="sys_theme_minigame_list"
                                                   placeholder="{{ __('Minigame') }}"
                                                   class="form-control  col-md-3" value="{{ setting('sys_theme_minigame_list') && setting('sys_theme_minigame_list') != '' ? setting('sys_theme_minigame_list'): '' }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Dịch vụ:</label>
                                            <input type="text" name="sys_theme_service_list"
                                                   placeholder="{{ __('Dịch vụ') }}"
                                                   class="form-control col-md-3" value="{{ setting('sys_theme_service_list') && setting('sys_theme_service_list') != '' ? setting('sys_theme_service_list'): '' }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Nick:</label>
                                            <input type="text" name="sys_theme_nick_list"
                                                   placeholder="{{ __('Nick') }}"
                                                   class="form-control col-md-3" value="{{ setting('sys_theme_nick_list') && setting('sys_theme_nick_list') != '' ? setting('sys_theme_nick_list'): '' }}">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        {{ Form::close() }}
                        {{Form::open(array('route'=>array('admin.theme-client.build.display-price'),'class'=>'form-horizontal','method'=>'POST'))}}
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-6 left-right blook-item-title">
                                        <span>Hiển thị giá</span>
                                        <br>
                                        <small style="font-size: 12px">(Trong trường hợp không chọn các module mặc định hiển thị giá)</small>
                                    </div>
                                    <div class="col-auto pr-0" style="margin-left: auto">
                                        <div class="d-flex align-items-center text-right">
                                            <div class="btn-group">
                                                <div class="btn-group">
                                                    <button type="submit" class="btn btn-success font-weight-bolder">
                                                        <i class="ki ki-check icon-sm"></i>
                                                        {{__('Cập nhật')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 left-right blook-item-body">

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Minigame:</label>
                                            <select class="form-control" name="sys_theme_minigame_display_price">
                                                @if(setting('sys_theme_minigame_display_price') == "")
                                                    <option value="1">Có</option>
                                                    <option value="0">Không</option>
                                                @else
                                                    @if(setting('sys_theme_minigame_display_price') == 1)
                                                        <option selected value="1">Có</option>
                                                        <option value="0">Không</option>
                                                    @else
                                                        <option value="1">Có</option>
                                                        <option selected value="0">Không</option>
                                                    @endif
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Dịch vụ:</label>
                                            <select class="form-control" name="sys_theme_service_display_price">
                                                @if(setting('sys_theme_service_display_price') == "")
                                                    <option value="1">Có</option>
                                                    <option value="0">Không</option>
                                                @else
                                                    @if(setting('sys_theme_service_display_price') == 1)
                                                        <option selected value="1">Có</option>
                                                        <option value="0">Không</option>
                                                    @else
                                                        <option value="1">Có</option>
                                                        <option selected value="0">Không</option>
                                                    @endif
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Nick:</label>
                                            <select class="form-control" name="sys_theme_nick_display_price">
                                                @if(setting('sys_theme_nick_display_price') == "")
                                                    <option value="1">Có</option>
                                                    <option value="0">Không</option>
                                                @else
                                                    @if(setting('sys_theme_nick_display_price') == 1)
                                                        <option selected value="1">Có</option>
                                                        <option value="0">Không</option>
                                                    @else
                                                        <option value="1">Có</option>
                                                        <option selected value="0">Không</option>
                                                    @endif
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div class="tab-pane" id="server-image" role="tabpanel">
                        {{Form::open(array('route'=>array('admin.theme-client.server.image'),'class'=>'form-horizontal','method'=>'POST'))}}
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-6 left-right blook-item-title">
                                        <span>Chọn server ảnh:</span>
                                        <br>
                                        <small style="font-size: 12px">(Trong trường hợp không chọn server ảnh dưới client mặc định là https://cdn.upanh.info)</small>

                                    </div>
                                    <div class="col-auto pr-0" style="margin-left: auto">
                                        <div class="d-flex align-items-center text-right">
                                            <div class="btn-group">
                                                <div class="btn-group">
                                                    <button type="submit" class="btn btn-success font-weight-bolder">
                                                        <i class="ki ki-check icon-sm"></i>
                                                        {{__('Cập nhật')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 left-right blook-item-body">

                                        <div class="form-group">
                                            <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Server:</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <select name="server_image" class="form-control select2 datatable-input server_image" id="kt_select2_2" data-placeholder="-- {{__('Chọn server ảnh')}} --"   style="width: 100%" >
                                                        @foreach(config('module.server-image.server') as $key => $item)
                                                            <option value="">Chọn server ảnh</option>
                                                            @if(setting('sys_server_image') && setting('sys_server_image') != '' && setting('sys_server_image') == $key)
                                                                <option selected value="{{ $key }}">{{ $item }}</option>
                                                            @else
                                                                <option value="{{ $key }}">{{ $item }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div class="tab-pane" id="server-api" role="tabpanel">
                        {{Form::open(array('route'=>array('admin.theme-client.server.api'),'class'=>'form-horizontal','method'=>'POST'))}}
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-6 left-right blook-item-title">
                                        <span>Chuyển link redireck 301:</span>
                                        <br>
                                        <small style="font-size: 12px">(Vui lòng bấm thêm link, điền đầy đủ link cũ và link mới và cập nhật để lưu thông tin)</small>

                                    </div>
                                    <div class="col-auto pr-0" style="margin-left: auto">
                                        <div class="d-flex align-items-center text-right">
                                            <div class="btn-group">
                                                <div class="btn-group">
                                                    <button type="submit" class="btn btn-success font-weight-bolder">
                                                        <i class="ki ki-check icon-sm"></i>
                                                        {{__('Cập nhật')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 left-right blook-item-body">

                                        <div class="form-group" style="padding-left: 16px">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Link cũ:</label>
                                                </div>
                                                <div class="col-6">
                                                    <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Link Mới:</label>
                                                </div>
                                            </div>

                                            <div class="row data__redirect">
                                                @if(setting('sys_redirect_301') == "")

                                                @else
                                                    @php
                                                        $redirect_301 = null;
                                                        $old_redirect_301 = null;
                                                        $sys_redirect_301 = json_decode(setting('sys_redirect_301'));
                                                        if (isset($sys_redirect_301)){
                                                            if ($sys_redirect_301->redirect_301){
                                                                $redirect_301 = $sys_redirect_301->redirect_301;
                                                            }
                                                            if ($sys_redirect_301->old_redirect_301){
                                                                $old_redirect_301 = $sys_redirect_301->old_redirect_301;
                                                            }
                                                        }
                                                    @endphp

                                                    @if(isset($redirect_301) && count($redirect_301) && isset($old_redirect_301) && count($old_redirect_301))
                                                        @foreach($redirect_301 as $key => $item)
                                                            <div class="col-md-12" style="padding-top: 8px;padding-bottom: 8px">
                                                                <div class="row " style="position: relative">
                                                                    <div class="col-6">
                                                                        <input type="text" class="form-control" name="old_redirect_301[]" value="{{ $item }}">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input type="text" class="form-control" name="redirect_301[]" value="{{ $old_redirect_301[$key] }}">
                                                                    </div>
                                                                </div>
                                                                <i class="la la-trash thungrac" style="position: absolute;top: 16px;left: -12px;font-size: 18px;cursor: pointer"></i>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="row" style="margin-top: 24px">
                                                <div class="col-auto ml-auto">
                                                    <button type="button" class="btn btn-primary btn__redirect">Thêm link</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div class="tab-pane" id="category-option" role="tabpanel">
                        {{Form::open(array('route'=>array('admin.theme-client.category-option'),'class'=>'form-horizontal','method'=>'POST'))}}
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-6 left-right blook-item-title">
                                        <span>Chọn danh mục hiển thị cho điểm bán:</span>
                                        {{--                                        <br>--}}
                                        {{--                                        <small style="font-size: 12px">(Trong trường hợp không cấu hình,url api điểm bán dưới client mặc định lấy trong .env)</small>--}}

                                    </div>
                                    <div class="col-auto pr-0" style="margin-left: auto">
                                        <div class="d-flex align-items-center text-right">
                                            <div class="btn-group">
                                                <div class="btn-group">
                                                    <button type="submit" class="btn btn-success font-weight-bolder">
                                                        <i class="ki ki-check icon-sm"></i>
                                                        {{__('Cập nhật')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(isset($services) && count($services))
                                        <div class="col-md-12 left-right blook-item-body">

                                            <div class="form-group">
                                                <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Danh mục dịch vụ widget 1:</label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @php

                                                            $arr_service_one = array();
                                                            if (setting('sys_service_widget_one') && setting('sys_service_widget_one') != ''){
                                                                $arr_service_one = explode('|',setting('sys_service_widget_one'));
                                                            }

                                                        @endphp
                                                        <select name="service_widget_one[]" multiple="multiple" title="Chọn dịch vụ" class="form-control select2 col-md-5 datatable-input kt_select2_service"  data-placeholder="{{__('Hoặc chọn shop')}}" style="width: 100%">
                                                            @foreach($services as $key => $service)
                                                                @if(count($arr_service_one))
                                                                    @if(in_array($service->id,$arr_service_one))
                                                                        <option selected value="{{ $service->id }}">{{ $service->title }}</option>
                                                                    @else
                                                                        <option value="{{ $service->id }}">{{ $service->title }}</option>
                                                                    @endif
                                                                @else
                                                                    <option value="{{ $service->id }}">{{ $service->title }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group" style="margin-top: 16px">
                                                    <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Danh mục dịch vụ widget 2:</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            @php

                                                                $arr_service_two = array();
                                                                if (setting('sys_service_widget_two') && setting('sys_service_widget_two') != ''){
                                                                    $arr_service_two = explode('|',setting('sys_service_widget_two'));
                                                                }

                                                            @endphp
                                                            <select name="service_widget_two[]" multiple="multiple" title="Chọn dịch vụ" class="form-control select2 col-md-5 datatable-input kt_select2_service"  data-placeholder="{{__('Hoặc chọn shop')}}" style="width: 100%">
                                                                @foreach($services as $key => $service)
                                                                    @if(count($arr_service_two))
                                                                        @if(in_array($service->id,$arr_service_two))
                                                                            <option selected value="{{ $service->id }}">{{ $service->title }}</option>
                                                                        @else
                                                                            <option value="{{ $service->id }}">{{ $service->title }}</option>
                                                                        @endif
                                                                    @else
                                                                        <option value="{{ $service->id }}">{{ $service->title }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-top: 16px">
                                                        <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Danh mục dịch vụ widget 3:</label>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                @php

                                                                    $arr_service_three = array();
                                                                    if (setting('sys_service_widget_three') && setting('sys_service_widget_three') != ''){
                                                                        $arr_service_three = explode('|',setting('sys_service_widget_three'));
                                                                    }

                                                                @endphp
                                                                <select name="service_widget_three[]" multiple="multiple" title="Chọn dịch vụ" class="form-control select2 col-md-5 datatable-input kt_select2_service"  data-placeholder="{{__('Hoặc chọn shop')}}" style="width: 100%">
                                                                    @foreach($services as $key => $service)
                                                                        @if(count($arr_service_three))
                                                                            @if(in_array($service->id,$arr_service_three))
                                                                                <option selected value="{{ $service->id }}">{{ $service->title }}</option>
                                                                            @else
                                                                                <option value="{{ $service->id }}">{{ $service->title }}</option>
                                                                            @endif
                                                                        @else
                                                                            <option value="{{ $service->id }}">{{ $service->title }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if(isset($nicks) && count($nicks))
                                                        <div class="col-md-12 left-right blook-item-body">
                                                            @php

                                                                $arr_nick_one = array();
                                                                if (setting('sys_nick_widget_one') && setting('sys_nick_widget_one') != ''){
                                                                    $arr_nick_one = explode('|',setting('sys_nick_widget_one'));
                                                                }

                                                            @endphp
                                                            <div class="form-group">
                                                                <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Danh mục nick widget 1:</label>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <select name="nick_widget_one[]" multiple="multiple" title="Chọn nick" class="form-control select2 col-md-5 datatable-input kt_select2_service"  data-placeholder="{{__('Hoặc chọn shop')}}" style="width: 100%">
                                                                            @foreach($nicks as $key => $account)
                                                                                @if(count($arr_nick_one))
                                                                                    @if(in_array($account['id'],$arr_nick_one))
                                                                                        <option selected value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                                    @else
                                                                                        <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                                    @endif
                                                                                @else
                                                                                    <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                                @endif

                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Danh mục nick widget 2:</label>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        @php

                                                                            $arr_nick_two = array();
                                                                            if (setting('sys_nick_widget_two') && setting('sys_nick_widget_two') != ''){
                                                                                $arr_nick_two = explode('|',setting('sys_nick_widget_two'));
                                                                            }

                                                                        @endphp
                                                                        <select name="nick_widget_two[]" multiple="multiple" title="Chọn nick" class="form-control select2 col-md-5 datatable-input kt_select2_service"  data-placeholder="{{__('Hoặc chọn shop')}}" style="width: 100%">
                                                                            @foreach($nicks as $key => $account)
                                                                                @if(count($arr_nick_two))
                                                                                    @if(in_array($account['id'],$arr_nick_two))
                                                                                        <option selected value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                                    @else
                                                                                        <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                                    @endif
                                                                                @else
                                                                                    <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                                @endif

                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize">Danh mục nick widget 3:</label>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        @php

                                                                            $arr_nick_three = array();
                                                                            if (setting('sys_nick_widget_three') && setting('sys_nick_widget_three') != ''){
                                                                                $arr_nick_three = explode('|',setting('sys_nick_widget_three'));
                                                                            }

                                                                        @endphp
                                                                        <select name="nick_widget_three[]" multiple="multiple" title="Chọn nick" class="form-control select2 col-md-5 datatable-input kt_select2_service"  data-placeholder="{{__('Hoặc chọn shop')}}" style="width: 100%">
                                                                            @foreach($nicks as $key => $account)
                                                                                @if(count($arr_nick_three))
                                                                                    @if(in_array($account['id'],$arr_nick_three))
                                                                                        <option selected value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                                    @else
                                                                                        <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                                    @endif
                                                                                @else
                                                                                    <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                                @endif

                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>
                                            {{ Form::close() }}
                                        </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="category-custom-option" role="tabpanel">
                        {{Form::open(array('route'=>array('admin.theme-client.category-custom-option'),'class'=>'form-horizontal','method'=>'POST'))}}
                        <div class="row marginauto blook-row">
                            <div class="col-md-12 left-right">
                                <!-- Block 1 -->
                                <div class="row marginauto blook-item-row">
                                    <div class="col-md-6 left-right blook-item-title">
                                        <span>Chọn danh mục nick custom:</span>
                                        {{--                                        <br>--}}
                                        {{--                                        <small style="font-size: 12px">(Trong trường hợp không cấu hình,url api điểm bán dưới client mặc định lấy trong .env)</small>--}}

                                    </div>
                                    <div class="col-auto pr-0" style="margin-left: auto">
                                        <div class="d-flex align-items-center text-right">
                                            <div class="btn-group">
                                                <div class="btn-group">
                                                    <button type="submit" class="btn btn-success font-weight-bolder">
                                                        <i class="ki ki-check icon-sm"></i>
                                                        {{__('Cập nhật')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if(isset($nicks) && count($nicks))
                                        @php

                                            $arr_service_custom_one = null;
                                            $nick_custom_widget_id = null;
                                             $nick_custom_widget_title_one = null;
                                             $nick_custom_widget_title_two = null;
                                             $nick_custom_widget_price_min = null;
                                             $nick_custom_widget_image_one = null;
                                             $nick_custom_widget_image_two = null;
                                             $nick_custom_widget_content_one = null;
                                             $nick_custom_widget_content_two = null;
                                             $nick_custom_widget_description_one = null;
                                             $nick_custom_widget_description_two = null;
                                             $nick_custom_widget_seo_description_one = null;
                                             $nick_custom_widget_seo_description_two = null;

                                            if (setting('sys_custom_widget_nick') && setting('sys_custom_widget_nick') != ''){
                                                $arr_service_custom_one = setting('sys_custom_widget_nick');
                                                $arr_service_custom_one = json_decode($arr_service_custom_one);
                                            }

                                            if (isset($arr_service_custom_one)){
                                                $nick_custom_widget_id = $arr_service_custom_one->nick_custom_widget_id??'';
                                                $nick_custom_widget_title_one = $arr_service_custom_one->nick_custom_widget_title_one??'';
                                                $nick_custom_widget_title_two = $arr_service_custom_one->nick_custom_widget_title_two??'';
                                                $nick_custom_widget_price_min = $arr_service_custom_one->nick_custom_widget_price_min??'';
                                                $nick_custom_widget_image_one = $arr_service_custom_one->nick_custom_widget_image_one??'';
                                                $nick_custom_widget_image_two = $arr_service_custom_one->nick_custom_widget_image_two??'';
                                                $nick_custom_widget_content_one = $arr_service_custom_one->nick_custom_widget_content_one??'';
                                                $nick_custom_widget_content_two = $arr_service_custom_one->nick_custom_widget_content_two??'';
                                                $nick_custom_widget_description_one = $arr_service_custom_one->nick_custom_widget_description_one??'';
                                                $nick_custom_widget_description_two = $arr_service_custom_one->nick_custom_widget_description_two??'';
                                                $nick_custom_widget_seo_description_one = $arr_service_custom_one->nick_custom_widget_seo_description_one??'';
                                                $nick_custom_widget_seo_description_two = $arr_service_custom_one->nick_custom_widget_seo_description_two??'';
                                            }

                                        @endphp
                                        <div class="col-md-12 left-right blook-item-body">

                                            <div class="form-group">
                                                <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize;margin-bottom: 12px">Danh mục nick custom 1:</label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="locale">{{ __('Chọn danh mục custom') }}</label>
                                                        <select name="nick_custom_widget_id[]" title="Chọn nick" class="form-control select2 col-md-5 datatable-input kt_select2_service"  data-placeholder="{{__('Chọn danh mục cần custom')}}" style="width: 100%">
                                                            @if(isset($nick_custom_widget_id[0]))
                                                                @foreach($nicks as $key => $account)
                                                                    @if((int)$nick_custom_widget_id[0] == (int)$account['id'])
                                                                        <option value="{{ $account['id'] }}" selected>{{ $account['title'] }}</option>
                                                                    @else
                                                                        <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                    @endif

                                                                @endforeach
                                                            @else
                                                                <option value="">Chọn danh mục cần custom</option>
                                                                @foreach($nicks as $key => $account)
                                                                    <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                @endforeach
                                                            @endif

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-top: 16px">
                                                    <div class="col-md-6">
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right">
                                                                <label for="locale">{{ __('Tiêu đề') }}</label>
                                                                @if(isset($nick_custom_widget_title_one[0]))
                                                                    <input type="text" name="nick_custom_widget_title_one[]" value="{{ $nick_custom_widget_title_one[0] }}" class="form-control" placeholder="Tiêu đề custom một">
                                                                @else
                                                                    <input type="text" name="nick_custom_widget_title_one[]" class="form-control" placeholder="Tiêu đề custom một">
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_description_one[0]))
                                                                    <textarea id="nick_custom_widget_description_one_1" name="nick_custom_widget_description_one[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" >{{ $nick_custom_widget_description_one[0] }}</textarea>
                                                                @else
                                                                    <textarea id="nick_custom_widget_description_one_1" name="nick_custom_widget_description_one[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" ></textarea>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Seo mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_seo_description_one[0]))
                                                                    <input type="text" id="nick_custom_widget_seo_description_one_1" name="nick_custom_widget_seo_description_one[]" value="{{ $nick_custom_widget_seo_description_one[0] }}"
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @else
                                                                    <input type="text" id="nick_custom_widget_seo_description_one_1" name="nick_custom_widget_seo_description_one[]" value=""
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @endif
                                                            </div>
                                                            {{--                                                                <div class="col-md-12 left-right" style="padding-top: 12px">--}}
                                                            {{--                                                                    <label for="locale">{{ __('Ảnh custom') }}</label>--}}
                                                            {{--                                                                    <div class="">--}}
                                                            {{--                                                                        <div class="fileinput ck-parent" data-provides="fileinput">--}}
                                                            {{--                                                                            <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">--}}
                                                            {{--                                                                                @if(isset($nick_custom_widget_image_one[0]))--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="{{ $nick_custom_widget_image_one[0] }}" alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_one[]" value="{{ $nick_custom_widget_image_one[0] }}">--}}
                                                            {{--                                                                                @else--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_one[]" value="">--}}
                                                            {{--                                                                                @endif--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                            <div>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-popup "> Thay đổi </a>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-btn-remove"> Xóa </a>--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                        </div>--}}
                                                            {{--                                                                    </div>--}}
                                                            {{--                                                                </div>--}}
                                                        </div>
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <div class="row marginauto">
                                                                    <div class="col-12 col-md-12 left-right">
                                                                        <label for="locale">{{ __('Nội dung') }}</label>
                                                                        @if(isset($nick_custom_widget_content_one[0]))
                                                                            <textarea id="nick_custom_widget_content_one_1" name="nick_custom_widget_content_one[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ $nick_custom_widget_content_one[0] }}</textarea>
                                                                        @else
                                                                            <textarea id="nick_custom_widget_content_one_1" name="nick_custom_widget_content_one[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" ></textarea>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right">
                                                                <label for="locale">{{ __('Tiêu đề') }}</label>
                                                                @if(isset($nick_custom_widget_title_two[0]))
                                                                    <input type="text" name="nick_custom_widget_title_two[]" value="{{ $nick_custom_widget_title_two[0] }}" class="form-control" placeholder="Tiêu đề custom hai">
                                                                @else
                                                                    <input type="text" name="nick_custom_widget_title_two[]" class="form-control" placeholder="Tiêu đề custom hai">
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_description_two[0]))
                                                                    <textarea id="nick_custom_widget_description_two_1" name="nick_custom_widget_description_two[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" >{{ $nick_custom_widget_description_two[0] }}</textarea>
                                                                @else
                                                                    <textarea id="nick_custom_widget_description_two_1" name="nick_custom_widget_description_two[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" ></textarea>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Seo mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_seo_description_two[0]))
                                                                    <input type="text" id="nick_custom_widget_seo_description_two_1" name="nick_custom_widget_seo_description_two[]" value="{{ $nick_custom_widget_seo_description_two[0] }}"
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @else
                                                                    <input type="text" id="nick_custom_widget_seo_description_two_1" name="nick_custom_widget_seo_description_two[]" value=""
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @endif
                                                            </div>
                                                            {{--                                                                <div class="col-md-12 left-right" style="padding-top: 12px">--}}
                                                            {{--                                                                    <label for="locale">{{ __('Ảnh custom') }}</label>--}}
                                                            {{--                                                                    <div class="">--}}
                                                            {{--                                                                        <div class="fileinput ck-parent" data-provides="fileinput">--}}
                                                            {{--                                                                            <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">--}}
                                                            {{--                                                                                @if(isset($nick_custom_widget_image_two[0]))--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="{{ $nick_custom_widget_image_two[0] }}"  alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_two[]" value="{{ $nick_custom_widget_image_two[0] }}">--}}
                                                            {{--                                                                                @else--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_two[]" value="">--}}
                                                            {{--                                                                                @endif--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                            <div>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-popup "> Thay đổi </a>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-btn-remove"> Xóa </a>--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                        </div>--}}
                                                            {{--                                                                    </div>--}}
                                                            {{--                                                                </div>--}}
                                                        </div>

                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <div class="row marginauto">
                                                                    <div class="col-12 col-md-12 left-right">
                                                                        <label for="locale">{{ __('Nội dung') }}</label>
                                                                        @if(isset($nick_custom_widget_content_two[0]))
                                                                            <textarea id="nick_custom_widget_content_two_1" name="nick_custom_widget_content_two[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ $nick_custom_widget_content_two[0] }}</textarea>
                                                                        @else
                                                                            <textarea id="nick_custom_widget_content_two_1" name="nick_custom_widget_content_two[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" ></textarea>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row" style="padding-top: 8px">
                                                    <div class="col-md-12">
                                                        <label for="locale">{{ __('Khoảng giá: (ví dụ: điền 200000 thì nick custom 1 sẽ trong khoảng giá 0 đến 200000 và nick custom 2 sẽ trong khoảng lớn hơn 200000)') }}</label>
                                                        @if(isset($nick_custom_widget_price_min[0]))
                                                            <input type="text" name="nick_custom_widget_price_min[]" value="{{ $nick_custom_widget_price_min[0] }}" class="form-control" placeholder="Khoảng giá">
                                                        @else
                                                            <input type="text" name="nick_custom_widget_price_min[]" class="form-control" placeholder="Khoảng giá">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <hr style="background: black;height: 2px">

                                            <div class="form-group">
                                                <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize;margin-bottom: 12px">Danh mục nick custom 2:</label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="locale">{{ __('Chọn danh mục custom') }}</label>
                                                        <select name="nick_custom_widget_id[]" title="Chọn nick" class="form-control select2 col-md-5 datatable-input kt_select2_service"  data-placeholder="{{__('Chọn danh mục cần custom')}}" style="width: 100%">
                                                            @if(isset($nick_custom_widget_id[1]))
                                                                @foreach($nicks as $key => $account)
                                                                    @if((int)$nick_custom_widget_id[1] == (int)$account['id'])
                                                                        <option value="{{ $account['id'] }}" selected>{{ $account['title'] }}</option>
                                                                    @else
                                                                        <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                    @endif

                                                                @endforeach
                                                            @else
                                                                <option value="">Chọn danh mục cần custom</option>
                                                                @foreach($nicks as $key => $account)
                                                                    <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                @endforeach
                                                            @endif

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-top: 16px">
                                                    <div class="col-md-6">
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right">
                                                                <label for="locale">{{ __('Tiêu đề') }}</label>
                                                                @if(isset($nick_custom_widget_title_one[1]))
                                                                    <input type="text" name="nick_custom_widget_title_one[]" value="{{ $nick_custom_widget_title_one[1] }}" class="form-control" placeholder="Tiêu đề custom một">
                                                                @else
                                                                    <input type="text" name="nick_custom_widget_title_one[]" class="form-control" placeholder="Tiêu đề custom một">
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_description_one[1]))
                                                                    <textarea id="nick_custom_widget_description_one_2" name="nick_custom_widget_description_one[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" >{{ $nick_custom_widget_description_one[1] }}</textarea>
                                                                @else
                                                                    <textarea id="nick_custom_widget_description_one_2" name="nick_custom_widget_description_one[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" ></textarea>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Seo mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_seo_description_one[1]))
                                                                    <input type="text" id="nick_custom_widget_seo_description_one_2" name="nick_custom_widget_seo_description_one[]" value="{{ $nick_custom_widget_seo_description_one[1] }}"
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @else
                                                                    <input type="text" id="nick_custom_widget_seo_description_one_2" name="nick_custom_widget_seo_description_one[]" value=""
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @endif
                                                            </div>
                                                            {{--                                                                <div class="col-md-12 left-right" style="padding-top: 12px">--}}
                                                            {{--                                                                    <div class="">--}}
                                                            {{--                                                                        <div class="fileinput ck-parent" data-provides="fileinput">--}}
                                                            {{--                                                                            <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">--}}
                                                            {{--                                                                                @if(isset($nick_custom_widget_image_one[1]))--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="{{ $nick_custom_widget_image_one[1] }}" alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_one[]" value="{{ $nick_custom_widget_image_one[1] }}">--}}
                                                            {{--                                                                                @else--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_one[]" value="">--}}
                                                            {{--                                                                                @endif--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                            <div>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-popup "> Thay đổi </a>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-btn-remove"> Xóa </a>--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                        </div>--}}
                                                            {{--                                                                    </div>--}}
                                                            {{--                                                                </div>--}}

                                                        </div>

                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <div class="row marginauto">
                                                                    <div class="col-12 col-md-12 left-right">
                                                                        <label for="locale">{{ __('Nội dung') }}</label>
                                                                        @if(isset($nick_custom_widget_content_one[1]))
                                                                            <textarea id="nick_custom_widget_content_one_2" name="nick_custom_widget_content_one[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ $nick_custom_widget_content_one[1] }}</textarea>
                                                                        @else
                                                                            <textarea id="nick_custom_widget_content_one_2" name="nick_custom_widget_content_one[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" ></textarea>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right">
                                                                <label for="locale">{{ __('Tiêu đề') }}</label>
                                                                @if(isset($nick_custom_widget_title_two[1]))
                                                                    <input type="text" name="nick_custom_widget_title_two[]" value="{{ $nick_custom_widget_title_two[1] }}" class="form-control" placeholder="Tiêu đề custom hai">
                                                                @else
                                                                    <input type="text" name="nick_custom_widget_title_two[]" class="form-control" placeholder="Tiêu đề custom hai">
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_description_two[1]))
                                                                    <textarea id="nick_custom_widget_description_two_2" name="nick_custom_widget_description_two[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" >{{ $nick_custom_widget_description_two[1] }}</textarea>
                                                                @else
                                                                    <textarea id="nick_custom_widget_description_two_2" name="nick_custom_widget_description_two[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" ></textarea>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Seo mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_seo_description_two[1]))
                                                                    <input type="text" id="nick_custom_widget_seo_description_two_2" name="nick_custom_widget_seo_description_two[]" value="{{ $nick_custom_widget_seo_description_two[1] }}"
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @else
                                                                    <input type="text" id="nick_custom_widget_seo_description_two_2" name="nick_custom_widget_seo_description_two[]" value=""
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @endif
                                                            </div>
                                                            {{--                                                                <div class="col-md-12 left-right" style="padding-top: 12px">--}}
                                                            {{--                                                                    <div class="">--}}
                                                            {{--                                                                        <div class="fileinput ck-parent" data-provides="fileinput">--}}
                                                            {{--                                                                            <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">--}}
                                                            {{--                                                                                @if(isset($nick_custom_widget_image_two[1]))--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="{{ $nick_custom_widget_image_two[1] }}"  alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_two[]" value="{{ $nick_custom_widget_image_two[1] }}">--}}
                                                            {{--                                                                                @else--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_two[]" value="">--}}
                                                            {{--                                                                                @endif--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                            <div>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-popup "> Thay đổi </a>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-btn-remove"> Xóa </a>--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                        </div>--}}
                                                            {{--                                                                    </div>--}}
                                                            {{--                                                                </div>--}}
                                                        </div>
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <div class="row marginauto">
                                                                    <div class="col-12 col-md-12 left-right">
                                                                        <label for="locale">{{ __('Nội dung') }}</label>
                                                                        @if(isset($nick_custom_widget_content_two[1]))
                                                                            <textarea id="nick_custom_widget_content_two_2" name="nick_custom_widget_content_two[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ $nick_custom_widget_content_two[1] }}</textarea>
                                                                        @else
                                                                            <textarea id="nick_custom_widget_content_two_2" name="nick_custom_widget_content_two[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" ></textarea>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-top: 8px">
                                                    <div class="col-md-12">
                                                        <label for="locale">{{ __('Khoảng giá: (ví dụ: điền 200000 thì nick custom 1 sẽ trong khoảng giá 0 đến 200000 và nick custom 2 sẽ trong khoảng lớn hơn 200000)') }}</label>
                                                        @if(isset($nick_custom_widget_price_min[1]))
                                                            <input type="text" name="nick_custom_widget_price_min[]" value="{{ $nick_custom_widget_price_min[1] }}" class="form-control" placeholder="Khoảng giá">
                                                        @else
                                                            <input type="text" name="nick_custom_widget_price_min[]" class="form-control" placeholder="Khoảng giá">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <hr style="background: black;height: 2px">
                                            <div class="form-group">
                                                <label for="order" style="font-size: 13px;font-weight: 600;text-transform: capitalize;margin-bottom: 12px">Danh mục nick custom 3:</label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="locale">{{ __('Chọn danh mục custom') }}</label>

                                                        <select name="nick_custom_widget_id[]" title="Chọn nick" class="form-control select2 col-md-5 datatable-input kt_select2_service"  data-placeholder="{{__('Chọn danh mục cần custom')}}" style="width: 100%">
                                                            @if(isset($nick_custom_widget_id[2]))
                                                                @foreach($nicks as $key => $account)
                                                                    @if((int)$nick_custom_widget_id[2] == (int)$account['id'])
                                                                        <option value="{{ $account['id'] }}" selected>{{ $account['title'] }}</option>
                                                                    @else
                                                                        <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                    @endif

                                                                @endforeach
                                                            @else
                                                                <option value="">Chọn danh mục cần custom</option>
                                                                @foreach($nicks as $key => $account)
                                                                    <option value="{{ $account['id'] }}">{{ $account['title'] }}</option>
                                                                @endforeach
                                                            @endif

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-top: 16px">
                                                    <div class="col-md-6">
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right">
                                                                <label for="locale">{{ __('Tiêu đề') }}</label>
                                                                @if(isset($nick_custom_widget_title_one[2]))
                                                                    <input type="text" name="nick_custom_widget_title_one[]" value="{{ $nick_custom_widget_title_one[2] }}" class="form-control" placeholder="Tiêu đề custom một">
                                                                @else
                                                                    <input type="text" name="nick_custom_widget_title_one[]" class="form-control" placeholder="Tiêu đề custom một">
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_description_one[2]))
                                                                    <textarea id="nick_custom_widget_description_one_3" name="nick_custom_widget_description_one[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" >{{ $nick_custom_widget_description_one[2] }}</textarea>
                                                                @else
                                                                    <textarea id="nick_custom_widget_description_one_3" name="nick_custom_widget_description_one[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" ></textarea>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Seo mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_seo_description_one[2]))
                                                                    <input type="text" id="nick_custom_widget_seo_description_one_3" name="nick_custom_widget_seo_description_one[]" value="{{ $nick_custom_widget_seo_description_one[2] }}"
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @else
                                                                    <input type="text" id="nick_custom_widget_seo_description_one_3" name="nick_custom_widget_seo_description_one[]" value=""
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @endif
                                                            </div>
                                                            {{--                                                                <div class="col-md-12 left-right" style="padding-top: 12px">--}}
                                                            {{--                                                                    <div class="">--}}
                                                            {{--                                                                        <div class="fileinput ck-parent" data-provides="fileinput">--}}
                                                            {{--                                                                            <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">--}}
                                                            {{--                                                                                @if(isset($nick_custom_widget_image_one[2]))--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="{{ $nick_custom_widget_image_one[2] }}" alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_one[]" value="{{ $nick_custom_widget_image_one[2] }}">--}}
                                                            {{--                                                                                @else--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_one[]" value="">--}}
                                                            {{--                                                                                @endif--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                            <div>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-popup "> Thay đổi </a>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-btn-remove"> Xóa </a>--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                        </div>--}}
                                                            {{--                                                                    </div>--}}
                                                            {{--                                                                </div>--}}
                                                        </div>

                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <div class="row marginauto">
                                                                    <div class="col-12 col-md-12 left-right">
                                                                        <label for="locale">{{ __('Nội dung') }}</label>
                                                                        @if(isset($nick_custom_widget_content_one[2]))
                                                                            <textarea id="nick_custom_widget_content_one_3" name="nick_custom_widget_content_one[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ $nick_custom_widget_content_one[2] }}</textarea>
                                                                        @else
                                                                            <textarea id="nick_custom_widget_content_one_3" name="nick_custom_widget_content_one[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" ></textarea>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right">
                                                                <label for="locale">{{ __('Tiêu đề') }}</label>
                                                                @if(isset($nick_custom_widget_title_two[2]))
                                                                    <input type="text" name="nick_custom_widget_title_two[]" value="{{ $nick_custom_widget_title_two[2] }}" class="form-control" placeholder="Tiêu đề custom hai">
                                                                @else
                                                                    <input type="text" name="nick_custom_widget_title_two[]" class="form-control" placeholder="Tiêu đề custom hai">
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_description_two[2]))
                                                                    <textarea id="nick_custom_widget_description_two_3" name="nick_custom_widget_description_two[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" >{{ $nick_custom_widget_description_two[2] }}</textarea>
                                                                @else
                                                                    <textarea id="nick_custom_widget_description_two_3" name="nick_custom_widget_description_two[]" class="form-control ckeditor-basic" data-height="150"  data-startup-mode="" ></textarea>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <label for="locale">{{ __('Seo mô tả') }}</label>
                                                                @if(isset($nick_custom_widget_seo_description_two[2]))
                                                                    <input type="text" id="nick_custom_widget_seo_description_two_3" name="nick_custom_widget_seo_description_two[]" value="{{ $nick_custom_widget_seo_description_two[2] }}"
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @else
                                                                    <input type="text" id="nick_custom_widget_seo_description_two_3" name="nick_custom_widget_seo_description_two[]" value=""
                                                                           placeholder=""
                                                                           class="form-control">
                                                                @endif
                                                            </div>
                                                            {{--                                                                <div class="col-md-12 left-right" style="padding-top: 12px">--}}
                                                            {{--                                                                    <div class="">--}}
                                                            {{--                                                                        <div class="fileinput ck-parent" data-provides="fileinput">--}}
                                                            {{--                                                                            <div class="fileinput-new thumbnail" style="width: 100px; height: 100px">--}}
                                                            {{--                                                                                @if(isset($nick_custom_widget_image_two[2]))--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="{{ $nick_custom_widget_image_two[2] }}"  alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_two[]" value="{{ $nick_custom_widget_image_two[2] }}">--}}
                                                            {{--                                                                                @else--}}
                                                            {{--                                                                                    <img class="ck-thumb" src="/assets/backend/themes/images/empty-photo.jpg" alt="">--}}
                                                            {{--                                                                                    <input class="ck-input" type="hidden" name="nick_custom_widget_image_two[]" value="">--}}
                                                            {{--                                                                                @endif--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                            <div>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-popup "> Thay đổi </a>--}}
                                                            {{--                                                                                <a href="#" class="btn red fileinput-exists ck-btn-remove"> Xóa </a>--}}
                                                            {{--                                                                            </div>--}}
                                                            {{--                                                                        </div>--}}
                                                            {{--                                                                    </div>--}}
                                                            {{--                                                                </div>--}}
                                                        </div>
                                                        <div class="row marginauto">
                                                            <div class="col-md-12 left-right" style="padding-top: 12px">
                                                                <div class="row marginauto">
                                                                    <div class="col-12 col-md-12 left-right">
                                                                        <label for="locale">{{ __('Nội dung') }}</label>
                                                                        @if(isset($nick_custom_widget_content_two[2]))
                                                                            <textarea id="nick_custom_widget_content_two_3" name="nick_custom_widget_content_two[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" >{{ $nick_custom_widget_content_two[2] }}</textarea>
                                                                        @else
                                                                            <textarea id="nick_custom_widget_content_two_3" name="nick_custom_widget_content_two[]" class="form-control ckeditor-source" data-height="400"   data-startup-mode="" ></textarea>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-top: 8px">
                                                    <div class="col-md-12">
                                                        <label for="locale">{{ __('Khoảng giá: (ví dụ: điền 200000 thì nick custom 1 sẽ trong khoảng giá 0 đến 200000 và nick custom 2 sẽ trong khoảng lớn hơn 200000)') }}</label>
                                                        @if(isset($nick_custom_widget_price_min[2]))
                                                            <input type="text" name="nick_custom_widget_price_min[]" value="{{ $nick_custom_widget_price_min[2] }}" class="form-control" placeholder="Khoảng giá">
                                                        @else
                                                            <input type="text" name="nick_custom_widget_price_min[]" class="form-control" placeholder="Khoảng giá">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>
                            {{ Form::close() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>


        <div class="modal fade" id="widgetModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    {{Form::open(array('route'=>array('admin.theme-client.build'),'class'=>'form-horizontal','id'=>'form-duplicate','method'=>'POST'))}}

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{__('Thêm widget hiển thị')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h3 style="font-size: 16px;padding-bottom: 16px">Bạn xác nhận cấu hình lại pages build.</h3>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                        <button type="submit" class="btn btn-primary" style="background: #1bc5bd;border: none" data-form="form-duplicate">{{__('Xác nhận')}}</button>
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="cloneModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    {{Form::open(array('route'=>array('admin.theme-client.duplicate-page-build',0),'class'=>'form-horizontal','method'=>'POST'))}}
                    <div class="modal-body">
                        <h3 style="font-size: 16px;padding-bottom: 16px">Chọn shop sẽ nhân bản:</h3>
                        <select name="shop_access" title="Chọn shop cần clone" class="form-control select2 col-md-5"  data-placeholder="{{__('Hoặc chọn shop')}}" id="kt_select2_1" style="width: 100%">
                            @foreach($client as $key => $item)
                                <option value="{{ $item->id }}">{{ $item->domain }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" class="id" value=""/>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
                        <button type="submit" class="btn btn-primary m-btn m-btn--custom">{{__('Nhân bản')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    @else
        <div class="card card-custom" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">
                        Vui lòng chọn shop để cấu hình.
                    </h3>
                </div>

            </div>
        </div>
    @endif

    <div class="modal fade" id="editTitle">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                {{Form::open(array('route'=>array('admin.theme-client.edit-title'),'class'=>'form-horizontal','id'=>'form-duplicate','method'=>'POST'))}}

                <div class="modal-body">
                    <h3 style="font-size: 16px;padding-bottom: 16px">Nhập tên cần sửa:</h3>
                    <input type="text" name="title" value="" class="title form-control">
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="group_id" class="group_id" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-primary" style="background: #1bc5bd;border: none" data-form="form-duplicate">{{__('Chỉnh sửa')}}</button>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- delete item Modal -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.theme-client.destroy-page-build',0),'class'=>'form-horizontal','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn Inactive?')}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-danger m-btn m-btn--custom">{{__('Inactive')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- delete item Modal -->
    <div class="modal fade" id="inDeleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                {{Form::open(array('route'=>array('admin.theme-client.indestroy-page-build',0),'class'=>'form-horizontal','method'=>'POST'))}}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{__('Xác nhận thao tác')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('Bạn thực sự muốn Active?')}}
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" class="id" value=""/>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Hủy')}}</button>
                    <button type="submit" class="btn btn-primary m-btn m-btn--custom">{{__('Active')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <link href="/assets/backend/assets/css/replication.css?v={{time()}}" rel="stylesheet" type="text/css"/>
@endsection

@section('scripts')
    <script>


        $(document).ready(function () {

            $('body').on('click', '.btn__redirect', function (e) {
                e.preventDefault();

                var html = `
                    <div class="col-md-12" style="padding-top: 8px;padding-bottom: 8px">
                        <div class="row " style="position: relative">
                            <div class="col-6">
                                <input type="text" class="form-control" name="old_redirect_301[]">
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" name="redirect_301[]">
                            </div>
                        </div>
                        <i class="la la-trash thungrac" style="position: absolute;top: 16px;left: -12px;font-size: 18px;cursor: pointer"></i>
                    </div>
                `;

                $('.data__redirect').append(html);
            })
            $('body').on('click', '.thungrac', function (e) {
                e.preventDefault();
                $(this).parent().remove();
            })

        });


        $('#colorpicker').on('input', function() {
            $('#hexcolor').val(this.value);
        });
        $('#hexcolor').on('input', function() {
            $('#colorpicker').val(this.value);
        });

        $('#colorpicker2').on('input', function() {
            $('#hexcolor2').val(this.value);
        });
        $('#hexcolor2').on('input', function() {
            $('#colorpicker2').val(this.value);
        });

        $('#colorpicker4').on('input', function() {
            $('#hexcolor4').val(this.value);
        });
        $('#hexcolor4').on('input', function() {
            $('#colorpicker4').val(this.value);
        });

        $('#colorpicker3').on('input', function() {
            $('#hexcolor3').val(this.value);
        });
        $('#hexcolor3').on('input', function() {
            $('#colorpicker3').val(this.value);
        });

        $('#colorpicker5').on('input', function() {
            $('#hexcolor5').val(this.value);
        });

        $('#hexcolor5').on('input', function() {
            $('#colorpicker5').val(this.value);
        });

        $('#colorpicker6').on('input', function() {
            $('#hexcolor6').val(this.value);
        });

        $('#hexcolor6').on('input', function() {
            $('#colorpicker6').val(this.value);
        });

        $('#colorpicker7').on('input', function() {
            $('#hexcolor7').val(this.value);
        });

        $('#hexcolor7').on('input', function() {
            $('#colorpicker7').val(this.value);
        });

        $('#colorpicker8').on('input', function() {
            $('#hexcolor8').val(this.value);
        });


        $('#colorpicker9').on('input', function() {
            $('#hexcolor9').val(this.value);
        });
        $('#hexcolor9').on('input', function() {
            $('#colorpicker9').val(this.value);
        });
        $('#colorpicker10').on('input', function() {
            $('#hexcolor10').val(this.value);
        });
        $('#hexcolor10').on('input', function() {
            $('#colorpicker10').val(this.value);
        });
        $('#colorpicker11').on('input', function() {
            $('#hexcolor11').val(this.value);
        });
        $('#hexcolor11').on('input', function() {
            $('#colorpicker11').val(this.value);
        });
        $('#colorpicker12').on('input', function() {
            $('#hexcolor12').val(this.value);
        });
        $('#hexcolor12').on('input', function() {
            $('#colorpicker12').val(this.value);
        });
        $('#colorpicker13').on('input', function() {
            $('#hexcolor13').val(this.value);
        });
        $('#hexcolor13').on('input', function() {
            $('#colorpicker13').val(this.value);
        });
        $('#colorpicker14').on('input', function() {
            $('#hexcolor14').val(this.value);
        });
        $('#hexcolor14').on('input', function() {
            $('#colorpicker14').val(this.value);
        });

        $('.btn-nhanban-custom').click(function (e) {
            e.preventDefault();
            var id_delete = '';
            var total = $("#nestable .nested-list-content input[type=checkbox]").length;
            if(total>0){
                if (confirm('Hiện đang có dữ liệu,nếu đồng bộ shop khác dữ liệu sẽ mất')) {}else {
                    return false;
                }
            }

            $('#cloneModal').modal('show');
        });

        $('.openWidgetModal').click(function (e) {
            e.preventDefault();

            $('#widgetModal').modal('show');
        })

        $('.btn-edit-title').click(function (e) {
            e.preventDefault();

            var title = $(this).data('title');
            $('#editTitle .title').val(title);

            var group_id = $(this).data('id');
            $('#editTitle .group_id').val(group_id);

            $('#editTitle').modal('show');
        })

        function getval(el,idHidden){
            console.log(idHidden);
            var txtSelected = $(el).find(":selected").text();
            $("#"+idHidden).val(txtSelected);
        }

        $(document).ready(function () {

            $(".kt_select2_service").select2();


            var idTheme = $("#theme_id option:selected").val();
            $("#loadDataAttribute").html("");
            loadAttributeforTheme(idTheme);
            $("#theme_id").on("change", function () {
                idTheme = $("#theme_id option:selected").val();
                $("#loadDataAttribute").html("");
                loadAttributeforTheme(idTheme);
            })
        });

        //func loadAttribute for Theme
        function loadAttributeforTheme(idTheme){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/theme/getAttribute',
                data: {
                    "idTheme":idTheme
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    if (data.status == "SUCCESS") {
                        //toast('{{__('Đã load thuộc tính trang')}}');
                        $("#loadDataAttribute").html(data.htmlAttribute);
                    } else {
                        toast(data.msg, 'error');
                        $("#loadDataAttribute").html("");
                    }
                },
                error: function (data) {
                    toast('{{__('Không thể load thuộc tính Theme. Vui lòng tải lại trang')}}', 'error');
                },
                complete: function (data) {
                    //KTUtil.btnRelease(btn);
                }
            });
        }

        $('.btn-submit-custom').click(function (e) {
            e.preventDefault();
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }

            var btn = this;
            KTUtil.btnWait(btn, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);

            var formSubmit = $('#' + $(btn).data('form'));
            var url = formSubmit.attr('action');
            $.ajax({
                type: "POST",
                url: url,
                data: formSubmit.serialize(), // serializes the form's elements.
                beforeSend: function (xhr) {
                },
                success: function (data) {
                    if (data.status == "SUCCESS") {
                        location.reload();
                        toast('{{__('Cập nhật thành công')}}');
                    } else {
                        toast(data.msg, 'error');
                    }
                },
                error: function (data) {
                    toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                },
                complete: function (data) {
                    KTUtil.btnRelease(btn);
                }
            });



        });

        $('.btn-submit-module').click(function (e) {
            e.preventDefault();
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }

            var btn = this;
            KTUtil.btnWait(btn, "spinner spinner-right spinner-white pr-15", '{{__('Chờ xử lý')}}', true);

            var formSubmit = $('#' + $(btn).data('form'));
            var url = formSubmit.attr('action');
            $.ajax({
                type: "POST",
                url: url,
                data: formSubmit.serialize(), // serializes the form's elements.
                beforeSend: function (xhr) {
                },
                success: function (data) {
                    if (data.status == "SUCCESS") {
                        location.reload();
                        toast('{{__('Cập nhật thành công')}}');
                    } else {
                        toast(data.msg, 'error');
                    }
                },
                error: function (data) {
                    toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                },
                complete: function (data) {
                    KTUtil.btnRelease(btn);
                }
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
                        elemThumb.attr("src", url);
                        elemInput.val(url);
                    });
                }
            });
        });
    </script>

    <script>


        //edit button
        $('.loadModal_toggle,.edit_toggle').each(function (index, elem) {
            $(elem).click(function (e) {

                e.preventDefault();
                $('#loadModal .modal-content').empty();
                $('#loadModal .modal-content').load($(this).data("url"),function(){
                    $('#loadModal').modal({show:true});
                    $("#kt_select2_2, #kt_select2_2_validate").select2();
                });
            });
        });

        //delete button
        $('.delete_toggle').each(function (index, elem) {
            $(elem).click(function (e) {

                e.preventDefault();
                $('#deleteModal .id').attr('value', $(elem).attr('rel'));
                $('#deleteModal').modal('toggle');
            });
        });
        //delete button all
        $('.delete_selected').click(function (e) {
            e.preventDefault();
            var id_delete = '';
            var total = $("#nestable .v_nested-list-content input[type=checkbox]:checked").length;

            if(total>0){
                var itemselect = '';
                $("#nestable input[type=checkbox]").each(function (index, elem) {
                    if ($(elem).is(':checked')) {
                        var ne_id = $(elem).attr('rel');

                        if (ne_id){
                            if (itemselect != '') {
                                itemselect += '|';
                            }

                            itemselect += ne_id;
                        }

                    }
                });
                $('#deleteModal .id').attr('value', itemselect);
                $('#deleteModal').modal('toggle');
            }
            else{
                alert('{{__('Vui lòng chọn dữ liệu cần inactive')}}');
            }

        });

        $('.delete_inselected').click(function (e) {
            e.preventDefault();
            var id_delete = '';
            var total = $("#nestable .v_nested-list-content input[type=checkbox]:checked").length;

            if(total>0){
                var itemselect = '';
                $("#nestable input[type=checkbox]").each(function (index, elem) {
                    if ($(elem).is(':checked')) {
                        var ne_id = $(elem).attr('rel');

                        if (ne_id){
                            if (itemselect != '') {
                                itemselect += '|';
                            }

                            itemselect += ne_id;
                        }

                    }
                });
                $('#inDeleteModal .id').attr('value', itemselect);
                $('#inDeleteModal').modal('toggle');
            }
            else{
                alert('{{__('Vui lòng chọn dữ liệu cần inactive')}}');
            }

        });

        //delete button all
        $('.clone_selected').click(function (e) {
            e.preventDefault();
            var id_delete = '';
            var total = $("#nestable .nested-list-content input[type=checkbox]:checked").length;
            if(total>0){
                $("#nestable input[type=checkbox]").each(function (index, elem) {
                    if ($(elem).is(':checked')) {
                        id_delete = id_delete + $(elem).attr('rel');
                        id_delete = id_delete + ',';
                        if (index !== total - 1) {

                        }
                    }
                });

                console.log(id_delete);
                $('#cloneModal .id').attr('value', id_delete);
                $('#cloneModal').modal('toggle');
            }
            else{
                alert('{{__('Vui lòng chọn dữ liệu cần clone')}}');
            }

        });

        //end delete button all

        // datatable.on("click", "#btnCheckAll", function () {
        //     $(".ckb_item input[type='checkbox']").prop('checked', this.checked).change();
        // })
        $("#nestable-menu-checkall").click(function(e) {
            e.preventDefault();
            action =$(this).attr('data-action');
            if (action == 1) {
                $(this).text('Chọn tất cả');
                $(this).attr('data-action',0);
                $(".nested-list-content .m-checkbox input[type='checkbox']").prop('checked', false).change();
            }
            else{
                $(this).text('Bỏ chọn tất cả');
                $(this).attr('data-action',1);
                $(".nested-list-content  .m-checkbox input[type='checkbox']").prop('checked', true).change();
            }

        });



        //nestable
        $(function () {
            $('.dd').nestable({
                dropCallback: function (details) {

                    var order = new Array();
                    $("li[data-id='" + details.destId + "']").find('ol:first').children().each(function (index, elem) {
                        order[index] = $(elem).attr('data-id');
                    });

                    if (order.length === 0) {
                        var rootOrder = new Array();
                        $("#nestable > ol > li").each(function (index, elem) {
                            rootOrder[index] = $(elem).attr('data-id');
                        });
                    }

                    $.post('{{route('admin.theme-client.build.order')}}',
                        {
                            _token:'{{ csrf_token() }}',
                            source: details.sourceId,
                            destination: details.destId,
                            order: JSON.stringify(order),
                            rootOrder: JSON.stringify(rootOrder)
                        },
                        function (data) {
                            // console.log('data '+data);
                        })
                        .done(function () {

                            $(".success-indicator").fadeIn(100).delay(1000).fadeOut();
                        })
                        .fail(function () {
                        })
                        .always(function () {
                        });
                }
            });


        });
        //nestable action
        $('#nestable-menu-action').on('click', function(e)
        {
            action =$(this).attr('data-action');
            if (action === 'expand-all') {


                $(this).text('Thu gọn');
                $(this).attr('data-action','collapse-all');
                //thực hiện thao tác expand-all
                $('.dd').nestable('expandAll');
            }
            else{
                $(this).text('Mở rộng');
                $(this).attr('data-action','expand-all');
                //thực hiện thao tác collapse-all
                $('.dd').nestable('collapseAll');
            }

        });
        //end nestable action
        $("#nestable .switch-status-theme").change(function () {
            var id_group = $(this).data('id');

            var c_status = $(this).find("input[type='checkbox']");
            var status;

            if (c_status.is(':checked')){
                status = 1;
            }else{
                status = 0;
            }

            $.ajax({
                type: "POST",
                url: '{{route('admin.theme-client.build.updatestatus')}}',
                data: {
                    '_token':'{{csrf_token()}}',
                    'status':status,
                    'id':id_group,
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {

                    if (data.success) {
                        // if (data.redirect + "" != "") {
                        //     location.href = data.redirect;
                        // }
                        // location.reload();
                        // location.href = data.redirect;
                        toast('{{__('Cập nhật thành công')}}');
                    } else {

                        toast(data.message, 'error');
                    }


                },
                error: function (data) {
                    toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                },
                complete: function (data) {

                }
            });

        })
        $("#nestable .checkbox_outline").change(function () {

            //click children
            $(this).closest('.dd-item').find("checkbox_outline").prop('checked', this.checked);
            var is_checked = $(this).is(':checked');


            $("#nestable .checkbox_outline").each(function (index, elem) {

                if ($(elem).is(':checked')) {
                    return false;
                }
            });
        });

        $('.ckeditor-source').each(function () {
            var elem_id=$(this).prop('id');
            var height=$(this).data('height');
            CKEDITOR.replace(elem_id, {
                filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                height:height
                // startupMode:'source',
            } );


        });

        $('.ckeditor-basic').each(function () {
            var elem_id=$(this).prop('id');
            var height=$(this).data('height');
            CKEDITOR.replace(elem_id, {
                filebrowserBrowseUrl     : "{{ route('admin.ckfinder_browser') }}",
                filebrowserImageBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Images&token=123",
                filebrowserFlashBrowseUrl: "{{ route('admin.ckfinder_browser') }}?type=Flash&token=123",
                filebrowserUploadUrl     : "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Files",
                filebrowserImageUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Images",
                filebrowserFlashUploadUrl: "{{ route('admin.ckfinder_connector') }}?command=QuickUpload&type=Flash",
                removeButtons: 'Source',
                height:height,
            } );
        });

        CKEDITOR.instances['nick_custom_widget_description_one_1'].on('change', function() {
            var value = this.document.getBody().getText();
            $('#nick_custom_widget_seo_description_one_1').val(value);
        });

        CKEDITOR.instances['nick_custom_widget_description_two_1'].on('change', function() {
            var value = this.document.getBody().getText();
            $('#nick_custom_widget_seo_description_two_1').val(value);
        });

        CKEDITOR.instances['nick_custom_widget_description_one_2'].on('change', function() {
            var value = this.document.getBody().getText();
            $('#nick_custom_widget_seo_description_one_2').val(value);
        });

        CKEDITOR.instances['nick_custom_widget_description_one_3'].on('change', function() {
            var value = this.document.getBody().getText();
            $('#nick_custom_widget_seo_description_one_3').val(value);
        });



        CKEDITOR.instances['nick_custom_widget_description_two_2'].on('change', function() {
            var value = this.document.getBody().getText();
            $('#nick_custom_widget_seo_description_two_2').val(value);
        });

        CKEDITOR.instances['nick_custom_widget_description_two_3'].on('change', function() {
            var value = this.document.getBody().getText();
            $('#nick_custom_widget_seo_description_two_3').val(value);
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
                                            <img src="${file.get( 'url' )}" alt="">
                                            <a rel="8" class="btn btn-xs  btn-icon btn-danger btn_delete_image" data-toggle="modal" data-target="#deleteModal"><i class="la la-close"></i></a>
                                        </div>`);
                        });
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




    </script>

@endsection


