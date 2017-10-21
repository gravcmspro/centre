$(window).scroll(function(e) {
    val = ($(window).scrollTop() / 150);
    $(".blurred-image").css("opacity", val);
});
