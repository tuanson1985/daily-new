$(document).ready(function () {
    $(function(){
        var slider = new Swiper ('.gallery-slider', {
            autoplay: {
                delay: 2000,

            },

            slidesPerView: 1,
            centeredSlides: true,
            loop: true,
            loopedSlides: 6, //スライドの枚数と同じ値を指定
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

        var thumbs = new Swiper ('.gallery-thumbs', {
            slidesPerView: 5,
            spaceBetween: 2, //スライドの枚数と同じ値を指定
            centeredSlides: true,
            loop: true,
            slideToClickedSlide: true,
        });

        slider.controller.control = thumbs;
        thumbs.controller.control = slider;
    });
})
