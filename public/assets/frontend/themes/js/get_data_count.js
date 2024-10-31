
jQuery(document).ready(function () {
    loadServicePurchaseCount();

    loadConfirmWithdrawCount();
});


    //load số dịch vụ thủ công
function loadServicePurchaseCount(){
    $.ajax({
        type: "GET",
        url: '/admin/service-purchase/count',
        data: {
            '_token':'{{csrf_token()}}'
        },
        beforeSend: function (xhr) {
        },
        success: function (data) {

            if (data.status == 1) {
               var text=data.data;
               if(text>99){
                   text="99+";
               }
               $('.serivce_purchase_label').text(text)
            } else {
                $('.serivce_purchase_label').text(0)
            }
        },
        error: function (data) {

        },
        complete: function (data) {
        }
    });
}

//load số dịch vụ thủ công

function loadConfirmWithdrawCount(){
    $.ajax({
        type: "GET",
        url: '/admin/confirm-withdraw/count',
        // data: {
        //     '_token':'{{csrf_token()}}'
        // },
        beforeSend: function (xhr) {
        },
        success: function (data) {

            if (data.status == 1) {
                var text=data.data;
                if(text>99){
                    text="99+";
                }
                $('.confirm_withdraw_label').text(text)
            } else {
                $('.confirm_withdraw_label').text(0)
            }
        },
        error: function (data) {

        },
        complete: function (data) {
        }
    });
}
