$(document).ready(function () {
	/* POPUP TOOLTIP INPUT */
	tippy('.input-register', {
        arrow: true,
        theme: 'honeybee',
        animation : 'fade',
        trigger : 'mouseenter focus',
        hideOnClick : 'false',
    });

	/* MOBILE MENU */
	$('body').scrollspy({ target: '#fly-menu' });
	var slideout = new Slideout({
	  'panel': document.getElementById('maincontent'),
	  'menu': document.getElementById('mobilemenu'),
	  'padding': 420,
	  'tolerance': 70
	});
	slideout.disableTouch();
	document.querySelector('.js-slideout-toggle').addEventListener('click', function() {
	  slideout.open();
	});
	document.querySelector('.js-slideout-close').addEventListener('click', function() {
	  slideout.close();
	});

	function closeMobileMenu(eve) {
	  eve.preventDefault();
	  slideout.close();
	}

	slideout
	  .on('beforeopen', function() {
	    this.panel.classList.add('panel-open');
	  })
	  .on('open', function() {
	    this.panel.addEventListener('click', closeMobileMenu);
	  })
	  .on('beforeclose', function() {
	    this.panel.classList.remove('panel-open');
	    this.panel.removeEventListener('click', closeMobileMenu);
	  });
	  	/*ANIMATION SHOW ELEMENTS ON SCROLL*/
	  	//Cache reference to window and animation items
		var $animation_elements = $('.scroll-to-show');
		var $window = $(window);
		var radius=100;
		function check_if_in_view() {
			var window_height = $window.height();
			var window_top_position = $window.scrollTop();
			var window_bottom_position = (window_top_position + window_height);

			$.each($animation_elements, function() {
			var $element = $(this);
			var element_height = $element.outerHeight();
			var element_top_position = $element.offset().top;
			var element_bottom_position = (element_top_position + element_height);

			//check to see if this current container is within viewport
			if ((element_bottom_position-radius>= window_top_position) &&
			    (element_top_position+radius <= window_bottom_position)) {
			  $element.addClass('in-view');
			} else {
			  //$element.removeClass('in-view');
			}
			});
		}
		$window.on('scroll resize', check_if_in_view);
		$window.trigger('scroll');	

		/* TOOL TIP*/
		tippy('.fa-question-circle', {arrow: true});
		tippy('.store-icon', {arrow: true,theme: 'honeybee'});
		tippy('#websModalTrigger', {
		  html: '#websModal', // OR document.querySelector('#my-template-id')
		  position:'bottom-end',
		  arrow: true,
		  animation: 'fade',
		  theme: 'light',
		  trigger:'click hover',
		  hideOnClick:'true',
		  interactive:"true"
		})
		tippy('.tippy', {arrow: true,theme: 'honeybee'});
		/*tippy('#nhanhoa_form_login_trigger', {
		  html: '#nhanhoa_form_login', // OR document.querySelector('#my-template-id')
		  position:'bottom-end',
		  arrow: false,
		  animation: 'fade',
		  theme: 'light',
		  trigger:'click hover',
		  hideOnClick:'true',
		  interactive:"true",
		  size:"small"
		})*/
		/*The select of payment type tab*/
		$('#payTabSelect').on('change', function(e) {
			console.log($(this).val())
		  $('#payTab li').eq($(this).val()).tab('show');
		});

		/* TOGGLE PRICING TABLE ROWS*/		
		$(".toggleRows").click(function(event) {
			event.preventDefault() ;			
			if($(this).hasClass("open-panel")){
				$(this).parent().parent().nextUntil(".end-row").toggle(); 
				$(this).html('<i class="fa fa-minus-square-o"></i>');
				$(this).removeClass("open-panel");
				$(this).addClass("close-panel");
			}
			else{
				$(this).parent().parent().nextUntil(".end-row").hide(); 
				$(this).html('<i class="fa fa-plus-square-o"></i>');
				$(this).removeClass("close-panel");
				$(this).addClass("open-panel");
			}
		});

		/*TOGGLE STATE OF OBJ*/
		$('.toggle-state').click(function(e){
		    e.stopPropagation();
		    $(this).addClass("state-on");
		    $(document.body).mousedown(function(event) {
	            var target = $(event.target);
	            if (!target.parents().andSelf().is($(this))) { // Clicked outside
	                
	            }
	            else{
	            	$('.toggle-state').removeClass("state-on");
	            }
	        });
		});

        /* FIEXED PACKAGE HEADER SCROLL */
        $('.fixed-item').each(function(i) {
            if( ! $('.fixed-item-frist').hasClass('fixed-item-frist-initiated') ) {
                $('.fixed-item-frist').css({
                    'height': $(this).height() + 'px'
                })
                .addClass('fixed-item-frist-initiated');
            }
            $(this).affix({
                offset: {
                    top: $(this).offset().top-$(this).height() 
                }
            }); 
        });
        var $scroll_elements = $('.fix-el');
        var $window = $(window);
        var radius=100;
        function check_if_affix_in_view() {
            var window_height = $window.height();
            var window_top_position = $window.scrollTop();
            var window_bottom_position = (window_top_position + window_height);

            $.each($scroll_elements, function() {
            var $element = $(this);
            var element_height = $element.outerHeight();
            var element_top_position = $element.offset().top;
            var element_bottom_position = (element_top_position + element_height);

            //check to see if this current container is within viewport
            if ((element_bottom_position-radius>= window_top_position) &&
                (element_top_position+radius <= window_bottom_position)) {
              $element.addClass('in-view');
            } else {
              $element.removeClass('in-view');
            }
            });
        }
        $window.on('scroll resize', check_if_affix_in_view);
        $window.trigger('scroll');

	/* SLIDER SUPPORT */
	$('.owl-carousel').owlCarousel({
        loop: true,
        autoplay:true,
        autoplayTimeout:5000,
        margin: 15,
        responsiveClass: true,
        navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
        responsive: {
        	0: {
            	items: 1,
            	nav: true
          	},
          	480:{
          		items: 2,
            	nav: true
          	},
          	480:{
          		items: 3,
            	nav: true
          	},
          	992: {
            	items: 4,
            	nav: true
          	},
          	1200: {
            	items: 6,
            	nav: true,
          	}
        }
    });
	
});