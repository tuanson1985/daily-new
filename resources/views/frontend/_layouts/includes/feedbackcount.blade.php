<style>
    .menu-text{position: relative;z-index: 1;}
    .menu-text .m-menu__link-badge{position: absolute;
        right: 0;
        background: red;
        padding: 2px;
        font-size: 10px;
        font-weight: bold;
        min-width: 20px;
        border-radius: 100px;text-align: center}
</style>
<script>
    jQuery(document).ready(function () {
        loadCommentCount();
    });
    function loadCommentCount(){
        $.ajax({
            type: "POST",
            url: '/admin/feedback/countComment',
            data: {
                '_token':'{{csrf_token()}}'
            },
            beforeSend: function (xhr) {
            },
            success: function (data) {

                if (data.status == "SUCCESS") {
                    $(".menu-item .menu-link .menu-text").each(function (){
                        if($(this).html() == "Danh sách ý kiến") {
                            const  htmlT = "<span class=\"m-menu__link-badge\">"+(data.data > 99 ? '99+' : data.data)+"</span>";
                            $(this).html("Danh sách ý kiến" + htmlT);
                        }
                    });
                } else {
                    $(".menu-item .menu-link .menu-text").each(function (){
                        if($(this).html() == "Danh sách ý kiến") {
                            const  htmlT = "<span class=\"m-menu__link-badge\">0</span>";
                            $(this).html("Danh sách ý kiến" + htmlT);
                        }
                    });
                }
            },
            error: function (data) {
                $(".menu-item .menu-link .menu-text").each(function (){
                    if($(this).html() == "Danh sách ý kiến") {
                        const  htmlT = "<span class=\"m-menu__link-badge\">0</span>";
                        $(this).html("Danh sách ý kiến" + htmlT);
                    }
                });
            },
            complete: function (data) {
            }
        });
    }
</script>
