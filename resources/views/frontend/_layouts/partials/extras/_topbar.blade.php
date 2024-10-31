{{-- Topbar --}}
<div class="topbar">
    {{-- Search --}}
    @if (config('layout.extras.search.display'))
        @if (config('layout.extras.search.layout') == 'offcanvas')
            <div class="topbar-item">
                <div class="btn btn-icon btn-clean btn-lg mr-1" id="kt_quick_search_toggle">
                    {{ Metronic::getSVG("assets/backend/themes/media/svg/icons/General/Search.svg", "svg-icon-xl svg-icon-primary") }}
                </div>
            </div>
        @else
            <div class="dropdown" id="kt_quick_search_toggle">
                {{-- Toggle --}}
                <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
                    <div class="btn btn-icon btn-clean btn-lg btn-dropdown mr-1">
                        {{ Metronic::getSVG("assets/backend/themes/media/svg/icons/General/Search.svg", "svg-icon-xl svg-icon-primary") }}
                    </div>
                </div>
                {{-- Dropdown --}}
                <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg">
                    @include('frontend._layouts.partials.extras.dropdown._search-dropdown')
                </div>
            </div>
        @endif
    @endif
    {{-- Notifications --}}
    @if (config('layout.extras.notifications.display'))
        @if (config('layout.extras.notifications.layout') == 'offcanvas')
            <div class="topbar-item">
                <div class="btn btn-icon btn-clean btn-lg mr-1 pulse pulse-primary" id="kt_quick_notifications_toggle">
                    {{ Metronic::getSVG("assets/backend/themes/media/svg/icons/Code/Compiling.svg", "svg-icon-xl svg-icon-primary") }}
                    <span class="pulse-ring"></span>
                </div>
            </div>
        @else
            <div class="dropdown">
                {{-- Toggle --}}
                <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
                    <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1 pulse pulse-primary">
                        {{ Metronic::getSVG("assets/backend/themes/media/svg/icons/Code/Compiling.svg", "svg-icon-xl svg-icon-primary") }}
                        <span class="pulse-ring"></span>
                    </div>
                </div>
                {{-- Dropdown --}}
                <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg">
                    <form>
                        @include('frontend._layouts.partials.extras.dropdown._notifications')
                    </form>
                </div>
            </div>
        @endif
    @endif
    {{-- Quick Actions --}}
    @if (config('layout.extras.quick-actions.display'))
        @if (config('layout.extras.quick-actions.layout') == 'offcanvas')
            <div class="topbar-item">
                <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1" id="kt_quick_actions_toggle">
                    {{ Metronic::getSVG("assets/backend/themes/media/svg/icons/Media/Equalizer.svg", "svg-icon-xl svg-icon-primary") }}
                </div>
            </div>
        @else
            <div class="dropdown">
                {{-- Toggle --}}
                <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
                    <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1">
                        {{ Metronic::getSVG("assets/backend/themes/media/svg/icons/Media/Equalizer.svg", "svg-icon-xl svg-icon-primary") }}
                    </div>
                </div>
                {{-- Dropdown --}}
                <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg">
                    @include('frontend._layouts.partials.extras.dropdown._quick-actions')
                </div>
            </div>
        @endif
    @endif
    {{-- My Cart --}}
    @if (config('layout.extras.cart.display'))
        <div class="dropdown">
            {{-- Toggle --}}
            <div class="topbar-item"  data-toggle="dropdown" data-offset="10px,0px">
                <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1">
                    {{ Metronic::getSVG("assets/backend/themes/media/svg/icons/Shopping/Cart3.svg", "svg-icon-xl svg-icon-primary") }}
                </div>
            </div>
            {{-- Dropdown --}}
            <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-xl dropdown-menu-anim-up">
                <form>
                    @include('frontend._layouts.partials.extras.dropdown._cart')
                </form>
            </div>
        </div>
    @endif
    {{-- Quick panel --}}
    @if (config('layout.header.topbar.quick-panel.display'))
        <div class="topbar-item">
            <div class="btn btn-icon btn-clean btn-lg mr-1" id="kt_quick_panel_toggle">
                {{ Metronic::getSVG("assets/backend/themes/media/svg/icons/Layout/Layout-4-blocks.svg", "svg-icon-xl svg-icon-primary") }}
            </div>
        </div>
    @endif
    {{-- @if(auth()->user()->hasRole('admin'))
    <a href="/admin/git-pull" class="btn btn-light btn-text-success btn-hover-text-success font-weight-bold dongbogit" >{{__('Đồng bộ git')}}</a>
    @endif --}}
    @include('frontend._layouts.partials.extras._client')
    @include('frontend._layouts.partials.extras._cache')
    @include('frontend._layouts.partials.extras._redirect')


    {{-- Languages --}}
    @if (config('layout.extras.languages.display'))
        <div class="dropdown">
            <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
                @include('frontend._layouts.partials.extras.dropdown._languages-active')
            </div>
            <div class="dropdown-menu p-0 m-0 dropdown-menu-anim-up dropdown-menu-sm dropdown-menu-right">
                @include('frontend._layouts.partials.extras.dropdown._languages')
            </div>
        </div>
    @endif
    {{-- User --}}
    @php
        $username = str_replace('tt_', '', auth('frontend')->user()->username);
    @endphp
    @if (config('layout.extras.user.display'))
        @if (config('layout.extras.user.layout') == 'offcanvas')
            <div class="topbar-item">
                <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
                    <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">{{__('Hi,')}}</span>
                    <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">{{$username}}</span>
                    <span class="symbol symbol-35 symbol-light-success">
          <span class="symbol-label font-size-h5 font-weight-bold">S</span>
          </span>
                </div>
            </div>
        @else
            <div class="dropdown">
                {{-- Toggle --}}
                <div class="topbar-item" data-toggle="dropdown" data-offset="0px,0px">
                    <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2">
                        <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">{{__('Hi,')}}</span>
                        <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">{{$username}}</span>
                        <span class="symbol symbol-35 symbol-light-success">
             <span class="symbol-label font-size-h5 font-weight-bold">{{strtoupper(substr($username, 0, 1))}}</span>
             </span>
                    </div>
                </div>
                {{-- Dropdown --}}
                <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg p-0">
                    @include('frontend._layouts.partials.extras.dropdown._user')
                </div>
            </div>
        @endif
    @endif
</div>