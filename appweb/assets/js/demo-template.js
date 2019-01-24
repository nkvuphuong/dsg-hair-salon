$(document).ready(function () {
    $( "#target-mobile" ).click(function() {
        $("#theme-container").css({"max-width": "375px", "max-height": "568px", "margin": "-310px 0px 0px -187px", "top": "50%", "left": "50%"});
        $(this).addClass("active");$( "#target-desktop" ).removeClass("active");$( "#target-tablet" ).removeClass("active");
    });
    $( "#target-tablet" ).click(function() {
        $("#theme-container").css({"max-width": "1024px", "max-height": "568px", "margin": "-310px 0px 0px -512px", "top": "50%", "left": "50%"});
        $(this).addClass("active");$( "#target-mobile" ).removeClass("active");$( "#target-desktop" ).removeClass("active");
    });
    $( "#target-desktop" ).click(function() {
        $("#theme-container").css({"max-width": "100%", "max-height": "100%", "margin": "0px", "top": "0px", "left": "0px"});
        $(this).addClass("active");$( "#target-mobile" ).removeClass("active");$( "#target-tablet" ).removeClass("active");
    });
});