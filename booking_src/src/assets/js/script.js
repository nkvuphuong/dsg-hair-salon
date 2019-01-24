$(document).ready(function () {
    new WOW().init();

    $("body").append('<p id="back-top"> <a href="#top"><i class="fa fa-chevron-up" aria-hidden="true"></i></a> </p>');
    $("#back-top").hide();
    $(window).scroll(function () {
        if ($(this).scrollTop() > 600) {
            $('#back-top').fadeIn();
        } else {
            $('#back-top').fadeOut();
        }
    });
    $('#back-top a').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });
});