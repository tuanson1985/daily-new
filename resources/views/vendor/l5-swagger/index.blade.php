
<!doctype html>
<html>
<head>
    <!-- META Tags -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>API | GQ GROUP</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/binarytorch/larecipe/assets/css/app.css') }}">
   <!-- FontAwesome -->
    <link rel="stylesheet" href="{{ asset('vendor/binarytorch/larecipe/assets/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/binarytorch/larecipe/assets/css/font-awesome-v4-shims.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/binarytorch/larecipe/assets/css/style.css') }}">
{{--    <link rel="stylesheet" href="{{ asset('assets/backend/themes/css/style.bundle.css') }}">--}}


{{--    <link rel="stylesheet" href="{{ asset('assets/backend/themes/plugins/global/plugins.bundle.css') }}">--}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="4ISeyBCRkLN35vvyrjvi2aWDvxCEMy8hIX5GtKWb">
    <link href="{{asset('swagger/css/style.css')}}" rel="stylesheet">


{{--    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}">--}}

</head>
<body>
<div id="app" v-cloak>
    @include('vendor.l5-swagger.layout.header')

    @include('vendor.l5-swagger.content')

    <larecipe-back-to-top></larecipe-back-to-top>
</div>


<script>
    window.config = [];
</script>



<script src="{{ asset('vendor/binarytorch/larecipe/assets/js/app.js') }}"></script>

<script>
    window.LaRecipe = new CreateLarecipe(config)
</script>

<!-- Google Analytics -->
<!-- /Google Analytics -->


<script>
    LaRecipe.run()
</script>

@php
    $url = '';
    foreach(config('module.api-document.url') as $key =>$val){
        $url = '/'.$val;
        break;
    }
@endphp
<input type="hidden" name="app-url" class="app-url" value="{{ config('app.url') }}/api/v1">
<input type="hidden" name="select_key_api" class="select_key_api" value="{{ $select_key??null }}">
<input type="hidden" name="domain_api" class="domain_api" value="{{ $domain??null }}">
{{--@dd($url)--}}
{{--<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-bundle.js') }}"></script>--}}
{{--<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js') }}"></script>--}}
<script src="{{asset('swagger/js/jquery-2.1.4.min.js')}}"></script>
<script src="{{asset('swagger/js/swagger-bundle.js')}}"></script>
<script src="{{ asset('vendor/binarytorch/larecipe/assets/js/main.js') }}"></script>

<script type="text/javascript">

    $(document).ready(function () {


        $(document).on('click', '.try-out__btn',function(e){

            let domain_api = $('.domain_api').val();
            let select_key_api = $('.select_key_api').val();

            if (domain_api && select_key_api){

                $('input[placeholder="domain"]').addClass('domain_api');
                $('input[placeholder="secret_key"]').addClass('select_key_api');
            }


        })


        $('.add-active-api').first().addClass('active');

        $(document).on('click', '.add-active-api',function(e){
            $('.add-active-api').each(function(i, obj) {
                //test
                $(this).removeClass('active');
            });
            $('.add-active-api').first().removeClass('active');
            $(this).addClass('active');

            let app = $('.app-url').val();
            $('.nav-search').val(app);
        })
    })

    $('body').on('change','#select-client',function(){
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

    let media_width = $(document).width();

    if(localStorage.getItem('larecipeSidebar') == null) {
        // localStorage.setItem('larecipeSidebar', !! 1);
        localStorage.setItem('larecipeSidebar', !! 1);

    }else {
        let checksiderbar = localStorage.getItem('larecipeSidebar');

        if (checksiderbar === "false"){
            $('.documentation').css('padding-left',16);
        }else {
            $('.documentation').css('padding-left',276);
        }

    }

</script>
<script>

    function selectVersion() {
        const vContainers = document.querySelectorAll('.opblock-tag-section');
        console.log(vContainers);
    }

    window.onload = function(data) {

        // Build a system
        const ui = SwaggerUIBundle({
            dom_id: '#swagger-ui',
            url: '{{ $url }}',
            layout: 'BaseLayout',
        })

        window.ui = ui
        $('.topbar').html('');
        $('.topbar').css('background','#FFFFFF');
        $('.opblock-tag-section').addClass('is-open');
        $('.authorization__btn').css('display','none');
    }

    $(document).on('click', '.url-swagger',function(e){
        e.preventDefault();
        var url = $(this).data('url');
        var title = $(this).data('title');

        $('.title-api').html('Quản lý API ' + title);

        SwaggerUIBundle({
            dom_id: '#swagger-ui',
            url: url,
            layout: 'BaseLayout',
        })
        $('.topbar').html('');
        $('.topbar').css('background','#FFFFFF');
        $('.authorization__btn').css('display','none');

    })


    $('.switch-label').on('click',function () {
        let checked = !$('.switch-checkbox:checked').length;

        if (checked){
            $('.documentation').css('padding-left',276);
        }else {
            $('.documentation').css('padding-left',16);
        }
    })

    $(document).on('click', '.opblock',function(e){
        let url = $(this).children().children();
        let path = $(url[1]).data('path');
        let app = $('.app-url').val();
        $('.nav-search').val(app + path);

    });



    $(document).on('keyup', '.search-header',function(e){
        let keyword = convertToSlug($(this).val());
        $('.opblock-tag-section').each(function () {

            let slug = $(this).children();
            let tag = $(slug[0]).data('tag');
            let tag_slug = convertToSlug(tag);
            console.log(tag_slug);
            $(this).toggle(tag_slug.indexOf(keyword) > -1)
        })
    });

    function convertToSlug(title) {
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
        // trả về kết quả
        return slug;
    }

    $(document).on('keyup', '.nav-search-navbar',function(e){
        let keywords = convertToSlug($(this).val());
        $('.url-swagger').each(function () {

            let slugs = $(this).data('title');
            let tag_slugs = convertToSlug(slugs);

            $(this).toggle(tag_slugs.indexOf(keywords) > -1)
        })
    });
</script>
</body>
</html>




