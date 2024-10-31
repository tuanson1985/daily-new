{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')
    <div class="d-flex align-items-center text-right">

        {{-- <div class="btn-group">
            <a href="{{route('admin.user.create')}}" type="button" class="btn btn-success font-weight-bolder">
                <i class="fas fa-plus-circle icon-md"></i>
                {{__('Thêm mới')}}
            </a>
        </div> --}}
    </div>
@endsection

{{-- Content --}}
@section('content')

    <!--begin::Card-->
    <div class="card card-custom">
        <!--begin::Card header-->
        <div class="card-header card-header-tabs-line nav-tabs-line-3x">
            <!--begin::Toolbar-->
            <div class="card-toolbar">
                <ul class="nav nav-tabs nav-bold nav-tabs-line nav-tabs-line-3x">
                    <!--begin::Item-->
                    <li class="nav-item mr-3">
                        <a class="nav-link active" data-toggle="tab" aria-controls="kt_user_edit_tab_1" role="tab" href="#kt_user_edit_tab_1">
                            <span class="nav-icon">
                                <span class="svg-icon">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         xmlns:xlink="http://www.w3.org/1999/xlink"
                                         width="24px" height="24px" viewBox="0 0 24 24"
                                         version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none"
                                           fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                            <path
                                                d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z"
                                                fill="#000000" fill-rule="nonzero"/>
                                            <path
                                                d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z"
                                                fill="#000000" opacity="0.3"/>
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                            </span>
                            <span class="nav-text font-size-lg">{{__('Hồ sơ cá nhân')}}</span>
                        </a>
                    </li>
{{--                    @if (Auth::user()->can('profile-change-password'))--}}
                    <li class="nav-item mr-3">
                        <a class="nav-link" role="tab"   data-toggle="tab" aria-controls="kt_user_edit_tab_3" href="#kt_user_edit_tab_3">
                            <span class="nav-icon">
                                <span class="svg-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         xmlns:xlink="http://www.w3.org/1999/xlink"
                                         width="24px" height="24px" viewBox="0 0 24 24"
                                         version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none"
                                           fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path
                                                d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                fill="#000000" opacity="0.3"/>
                                            <path
                                                d="M12,11 C10.8954305,11 10,10.1045695 10,9 C10,7.8954305 10.8954305,7 12,7 C13.1045695,7 14,7.8954305 14,9 C14,10.1045695 13.1045695,11 12,11 Z"
                                                fill="#000000" opacity="0.3"/>
                                            <path
                                                d="M7.00036205,16.4995035 C7.21569918,13.5165724 9.36772908,12 11.9907452,12 C14.6506758,12 16.8360465,13.4332455 16.9988413,16.5 C17.0053266,16.6221713 16.9988413,17 16.5815,17 C14.5228466,17 11.463736,17 7.4041679,17 C7.26484009,17 6.98863236,16.6619875 7.00036205,16.4995035 Z"
                                                fill="#000000" opacity="0.3"/>
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                            </span>
                            <span class="nav-text font-size-lg">{{__('Đổi mật khẩu')}}</span>
                        </a>
                    </li>
{{--                    @endif--}}
{{--                    @if (Auth::user()->can('profile-change-password'))--}}
                    <li class="nav-item mr-3">
                        <a class="nav-link" role="tab"   data-toggle="tab" aria-controls="kt_user_edit_tab_4" href="#kt_user_edit_tab_4">
                            <span class="nav-icon">
                                <span class="svg-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         xmlns:xlink="http://www.w3.org/1999/xlink"
                                         width="24px" height="24px" viewBox="0 0 24 24"
                                         version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none"
                                           fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path
                                                d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                fill="#000000" opacity="0.3"/>
                                            <path
                                                d="M12,11 C10.8954305,11 10,10.1045695 10,9 C10,7.8954305 10.8954305,7 12,7 C13.1045695,7 14,7.8954305 14,9 C14,10.1045695 13.1045695,11 12,11 Z"
                                                fill="#000000" opacity="0.3"/>
                                            <path
                                                d="M7.00036205,16.4995035 C7.21569918,13.5165724 9.36772908,12 11.9907452,12 C14.6506758,12 16.8360465,13.4332455 16.9988413,16.5 C17.0053266,16.6221713 16.9988413,17 16.5815,17 C14.5228466,17 11.463736,17 7.4041679,17 C7.26484009,17 6.98863236,16.6619875 7.00036205,16.4995035 Z"
                                                fill="#000000" opacity="0.3"/>
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                            </span>
                            <span class="nav-text font-size-lg">{{__('Đổi mật khẩu cấp 2')}}</span>
                        </a>
                    </li>
{{--                    @endif--}}
                    <!--end::Item-->
                </ul>
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">

            <div class="tab-content">
                <!--begin::Tab-->
                <div class="tab-pane show active px-7" id="kt_user_edit_tab_1" role="tabpanel" id="kt_user_edit_tab_1">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-7 my-2">

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">Avatar</label>
                                <div class="col-9">
                                    <div class="image-input image-input-empty image-input-outline"
                                         id="kt_user_edit_avatar"
                                         style="background-image: url('{{asset('assets/backend/themes/media/users/blank.png')}}')">
                                        <div class="image-input-wrapper"></div>
                                        <label
                                            class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                            data-action="change" data-toggle="tooltip" title=""
                                            data-original-title="Change avatar">
                                            <i class="fa fa-pen icon-sm text-muted"></i>
                                            <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg"/>
                                            <input type="hidden" name="profile_avatar_remove"/>
                                        </label>
                                        <span
                                            class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                            data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                                                                        <i class="ki ki-bold-close icon-xs text-muted"></i>
                                                                    </span>
                                        <span
                                            class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                            data-action="remove" data-toggle="tooltip" title="Remove avatar">
                                                                        <i class="ki ki-bold-close icon-xs text-muted"></i>
                                                                    </span>
                                    </div>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">Email</label>
                                <div class="col-9">
                                    <div class="input-group input-group-lg ">
                                        <div class="input-group-prepend">
                                                                        <span class="input-group-text">
                                                                            <i class="la la-at"></i>
                                                                        </span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg" readonly
                                               value="{{auth()->user()->email}}" placeholder="Email"/>
                                    </div>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">{{__('Số điện thoại')}}</label>
                                <div class="col-9">
                                    <div class="input-group input-group-lg ">
                                        <div class="input-group-prepend">
                                                                        <span class="input-group-text">
                                                                            <i class="la la-phone"></i>
                                                                        </span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg" readonly
                                               value="{{auth()->user()->phone}}" placeholder="Phone"/>
                                    </div>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">{{__('Số dư')}}</label>
                                <div class="col-9">
                                    <div class="input-group input-group-lg ">
                                        <div class="input-group-prepend">
                                                                        <span class="input-group-text">
                                                                            VNĐ
                                                                        </span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg" readonly
                                               value="{{ number_format(auth()->user()->balance) }}" placeholder="{{__('Số dư')}}"/>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">{{__('Số tiền dịch vụ đang hold')}}</label>
                                <div class="col-9">
                                    <div class="input-group input-group-lg ">
                                        <div class="input-group-prepend">
                                                                        <span class="input-group-text">
                                                                            VNĐ
                                                                        </span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg" readonly
                                               value="{{ number_format($price_control_total) }}" placeholder="{{__('Số tiền dịch vụ đang hold')}}"/>
                                    </div>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">{{__('Họ')}}</label>
                                <div class="col-9">
                                    <input class="form-control form-control-lg " type="text"
                                           value="{{auth()->user()->lastname}}"/>
                                </div>
                            </div>
                            <!--end::Group-->
                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">{{__('Tên')}}</label>
                                <div class="col-9">
                                    <input class="form-control form-control-lg " type="text"
                                           value="{{auth()->user()->firstname}}"/>
                                </div>
                            </div>
                            <!--end::Group-->

                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Tab-->
                <!--begin::Tab-->
                <div class="tab-pane px-7" id="kt_user_edit_tab_2" role="tabpanel">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-7">
                            <div class="my-2">
                                <!--begin::Row-->
                                <div class="row">
                                    <label class="col-form-label col-3 text-lg-right text-left"></label>
                                    <div class="col-9">
                                        <h6 class="text-dark font-weight-bold mb-10">Account:</h6>
                                    </div>
                                </div>
                                <!--end::Row-->
                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-form-label col-3 text-lg-right text-left">Username</label>
                                    <div class="col-9">
                                        <div class="spinner spinner-sm spinner-success spinner-right spinner-input">
                                            <input class="form-control form-control-lg form-control-solid"
                                                   type="text" value="nick84"/>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Group-->
                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-form-label col-3 text-lg-right text-left">Email
                                        Address</label>
                                    <div class="col-9">
                                        <div class="input-group input-group-lg input-group-solid">
                                            <div class="input-group-prepend">
                                                                            <span class="input-group-text">
                                                                                <i class="la la-at"></i>
                                                                            </span>
                                            </div>
                                            <input type="text"
                                                   class="form-control form-control-lg form-control-solid"
                                                   value="nick.watson@loop.com" placeholder="Email"/>
                                        </div>
                                        <span class="form-text text-muted">Email will not be publicly displayed.
                                                                    <a href="#">Learn more</a>.</span>
                                    </div>
                                </div>
                                <!--end::Group-->
                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-form-label col-3 text-lg-right text-left">Language</label>
                                    <div class="col-9">
                                        <select class="form-control form-control-lg form-control-solid">
                                            <option>Select Language...</option>
                                            <option value="id">Bahasa Indonesia - Indonesian</option>
                                            <option value="msa">Bahasa Melayu - Malay</option>
                                            <option value="ca">Català - Catalan</option>
                                            <option value="cs">Čeština - Czech</option>
                                            <option value="da">Dansk - Danish</option>
                                            <option value="de">Deutsch - German</option>
                                            <option value="en" selected="selected">English</option>
                                            <option value="en-gb">English UK - British English</option>
                                            <option value="es">Español - Spanish</option>
                                            <option value="eu">Euskara - Basque (beta)</option>
                                            <option value="fil">Filipino</option>
                                            <option value="fr">Français - French</option>
                                            <option value="ga">Gaeilge - Irish (beta)</option>
                                            <option value="gl">Galego - Galician (beta)</option>
                                            <option value="hr">Hrvatski - Croatian</option>
                                            <option value="it">Italiano - Italian</option>
                                            <option value="hu">Magyar - Hungarian</option>
                                            <option value="nl">Nederlands - Dutch</option>
                                            <option value="no">Norsk - Norwegian</option>
                                            <option value="pl">Polski - Polish</option>
                                            <option value="pt">Português - Portuguese</option>
                                            <option value="ro">Română - Romanian</option>
                                            <option value="sk">Slovenčina - Slovak</option>
                                            <option value="fi">Suomi - Finnish</option>
                                            <option value="sv">Svenska - Swedish</option>
                                            <option value="vi">Tiếng Việt - Vietnamese</option>
                                            <option value="tr">Türkçe - Turkish</option>
                                            <option value="el">Ελληνικά - Greek</option>
                                            <option value="bg">Български език - Bulgarian</option>
                                            <option value="ru">Русский - Russian</option>
                                            <option value="sr">Српски - Serbian</option>
                                            <option value="uk">Українська мова - Ukrainian</option>
                                            <option value="he">עִבְרִית - Hebrew</option>
                                            <option value="ur">اردو - Urdu (beta)</option>
                                            <option value="ar">العربية - Arabic</option>
                                            <option value="fa">فارسی - Persian</option>
                                            <option value="mr">मराठी - Marathi</option>
                                            <option value="hi">हिन्दी - Hindi</option>
                                            <option value="bn">বাংলা - Bangla</option>
                                            <option value="gu">ગુજરાતી - Gujarati</option>
                                            <option value="ta">தமிழ் - Tamil</option>
                                            <option value="kn">ಕನ್ನಡ - Kannada</option>
                                            <option value="th">ภาษาไทย - Thai</option>
                                            <option value="ko">한국어 - Korean</option>
                                            <option value="ja">日本語 - Japanese</option>
                                            <option value="zh-cn">简体中文 - Simplified Chinese</option>
                                            <option value="zh-tw">繁體中文 - Traditional Chinese</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->
                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-form-label col-3 text-lg-right text-left">Time Zone</label>
                                    <div class="col-9">
                                        <select class="form-control form-control-lg form-control-solid">
                                            <option data-offset="-39600" value="International Date Line West">
                                                (GMT-11:00) International Date Line West
                                            </option>
                                            <option data-offset="-39600" value="Midway Island">(GMT-11:00) Midway
                                                Island
                                            </option>
                                            <option data-offset="-39600" value="Samoa">(GMT-11:00) Samoa</option>
                                            <option data-offset="-36000" value="Hawaii">(GMT-10:00) Hawaii</option>
                                            <option data-offset="-28800" value="Alaska">(GMT-08:00) Alaska</option>
                                            <option data-offset="-25200" value="Pacific Time (US &amp; Canada)">
                                                (GMT-07:00) Pacific Time (US &amp; Canada)
                                            </option>
                                            <option data-offset="-25200" value="Tijuana">(GMT-07:00) Tijuana
                                            </option>
                                            <option data-offset="-25200" value="Arizona">(GMT-07:00) Arizona
                                            </option>
                                            <option data-offset="-21600" value="Mountain Time (US &amp; Canada)">
                                                (GMT-06:00) Mountain Time (US &amp; Canada)
                                            </option>
                                            <option data-offset="-21600" value="Chihuahua">(GMT-06:00) Chihuahua
                                            </option>
                                            <option data-offset="-21600" value="Mazatlan">(GMT-06:00) Mazatlan
                                            </option>
                                            <option data-offset="-21600" value="Saskatchewan">(GMT-06:00)
                                                Saskatchewan
                                            </option>
                                            <option data-offset="-21600" value="Central America">(GMT-06:00) Central
                                                America
                                            </option>
                                            <option data-offset="-18000" value="Central Time (US &amp; Canada)">
                                                (GMT-05:00) Central Time (US &amp; Canada)
                                            </option>
                                            <option data-offset="-18000" value="Guadalajara">(GMT-05:00)
                                                Guadalajara
                                            </option>
                                            <option data-offset="-18000" value="Mexico City">(GMT-05:00) Mexico
                                                City
                                            </option>
                                            <option data-offset="-18000" value="Monterrey">(GMT-05:00) Monterrey
                                            </option>
                                            <option data-offset="-18000" value="Bogota">(GMT-05:00) Bogota</option>
                                            <option data-offset="-18000" value="Lima">(GMT-05:00) Lima</option>
                                            <option data-offset="-18000" value="Quito">(GMT-05:00) Quito</option>
                                            <option data-offset="-14400" value="Eastern Time (US &amp; Canada)">
                                                (GMT-04:00) Eastern Time (US &amp; Canada)
                                            </option>
                                            <option data-offset="-14400" value="Indiana (East)">(GMT-04:00) Indiana
                                                (East)
                                            </option>
                                            <option data-offset="-14400" value="Caracas">(GMT-04:00) Caracas
                                            </option>
                                            <option data-offset="-14400" value="La Paz">(GMT-04:00) La Paz</option>
                                            <option data-offset="-14400" value="Georgetown">(GMT-04:00) Georgetown
                                            </option>
                                            <option data-offset="-10800" value="Atlantic Time (Canada)">(GMT-03:00)
                                                Atlantic Time (Canada)
                                            </option>
                                            <option data-offset="-10800" value="Santiago">(GMT-03:00) Santiago
                                            </option>
                                            <option data-offset="-10800" value="Brasilia">(GMT-03:00) Brasilia
                                            </option>
                                            <option data-offset="-10800" value="Buenos Aires">(GMT-03:00) Buenos
                                                Aires
                                            </option>
                                            <option data-offset="-9000" value="Newfoundland">(GMT-02:30)
                                                Newfoundland
                                            </option>
                                            <option data-offset="-7200" value="Greenland">(GMT-02:00) Greenland
                                            </option>
                                            <option data-offset="-7200" value="Mid-Atlantic">(GMT-02:00)
                                                Mid-Atlantic
                                            </option>
                                            <option data-offset="-3600" value="Cape Verde Is.">(GMT-01:00) Cape
                                                Verde Is.
                                            </option>
                                            <option data-offset="0" value="Azores">(GMT) Azores</option>
                                            <option data-offset="0" value="Monrovia">(GMT) Monrovia</option>
                                            <option data-offset="0" value="UTC">(GMT) UTC</option>
                                            <option data-offset="3600" value="Dublin">(GMT+01:00) Dublin</option>
                                            <option data-offset="3600" value="Edinburgh">(GMT+01:00) Edinburgh
                                            </option>
                                            <option data-offset="3600" value="Lisbon">(GMT+01:00) Lisbon</option>
                                            <option data-offset="3600" value="London">(GMT+01:00) London</option>
                                            <option data-offset="3600" value="Casablanca">(GMT+01:00) Casablanca
                                            </option>
                                            <option data-offset="3600" value="West Central Africa">(GMT+01:00) West
                                                Central Africa
                                            </option>
                                            <option data-offset="7200" value="Belgrade">(GMT+02:00) Belgrade
                                            </option>
                                            <option data-offset="7200" value="Bratislava">(GMT+02:00) Bratislava
                                            </option>
                                            <option data-offset="7200" value="Budapest">(GMT+02:00) Budapest
                                            </option>
                                            <option data-offset="7200" value="Ljubljana">(GMT+02:00) Ljubljana
                                            </option>
                                            <option data-offset="7200" value="Prague">(GMT+02:00) Prague</option>
                                            <option data-offset="7200" value="Sarajevo">(GMT+02:00) Sarajevo
                                            </option>
                                            <option data-offset="7200" value="Skopje">(GMT+02:00) Skopje</option>
                                            <option data-offset="7200" value="Warsaw">(GMT+02:00) Warsaw</option>
                                            <option data-offset="7200" value="Zagreb">(GMT+02:00) Zagreb</option>
                                            <option data-offset="7200" value="Brussels">(GMT+02:00) Brussels
                                            </option>
                                            <option data-offset="7200" value="Copenhagen">(GMT+02:00) Copenhagen
                                            </option>
                                            <option data-offset="7200" value="Madrid">(GMT+02:00) Madrid</option>
                                            <option data-offset="7200" value="Paris">(GMT+02:00) Paris</option>
                                            <option data-offset="7200" value="Amsterdam">(GMT+02:00) Amsterdam
                                            </option>
                                            <option data-offset="7200" value="Berlin">(GMT+02:00) Berlin</option>
                                            <option data-offset="7200" value="Bern">(GMT+02:00) Bern</option>
                                            <option data-offset="7200" value="Rome">(GMT+02:00) Rome</option>
                                            <option data-offset="7200" value="Stockholm">(GMT+02:00) Stockholm
                                            </option>
                                            <option data-offset="7200" value="Vienna">(GMT+02:00) Vienna</option>
                                            <option data-offset="7200" value="Cairo">(GMT+02:00) Cairo</option>
                                            <option data-offset="7200" value="Harare">(GMT+02:00) Harare</option>
                                            <option data-offset="7200" value="Pretoria">(GMT+02:00) Pretoria
                                            </option>
                                            <option data-offset="10800" value="Bucharest">(GMT+03:00) Bucharest
                                            </option>
                                            <option data-offset="10800" value="Helsinki">(GMT+03:00) Helsinki
                                            </option>
                                            <option data-offset="10800" value="Kiev">(GMT+03:00) Kiev</option>
                                            <option data-offset="10800" value="Kyiv">(GMT+03:00) Kyiv</option>
                                            <option data-offset="10800" value="Riga">(GMT+03:00) Riga</option>
                                            <option data-offset="10800" value="Sofia">(GMT+03:00) Sofia</option>
                                            <option data-offset="10800" value="Tallinn">(GMT+03:00) Tallinn</option>
                                            <option data-offset="10800" value="Vilnius">(GMT+03:00) Vilnius</option>
                                            <option data-offset="10800" value="Athens">(GMT+03:00) Athens</option>
                                            <option data-offset="10800" value="Istanbul">(GMT+03:00) Istanbul
                                            </option>
                                            <option data-offset="10800" value="Minsk">(GMT+03:00) Minsk</option>
                                            <option data-offset="10800" value="Jerusalem">(GMT+03:00) Jerusalem
                                            </option>
                                            <option data-offset="10800" value="Moscow">(GMT+03:00) Moscow</option>
                                            <option data-offset="10800" value="St. Petersburg">(GMT+03:00) St.
                                                Petersburg
                                            </option>
                                            <option data-offset="10800" value="Volgograd">(GMT+03:00) Volgograd
                                            </option>
                                            <option data-offset="10800" value="Kuwait">(GMT+03:00) Kuwait</option>
                                            <option data-offset="10800" value="Riyadh">(GMT+03:00) Riyadh</option>
                                            <option data-offset="10800" value="Nairobi">(GMT+03:00) Nairobi</option>
                                            <option data-offset="10800" value="Baghdad">(GMT+03:00) Baghdad</option>
                                            <option data-offset="14400" value="Abu Dhabi">(GMT+04:00) Abu Dhabi
                                            </option>
                                            <option data-offset="14400" value="Muscat">(GMT+04:00) Muscat</option>
                                            <option data-offset="14400" value="Baku">(GMT+04:00) Baku</option>
                                            <option data-offset="14400" value="Tbilisi">(GMT+04:00) Tbilisi</option>
                                            <option data-offset="14400" value="Yerevan">(GMT+04:00) Yerevan</option>
                                            <option data-offset="16200" value="Tehran">(GMT+04:30) Tehran</option>
                                            <option data-offset="16200" value="Kabul">(GMT+04:30) Kabul</option>
                                            <option data-offset="18000" value="Ekaterinburg">(GMT+05:00)
                                                Ekaterinburg
                                            </option>
                                            <option data-offset="18000" value="Islamabad">(GMT+05:00) Islamabad
                                            </option>
                                            <option data-offset="18000" value="Karachi">(GMT+05:00) Karachi</option>
                                            <option data-offset="18000" value="Tashkent">(GMT+05:00) Tashkent
                                            </option>
                                            <option data-offset="19800" value="Chennai">(GMT+05:30) Chennai</option>
                                            <option data-offset="19800" value="Kolkata">(GMT+05:30) Kolkata</option>
                                            <option data-offset="19800" value="Mumbai">(GMT+05:30) Mumbai</option>
                                            <option data-offset="19800" value="New Delhi">(GMT+05:30) New Delhi
                                            </option>
                                            <option data-offset="19800" value="Sri Jayawardenepura">(GMT+05:30) Sri
                                                Jayawardenepura
                                            </option>
                                            <option data-offset="20700" value="Kathmandu">(GMT+05:45) Kathmandu
                                            </option>
                                            <option data-offset="21600" value="Astana">(GMT+06:00) Astana</option>
                                            <option data-offset="21600" value="Dhaka">(GMT+06:00) Dhaka</option>
                                            <option data-offset="21600" value="Almaty">(GMT+06:00) Almaty</option>
                                            <option data-offset="21600" value="Urumqi">(GMT+06:00) Urumqi</option>
                                            <option data-offset="23400" value="Rangoon">(GMT+06:30) Rangoon</option>
                                            <option data-offset="25200" value="Novosibirsk">(GMT+07:00)
                                                Novosibirsk
                                            </option>
                                            <option data-offset="25200" value="Bangkok">(GMT+07:00) Bangkok</option>
                                            <option data-offset="25200" value="Hanoi">(GMT+07:00) Hanoi</option>
                                            <option data-offset="25200" value="Jakarta">(GMT+07:00) Jakarta</option>
                                            <option data-offset="25200" value="Krasnoyarsk">(GMT+07:00)
                                                Krasnoyarsk
                                            </option>
                                            <option data-offset="28800" value="Beijing">(GMT+08:00) Beijing</option>
                                            <option data-offset="28800" value="Chongqing">(GMT+08:00) Chongqing
                                            </option>
                                            <option data-offset="28800" value="Hong Kong">(GMT+08:00) Hong Kong
                                            </option>
                                            <option data-offset="28800" value="Kuala Lumpur">(GMT+08:00) Kuala
                                                Lumpur
                                            </option>
                                            <option data-offset="28800" value="Singapore">(GMT+08:00) Singapore
                                            </option>
                                            <option data-offset="28800" value="Taipei">(GMT+08:00) Taipei</option>
                                            <option data-offset="28800" value="Perth">(GMT+08:00) Perth</option>
                                            <option data-offset="28800" value="Irkutsk">(GMT+08:00) Irkutsk</option>
                                            <option data-offset="28800" value="Ulaan Bataar">(GMT+08:00) Ulaan
                                                Bataar
                                            </option>
                                            <option data-offset="32400" value="Seoul">(GMT+09:00) Seoul</option>
                                            <option data-offset="32400" value="Osaka">(GMT+09:00) Osaka</option>
                                            <option data-offset="32400" value="Sapporo">(GMT+09:00) Sapporo</option>
                                            <option data-offset="32400" value="Tokyo">(GMT+09:00) Tokyo</option>
                                            <option data-offset="32400" value="Yakutsk">(GMT+09:00) Yakutsk</option>
                                            <option data-offset="34200" value="Darwin">(GMT+09:30) Darwin</option>
                                            <option data-offset="34200" value="Adelaide">(GMT+09:30) Adelaide
                                            </option>
                                            <option data-offset="36000" value="Canberra">(GMT+10:00) Canberra
                                            </option>
                                            <option data-offset="36000" value="Melbourne">(GMT+10:00) Melbourne
                                            </option>
                                            <option data-offset="36000" value="Sydney">(GMT+10:00) Sydney</option>
                                            <option data-offset="36000" value="Brisbane">(GMT+10:00) Brisbane
                                            </option>
                                            <option data-offset="36000" value="Hobart">(GMT+10:00) Hobart</option>
                                            <option data-offset="36000" value="Vladivostok">(GMT+10:00)
                                                Vladivostok
                                            </option>
                                            <option data-offset="36000" value="Guam">(GMT+10:00) Guam</option>
                                            <option data-offset="36000" value="Port Moresby">(GMT+10:00) Port
                                                Moresby
                                            </option>
                                            <option data-offset="36000" value="Solomon Is.">(GMT+10:00) Solomon
                                                Is.
                                            </option>
                                            <option data-offset="39600" value="Magadan">(GMT+11:00) Magadan</option>
                                            <option data-offset="39600" value="New Caledonia">(GMT+11:00) New
                                                Caledonia
                                            </option>
                                            <option data-offset="43200" value="Fiji">(GMT+12:00) Fiji</option>
                                            <option data-offset="43200" value="Kamchatka">(GMT+12:00) Kamchatka
                                            </option>
                                            <option data-offset="43200" value="Marshall Is.">(GMT+12:00) Marshall
                                                Is.
                                            </option>
                                            <option data-offset="43200" value="Auckland">(GMT+12:00) Auckland
                                            </option>
                                            <option data-offset="43200" value="Wellington">(GMT+12:00) Wellington
                                            </option>
                                            <option data-offset="46800" value="Nuku'alofa">(GMT+13:00) Nuku'alofa
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->
                                <!--begin::Group-->
                                <div class="form-group row align-items-center">
                                    <label
                                        class="col-form-label col-3 text-lg-right text-left">Communication</label>
                                    <div class="col-9">
                                        <div class="checkbox-inline">
                                            <label class="checkbox">
                                                <input type="checkbox" checked="checked"/>
                                                <span></span>Email</label>
                                            <label class="checkbox">
                                                <input type="checkbox" checked="checked"/>
                                                <span></span>SMS</label>
                                            <label class="checkbox">
                                                <input type="checkbox"/>
                                                <span></span>Phone</label>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Group-->
                            </div>
                        </div>
                    </div>
                    <!--end::Row-->
                    <div class="separator separator-dashed my-10"></div>
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-7">
                            <!--begin::Row-->
                            <div class="row">
                                <label class="col-form-label col-3 text-lg-right text-left"></label>
                                <div class="col-9">
                                    <h6 class="text-dark font-weight-bold mb-10">Security:</h6>
                                </div>
                            </div>
                            <!--end::Row-->
                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">Login
                                    verification</label>
                                <div class="col-9">
                                    <button type="button" class="btn btn-light-primary font-weight-bold btn-sm">
                                        Setup login verification
                                    </button>
                                    <div class="form-text text-muted mt-3">After you log in, you will be asked for
                                        additional information to confirm your identity and protect your account
                                        from being compromised.
                                        <a href="#">Learn more</a>.
                                    </div>
                                </div>
                            </div>
                            <!--end::Group-->
                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">Password reset
                                    verification</label>
                                <div class="col-9">
                                    <div class="checkbox-inline">
                                        <label class="checkbox mb-2">
                                            <input type="checkbox"/>
                                            <span></span>Require personal information to reset your
                                            password.</label>
                                    </div>
                                    <div class="form-text text-muted">For extra security, this requires you to
                                        confirm your email or phone number when you reset your password.
                                        <a href="#" class="font-weight-bold">Learn more</a>.
                                    </div>
                                </div>
                            </div>
                            <!--end::Group-->
                            <!--begin::Group-->
                            <div class="form-group row mt-10">
                                <label class="col-3"></label>
                                <div class="col-9">
                                    <button type="button" class="btn btn-light-danger font-weight-bold btn-sm">
                                        Deactivate your account ?
                                    </button>
                                </div>
                            </div>
                            <!--end::Group-->
                        </div>
                        <div class="col-xl-2"></div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Tab-->
                <!--begin::Tab-->
{{--                @if (Auth::user()->can('profile-change-password'))--}}
                <div class="tab-pane px-7" id="kt_user_edit_tab_3" role="tabpanel" id="kt_user_edit_tab_3">
                    <form class="form" action="{{ route('admin.postChangeCurrentPassword') }}" method="POST">
                    {{ csrf_field() }}
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--begin::Row-->
                        <div class="row">
                            <div class="col-xl-2"></div>
                            <div class="col-xl-7">


                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-form-label col-3 text-lg-right text-left">{{__('Mật khẩu cũ')}}</label>
                                    <div class="col-9">
                                        <input class="form-control form-control-lg  mb-1"
                                               type="password" name="old_password" value=""/>

                                    </div>
                                </div>
                                <!--end::Group-->
                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-form-label col-3 text-lg-right text-left">{{__('Mật khẩu mới')}}</label>
                                    <div class="col-9">
                                        <input class="form-control form-control-lg " type="password" name="password" value=""/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-3 text-lg-right text-left">{{__('Xác nhận mật khẩu mới')}}</label>
                                    <div class="col-9">
                                        <input class="form-control form-control-lg " type="password" name="password_confirmation" value=""/>
                                    </div>
                                </div>
                                <!--end::Group-->
                                <!--begin::Group-->
                                {{-- <div class="form-group row">
                                    <label class="col-form-label col-3 text-lg-right text-left">{{__('Xác nhận mật khẩu ')}}</label>
                                    <div class="col-9">
                                        <input class="form-control form-control-lg " type="password"
                                               value=""/>
                                    </div>
                                </div> --}}
                                <!--end::Group-->
                            </div>
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer pb-0">
                        <div class="row">
                            <div class="col-xl-2"></div>
                            <div class="col-xl-7">
                                <div class="row">
                                    <div class="col-3"></div>
                                    <div class="col-9">
                                        <button type="submit" class="btn btn-light-primary font-weight-bold">Cập nhật</button>
                                        <button type="reset" class="btn btn-clean font-weight-bold">Hủy</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Footer-->
                    </form>
                </div>
{{--            @endif--}}
                <!--end::Tab-->
                {{--                @if (Auth::user()->can('profile-change-password'))--}}
                <div class="tab-pane px-7" id="kt_user_edit_tab_4" role="tabpanel" id="kt_user_edit_tab_4">
                    <form class="form" action="{{ route('admin.postChangeCurrentPassword2') }}" method="POST">
                    {{ csrf_field() }}
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--begin::Row-->
                        <div class="row">
                            <div class="col-xl-2"></div>
                            <div class="col-xl-7">


                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-form-label col-3 text-lg-right text-left">{{__('Mật khẩu cấp 2 cũ')}}</label>
                                    <div class="col-9">
                                        <input class="form-control form-control-lg  mb-1"
                                               type="password" name="old_password" value=""/>

                                    </div>
                                </div>
                                <!--end::Group-->
                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-form-label col-3 text-lg-right text-left">{{__('Mật khẩu cấp 2 mới')}}</label>
                                    <div class="col-9">
                                        <input class="form-control form-control-lg " type="password" name="password" value=""/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-3 text-lg-right text-left">{{__('Xác nhận mật khẩu cấp 2 mới')}}</label>
                                    <div class="col-9">
                                        <input class="form-control form-control-lg " type="password" name="password_confirmation" value=""/>
                                    </div>
                                </div>
                                <!--end::Group-->
                                <!--begin::Group-->
                            {{-- <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">{{__('Xác nhận mật khẩu ')}}</label>
                                <div class="col-9">
                                    <input class="form-control form-control-lg " type="password"
                                           value=""/>
                                </div>
                            </div> --}}
                            <!--end::Group-->
                            </div>
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer pb-0">
                        <div class="row">
                            <div class="col-xl-2"></div>
                            <div class="col-xl-7">
                                <div class="row">
                                    <div class="col-3"></div>
                                    <div class="col-9">
                                        <button type="submit" class="btn btn-light-primary font-weight-bold">Cập nhật</button>
                                        <button type="reset" class="btn btn-clean font-weight-bold">Hủy</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Footer-->
                    </form>
                </div>
            {{--            @endif--}}
                <!--end::Tab-->
            </div>

        </div>
        <!--begin::Card body-->
    </div>
    <!--end::Card-->






@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')


    <script src="{{asset('assets/backend/themes/js/pages/custom/user/edit-user.js')}}"></script>

@endsection
