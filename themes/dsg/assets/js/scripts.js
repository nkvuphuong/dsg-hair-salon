$(document).ready(function() {
    /*////////////// MY SLIDER ///////////////*/
    $('#my-slider').show().sliderPro({
        width: 1976, 
        height:800,               
        arrows: true,
        fade: true,
        autoHeight:true,
        centerImage:false,
        autoScaleLayers: false,
        
        buttons: true,  
        thumbnailArrows: true,
        autoplay: true,
        slideSpeed : 300,
        breakpoints: { 
          768: {
            width: 1024, 
        height:800,               
        arrows: true,
        fade: false,
        fadeArrows :false,
        autoHeight:false,
        centerImage:false,
        autoScaleLayers: false,

        buttons: true,  
        thumbnailArrows: true,
        autoplay: true,
        slideSpeed : 300,
          }
        },
    });

    /*////////////// MOBILE NAV ///////////////*/
    $('.mobile-menu nav').show().meanmenu({
        meanMenuContainer: '.menu_mobile_v1',
        meanScreenWidth: "990",
        meanRevealPosition: "left",
        meanMenuOpen: "<span></span>"
    });
    $('.mean-bar').append($('.navbar-nav .mean_book').html());
    $(window).resize(function() {$('.mean-bar').append($('.navbar-nav .mean_book').html())});
});