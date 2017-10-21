$('.video').parent().click(function () {
    if($(this).children(".video").get(0).paused){
        $(this).children(".video").get(0).play();
        $(this).children(".video-btn").fadeOut();
    }else{
       $(this).children(".video").get(0).pause();
        $(this).children(".video-btn").fadeIn();
    }
});
