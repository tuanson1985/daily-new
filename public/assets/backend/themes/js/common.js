
$('.input-price').mask('000.000.000.000.000', {reverse: true});


function number_format(number,delemiter=","){

    if (typeof number === 'undefined' || number==null) {
        return 0;
    }
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, delemiter)
}



(function($){
    $.fn.extend({
        donetyping: function(callback,timeout){
            timeout = timeout || 1e3; // 1 second default timeout
            var timeoutReference,
                doneTyping = function(el){
                    if (!timeoutReference) return;
                    timeoutReference = null;
                    callback.call(el);
                };
            return this.each(function(i,el){
                var $el = $(el);
                // Chrome Fix (Use keyup over keypress to detect backspace)
                // thank you @palerdot
                $el.is(':input') && $el.on('keyup keypress paste',function(e){
                    // This catches the backspace button in chrome, but also prevents
                    // the event from triggering too preemptively. Without this line,
                    // using tab/shift+tab will make the focused element fire the callback.
                    if (e.type=='keyup' && e.keyCode!=8) return;

                    // Check if timeout has been set. If it has, "reset" the clock and
                    // start over again.
                    if (timeoutReference) clearTimeout(timeoutReference);
                    timeoutReference = setTimeout(function(){
                        // if we made it here, our timeout has elapsed. Fire the
                        // callback
                        doneTyping(el);
                    }, timeout);
                }).on('blur',function(){
                    // If we can, fire the event since we're leaving the field
                    doneTyping(el);
                });
            });
        }
    });
})(jQuery);


function toast(message,state){


    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "4000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    if(state=="success"){
        toastr.success(message);
    }
    else if(state=="warning"){
        toastr.warning(message);
    }
    else if(state=="error"){
        toastr.error(message);
    }
    else if(state=="info"){
        toastr.info(message);
    }
    else{
        toastr.success(message);
    }


}



$(document).ready(function(){



    // datetimepicker
    $('.datetimepicker-default').datetimepicker({
        useCurrent: true,
        autoclose: true,
        format: "DD/MM/YYYY HH:mm:ss"
    });



    $("#price_old,#percent_sale").on("keyup", function() {

        var price_old=$("#price_old").val().replace(/[^\d]+/g, '');
        var percent_sale=$("#percent_sale").val().replace(/[^\d]+/g, '');
        var price=$("#price").val().replace(/[^\d]+/g, '');

        console.log(price_old);
        console.log(percent_sale);
        console.log(price);
        //set sale
        if(percent_sale!="" &percent_sale!=0){

            if(percent_sale>100){
                $("#percent_sale").val(100);
                $("#price").val(0);
                return false;
            }

            var real_sale=price_old*percent_sale/100;
            $("#price").val(price_old - real_sale);
            $("#price").val(price_old - real_sale).trigger('input');
        }
        else{

            $("#price").val(price_old).trigger('input');
        }

    });


    $("#price").on("keyup", function() {

        var price_old=$("#price_old").val().replace(/[^\d]+/g, '');
        var price=$("#price").val().replace(/[^\d]+/g, '');


        if(price > price_old){

            $("#price").val(price_old).trigger('input');;
            $("#percent_sale").val(0);
            return false;
        }
        //set sale
        if(price!="" &price!=0){

            var real_percent_sale = price/price_old*100;
            $("#percent_sale").val(100-real_percent_sale).trigger('input');;
        }
        else{

            $("#percent_sale").val(0).trigger('input');
        }
    });


});



