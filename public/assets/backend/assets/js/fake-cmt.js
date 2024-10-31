
//Sử lý chát có sẵn

var chatArrayDefault = [
    'Dịch vụ nạp uy tín ghê',
    'Uy tín không anh em.',
    'Vãi vừa ấn nạp xong vào game có ngay (y)',
    'Web uy tín đấy, vừa nạp 500k xong.',
    'Nãy có ông bạn nạp 500k xong vào nạp luôn, quá xịn admin ơi.',
    'Thanks admin <3 , uy tín lắm luôn',
    'Nhanh gọn uy tín, thanks admin',
    'Web xịn không scam nha mọi người',
    'Hàng sạch, thanks admin',
    'Vừa nạp xong, quá ngon',
    'Web ok không anh em, có scam không?',
    'Vừa chạy ra quán mua 500k thẻ nạp ăn luôn, ngon quá admin',
    'Nhập nhầm mã thẻ với serial báo admin xử lý trong vòng 1 nốt nhạc, uy tín quá admin ơi',
    'Cứ tưởng lừa đảo, nạp thử 200k nhận luôn kim cương trong 10s',
    '1 vote uy tín cho web nhé, quá ngon luôn',
    'Bị lừa nhiều rồi, giờ mới tìm được web uy tín, thanks ad',
    'Vừa nạp 100k xong',
    'Web ngon vl',
    'Anh em nào chưa nạp thì vào nạp ngay đi đang có khuyến mại',
    'Uy tín lắm admin',
    'Vote 10000k sao nhé, quá uy tín',
    'Có anh em nào vừa từ youtube qua đây nạp k',
    'Ông em vừa giới thiệu, nạp cái ăn luôn, ngon vc',
    'Uy tín nhé anh em',
    'Đã nạp thành công',
    'Đã nạp ở đây 20tr tiền thẻ, vote uy tín nhé',
    'Web nạp ngon thế này mà giờ mới biết',
    'Đã nạp, nhanh lắm nhé',
    'Ngon vcl, +5 sao cho admin',
    'Nghe anh em review ngon quá, tôi ra làm cái thẻ 500k nạp đây',
    'Không scam, web nạp thật, nhận thật nhé !',
    'Đã nạp và thấy ngon ngọt nhé ae',
    'Web này trùm nạp mẹ rồi',
    'Web được đấy anh em',
    'Thấy web được nhiều anh em nạp rồi, yên tâm nạp hehe',
    'Anh em không phải sợ đâu, tôi nạp nhiều web này rồi',
    'Web xịn không scam nha mọi người'
];

//Set thời gian minmax
var lengthChatDefault = 16;
var monthsDefault = [30, 35, 40, 45, 50, 55, 60, 65];

var totaltime = 0;
var arr_time = [];

for (let i = 0; i < lengthChatDefault; i++){

    var randomDefault = Math.floor(Math.random() * monthsDefault.length);
    var i_minite = monthsDefault[randomDefault];
    totaltime = totaltime + i_minite;
    arr_time.push(i_minite);

}

// totaltime = totaltime + monthsDefault[0];

//App chat

var d_totaltime = 0;
var c_totaltime = 0;

for (let j = 0; j < lengthChatDefault; j++){
    // console.log(arr_time[j])
    var dateTimeDefault = new Date();
    var datenow = new Date();

    d_totaltime = d_totaltime + arr_time[j]*1;
    c_totaltime = (totaltime - d_totaltime)*1;
    if (j == 0){
        dateTimeDefault.setSeconds(dateTimeDefault.getSeconds() - totaltime); // timestamp
    }else{
        dateTimeDefault.setSeconds(dateTimeDefault.getSeconds() - c_totaltime); // timestamp
    }
    var t_time;
    var e_minute = datenow.getMinutes();
    var s_minute = dateTimeDefault.getMinutes();
    var e_hour = datenow.getHours();
    var s_hour = dateTimeDefault.getHours();

    if (e_hour == s_hour){
        if (e_minute == s_minute){
            t_time = " Vừa xong";
        }else{
            t_time = e_minute - s_minute + " Phút trước";
        }
    }else{
        t_time = e_hour - s_hour + " Giờ trước";
    }

    dateTimeDefault = new Date(dateTimeDefault).toLocaleTimeString().replace(/([\d]+:[\d]{2})(:[\d]{2})(.*)/, "$1$3");
    const randomChatDefault = Math.floor(Math.random() * chatArrayDefault.length);

    var htmldefault = `
    <li>
        <div class="comment-item comment-item-khach">
            <div class="comment-avatar">
                <img
                    src="/assets/backend/images/anhdaidien.svg"
                    alt="">
            </div>
            <div class="comment-detail">
                <div class="comment-info">
                    <p>Khách</p>
                    <span>${dateTimeDefault}, </span><small class="data_time_minigame">${t_time}</small>
                </div>
                <div class="comment-content">
                    ${chatArrayDefault[randomChatDefault]}
                </div>
                <div class="comment-interact">
                    <span id="likeComment"><img
                            src="/assets/backend/images/hearts-suit 1.svg"
                            alt=""> Thích</span>
                    <span id="replyComment"><img
                            src="/assets/backend/images/comment 1.svg"
                            alt=""> Trả lời</span>
                </div>
            </div>
        </div>

    </li>`

    $('.list-unstyled').append(htmldefault);

}


$('#chatModal .chat-scroll').scrollTop($('#chatModal .chat-scroll')[0].scrollHeight);

(function loop(index = 0,l_monthsDefault = arr_time) {

    var months = [30, 35, 40, 45, 50, 55, 60, 65];

    var random = Math.floor(Math.random() * months.length);

    setTimeout(function() {

        var chatArray = [
            'Dịch vụ nạp uy tín ghê',
            'Uy tín không anh em.',
            'Vãi vừa ấn nạp xong vào game có ngay (y)',
            'Web uy tín đấy, vừa nạp 500k xong.',
            'Nãy có ông bạn nạp 500k xong vào nạp luôn, quá xịn admin ơi.',
            'Thanks admin <3 , uy tín lắm luôn',
            'Nhanh gọn uy tín, thanks admin',
            'Web xịn không scam nha mọi người',
            'Hàng sạch, thanks admin',
            'Vừa nạp xong, quá ngon',
            'Web ok không anh em, có scam không?',
            'Vừa chạy ra quán mua 500k thẻ nạp ăn luôn, ngon quá admin',
            'Nhập nhầm mã thẻ với serial báo admin xử lý trong vòng 1 nốt nhạc, uy tín quá admin ơi',
            'Cứ tưởng lừa đảo, nạp thử 200k nhận luôn kim cương trong 10s',
            '1 vote uy tín cho web nhé, quá ngon luôn',
            'Bị lừa nhiều rồi, giờ mới tìm được web uy tín, thanks ad',
            'Vừa nạp 100k xong',
            'Web ngon vl',
            'Anh em nào chưa nạp thì vào nạp ngay đi đang có khuyến mại',
            'Uy tín lắm admin',
            'Vote 10000k sao nhé, quá uy tín',
            'Có anh em nào vừa từ youtube qua đây nạp k',
            'Ông em vừa giới thiệu, nạp cái ăn luôn, ngon vc',
            'Uy tín nhé anh em',
            'Đã nạp thành công',
            'Đã nạp ở đây 20tr tiền thẻ, vote uy tín nhé',
            'Web nạp ngon thế này mà giờ mới biết',
            'Đã nạp, nhanh lắm nhé',
            'Ngon vcl, +5 sao cho admin',
            'Nghe anh em review ngon quá, tôi ra làm cái thẻ 500k nạp đây',
            'Không scam, web nạp thật, nhận thật nhé !',
            'Đã nạp và thấy ngon ngọt nhé ae',
            'Web này trùm nạp mẹ rồi',
            'Web được đấy anh em',
            'Thấy web được nhiều anh em nạp rồi, yên tâm nạp hehe',
            'Anh em không phải sợ đâu, tôi nạp nhiều web này rồi',
            'Web xịn không scam nha mọi người'
        ];

        var randomChat = Math.floor(Math.random() * chatArray.length);

        var chatHistory = $('.chat-history');

        var chatHistoryList = chatHistory.find('ul');

        var dateTime = new Date().toLocaleTimeString().replace(/([\d]+:[\d]{2})(:[\d]{2})(.*)/, "$1$3");

        var html = `
            <li>

                <div class="comment-item comment-item-khach">
                    <div class="comment-avatar">
                        <img src="/assets/backend/images/anhdaidien.svg" alt="">
                    </div>
                    <div class="comment-detail">
                        <div class="comment-info">
                            <p>Khách</p>
                            <span>${dateTime}, </span><small class="data_time_minigame">Vừa xong</small>
                        </div>
                        <div class="comment-content">
                            ${chatArray[randomChat]}
                        </div>
                        <div class="comment-interact">
                            <span id="likeComment"><img src="/assets/backend/images/hearts-suit 1.svg" alt=""> Thích</span>
                            <span id="replyComment"><img src="/assets/backend/images/comment 1.svg" alt=""> Trả lời</span>
                        </div>
                    </div>
                </div>

            </li>`;

        index = index + 1;

        chatHistoryList.append(html);
        $('#chatModal .chat-scroll').scrollTop($('#chatModal .chat-scroll')[0].scrollHeight);

        l_monthsDefault.push(months[random]);

        loop(index,l_monthsDefault);

        var ptotaltime = 0;
        var d_ptotaltime = 0;
        var c_ptotaltime = 0;

        for (let k = 0; k < l_monthsDefault.length; k++){
            ptotaltime = ptotaltime + l_monthsDefault[k];
        }

        $(".chat-history ul li .comment-item-khach .data_time_minigame").each(function (key) {
            var c_dateTimeDefault = new Date();
            var c_datenow = new Date();

            d_ptotaltime = d_ptotaltime + l_monthsDefault[key]*1;
            c_ptotaltime = (ptotaltime - d_ptotaltime)*1;

            if (key == 0){
                c_dateTimeDefault.setSeconds(c_dateTimeDefault.getSeconds() - ptotaltime); // timestamp
            }else{
                c_dateTimeDefault.setSeconds(c_dateTimeDefault.getSeconds() - c_ptotaltime); // timestamp
            }

            var t_ptime;
            var e_pminute = c_datenow.getMinutes();
            var s_pminute = c_dateTimeDefault.getMinutes();
            var e_phour = c_datenow.getHours();
            var s_phour = c_dateTimeDefault.getHours();

            if (e_phour == s_phour){
                if (e_pminute == s_pminute){
                    t_ptime = " Vừa xong";
                }else{
                    t_ptime = e_pminute - s_pminute + " Phút trước";
                }
            }else{
                t_ptime = e_phour - s_phour + " Giờ trước";
            }

            $(this).html('');
            $(this).html(t_ptime);
        });

        $(".chat-history ul li .comment-item-user").each(function (key) {
            var data_hour = $(this).data('hour');
            var data_minute = $(this).data('minute');
            var u_datenow = new Date();

            var t_utime;
            var e_uminute = u_datenow.getMinutes();
            var s_uminute = data_minute*1;
            var e_uhour = u_datenow.getHours();
            var s_uhour = data_hour*1;

            if (e_uhour == s_uhour){
                if (e_uminute == s_uminute){
                    t_utime = " Vừa xong";
                }else{
                    t_utime = e_uminute - s_uminute + " Phút trước";
                }
            }else{
                t_utime = e_uhour - s_uhour + " Giờ trước";
            }

            $(".chat-history ul li .comment-item-user .data_time_user").each(function (c_key) {
                if (key == c_key){
                    $(this).html('');
                    $(this).html(t_utime);
                }
            })

            console.log("e_uhour= " + e_uhour);
            console.log("s_uhour= " + s_uhour);
            console.log("e_uminute= " + e_uminute);
            console.log("s_uminute= " + s_uminute);
            console.log("t_utime= " + t_utime);


        })

    }, months[random]*1000);

}());

(function () {
    var chat = {
        messageToSend: '',
        messageResponses: [
            'Dịch vụ nạp uy tín ghê',
            'Uy tín không anh em.',
            'Vãi vừa ấn nạp xong vào game có ngay (y)',
            'Web uy tín đấy, vừa nạp 500k xong.',
            'Nãy có ông bạn nạp 500k xong vào nạp luôn, quá xịn admin ơi.',
            'Thanks admin <3 , uy tín lắm luôn',
            'Nhanh gọn uy tín, thanks admin',
            'Web xịn không scam nha mọi người',
            'Hàng sạch, thanks admin',
            'Vừa nạp xong, quá ngon',
            'Web ok không anh em, có scam không?',
            'Vừa chạy ra quán mua 500k thẻ nạp ăn luôn, ngon quá admin',
            'Nhập nhầm mã thẻ với serial báo admin xử lý trong vòng 1 nốt nhạc, uy tín quá admin ơi',
            'Cứ tưởng lừa đảo, nạp thử 200k nhận luôn kim cương trong 10s',
            '1 vote uy tín cho web nhé, quá ngon luôn',
            'Bị lừa nhiều rồi, giờ mới tìm được web uy tín, thanks ad',
            'Vừa nạp 100k xong',
            'Web ngon vl',
            'Anh em nào chưa nạp thì vào nạp ngay đi đang có khuyến mại',
            'Uy tín lắm admin',
            'Vote 10000k sao nhé, quá uy tín',
            'Có anh em nào vừa từ youtube qua đây nạp k',
            'Ông em vừa giới thiệu, nạp cái ăn luôn, ngon vc',
            'Uy tín nhé anh em',
            'Đã nạp thành công',
            'Đã nạp ở đây 20tr tiền thẻ, vote uy tín nhé',
            'Web nạp ngon thế này mà giờ mới biết',
            'Đã nạp, nhanh lắm nhé',
            'Ngon vcl, +5 sao cho admin',
            'Nghe anh em review ngon quá, tôi ra làm cái thẻ 500k nạp đây',
            'Không scam, web nạp thật, nhận thật nhé !',
            'Đã nạp và thấy ngon ngọt nhé ae',
            'Web này trùm nạp mẹ rồi',
            'Web được đấy anh em',
            'Thấy web được nhiều anh em nạp rồi, yên tâm nạp hehe',
            'Anh em không phải sợ đâu, tôi nạp nhiều web này rồi',
            'Web xịn không scam nha mọi người'
        ],
        init: function () {
            this.cacheDOM();
            this.bindEvents();
            this.render();
        },
        cacheDOM: function () {
            this.$chatHistory = $('.chat-history');
            this.$button = $('.btn-send-message');
            this.$textarea = $('#message-to-send');
            this.$chatHistoryList = this.$chatHistory.find('ul');
        },
        bindEvents: function () {
            this.$button.on('click', this.addMessage.bind(this));
            this.$textarea.on('keyup', this.addMessageEnter.bind(this));
        },
        render: function () {
            this.scrollToBottom();
            if (this.messageToSend.trim() !== '') {
                // var template = Handlebars.compile($("#message-template").html());
                // var context = {
                //     messageOutput: this.messageToSend,
                //     time: this.getCurrentTime()
                // };
                // this.$chatHistoryList.append(template(context));
                // this.scrollToBottom();
                // this.$textarea.val('');

            }

            this.scrollToBottom();


        },
        addMessage: function () {
            this.messageToSend = this.$textarea.val();
            this.render();
        },
        addMessageEnter: function (event) {
            // enter was pressed
            if (event.keyCode === 13) {
                this.addMessage();
            }
        },
        scrollToBottom: function () {
            $('#chatModal .chat-scroll').scrollTop($('#chatModal .chat-scroll')[0].scrollHeight);
        },
        getCurrentTime: function () {
            return new Date().toLocaleTimeString().
            replace(/([\d]+:[\d]{2})(:[\d]{2})(.*)/, "$1$3");
        },
        getRandomItem: function (arr) {
            return arr[Math.floor(Math.random() * arr.length)];
        }
    };

    chat.init();

})();

$('body').on('click','.btn-send-message',function(){
    var context = $('#message-to-send').val();
    var uf_dateTime = new Date();
    var uminute = uf_dateTime.getMinutes();
    var uhour = uf_dateTime.getHours();

    var u_dateTime = new Date().toLocaleTimeString().replace(/([\d]+:[\d]{2})(:[\d]{2})(.*)/, "$1$3");
    var u_html = `
        <li>
            <div class="comment-item comment-item-own comment-item-user" data-hour="${uhour}" data-minute="${uminute}">

                <div class="comment-detail comment-detail-own">
                    <div class="comment-info comment-info-own">

                        <span>${u_dateTime}, </span><small class="data_time_user">Vừa xong</small>
                        <p>Bạn</p>
                    </div>
                    <div class="comment-content comment-content-own">
                        ${context}
                    </div>
                    <div class="comment-interact comment-interact-own">
                        <span id="likeComment"><img
                                src="/assets/backend/images/hearts-suit 1.svg" alt=""> Thích</span>
                        <span id="replyComment"><img
                                src="/assets/backend/images/comment 1.svg" alt=""> Trả lời</span>
                    </div>
                </div>
                <div class="comment-avatar">
                    <img src="/assets/backend/images/user_avatar.png" alt="">
                </div>
            </div>
        </li>`;

    $('.list-unstyled').append(u_html);

    $('#chatModal .chat-scroll').scrollTop($('#chatModal .chat-scroll')[0].scrollHeight);

    $('#message-to-send').val('');
})





