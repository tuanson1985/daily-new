{{--
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 4 & Angular 8
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
 --}}
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{ Metronic::printAttrs('html') }} {{ Metronic::printClasses('html') }}>
<head>
    <meta charset="utf-8"/>

    {{-- Title Section --}}
    <title>Đại lý v2 | @yield('title', $page_breadcrumbs[0]['title'] ?__($page_breadcrumbs[0]['title']): '')</title>

    {{-- Meta Data --}}
    <meta name="description" content="@yield('page_description', $page_description ?? '')"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('assets/backend/images/logdailyv2.jpg') }}" />

    {{-- Fonts --}}
    {{ Metronic::getGoogleFontsInclude() }}

    {{-- Global Theme Styles (used by all pages) --}}
    @foreach(config('layout.resources.css') as $style)
        <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($style)) : asset($style) }}" rel="stylesheet" type="text/css"/>
    @endforeach

    {{-- Layout Themes (used by all pages) --}}
    @foreach (Metronic::initThemes() as $theme)
        <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($theme)) : asset($theme) }}" rel="stylesheet" type="text/css"/>
    @endforeach
    <link href="/assets/backend/assets/css/fixcss.css" rel="stylesheet" type="text/css"/>
    {{-- Includable CSS --}}
    @yield('styles')
</head>

<body {{ Metronic::printAttrs('body') }} {{ Metronic::printClasses('body') }}>

@if (config('layout.page-loader.type') != '')
    @include('admin._layouts.partials._page-loader')
@endif

@include('admin._layouts.base._layout')

{{--<div class="modal fade" id="LoadModal" role="dialog" style="display: none;" aria-hidden="true">--}}
{{--    <div class="modal-dialog " role="document">--}}

{{--        <div class="modal-content">--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<div class="modal fade" id="LoadModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> {{__('Đang tải dữ liệu...')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">

            </div>


        </div>
    </div>
</div>
@if(Auth::user()->can(['access-user']) || Auth::user()->hasRole('admin'))
<div class="modal fade" id="access_user">
    <div class="modal-dialog">
        <div class="modal-content">
            {{Form::open(array('route'=>array('admin.access_user',0),'class'=>'form-horizontal','method'=>'POST'))}}
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('Xác nhận thao tác')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Lý do truy cập tài khoản này:</label>
                    <input type="text" name="description" class="form-control" placeholder="Vui lòng nhập nội dung" autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" class="id-access-user" value=""/>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-danger m-btn m-btn--custom">Truy cập</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endif




<script>
    var HOST_URL = "{{ url()->current() }}";
    {{--var ROOT_DOMAIN = "{{Request::getSchemeAndHttpHost()}}"--}}
    var ROOT_DOMAIN = "{{env('FRONTEND_URL')}}";
    var MEDIA_URL = "{{ config('module.media.url')}}";
</script>


{{-- Global Config (global config for global JS scripts) --}}
<script>
    var KTAppSettings = {!! json_encode(config('layout.js'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) !!};
</script>
@if(config('etc.used_vue'))
<script src="{{ mix('assets/backend/assets/vuejs/admin.js') }}?v=1.5"></script>
@endif
{{-- Global Theme JS Bundle (used by all pages)  --}}
@foreach(config('layout.resources.js') as $script)
    <script src="{{ asset($script) }}" type="text/javascript"></script>
@endforeach

{{-- Includable JS --}}
@yield('scripts')
<script src="/assets/backend/assets/js/custom.js?v=1.0"></script>

<script>

    $(document).ready(function () {
        $('body').on('click', '.load-modal', function(e) {
            e.preventDefault();
            var curModal = $('#LoadModal');
            curModal.find('.modal-content .modal-body').html("<div class=\" overlay overlay-block\"><div class=\"overlay-layer rounded bg-primary-o-20\"><div class=\"spinner spinner-track spinner-primary mr-15 \"></div></div></div>");
            curModal.modal('show').find('.modal-content').load($(this).attr('rel'));
        });
        $('#search_aside_menu').donetyping(function() {
            var q = $(this).val();
            if(q == null || q == "" || q == undefined){
                $('.nav-search-in-values').css('display','none');
                $('.xoatimkiem').css('display','none');
                return false
            }
        }, 300);


        $('body').on('click','.closemenusiba',function(){
            $('#kt_aside').removeClass('aside-on');
            $("#kt_aside").removeData("offcanvas-aside");
            $('.aside-overlay').remove();

        })

        $('body').on('click','.xoatimkiem',function(){
            $('#search_aside_menu').val('');
            $('.nav-search-in-values').css('display','none');
            $('.xoatimkiem').css('display','none');
            const KEYWORD = changeTitleToSlug($('#search_aside_menu').val());

            let index = 0;
            $("#kt_aside_menu .menu-nav li").each(function () {
                var textone = $(this).text().toLowerCase();

                textone = changeTitleToSlug(textone);

                $(this).toggle(textone.indexOf(KEYWORD) > -1);

            });
            // return false
        })

        $('#search_aside_menu').on('keyup', function () {
            var abc = $(this).val();

            if (abc == null || abc == '' || abc == undefined){}else {
                $('.xoatimkiem').css('display','block');
            }

            const KEYWORD = changeTitleToSlug($(this).val());

            let index = 0;
            $("#kt_aside_menu .menu-nav li").each(function () {
                var textone = getTextWithSpaces(this);
                // var textone = $(this).text().toLowerCase();

                textone = changeTitleToSlug(textone);

                $(this).toggle(textone.indexOf(KEYWORD) > -1);

                if (textone.indexOf(KEYWORD) > -1) {
                    let pre_el = $(this).prev();
                    let i = $(this).index();
                    while (i) {
                        if (pre_el.hasClass('menu-section') && !$(this).hasClass('menu-section')) {
                            pre_el.show();
                            break;
                        }
                        pre_el = pre_el.prev();
                        i--;
                    }
                    index++;
                }
            });
            let index2 = 0;
            $("#kt_aside_menu .menu-nav .menu-item-submenu").each(function () {
                var textone = $(this).children('a').find('.menu-text').text().toLowerCase();
                textone = changeTitleToSlug(textone);
                if (textone.indexOf(KEYWORD) > -1 ) {
                    $(this).find('.menu-subnav').children().show();
                    let pre_el = $(this).prev();
                    let i = $(this).index();
                    while (i) {
                        if (pre_el.hasClass('menu-section')) {
                            pre_el.show();
                            break;
                        }
                        pre_el = pre_el.prev();
                        i--;
                    }

                    index2++;
                }

                // $(this).addClass('menu-item-open');
            });
            let index3 = 0;
            $("#kt_aside_menu .menu-nav .menu-section").each(function () {
                var textone = $(this).find('.menu-text').text().toLowerCase();
                textone = changeTitleToSlug(textone);
                if (textone.indexOf(KEYWORD) > -1) {
                    let next_el = $(this).next();
                    while (next_el.hasClass('menu-item')){
                        next_el.show();
                        next_el.find('.menu-subnav').children().show();
                        next_el = next_el.next();

                    }
                }
                index3++;
            })

            $("#kt_aside_menu .menu-nav li.menu-item-parent").hide();

            var html = '';

            if (parseInt(index) == 0){
                html += '<span class="kocodulieumenu" style="color: red;font-size: 14px;padding: 8px 12px">';
                html += 'Dữ liệu cần tìm không tồn tại';
                html += '</span>';
            }else {
                html = $('#kt_aside_menu').html();
            }

            $('#result-search-menu').html(html);
            $('.nav-search-in-values').css('display','block');
            $('.nav-search-in-value-load-more').css('display','none');
        });

        $('body').on('click', '#result-search-menu .menu-toggle', function(e) {
            e.preventDefault();
                let addclasss = $(this).parent();
            // alert(addclasss);
            if (addclasss.hasClass('menu-item-open')) {
                addclasss.removeClass('menu-item-open');

                return false;
            }
            addclasss.addClass('menu-item-open');
        });

        $('body').on('change','#select-client',function(){
            if($('#edit_flag').val()!=undefined){
                return;
            }
            var id = $(this).val();
            $.ajax({
                type: "POST",
                url: "{{route('admin.shop.switch')}}",
                data: {
                    '_token':'{{csrf_token()}}',
                    'id':id,
                },
                beforeSend: function (xhr) {
                    $(this).prop('disabled', true);
                },
                success: function (data) {
                    if (data.status == 1) {
                        toast(data.message);
                        if(data.redirect){
                            // window.location.href = data.redirect;
                            window.location.reload();
                        }
                    }
                    else {
                        toast('{{__('Cập nhật thất bại.Vui lòng thử lại')}}', 'error');
                    }
                },
                error: function (data) {
                    if(data.status === 429) {
                        toast('{{__('Bạn đã thao tác quá nhiều lần, không thể cập nhật')}}', 'error');
                    }
                    else {
                        toast('{{__('Lỗi hệ thống, vui lòng liên hệ QTV để xử lý')}}', 'error');
                    }
                },
                complete: function (data) {
                    $(this).prop('disabled', false);
                }
            });
        })

        function changeTitleToSlug(title) {
            var slug;


            //Đổi chữ hoa thành chữ thường
            slug = title.toLowerCase();

            //Đổi ký tự có dấu thành không dấu
            slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
            slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
            slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
            slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
            slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
            slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
            slug = slug.replace(/đ/gi, 'd');
            //Xóa các ký tự đặt biệt
            slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\<|\'|\"|\:|\;|_/gi, '');
            //Đổi khoảng trắng thành ký tự gạch ngang
            slug = slug.replace(/ /gi, "-");
            //Đổi nhiều ký tự gạch ngang liên tiếp thành 1 ký tự gạch ngang
            //Phòng trường hợp người nhập vào quá nhiều ký tự trắng
            slug = slug.replace(/\-\-\-\-\-/gi, '-');
            slug = slug.replace(/\-\-\-\-/gi, '-');
            slug = slug.replace(/\-\-\-/gi, '-');
            slug = slug.replace(/\-\-/gi, '-');
            //Xóa các ký tự gạch ngang ở đầu và cuối
            slug = '@' + slug + '@';
            slug = slug.replace(/\@\-|\-\@|\@/gi, '');
            //In slug ra textbox có id “slug”

            return slug;
        }
        $('body').on('click','.btn-access-user',function(){
            var id = $(this).data('id');
            $('#access_user .id-access-user').attr('value', id);
            $('#LoadModal').modal('hide');
            $('#access_user').modal('show');
        });

    });

    function collectTextNodes(element, texts) {
        for (var child= element.firstChild; child!==null; child= child.nextSibling) {
            if (child.nodeType===3)
                texts.push(child);
            else if (child.nodeType===1)
                collectTextNodes(child, texts);
        }
    }
    function getTextWithSpaces(element) {
        var texts= [];
        collectTextNodes(element, texts);
        for (var i= texts.length; i-->0;)
            texts[i]= texts[i].data;
        return texts.join('>');
    }

    $(document).ready(function () {
        let $scroll = $('#kt_aside_menu');
        let elm_active = $('.menu-item-active');
        let root_elm = $('.menu-item-submenu.menu-item-open');
        if(root_elm.length) {
            $scroll.scrollTop(root_elm.position().top + $scroll.scrollTop() - $scroll.height()/4);
        } else {
            $scroll.scrollTop(elm_active.position().top + $scroll.scrollTop() - ($scroll.height()/2 - (elm_active.height() / 2)));
        }
    });

</script>



@include('admin._layouts.includes.notifications')
@include('admin._layouts.includes.feedbackcount')
{{--@include('admin._layouts.includes.service-purchase-count')--}}
</body>
</html>

