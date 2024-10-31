{{-- Aside --}}

@php
    $kt_logo_image = 'logo-light.png';
@endphp

@if (config('layout.brand.self.theme') === 'light')
    @php $kt_logo_image = 'logo-dark.png' @endphp
@elseif (config('layout.brand.self.theme') === 'dark')
    @php $kt_logo_image = 'logo-light.png' @endphp
@endif

<div class="aside aside-left {{ Metronic::printClasses('aside', false) }} d-flex flex-column flex-row-auto"
     id="kt_aside">

    {{-- Brand --}}
    <div class="brand flex-column-auto {{ Metronic::printClasses('brand', false) }}" id="kt_brand">
        <div class="brand-logo">
            <a href="{{ route('frontend.index') }}" style="font-size: 18px;text-transform: uppercase;color: white !important;position: relative">
                {{--Admin Panel--}}
                <img alt="{{ config('app.name') }}" src="{{ asset('assets/frontend/themes/media/logos/'.$kt_logo_image) }}"/>

                @if(config('app.env') == "production")
                <div class="div_environment div_environment_life">
                    <span class="environment" style="">LIVE</span>
                </div>
                @else
                    <div class="div_environment div_environment_dev">
                        <span class="environment" style="">{{ config('app.env') }}</span>
                    </div>
                @endif
            </a>
        </div>

        @if (config('layout.aside.self.minimize.toggle'))
            <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
                {{ Metronic::getSVG("assets/frontend/themes/media/svg/icons/Navigation/Angle-double-left.svg", "svg-icon-xl") }}
            </button>
        @endif

    </div>
    <div class="row" style="margin: 0 auto;width: 100%" id="fakemenuadmin">
        <i class="fas fa-window-close closemenusiba"></i>
        <div class="col-md-12" style="padding: 0 12px">
            <input id="search_aside_menu" style="height: 30px" type="text" autofocus="false" class="form-control search_aside_menu" placeholder="{{__('Tìm kiếm')}}" name="search_aside_menu" style="position: relative">
            <i class="fas fa-times xoatimkiem"></i>
        </div>
        <div class="nav-search-in-values aside-menu scroll ps ps--active-y">
            <ul class="menu-nav result-search-menu" id="result-search-menu">

            </ul>
        </div>
    </div>


    {{-- Aside menu --}}
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">

        @if (config('layout.aside.self.display') === false)
            <div class="header-logo">
                <a href="{{ route('frontend.index') }}">
                    <img alt="{{ config('app.name') }}" src="{{ asset('assets/backend/themes/media/logos/'.$kt_logo_image) }}"/>
                </a>
            </div>
        @endif

        <div
            id="kt_aside_menu"
            class="aside-menu my-4 {{ Metronic::printClasses('aside_menu', false) }}"
            data-menu-vertical="1"
            {{ Metronic::printAttrs('aside_menu') }}>


            <ul class="menu-nav {{ Metronic::printClasses('aside_menu_nav', false) }}"  id="menu-nav-search">
                {{ \App\Classes\Theme\MenuFrontend::renderVerMenu(config('menu_aside_frontend.items')) }}
            </ul>
        </div>
    </div>

</div>

<style>

    .environment{
        font-size: 10px;
    }
    .div_environment{
        text-align: center;
        border-radius: 100px;
        width: 30px;
        height: 30px;
        position: absolute;
        right: -20px;
        top: -12px;
    }
    .div_environment_life{
        background: dodgerblue;
    }
    .div_environment_dev{
        background: #F67600;
    }
    /*search scroll*/
    #fakemenuadmin .xoatimkiem{
        position: absolute;
        right: 20px;
        top: 8px;
        display: none;
        cursor: pointer;
    }
    #fakemenuadmin #result-search-menu{
        max-height: 100%;
        overflow: auto;
        margin-bottom: 14px;
        padding: 8px 0;
    }

    #fakemenuadmin #rresult-search-menu .menu-nav{
        padding-top: 0!important;
    }
    #fakemenuadmin #result-search-menu .menu-nav{
        padding-top: 0!important;
    }


    #fakemenuadmin #result-search-menu .flaticon-more-v2{
        display: none;
    }
    #fakemenuadmin #result-search-menu::-webkit-scrollbar-track
    {
        position: absolute;
        top: 100px;
        left: -60px;
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        /*background-color: #121212;*/
        background-color:  #2A2A2A;

        /*border-radius: 100px;*/
        border: none;
    }
    #fakemenuadmin #result-search-menu::-webkit-scrollbar-thumb{
        background-color: pink;

    }
    #fakemenuadmin #result-search-menu::-webkit-scrollbar
    {

        width: 6px;
        /*background-color: pink;*/
        /*border-radius: 100px;*/
        border: none;

    }

    #fakemenuadmin #result-search-menu::-webkit-scrollbar-thumb
    {

        background-color: #3a3a3c;
        /*border: 2px solid #121212;*/
        border-radius: 100px;
        border: none;
        margin-left: 20px;
        height: 20px;
    }


    /*search scroll*/
    #fakemenuadmin .notification-menu-in-content {
        position: relative;
        background-color: rgba(145, 71, 255, 0.08);
        /*margin-top: 10px;*/

    }
    #fakemenuadmin .notification-menu-in-content:hover .notification-menu-content{
        background-color: #2A2A2A;
        text-decoration: none;
        transition: .2s ease-out;
        cursor: pointer;
        border: none;

    }
    #fakemenuadmin .notification-menu-in-content a:hover{
        background-color: #2A2A2A;
        text-decoration: none;
    }
    #fakemenuadmin .notification-menu-content{
        display: flex;
        padding: 12px 16px 0;
    }
    #fakemenuadmin .notification-menu-content-img{
        width: 40px;
        height: 40px;
        margin-top: -5px;
    }
    #fakemenuadmin .notification-menu-content-img img{
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 100px;

    }
    #fakemenuadmin .notification-menu-content-in{
        flex: 1;
        margin-left: 12px;

    }
    #fakemenuadmin .notification-menu-in{
        margin-top: -10px;
        padding-bottom: 12px;
    }
    #fakemenuadmin .notification-menu-content-in p{

    }
    #fakemenuadmin .notification-menu .avatar {
        /*padding: 2px 0;*/
    }
    #fakemenuadmin .notification-menu-content-in p:first-child{
        font-size: 14px;
        color: #FFFFFF;
        line-height: 20px;
        position: relative;
        /*overflow: hidden;*/
        /*text-overflow: ellipsis;*/
        /*-webkit-line-clamp: 2;*/
        /*display: -webkit-box;*/
        /*-webkit-box-orient: vertical;*/

    }
    #fakemenuadmin .notification-menu-content-in p:last-child{
        font-size: 12px;
        line-height: 1px;
        color: #7D7D7D;
    }
    #fakemenuadmin .notification-menu li a{
        text-decoration: none;
    }
    #fakemenuadmin .notification-menu li a:hover{
        opacity: 60%;
        transition: .2s ease-out;
    }
    #fakemenuadmin .notification-menu-more{
        text-align: center;
        background-color: #2A2A2A;
        width: 100% !important;
        border-radius:0 0 16px 16px;
        padding: 18px 0 18px 12px;
    }
    #fakemenuadmin .notification-menu-more span{
        font-size: 14px;
        color: #9147FF;
        padding: 4px ;
        vertical-align: center;

    }

    #fakemenuadmin .notification-menu-more a{
        text-decoration: none;
    }
    #fakemenuadmin .notification-menu-more a:hover{
        opacity: 70%;
        transition: .2s ease;
    }

    #fakemenuadmin .notification_bell img{
        width: 24px;
        height: 24px;
    }

    #fakemenuadmin .notification_bell p{
        position: absolute;
        margin: 0;
        background-color: #DA4343;
        color: #FFFFFF;
        top: -6px;
        left: 10px;
        justify-content: center;
        padding: 8px 6px 8px 6px;
        line-height: 1px;

        text-align: center;
        border-radius: 100%;
        font-size: 10px;
    }

    #fakemenuadmin .content.header_transparent {
        margin-top: -60px;
    }

    @media only screen and (max-width: 1024px){
        #fakemenuadmin .notification-menu {

            left: -250px;


        }
        #fakemenuadmin .notification-menu-in:before{

            right: 50px;


        }
        #fakemenuadmin .notification-menu:before{
            /* right: 56px; */
            right: 100px;
        }
        #fakemenuadmin .mic_idol_content_detail .swiper-button-prev,.mic_idol_content_detail .swiper-button-next{
            display: none
        }

    }

    #fakemenuadmin .nav_img{
        position: absolute;
        top: 50%;
        left: 20px;

        transform: translateY(-50%);
        display: none;

    }
    #fakemenuadmin .nav_overlay{
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: rgba(0,0,0,0.3);
        display: none;
        animation: fadeIn linear 0.3s;

    }
    #fakemenuadmin .nav-search{
        position: absolute;
        /* top: 50%;
        left: 50%; */
        display: none;
        top: 64px;
        right: 0;
        transform: translateY(-50%);
    }
    #fakemenuadmin .nav-search form{
        /* background-color: #1E1E1E; */
        display: flex;
        align-items: center;
        background-color: #2c2e3e;
        border-radius: 8px;
        color: white;
    }
    #fakemenuadmin .nav-info-search{
        display: none;
        cursor: pointer;
    }

    #fakemenuadmin .nav-search-in-values{
        position: absolute;
        width: 100%;
        margin-top: 30px;
        z-index: 9999;
        background-color: #222433;
        box-shadow: 0 0 5px 2px rgba(0, 0, 0, 0.2);
        list-style: none;
        display: none;
        /*border-radius: 8px;*/
        height: 100%;
    }

    #fakemenuadmin .nav-search-in-value-detail{
        width: 100%;
        padding: 16px 0 8px 0;

    }

    #fakemenuadmin .nav-search-in-value-detail a{
        display: flex;
    }

    #fakemenuadmin .nav-search-in-value-detail-img{
        width: 40px;
        height: 40px;
        border-radius: 100%;
        margin-left: 20px

    }

    @media only screen and (min-width: 376px) and (max-width: 573px) {
        #search_aside_menu{
            font-size: 14px!important;
            padding: 2px 8px!important;
        }
    }

    @media only screen and (max-width: 376px) {
        #search_aside_menu{
            font-size: 14px!important;
            padding: 2px 8px!important;
        }
    }


</style>
@section('scripts')

@endsection
