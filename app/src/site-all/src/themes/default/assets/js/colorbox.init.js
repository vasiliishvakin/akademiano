$(document).ready(function () {
    $(".photo-thumb").colorbox({
        rel: 'photo-thumb',
        slideshow: true,
        slideshowSpeed: 4000,
        slideshowAuto: false,
//            transition:"none",
        speed: 400,
        maxWidth: "100%", maxHeight: "100%",
        "isFitWidth": true,

        current: "{current} из {total}",
        slideshowStart: "начать слайдшоу",
        slideshowStop: "остановить слайдшоу"
    });
});