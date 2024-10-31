<!-- BEGIN: Header -->
<header id="m_header" class="m-grid__item    m-header " m-minimize-offset="200" m-minimize-mobile-offset="200">
    <div class="m-container m-container--fluid m-container--full-height">
        <div class="m-stack m-stack--ver m-stack--desktop">
            <!-- BEGIN: Brand -->
            <div class="m-stack__item m-brand  m-brand--skin-dark ">
                <div class="m-stack m-stack--ver m-stack--general">
                    <div class="m-stack__item m-stack__item--middle m-brand__logo">
                        <a href="/admin" class="m-brand__logo-wrapper">
                            <img alt="" src="/assets/backend/theme/assets/demo/default/media/img/logo/logo_default_dark.png"/>
                        </a>
                    </div>
                    <div class="m-stack__item m-stack__item--middle m-brand__tools">
                        <!-- BEGIN: Left Aside Minimize Toggle -->
                        <a href="javascript:;" id="m_aside_left_minimize_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-desktop-inline-block  ">
                            <span></span>
                        </a>
                        <!-- END -->
                        <!-- BEGIN: Responsive Aside Left Menu Toggler -->
                        <a href="javascript:;" id="m_aside_left_offcanvas_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
                            <span></span>
                        </a>
                        <!-- END -->
                        <!-- BEGIN: Topbar Toggler -->
                        <a id="m_aside_header_topbar_mobile_toggle" href="javascript:;" class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
                            <i class="flaticon-more"></i>
                        </a>
                        <!-- BEGIN: Topbar Toggler -->
                    </div>
                </div>
            </div>
            <!-- END: Brand -->
            <div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">
                <!-- BEGIN: Topbar -->
                <div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general m-stack--fluid">
                    <div class="m-stack__item m-topbar__nav-wrapper">
                        <ul class="m-topbar__nav m-nav m-nav--inline">

                            <li class="m-nav__item m-topbar__notifications m-topbar__notifications--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-center 	m-dropdown--mobile-full-width" m-dropdown-toggle="click" m-dropdown-persistent="1">
                                <a href="#" class="m-nav__link m-dropdown__toggle" id="m_topbar_notification_icon">
                                    <span class="m-nav__link-badge m-badge m-badge--dot m-badge--dot-small m-badge--danger"></span>
                                    <span class="m-nav__link-icon"><i class="flaticon-alarm"></i></span>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__header m--align-center" style="background: url('/assets/backend/theme/assets/app/media/img/misc/notification_bg.jpg'); background-size: cover;">
                                            <span class="m-dropdown__header-title">Thông báo</span>
                                        </div>
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">

                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="topbar_notifications_notifications" role="tabpanel">
                                                        <div class="m-scrollable" data-scrollable="true" data-height="250" data-mobile-height="200">
                                                            <div class="m-list-timeline m-list-timeline--skin-light">
                                                                <div class="m-list-timeline__items" id="notification-menu">


                                                                    @foreach( Auth::guard('frontend')->user()->unreadNotifications()->take(10)->get() as  $nofity)

                                                                        @if($nofity->type==1)
                                                                            <div class="m-list-timeline__item">
                                                                                <span class="m-list-timeline__badge"></span>
                                                                                <span class="m-list-timeline__text"><span class="m-badge m-badge--info m-badge--wide">Nạp thẻ</span> Thành viên {{$nofity->data['username']}} đã gửi yêu cầu nạp thẻ chậm - <a href="/admin/notifications/{{$nofity->id}}" class="m-link">Xem</a></span>
                                                                                <span class="m-list-timeline__time">{{\App\Library\Helpers::ConvertToAgoTime($nofity->created_at)}}</span>
                                                                            </div>
                                                                        @elseif($nofity->type==2)
                                                                                <div class="m-list-timeline__item">
                                                                                    <span class="m-list-timeline__badge"></span>
                                                                                    <span class="m-list-timeline__text"><span class="m-badge m-badge--danger m-badge--wide">Rút tiền</span> Thành viên {{$nofity->data['username']}} đã tạo lệnh rút tiền #{{$nofity->data['id']}} - <a href="/admin/notifications/{{$nofity->id}}" class="m-link">Xem</a></span>
                                                                                    <span class="m-list-timeline__time">{{\App\Library\Helpers::ConvertToAgoTime($nofity->created_at)}}</span>
                                                                                </div>
                                                                        @endif
                                                                    @endforeach


                                                                    {{--<div class="m-list-timeline__item">--}}
                                                                    {{--<span class="m-list-timeline__badge"></span>--}}
                                                                    {{--<span class="m-list-timeline__text">New invoice received</span>--}}
                                                                    {{--<span class="m-list-timeline__time">20 mins</span>--}}
                                                                    {{--</div>--}}
                                                                   {{----}}
                                                                    {{--<div class="m-list-timeline__item">--}}
                                                                    {{--<span class="m-list-timeline__badge"></span>--}}
                                                                    {{--<span class="m-list-timeline__text">System error - <a href="#" class="m-link">Check</a></span>--}}
                                                                    {{--<span class="m-list-timeline__time">2 hrs</span>--}}
                                                                    {{--</div>--}}
                                                                    {{--<div class="m-list-timeline__item m-list-timeline__item--read">--}}
                                                                    {{--<span class="m-list-timeline__badge"></span>--}}
                                                                    {{--<span href="#" class="m-list-timeline__text">New order received <span class="m-badge m-badge--danger m-badge--wide">urgent</span></span>--}}
                                                                    {{--<span class="m-list-timeline__time">7 hrs</span>--}}
                                                                    {{--</div>--}}
                                                                    {{--<div class="m-list-timeline__item m-list-timeline__item--read">--}}
                                                                    {{--<span class="m-list-timeline__badge"></span>--}}
                                                                    {{--<span class="m-list-timeline__text">Production server down</span>--}}
                                                                    {{--<span class="m-list-timeline__time">3 hrs</span>--}}
                                                                    {{--</div>--}}
                                                                    {{--<div class="m-list-timeline__item">--}}
                                                                    {{--<span class="m-list-timeline__badge"></span>--}}
                                                                    {{--<span class="m-list-timeline__text">Production server up</span>--}}
                                                                    {{--<span class="m-list-timeline__time">5 hrs</span>--}}
                                                                    {{--</div>--}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="topbar_notifications_events" role="tabpanel">
                                                        <div class="m-scrollable" data-scrollable="true" data-height="250" data-mobile-height="200">
                                                            <div class="m-list-timeline m-list-timeline--skin-light">
                                                                <div class="m-list-timeline__items">
                                                                    <div class="m-list-timeline__item">
                                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-success"></span>
                                                                        <a href="#" class="m-list-timeline__text">New order received</a>
                                                                        <span class="m-list-timeline__time">Just now</span>
                                                                    </div>
                                                                    <div class="m-list-timeline__item">
                                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-danger"></span>
                                                                        <a href="#" class="m-list-timeline__text">New invoice received</a>
                                                                        <span class="m-list-timeline__time">20 mins</span>
                                                                    </div>
                                                                    <div class="m-list-timeline__item">
                                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-success"></span>
                                                                        <a href="#" class="m-list-timeline__text">Production server up</a>
                                                                        <span class="m-list-timeline__time">5 hrs</span>
                                                                    </div>
                                                                    <div class="m-list-timeline__item">
                                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-info"></span>
                                                                        <a href="#" class="m-list-timeline__text">New order received</a>
                                                                        <span class="m-list-timeline__time">7 hrs</span>
                                                                    </div>
                                                                    <div class="m-list-timeline__item">
                                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-info"></span>
                                                                        <a href="#" class="m-list-timeline__text">System shutdown</a>
                                                                        <span class="m-list-timeline__time">11 mins</span>
                                                                    </div>
                                                                    <div class="m-list-timeline__item">
                                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-info"></span>
                                                                        <a href="#" class="m-list-timeline__text">Production server down</a>
                                                                        <span class="m-list-timeline__time">3 hrs</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="topbar_notifications_logs" role="tabpanel">
                                                        <div class="m-stack m-stack--ver m-stack--general" style="min-height: 180px;">
                                                            <div class="m-stack__item m-stack__item--center m-stack__item--middle">
                                                                <span class="">All caught up!<br>No new logs.</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light" m-dropdown-toggle="click">
                                <a href="#" class="m-nav__link m-dropdown__toggle">
										<span class="m-topbar__userpic">
											<img src="/assets/backend/theme/assets/app/media/img/users/user4.jpg" class="m--img-rounded m--marginless" alt=""/>
										</span>
                                    <span class="m-topbar__username m--hide">Nick</span>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__header m--align-center" style="background: url(/assets/backend/theme/assets/app/media/img/misc/user_profile_bg.jpg); background-size: cover;">
                                            <div class="m-card-user m-card-user--skin-dark">
                                                <div class="m-card-user__pic">
                                                    <img src="/assets/backend/theme/assets/app/media/img/users/user4.jpg" class="m--img-rounded m--marginless" alt=""/>
                                                    <!--
                                                    <span class="m-type m-type--lg m--bg-danger"><span class="m--font-light">S<span><span>
                                                    -->
                                                </div>
                                                <div class="m-card-user__details">
                                                    <span class="m-card-user__name m--font-weight-500">{{Auth::guard('frontend')->user()->username}}</span>
                                                    <a href="#" class="m-card-user__email m--font-weight-300 m-link">{{Auth::guard('frontend')->user()->email}}</a>
                                                    <span style="margin-top:5px;" class="m-card-user__name m--font-weight-500">
                                                        {{number_format(Auth::guard('frontend')->user()->balance)}} VNĐ
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav m-nav--skin-light">
                                                    <li class="m-nav__section m--hide">
                                                        <span class="m-nav__section-text">Section</span>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="/admin/profile" class="m-nav__link">
                                                            <i class="m-nav__link-icon flaticon-profile-1"></i>
                                                            <span class="m-nav__link-title">
																	<span class="m-nav__link-wrap">
																		<span class="m-nav__link-text">Thông tin tài khoản</span>
																	</span>
																</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="/admin/change-password" class="m-nav__link">
                                                            <i class="m-nav__link-icon flaticon-profile-1"></i>
                                                            <span class="m-nav__link-title">
																	<span class="m-nav__link-wrap">
																		<span class="m-nav__link-text">Đổi mật khẩu</span>
																	</span>
																</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="indexa80c.html?page=header/profile&amp;demo=default" class="m-nav__link">
                                                            <i class="m-nav__link-icon flaticon-share"></i>
                                                            <span class="m-nav__link-text">Lịch sử hoạt động</span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="indexa80c.html?page=header/profile&amp;demo=default" class="m-nav__link">
                                                            <i class="m-nav__link-icon flaticon-chat-1"></i>
                                                            <span class="m-nav__link-text">Hộp thư</span>
                                                        </a>
                                                    </li>

                                                    <li class="m-nav__separator m-nav__separator--fit">
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="{{route('logout')}}" class="btn m-btn--pill    btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder">Logout</a>

                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- END: Topbar -->            </div>
        </div>
    </div>
</header>
<!-- END: Header -->
