$(document).ready(function () {
    $('body').on('click', '.img-browser', function(){
        $(this).parent().find('input[type="file"]').click();
    });
    $('.img-preview').change(function(event){
        var el = $(this);
        var reader = new FileReader();
        reader.onload = function(){
            $(el.data('for')).attr('src', reader.result);
        };
        reader.readAsDataURL(event.target.files[0]);
    });

    $('body').on('click', '.remove-media', function(){
        var el = $(this);
        if (confirm("Chắc chắn xoá?")) {
            $.ajax('/admin/ajax?action=delete_image&id='+el.data('id')).done(function() {
                el.parent().remove();
            });
        }
    });
});
