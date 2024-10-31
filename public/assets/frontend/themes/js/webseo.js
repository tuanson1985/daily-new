function createSlugFromKeyword(keyword) {
    var slug = keyword
        .toLowerCase()
        .trim()
        .replace(/&/g, "-and-")
        .replace(/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/g, "a")
        .replace(/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/g, "e")
        .replace(/(ì|í|ị|ỉ|ĩ)/g, "i")
        .replace(/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/g, "o")
        .replace(/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/g, "u")
        .replace(/(ỳ|ý|ỵ|ỷ|ỹ)/g, "y")
        .replace(/(đ)/g, "d")
        .replace(/[^a-z0-9-]+/g, "-")
        .replace(/\-\-+/g, "-")
        .replace(/^-+|-+$/g, "");
    return slug;
}



$(document).ready(function(){

   var rootDomain=ROOT_DOMAIN;

    $("#btn-slug-edit").click(function (e) {
        e.preventDefault();
        var currentSlug =$("#current-slug").val();
        $(this).hide();
        $("#btn-slug-renew").hide();
        $("#label-slug").hide();
        //show input edit slug
        $("#input-slug-edit").css('display', 'inline-block');
        $("#input-slug-edit").val(currentSlug);
        $("#input-slug-edit").focus()
        //show buttons confirm
        $("#btn-slug-ok").css('display', 'inline-block');
        $("#btn-slug-cancel").css('display', 'inline-block');
    });


    $("#btn-slug-ok").click(function (e) {
        e.preventDefault();

        var currentSlug =createSlugFromKeyword($("#input-slug-edit").val());

        $(this).hide();
        $("#btn-slug-cancel").hide();
        $("#input-slug-edit").hide();
        //show buttons confirm và set label đã ghi đè
        $("#label-slug").text(currentSlug).show();
        $('#google_slug').text(currentSlug);
        $("#is_slug_override").val(1);

        $("#current-slug").val(currentSlug);

        //set link cho thẻ a Permalink
        $("#permalink").attr("href", rootDomain+"/"+currentSlug)

        $("#btn-slug-renew").css('display', 'inline-block');
        $("#btn-slug-edit").css('display', 'inline-block');
    });


    $("#btn-slug-cancel").click(function (e) {
        e.preventDefault();
        // var currentSlug =$("#current-slug").val();
        $(this).hide();
        $("#btn-slug-ok").hide();
        $("#input-slug-edit").hide();

        //show buttons confirm
        $("#label-slug").show();
        $("#btn-slug-renew").css('display', 'inline-block');
        $("#btn-slug-edit").css('display', 'inline-block');
    });

    $("#btn-slug-renew").click(function (e) {
        e.preventDefault();
        var title =$("#title_gen_slug").val();
        var slugRenew=createSlugFromKeyword(title);
        $("#current-slug").val(slugRenew);
        $("#label-slug").text(slugRenew)
        $('#google_slug').text(slugRenew);
        //set link cho thẻ a Permalink
        $("#permalink").attr("href", rootDomain+"/"+slugRenew);
        $("#is_slug_override").val('');
    });



    $("#title_gen_slug").on("keyup", function() {
        $isSlugOverride=$("#is_slug_override").val();
        console.log($isSlugOverride);

        var title = $(this).val();
        var slugRenew=createSlugFromKeyword(title);

        if($isSlugOverride!=1){
            $("#current-slug").val(slugRenew);
            $("#label-slug").text(slugRenew)
        }

        //set link cho thẻ a Permalink
        $("#permalink").attr("href", rootDomain+"/"+slugRenew)


        // Set title cho phần seo
        let google_title =
            "Tiêu đề seo website không vượt quá 70 kí tự (tốt nhất từ 60-70 kí tự)";
        if (title) {
            google_title = title;
        }
        if (google_title.length > 70) {
            google_title = google_title.substr(0, 67) + "...";
        }
        $('#seo_title').val(title);
        $('#google_title').text(google_title);

        if($isSlugOverride!=1 ){
            $('#google_slug').text(slugRenew);
        }

        //END  Set title cho phần seo

    });

    //description
    var description=$('#description');
    var google_description_default= "Mô tả seo website không vượt quá 160 kí tự. Là những đoạn mô tả ngắn gọn về website, bài viết...";


    if (CKEDITOR.instances[description.attr("id")] == undefined) {
        description.on("change", function() {
            var description_content = $('#description').val();
            $('#seo_description').val(description_content);
            $('#google_description').text(description_content);
        });
    } else {
        CKEDITOR.instances[description.attr("id")].on("change", function() {
            // var description_content = CKEDITOR.instances[description.attr("id")].getBody().getText();

            var description_content = CKEDITOR.instances[description.attr("id")].getData();
            description_content = $(description_content).text(); // html to text
            description_content = description_content.replace(/\r?\n|\r/gm," "); // remove line breaks
            description_content = description_content.replace(/\s\s+/g, " ").trim(); // remove double spaces
            console.log(description_content);

            $('#seo_description').val(description_content);

            if(description_content==""){

                $('#google_description').text(google_description_default);
            }
            else{
                if(description_content.length > 160){
                    $('#google_description').html(description_content.substr(0, 160));
                }
                else{
                    $('#google_description').text(description_content);
                }
            }
        });
    }


    $(".btn-spin-content").click(function (e){
        e.preventDefault();
        //var mySelection =CKEDITOR.instances["content"].getSelectedHtml().getHTML();
        // var mySelection =CKEDITOR.instances["content"].getSelection().getSelectedText()

        var mySelection = getSelectionHtml(CKEDITOR.instances["content"]);
        alert(mySelection.length);
        if(mySelection=="" || mySelection.length<32){
            alert('Dữ liệu không được nhỏ hơn 32 ký tự');
        }
        var rootDomain=ROOT_DOMAIN;
        $.ajax({
            type: "POST",
            url: '/admin/seo/spin-content',
            data: {
                '_token':$('meta[name="csrf-token"]').attr('content'),
                'text':mySelection,
            },
            beforeSend: function (xhr) {
            },
            success: function (data) {


                if (data.status == 1) {
                    var newText=data.text;
                    CKEDITOR.instances["content"].insertHtml(newText);

                } else {
                    var newText=data.text??"";
                    CKEDITOR.instances["content"].insertHtml('dcm');
                }
            },
            error: function (data) {

            },
            complete: function (data) {
            }
        });







    });

    function getSelectionHtml(editor) {
        var sel = editor.getSelection();
        var ranges = sel.getRanges();
        var el = new CKEDITOR.dom.element("div");
        for (var i = 0, len = ranges.length; i < len; ++i) {
            el.append(ranges[i].cloneContents());
        }
        return el.getHtml();
    }



    function spinContent(text) {


    }




});
